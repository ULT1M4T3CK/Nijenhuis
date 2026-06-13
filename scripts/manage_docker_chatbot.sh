#!/bin/bash
# Docker Chatbot Management Script
# Manages the Docker container for the chatbot server

set -e

CONTAINER_NAME="nijenhuis-chatbot"

usage() {
    echo "Usage: $0 {start|stop|restart|status|logs|shell|help}"
    echo ""
    echo "Commands:"
    echo "  start    - Start the Docker container"
    echo "  stop     - Stop the Docker container"
    echo "  restart  - Restart the Docker container"
    echo "  status   - Show container status"
    echo "  logs     - Show container logs (tail -f)"
    echo "  shell    - Open shell in container"
    echo "  help     - Show this help message"
    exit 1
}

case "${1}" in
    start)
        echo "Starting Docker container: $CONTAINER_NAME"
        docker start "$CONTAINER_NAME" 2>/dev/null || docker-compose up -d chatbot 2>/dev/null || {
            echo "❌ Container not found. Check docker-compose.yml or create container first."
            exit 1
        }
        echo "✅ Container started"
        echo ""
        echo "Waiting for health check..."
        sleep 3
        docker ps --filter "name=$CONTAINER_NAME" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        ;;
    stop)
        echo "Stopping Docker container: $CONTAINER_NAME"
        docker stop "$CONTAINER_NAME" && echo "✅ Container stopped" || {
            echo "❌ Failed to stop container"
            exit 1
        }
        ;;
    restart)
        echo "Restarting Docker container: $CONTAINER_NAME"
        docker restart "$CONTAINER_NAME" && echo "✅ Container restarted" || {
            echo "❌ Failed to restart container"
            exit 1
        }
        sleep 3
        docker ps --filter "name=$CONTAINER_NAME" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        ;;
    status)
        echo "============================================================"
        echo "Docker Chatbot Container Status"
        echo "============================================================"
        if docker ps --filter "name=$CONTAINER_NAME" --format "{{.Names}}" | grep -q "$CONTAINER_NAME"; then
            echo "✅ Container Status: RUNNING"
            echo ""
            docker ps --filter "name=$CONTAINER_NAME" --format "table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}"
            echo ""
            echo "Health Check:"
            HEALTH=$(docker inspect --format='{{.State.Health.Status}}' "$CONTAINER_NAME" 2>/dev/null || echo "no-healthcheck")
            echo "  Health: $HEALTH"
            echo ""
            echo "Testing API endpoint..."
            if curl -s http://localhost:5001/api/health > /dev/null 2>&1; then
                echo "  ✅ API responding"
            else
                echo "  ❌ API not responding"
            fi
        else
            echo "❌ Container Status: NOT RUNNING"
            echo ""
            if docker ps -a --filter "name=$CONTAINER_NAME" --format "{{.Names}}" | grep -q "$CONTAINER_NAME"; then
                echo "Container exists but is stopped. Start it with:"
                echo "  bash scripts/manage_docker_chatbot.sh start"
            else
                echo "Container not found. Create it first with docker-compose or docker run."
            fi
        fi
        ;;
    logs)
        echo "Showing logs for: $CONTAINER_NAME"
        echo "(Press Ctrl+C to exit)"
        echo ""
        docker logs -f "$CONTAINER_NAME" 2>/dev/null || {
            echo "❌ Container not found or not running"
            exit 1
        }
        ;;
    shell)
        echo "Opening shell in container: $CONTAINER_NAME"
        docker exec -it "$CONTAINER_NAME" /bin/bash || docker exec -it "$CONTAINER_NAME" /bin/sh || {
            echo "❌ Failed to open shell"
            exit 1
        }
        ;;
    help|--help|-h)
        usage
        ;;
    *)
        echo "❌ Unknown command: ${1}"
        echo ""
        usage
        ;;
esac

