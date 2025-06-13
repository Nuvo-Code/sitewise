#!/bin/bash

# Build script for multi-architecture Docker images
# This script builds and pushes Docker images for both AMD64 and ARM64 architectures

set -e

# Configuration
IMAGE_NAME="nuvocode/sitewise"
TAG="${1:-latest}"
PLATFORMS="linux/amd64,linux/arm64"

echo "🚀 Building multi-architecture Docker image: ${IMAGE_NAME}:${TAG}"
echo "📦 Platforms: ${PLATFORMS}"

# Check if buildx is available
if ! docker buildx version > /dev/null 2>&1; then
    echo "❌ Docker buildx is not available. Please install Docker Desktop or enable buildx."
    exit 1
fi

# Create a new builder instance if it doesn't exist
BUILDER_NAME="sitewise-builder"
if ! docker buildx inspect $BUILDER_NAME > /dev/null 2>&1; then
    echo "🔧 Creating new buildx builder: $BUILDER_NAME"
    docker buildx create --name $BUILDER_NAME --driver docker-container --bootstrap
fi

# Use the builder
docker buildx use $BUILDER_NAME

echo "🔨 Building and pushing multi-architecture image..."

# Build and push the multi-architecture image
docker buildx build \
    --platform $PLATFORMS \
    --tag ${IMAGE_NAME}:${TAG} \
    --push \
    .

echo "✅ Successfully built and pushed ${IMAGE_NAME}:${TAG} for platforms: ${PLATFORMS}"
echo ""
echo "🎯 To use this image in Coolify, make sure your docker-compose.coolify.yml uses:"
echo "   image: '${IMAGE_NAME}:${TAG}'"
echo ""
echo "💡 You can also build for a specific platform only:"
echo "   docker buildx build --platform linux/amd64 --tag ${IMAGE_NAME}:${TAG}-amd64 --push ."
echo "   docker buildx build --platform linux/arm64 --tag ${IMAGE_NAME}:${TAG}-arm64 --push ."
