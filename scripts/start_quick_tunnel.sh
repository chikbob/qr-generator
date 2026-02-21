#!/usr/bin/env bash
set -euo pipefail

# Runs Cloudflare Quick Tunnel and automatically updates QR_PUBLIC_BASE_URL in .env.
# Usage:
#   ./scripts/start_quick_tunnel.sh
#   ./scripts/start_quick_tunnel.sh http://localhost:8080

TARGET_URL="${1:-http://localhost:8080}"
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

apply_laravel_config() {
  if docker-compose -f "${ROOT_DIR}/docker-compose.yml" exec -T app php artisan config:clear >/dev/null 2>&1 \
    && docker-compose -f "${ROOT_DIR}/docker-compose.yml" exec -T app php artisan config:cache >/dev/null 2>&1; then
    echo "Applied Laravel config cache with new QR_PUBLIC_BASE_URL"
  else
    echo "Could not apply Laravel config cache automatically."
    echo "Run manually:"
    echo "  docker-compose exec app php artisan config:clear"
    echo "  docker-compose exec app php artisan config:cache"
  fi
}

pick_quick_tunnel_url() {
  local line="$1"
  local token
  while IFS= read -r token; do
    [[ -z "${token}" ]] && continue
    if [[ "${token}" == "https://api.trycloudflare.com" ]]; then
      continue
    fi
    echo "${token}"
    return 0
  done < <(echo "${line}" | grep -Eo 'https://[a-z0-9-]+\.trycloudflare\.com' || true)
  return 1
}

echo "Starting quick tunnel to ${TARGET_URL}"
echo "Press Ctrl+C to stop the tunnel."

cloudflared tunnel --protocol http2 --url "${TARGET_URL}" 2>&1 | while IFS= read -r line; do
  echo "${line}"

  if [[ "${APPLIED}" -eq 0 ]]; then
    QUICK_URL="$(pick_quick_tunnel_url "${line}" || true)"
    if [[ -n "${QUICK_URL}" ]]; then
      update_env_value "${QUICK_URL}"
      apply_laravel_config
      echo "Updated QR_PUBLIC_BASE_URL=${QUICK_URL}"
      APPLIED=1
    fi
  fi
done
