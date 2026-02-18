# Laravel Deployment Pipeline

Complete CI/CD deployment pipeline for Laravel application with Docker.

## Pipeline Architecture

```
Push to main/develop
    ↓
GitHub Actions Workflow
    ├─ Test Stage
    │  ├─ Setup PHP 8.1
    │  ├─ Install Composer dependencies
    │  ├─ Setup Node.js 20
    │  ├─ Run PHPUnit tests
    │  ├─ Build frontend assets
    │  └─ Run code linting (Laravel Pint)
    │
    ├─ Build Stage (on main/develop push)
    │  ├─ Build multi-stage Docker image
    │  └─ Push to GitHub Container Registry
    │
    └─ Deploy Stage (on main branch push)
       └─ SSH to production server and run deploy script
```

## Files Created

### 1. **GitHub Actions Workflow** (`.github/workflows/deploy.yml`)
- **Test Job**: Runs unit tests and linting on every push/PR
- **Build Job**: Creates optimized Docker image and pushes to registry
- **Deploy Job**: Deploys to production on main branch merges

### 2. **Production Dockerfile** (`Dockerfile.prod`)
Multi-stage build:
- **Stage 1**: Build PHP dependencies with Composer
- **Stage 2**: Build frontend assets with Node
- **Stage 3**: Slim runtime with Apache, PHP, and compiled assets

### 3. **Production Docker Compose** (`docker-compose.prod.yml`)
Services:
- **app**: Laravel application with health checks
- **db**: MySQL 8.0 with persistent storage
- **phpmyadmin**: Database management UI
- **redis**: Caching and session store

### 4. **Deployment Scripts**

#### `scripts/deploy.sh` - Production Deployment
```bash
./scripts/deploy.sh
```
- Pulls latest image from registry
- Stops old containers
- Starts new containers
- Runs migrations
- Verifies health

#### `scripts/setup-dev.sh` - Local Development Setup
```bash
./scripts/setup-dev.sh
```
- Generates app key
- Starts local docker-compose
- Runs migrations and seeds
- Builds frontend assets

### 5. **Configuration Files**

#### `.env.production.example`
Template for production environment variables

#### `.dockerignore`
Optimizes build context size

#### `routes/health.php`
Health check endpoint for container liveness probes

#### `tests/Feature/HealthCheckTest.php`
Unit tests for health endpoints

## Setup Instructions

### 1. Initial Setup

Copy environment templates:
```bash
cp .env.example .env
cp .env.production.example .env.production
```

Edit `.env.production` with actual values:
```env
APP_KEY=base64:...                    # Generate with `php artisan key:generate`
APP_URL=https://yourdomain.com
DB_PASSWORD=your_secure_password
REGISTRY=ghcr.io
IMAGE_NAME=your-org/laravel-app
```

### 2. Local Development

```bash
./scripts/setup-dev.sh
```

Access the app:
- App: http://localhost:8080
- PHPMyAdmin: http://localhost:8081

### 3. GitHub Setup

Add secrets to GitHub Actions:
1. Go to repository Settings → Secrets and variables → Actions
2. Add the following secrets:

```
DEPLOY_HOST         = your-production-server.com
DEPLOY_USER         = deploy_user
DEPLOY_KEY          = (private SSH key for deployment)
DEPLOY_PATH         = /path/to/app/on/server
```

Ensure your server has:
- Docker and Docker Compose installed
- SSH key-based authentication configured
- Proper permissions for deploy user

### 4. Container Registry Setup

For GitHub Container Registry (GHCR):
1. Create a Personal Access Token (PAT) with `read:packages` scope
2. GitHub Actions automatically uses `GITHUB_TOKEN` for pushes

## Running Tests Locally

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test file
docker-compose exec app php artisan test tests/Feature/HealthCheckTest.php

# Run with coverage
docker-compose exec app php artisan test --coverage
```

## Production Deployment

### First-time Server Setup

SSH to your server and run:
```bash
# Create app directory
mkdir -p /path/to/app

# Initialize git repo
cd /path/to/app
git init
git config receive.denyCurrentBranch updateInstead

# Create deploy directory for scripts
mkdir -p scripts

# Copy deploy.sh
scp scripts/deploy.sh user@host:/path/to/app/scripts/

# Set permissions
ssh user@host chmod +x /path/to/app/scripts/deploy.sh

# Copy .env.production
scp .env.production user@host:/path/to/app/
```

### Deploy a New Version

Simply push to main branch:
```bash
git push origin main
```

GitHub Actions will automatically:
1. Run tests
2. Build Docker image
3. Push to registry
4. Deploy to production

### Manual Deployment

If you need to deploy manually:
```bash
ssh deploy_user@your-server.com "cd /path/to/app && bash scripts/deploy.sh"
```

## Monitoring & Logs

View container logs:
```bash
docker-compose -f docker-compose.prod.yml logs -f app
```

Check container health:
```bash
docker-compose -f docker-compose.prod.yml ps
```

Access container shell:
```bash
docker-compose -f docker-compose.prod.yml exec app bash
```

## Database Backups

Backup MySQL database:
```bash
docker-compose -f docker-compose.prod.yml exec db mysqldump -u laravel -p $DB_PASSWORD $DB_DATABASE > backup.sql
```

Restore from backup:
```bash
docker-compose -f docker-compose.prod.yml exec -T db mysql -u laravel -p $DB_PASSWORD $DB_DATABASE < backup.sql
```

## Scaling & Performance

### Enable Redis for Sessions/Caching

Edit `.env.production`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
```

### Optimize for Production

The `Dockerfile.prod` includes:
- Multi-stage builds (reduces final image size)
- Compiled assets from frontend build
- Production-ready Apache configuration
- Config and route caching

### Resource Limits

Edit `docker-compose.prod.yml` to add resource limits:
```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G
```

## Troubleshooting

### Deployment Fails

1. Check GitHub Actions logs for build/test failures
2. Verify Docker image was pushed to registry
3. Check SSH keys and server connectivity
4. Review deployment script output: `ssh user@host docker-compose -f docker-compose.prod.yml logs`

### App Not Responding

```bash
# Check if containers are running
docker-compose -f docker-compose.prod.yml ps

# View app logs
docker-compose -f docker-compose.prod.yml logs app

# Restart services
docker-compose -f docker-compose.prod.yml restart
```

### Database Connection Issues

```bash
# Check MySQL is running
docker-compose -f docker-compose.prod.yml exec db mysql -u laravel -p

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate
```

## Next Steps

1. Configure GitHub Actions secrets for your deployment environment
2. Test locally with `./scripts/setup-dev.sh`
3. Push to main branch to trigger production deployment
4. Monitor logs and verify health check endpoints
5. Set up monitoring and alerting (CloudWatch, DataDog, etc.)

## Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Compose Reference](https://docs.docker.com/compose/reference/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
