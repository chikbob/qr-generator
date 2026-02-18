#!/bin/bash
set -e

# Laravel App Deployment Script
# This script deploys the app to a production server

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Configuration
IMAGE_REGISTRY="${IMAGE_REGISTRY:-ghcr.io}"
IMAGE_NAME="${IMAGE_NAME:-your-org/laravel-app}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
COMPOSE_FILE="docker-compose.prod.yml"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if docker and docker-compose are installed
check_requirements() {
    log_info "Checking requirements..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed"
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed"
        exit 1
    fi
    
    log_info "Requirements met"
}

# Load environment variables
load_env() {
    log_info "Loading environment variables..."
    
    if [ ! -f "$PROJECT_ROOT/.env.production" ]; then
        log_error ".env.production not found"
        exit 1
    fi
    
    export $(cat "$PROJECT_ROOT/.env.production" | grep -v '#' | xargs)
}

# Pull latest image
pull_image() {
    log_info "Pulling latest Docker image..."
    
    docker pull "$IMAGE_REGISTRY/$IMAGE_NAME:$IMAGE_TAG" || {
        log_error "Failed to pull image"
        exit 1
    }
}

# Stop existing containers
stop_services() {
    log_info "Stopping existing services..."
    
    cd "$PROJECT_ROOT"
    docker-compose -f "$COMPOSE_FILE" down 2>/dev/null || true
}

# Start services
start_services() {
    log_info "Starting services..."
    
    cd "$PROJECT_ROOT"
    REGISTRY="$IMAGE_REGISTRY" IMAGE_NAME="$IMAGE_NAME" IMAGE_TAG="$IMAGE_TAG" \
        docker-compose -f "$COMPOSE_FILE" up -d
    
    if [ $? -ne 0 ]; then
        log_error "Failed to start services"
        exit 1
    fi
}

# Wait for services to be healthy
wait_for_health() {
    log_info "Waiting for services to be healthy..."
    
    max_attempts=30
    attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        if docker-compose -f "$COMPOSE_FILE" ps | grep -q "healthy"; then
            log_info "Services are healthy"
            return 0
        fi
        
        attempt=$((attempt + 1))
        sleep 2
    done
    
    log_warn "Services did not become healthy within timeout"
}

# Run migrations
run_migrations() {
    log_info "Running database migrations..."
    
    cd "$PROJECT_ROOT"
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
    
    if [ $? -ne 0 ]; then
        log_error "Migrations failed"
        exit 1
    fi
}

# Verify deployment
verify_deployment() {
    log_info "Verifying deployment..."
    
    cd "$PROJECT_ROOT"
    
    # Check if app container is running
    if ! docker-compose -f "$COMPOSE_FILE" ps app | grep -q "Up"; then
        log_error "App container is not running"
        exit 1
    fi
    
    # Check if database is running
    if ! docker-compose -f "$COMPOSE_FILE" ps db | grep -q "Up"; then
        log_error "Database container is not running"
        exit 1
    fi
    
    log_info "Deployment verified"
}

# Rollback function
rollback() {
    log_warn "Rolling back deployment..."
    
    cd "$PROJECT_ROOT"
    docker-compose -f "$COMPOSE_FILE" down
    
    log_info "Rollback complete"
}

# Main deployment flow
main() {
    log_info "Starting deployment..."
    
    check_requirements
    load_env
    
    # Set trap to rollback on error
    trap rollback ERR
    
    pull_image
    stop_services
    start_services
    wait_for_health
    run_migrations
    verify_deployment
    
    log_info "Deployment completed successfully!"
    log_info "App URL: $APP_URL"
}

# Run main function
main "$@"
