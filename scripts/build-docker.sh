#!/bin/bash

# Sitewise Docker Build Script
# This script builds the Docker image for the Sitewise platform

set -e

# Configuration
IMAGE_NAME="nuvocode/sitewise"
DEFAULT_TAG="latest"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Parse command line arguments
TAG=${1:-$DEFAULT_TAG}
PUSH=${2:-false}

print_status "Building Sitewise Docker image..."
print_status "Image: ${IMAGE_NAME}:${TAG}"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Build the Docker image
print_status "Starting Docker build..."
if docker build -t "${IMAGE_NAME}:${TAG}" .; then
    print_success "Docker image built successfully: ${IMAGE_NAME}:${TAG}"
else
    print_error "Docker build failed!"
    exit 1
fi

# Tag as latest if not already latest
if [ "$TAG" != "latest" ]; then
    print_status "Tagging as latest..."
    docker tag "${IMAGE_NAME}:${TAG}" "${IMAGE_NAME}:latest"
    print_success "Tagged as ${IMAGE_NAME}:latest"
fi

# Show image info
print_status "Image information:"
docker images "${IMAGE_NAME}" --format "table {{.Repository}}\t{{.Tag}}\t{{.Size}}\t{{.CreatedAt}}"

# Push to registry if requested
if [ "$PUSH" = "true" ] || [ "$PUSH" = "yes" ] || [ "$PUSH" = "1" ]; then
    print_status "Pushing image to Docker Hub..."
    
    # Check if logged in to Docker Hub
    if ! docker info | grep -q "Username:"; then
        print_warning "You may need to login to Docker Hub first:"
        print_warning "docker login"
    fi
    
    if docker push "${IMAGE_NAME}:${TAG}"; then
        print_success "Successfully pushed ${IMAGE_NAME}:${TAG}"
        
        if [ "$TAG" != "latest" ]; then
            if docker push "${IMAGE_NAME}:latest"; then
                print_success "Successfully pushed ${IMAGE_NAME}:latest"
            else
                print_error "Failed to push ${IMAGE_NAME}:latest"
            fi
        fi
    else
        print_error "Failed to push image to Docker Hub"
        exit 1
    fi
fi

print_success "Build process completed!"
print_status "To run the container locally:"
print_status "docker run -p 8000:8000 ${IMAGE_NAME}:${TAG}"
