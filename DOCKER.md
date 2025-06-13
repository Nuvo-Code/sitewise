# Sitewise Docker Guide

This guide explains how to build, publish, and deploy the Sitewise Docker image with multi-architecture support.

## Quick Start

### For Production (Multi-Architecture)

**⚠️ Important for Coolify**: Always use multi-architecture builds to avoid "exec format error"

```bash
# Build and push multi-architecture image (AMD64 + ARM64)
./build-multiarch.sh

# Build with specific tag
./build-multiarch.sh v1.0.0
```

### For Local Development

```bash
# Build for current platform only (faster)
./build-local.sh

# Run with docker-compose
docker-compose up -d
```

### Legacy Build Scripts (if available)

```bash
# Build with default 'latest' tag
./scripts/build-docker.sh

# Build with a specific tag
./scripts/build-docker.sh v1.0.0

# Build and push to Docker Hub
./scripts/build-docker.sh latest true
```

## Manual Commands

### Building Manually

```bash
# Build the image
docker build -t nuvocode/sitewise:latest .

# Tag for different versions
docker tag nuvocode/sitewise:latest nuvocode/sitewise:v1.0.0
```

### Publishing Manually

```bash
# Login to Docker Hub (if not already logged in)
docker login

# Push the image
docker push nuvocode/sitewise:latest
docker push nuvocode/sitewise:v1.0.0
```

## Running the Container

### Local Development

```bash
# Run the container
docker run -p 80:80 nuvocode/sitewise:latest

# Run with environment variables
docker run -p 80:80 \
  -e APP_ENV=production \
  -e APP_KEY=your-app-key \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=your-db-host \
  nuvocode/sitewise:latest
```

### Production Deployment

For production deployments with Coolify, use the provided `docker-compose.coolify.yml`:

```yaml
# docker-compose.coolify.yml (already configured)
services:
  app:
    image: 'nuvocode/sitewise:latest'
    ports:
      - "${APP_PORT:-80}:80"
    environment:
      - APP_ENV=production
      - 'APP_KEY=${APP_KEY}'
      - DB_CONNECTION=pgsql
      - 'DB_HOST=${DB_HOST:-database}'
    depends_on:
      - database
      - redis
```

## Image Details

- **Base Image**: unit:1.34.1-php8.3 (NGINX Unit with PHP 8.3)
- **Exposed Port**: 80 (changed from 8000 for better compatibility)
- **Working Directory**: /var/www/html
- **User**: unit
- **PHP Extensions**: pcntl, opcache, pdo, pdo_pgsql, pgsql, pdo_mysql, intl, zip, gd, exif, ftp, bcmath, redis
- **Architectures**: linux/amd64, linux/arm64

## Environment Variables

The container supports all standard Laravel environment variables:

- `APP_ENV` - Application environment (production, staging, etc.)
- `APP_KEY` - Application encryption key
- `APP_URL` - Application URL
- `DB_CONNECTION` - Database connection type
- `DB_HOST` - Database host
- `DB_PORT` - Database port
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

## Architecture Support

The Docker image supports multiple architectures:
- **linux/amd64** - Intel/AMD 64-bit (most cloud servers, Coolify)
- **linux/arm64** - ARM 64-bit (Apple Silicon, ARM servers)

## Troubleshooting

### "exec format error" in Coolify

This error occurs when there's an architecture mismatch between your built image and the deployment server.

**Solution**: Use multi-architecture builds:

```bash
# Build for both AMD64 and ARM64
./build-multiarch.sh

# Or build for specific architecture if you know your server type
docker buildx build --platform linux/amd64 --tag nuvocode/sitewise:latest --push .
```

**Check your server architecture**:
```bash
# On your Coolify server
uname -m
# x86_64 = AMD64 (most common)
# aarch64 = ARM64
```

### Common Issues

1. **Architecture Mismatch**: Use `./build-multiarch.sh` for production
2. **Permission Issues**: Ensure storage and cache directories are writable
3. **Database Connection**: Verify database environment variables
4. **App Key**: Generate and set APP_KEY for production
5. **Port Conflicts**: Ensure ports 80, 5432, 6379 are available locally

### Logs

To view container logs:

```bash
# Docker Compose
docker-compose logs -f app

# Single container
docker logs <container-id>
```

### Shell Access

To access the container shell:

```bash
# Docker Compose
docker-compose exec app /bin/bash

# Single container
docker exec -it <container-id> /bin/bash
```
