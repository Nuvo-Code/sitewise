#!/bin/bash

# Build script for local development
# This script builds a Docker image for the current platform only

set -e

# Configuration
IMAGE_NAME="nuvocode/sitewise"
TAG="${1:-latest}"

echo "🚀 Building local Docker image: ${IMAGE_NAME}:${TAG}"

# Build the image for current platform
docker build -t ${IMAGE_NAME}:${TAG} .

echo "✅ Successfully built ${IMAGE_NAME}:${TAG} for local platform"
echo ""
echo "🎯 To run locally:"
echo "   docker-compose up -d"
echo ""
echo "📤 To push to registry:"
echo "   docker push ${IMAGE_NAME}:${TAG}"
echo ""
echo "⚠️  Note: This image is built for your current platform only."
echo "   For production deployment, use ./build-multiarch.sh instead."
