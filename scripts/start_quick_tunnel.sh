#!/usr/bin/env bash
set -euo pipefail

# Runs Cloudflare Quick Tunnel and automatically updates QR_PUBLIC_BASE_URL in .env.
# Usage:
#   ./scripts/start_quick_tunnel.sh
#   ./scripts/start_quick_tunnel.sh http://localhost:8080
#   ./scripts/start_quick_tunnel.sh http://localhost:8080 https://my-public-host.example.com

TARGET_URL="${1:-http://localhost:8080}"
MANUAL_PUBLIC_URL="${2:-}"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/.env"
APPLIED=0

if ! command -v cloudflared >/dev/null 2>&1; then
  echo "cloudflared not found in PATH"
  exit 1
fi

if ! command -v docker-compose >/dev/null 2>&1; then
  echo "docker-compose not found in PATH"
  exit 1
fi

if [[ ! -f "${ENV_FILE}" ]]; then
  echo ".env not found at ${ENV_FILE}"
  exit 1
fi

update_env_value() {
  local new_url="$1"
  local tmp_file
  tmp_file="$(mktemp)"

  awk -v val="${new_url}" '
    BEGIN { replaced = 0 }
    /^QR_PUBLIC_BASE_URL=/ {
      print "QR_PUBLIC_BASE_URL=" val
      replaced = 1
      next
    }
    { print }
    END {
      if (!replaced) {
        print "QR_PUBLIC_BASE_URL=" val
      }
    }
  ' "${ENV_FILE}" > "${tmp_file}"

  mv "${tmp_file}" "${ENV_FILE}"
}

normalize_public_url() {
  local value="$1"
  local no_scheme host

  value="${value%%[[:space:]]*}"
  value="${value%/}"

  if [[ ! "${value}" =~ ^https?://[a-zA-Z0-9.-]+(:[0-9]+)?(/.*)?$ ]]; then
    return 1
  fi

  no_scheme="${value#http://}"
  no_scheme="${no_scheme#https://}"
  host="${no_scheme%%/*}"
  host="${host%%:*}"

  if [[ "${host}" == "localhost" ]]; then
    echo "${value}"
    return 0
  fi

  if [[ ! "${host}" =~ ^[0-9.]+$ ]] && [[ "${host}" != *.* ]]; then
    return 1
  fi

  if [[ "${host}" =~ ^www$ ]]; then
    return 1
  fi

  echo "${value}"
  return 0
}

apply_laravel_config() {
  if docker-compose -f "${ROOT_DIR}/docker-compose.yml" exec -T app php artisan config:clear >/dev/null 2>&1 \
    && docker-compose -f "${ROOT_DIR}/docker-compose.yml" exec -T app php artisan config:cache >/dev/null 2>&1; then
    echo "Applied Laravel config cache with new QR_PUBLIC_BASE_URL"

    if docker-compose -f "${ROOT_DIR}/docker-compose.yml" exec -T app php artisan qr:refresh-dynamic; then
      echo "Regenerated existing dynamic QR images with the new public URL"
    else
      echo "Could not regenerate existing dynamic QR images automatically."
      echo "Run manually:"
      echo "  docker-compose exec app php artisan qr:refresh-dynamic"
    fi
  else
    echo "Could not apply Laravel config cache automatically."
    echo "Run manually:"
    echo "  docker-compose exec app php artisan config:clear"
    echo "  docker-compose exec app php artisan config:cache"
  fi
}

pick_quick_tunnel_url() {
  local line="$1"
  local clean_line="$1"
  local token
  local msg=""

  # Strip ANSI escape sequences if present.
  clean_line="$(printf '%s' "${clean_line}" | sed -E $'s/\x1B\\[[0-9;]*[A-Za-z]//g')"

  # Parse JSON output from cloudflared when available.
  if [[ "${clean_line}" == \{* ]] && command -v jq >/dev/null 2>&1; then
    msg="$(printf '%s' "${clean_line}" | jq -r '.message // empty' 2>/dev/null || true)"
    if [[ -n "${msg}" ]]; then
      clean_line="${msg}"
    fi
  fi

  trim_trailing_punctuation() {
    local t="$1"
    printf '%s' "${t}" | sed -E 's/[|),;]+$//; s/[.]+$//'
  }

  # 1) Preferred tunnel hosts (old and new patterns).
  while IFS= read -r token; do
    [[ -z "${token}" ]] && continue
    token="$(trim_trailing_punctuation "${token}")"
    if [[ "${token}" == "https://api.trycloudflare.com" ]]; then
      continue
    fi
    echo "${token}"
    return 0
  done < <(echo "${clean_line}" | grep -Eio 'https://[a-z0-9.-]+\.(trycloudflare\.com|cfargotunnel\.com|argotunnel\.com)' || true)

  # 2) Fallback: any public https URL from cloudflared logs except known non-tunnel links.
  while IFS= read -r token; do
    [[ -z "${token}" ]] && continue
    token="$(trim_trailing_punctuation "${token}")"

    case "${token}" in
      https://api.trycloudflare.com|https://developers.cloudflare.com/*|https://github.com/*|https://www.cloudflare.com/*|https://dash.cloudflare.com/*)
        continue
        ;;
      https://localhost*|https://127.0.0.1*|https://0.0.0.0*)
        continue
        ;;
    esac

    echo "${token}"
    return 0
  done < <(echo "${clean_line}" | grep -Eio 'https://[^[:space:]"]+' || true)

  return 1
}

echo "Starting quick tunnel to ${TARGET_URL}"
echo "Press Ctrl+C to stop the tunnel."

if [[ -n "${MANUAL_PUBLIC_URL}" ]]; then
  if ! MANUAL_PUBLIC_URL="$(normalize_public_url "${MANUAL_PUBLIC_URL}")"; then
    echo "Invalid manual public URL: ${2}"
    echo "Expected format: https://your-public-host.example.com"
    exit 1
  fi

  update_env_value "${MANUAL_PUBLIC_URL}"
  apply_laravel_config
  echo "Using manual public URL override: ${MANUAL_PUBLIC_URL}"
  APPLIED=1
fi

while IFS= read -r line; do
  echo "${line}"

  if [[ "${APPLIED}" -eq 0 ]]; then
    QUICK_URL="$(pick_quick_tunnel_url "${line}" || true)"
    QUICK_URL="$(normalize_public_url "${QUICK_URL:-}" || true)"
    if [[ -n "${QUICK_URL}" ]]; then
      update_env_value "${QUICK_URL}"
      apply_laravel_config
      echo "Updated QR_PUBLIC_BASE_URL=${QUICK_URL}"
      echo "Current .env value: $(grep -E '^QR_PUBLIC_BASE_URL=' "${ENV_FILE}" | tail -n1)"
      APPLIED=1
    fi
  fi
done < <(cloudflared tunnel --config /dev/null --protocol http2 --output json --url "${TARGET_URL}" 2>&1)

if [[ "${APPLIED}" -eq 0 ]]; then
  echo "Warning: quick tunnel public URL was not detected, QR_PUBLIC_BASE_URL was not changed."
  echo "You can set it manually:"
  echo "  ./scripts/start_quick_tunnel.sh ${TARGET_URL} https://your-public-host.example.com"
fi
