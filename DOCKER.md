# Sitewise Docker Guide

This guide explains how to build, publish, and deploy the Sitewise Docker image.

## Quick Start

### Building the Image

To build the Docker image locally:

```bash
# Build with default 'latest' tag
./scripts/build-docker.sh

# Build with a specific tag
./scripts/build-docker.sh v1.0.0

# Build and push to Docker Hub
./scripts/build-docker.sh latest true
```

### Publishing to Docker Hub

To build and publish the image to Docker Hub:

```bash
# Publish with default 'latest' tag
./scripts/publish-docker.sh

# Publish with a specific tag
./scripts/publish-docker.sh v1.0.0
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
docker run -p 8000:8000 nuvocode/sitewise:latest

# Run with environment variables
docker run -p 8000:8000 \
  -e APP_ENV=production \
  -e APP_KEY=your-app-key \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=your-db-host \
  nuvocode/sitewise:latest
```

### Production Deployment

For production deployments with Coolify or other platforms, use:

```yaml
# docker-compose.yml example
version: '3.8'
services:
  sitewise:
    image: nuvocode/sitewise:latest
    ports:
      - "8000:8000"
    environment:
      - APP_ENV=production
      - APP_KEY=${APP_KEY}
      - DB_CONNECTION=${DB_CONNECTION}
      - DB_HOST=${DB_HOST}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
    volumes:
      - storage_data:/var/www/html/storage
    restart: unless-stopped

volumes:
  storage_data:
```

## Image Details

- **Base Image**: unit:1.34.1-php8.3 (NGINX Unit with PHP 8.3)
- **Exposed Port**: 8000
- **Working Directory**: /var/www/html
- **User**: unit
- **PHP Extensions**: pcntl, opcache, pdo, pdo_pgsql, pgsql, pdo_mysql, intl, zip, gd, exif, ftp, bcmath, redis

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

## Troubleshooting

### Common Issues

1. **Permission Issues**: Ensure storage and cache directories are writable
2. **Database Connection**: Verify database environment variables
3. **App Key**: Generate and set APP_KEY for production

### Logs

To view container logs:

```bash
docker logs <container-id>
```

### Shell Access

To access the container shell:

```bash
docker exec -it <container-id> /bin/bash
```
