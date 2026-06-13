#!/bin/bash
# Start PHP dev server on first available port in range 8000-8999

cd "$(dirname "$0")/.."

for port in 8000 8080 8888 8765 3000 5000 9000; do
  if ! lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "Starting server on http://localhost:$port"
    php -S localhost:$port router.php
    exit 0
  fi
done

echo "No free port found in range. Try: php -S localhost:9999 router.php"
exit 1
