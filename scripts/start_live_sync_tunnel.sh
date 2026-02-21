#!/usr/bin/env bash
set -euo pipefail

# Starts auto-rebuild mode for public assets + quick tunnel.
# Good for external testing via trycloudflare without manual rebuilds.

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WATCH_PID=""

cleanup() {
  if [[ -n "${WATCH_PID}" ]] && kill -0 "${WATCH_PID}" >/dev/null 2>&1; then
    kill "${WATCH_PID}" >/dev/null 2>&1 || true
  fi
}
trap cleanup EXIT INT TERM

cd "${ROOT_DIR}"

if ! command -v docker-compose >/dev/null 2>&1; then
  echo "docker-compose not found in PATH"
  exit 1
fi

if ! command -v cloudflared >/dev/null 2>&1; then
  echo "cloudflared not found in PATH"
  exit 1
fi

echo "1) Ensuring containers are running..."
docker-compose up -d

echo "2) Disabling Vite hot mode (public/hot)..."
rm -f "${ROOT_DIR}/public/hot"

echo "3) Starting asset watcher in container (vite build --watch)..."
docker-compose exec -T app npm run build -- --watch &
WATCH_PID=$!

echo "4) Starting quick tunnel (auto-updates QR_PUBLIC_BASE_URL)..."
"${ROOT_DIR}/scripts/start_quick_tunnel.sh" "http://localhost:8080"
