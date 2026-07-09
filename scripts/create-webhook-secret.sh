#!/usr/bin/env bash
set -euo pipefail

SECRET_DIR="/home/snelbert/web/hunterswell.co.uk/private"
SECRET_FILE="$SECRET_DIR/github_webhook_secret"

mkdir -p "$SECRET_DIR"
chmod 700 "$SECRET_DIR" || true

if [ -f "$SECRET_FILE" ]; then
  echo "Secret already exists at: $SECRET_FILE"
  echo "Leaving it unchanged."
  echo
  echo "Use this value as the GitHub webhook secret:"
  cat "$SECRET_FILE"
  echo
  exit 0
fi

if command -v openssl >/dev/null 2>&1; then
  openssl rand -hex 32 > "$SECRET_FILE"
else
  date +%s%N | sha256sum | awk '{print $1}' > "$SECRET_FILE"
fi

chmod 600 "$SECRET_FILE" || true

echo "Created webhook secret at: $SECRET_FILE"
echo
echo "Use this value as the GitHub webhook secret:"
cat "$SECRET_FILE"
echo
