#!/usr/bin/env bash
set -euo pipefail

# Starts an existing Cloudflare named tunnel and syncs QR_PUBLIC_BASE_URL.
# Usage:
#   ./scripts/start_named_tunnel.sh <tunnel-name> [hostname]

if [[ $# -lt 1 || $# -gt 2 ]]; then
  echo "Usage: $0 <tunnel-name> [hostname]"
  exit 1
fi

TUNNEL_NAME="$1"
HOSTNAME="${2:-}"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/.env"
CF_CONFIG="${HOME}/.cloudflared/config.yml"

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

if [[ -z "${HOSTNAME}" ]]; then
  if [[ -f "${CF_CONFIG}" ]]; then
    HOSTNAME="$(awk '/hostname:/ { print $2; exit }' "${CF_CONFIG}" || true)"
  fi
fi

if [[ -z "${HOSTNAME}" ]]; then
  echo "Could not detect hostname from ${CF_CONFIG}."
  echo "Pass hostname explicitly: $0 ${TUNNEL_NAME} qr.your-domain.com"
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

PUBLIC_URL="https://${HOSTNAME}"

echo "Using hostname: ${HOSTNAME}"
update_env_value "${PUBLIC_URL}"
apply_laravel_config
echo "Updated QR_PUBLIC_BASE_URL=${PUBLIC_URL}"
echo "Starting named tunnel: ${TUNNEL_NAME}"
echo "Press Ctrl+C to stop."

cloudflared tunnel run "${TUNNEL_NAME}"
