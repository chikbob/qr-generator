#!/bin/bash

# Local development deployment script
# Sets up the app locally with docker-compose

set -e

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSE_FILE="docker-compose.yml"

GREEN='\033[0;32m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[DEV SETUP]${NC} $1"
}

log "Setting up Laravel app for local development..."

cd "$PROJECT_ROOT"

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    log "Creating .env from .env.example"
    cp .env.example .env
fi

# Copy production env example
if [ ! -f .env.production ]; then
    log "Creating .env.production from .env.production.example"
    cp .env.production.example .env.production
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" .env || grep -q "APP_KEY=base64:YOUR_APP_KEY" .env; then
    log "Generating APP_KEY"
    docker-compose run --rm app php artisan key:generate
fi

# Build and start containers
log "Building and starting containers..."
docker-compose up -d --build

# Wait for MySQL to be ready
log "Waiting for database to be ready..."
sleep 5

# Run migrations
log "Running migrations and seeding..."
docker-compose exec -T app php artisan migrate:fresh --seed

# Build frontend assets
log "Building frontend assets..."
docker-compose exec -T app npm run build

log "Setup complete!"
log "App is running at http://localhost:8080"
log "PHPMyAdmin is available at http://localhost:8081"
log ""
log "Useful commands:"
log "  docker-compose up         - Start containers"
log "  docker-compose down       - Stop containers"
log "  docker-compose logs -f    - View logs"
log "  docker-compose exec app bash  - Access app container"
