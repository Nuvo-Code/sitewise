#!/bin/bash

# Sitewise Docker Publish Script
# This script builds and publishes the Docker image for the Sitewise platform

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

print_status "Publishing Sitewise Docker image..."
print_status "Image: ${IMAGE_NAME}:${TAG}"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if logged in to Docker Hub
print_status "Checking Docker Hub authentication..."
if ! docker info | grep -q "Username:"; then
    print_warning "Not logged in to Docker Hub. Attempting login..."
    if ! docker login; then
        print_error "Failed to login to Docker Hub"
        exit 1
    fi
fi

# Build the image
print_status "Building Docker image..."
if ! ./scripts/build-docker.sh "$TAG"; then
    print_error "Failed to build Docker image"
    exit 1
fi

# Push to Docker Hub
print_status "Pushing to Docker Hub..."
if docker push "${IMAGE_NAME}:${TAG}"; then
    print_success "Successfully pushed ${IMAGE_NAME}:${TAG}"
else
    print_error "Failed to push ${IMAGE_NAME}:${TAG}"
    exit 1
fi

# Push latest tag if not already latest
if [ "$TAG" != "latest" ]; then
    print_status "Pushing latest tag..."
    if docker push "${IMAGE_NAME}:latest"; then
        print_success "Successfully pushed ${IMAGE_NAME}:latest"
    else
        print_error "Failed to push ${IMAGE_NAME}:latest"
    fi
fi

print_success "Successfully published Sitewise Docker image!"
print_status "Image is now available at: https://hub.docker.com/r/nuvocode/sitewise"
print_status ""
print_status "To use in Coolify or other deployments:"
print_status "docker pull ${IMAGE_NAME}:${TAG}"
print_status "docker run -p 8000:8000 ${IMAGE_NAME}:${TAG}"
