#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   ./scripts/setup_cloudflared_named_tunnel.sh <tunnel-name> <hostname>
# Example:
#   ./scripts/setup_cloudflared_named_tunnel.sh qr-app qr.example.com

if ! command -v cloudflared >/dev/null 2>&1; then
  echo "cloudflared not found in PATH"
  exit 1
fi

if [[ $# -ne 2 ]]; then
  echo "Usage: $0 <tunnel-name> <hostname>"
  exit 1
fi

TUNNEL_NAME="$1"
HOSTNAME="$2"
CONFIG_DIR="${HOME}/.cloudflared"

mkdir -p "${CONFIG_DIR}"

echo "1) Login to Cloudflare (browser authorization required)..."
cloudflared tunnel login

echo "2) Create tunnel..."
CREATE_OUTPUT="$(cloudflared tunnel create "${TUNNEL_NAME}")"
echo "${CREATE_OUTPUT}"

TUNNEL_ID="$(echo "${CREATE_OUTPUT}" | grep -Eo '[0-9a-f-]{36}' | head -n1)"
if [[ -z "${TUNNEL_ID}" ]]; then
  echo "Failed to parse tunnel id. Check the output above."
  exit 1
fi

echo "3) Create DNS route..."
cloudflared tunnel route dns "${TUNNEL_NAME}" "${HOSTNAME}"

echo "4) Write config file to ${CONFIG_DIR}/config.yml..."
cat > "${CONFIG_DIR}/config.yml" <<EOF
tunnel: ${TUNNEL_ID}
credentials-file: ${CONFIG_DIR}/${TUNNEL_ID}.json

ingress:
  - hostname: ${HOSTNAME}
    service: http://localhost:8080
  - service: http_status:404
EOF

echo "Done."
echo "Next:"
echo "  - set QR_PUBLIC_BASE_URL=https://${HOSTNAME} in .env"
echo "  - run: docker-compose exec app php artisan config:clear"
echo "  - run: docker-compose exec app php artisan config:cache"
echo "  - run tunnel: cloudflared tunnel run ${TUNNEL_NAME}"
