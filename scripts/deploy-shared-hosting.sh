#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

PHP_BIN="${PHP_BIN:-php}"
USER_DEFINED_COMPOSER_CMD="${COMPOSER_CMD:-}"
COMPOSER_BIN="${COMPOSER_BIN:-}"
PUBLIC_DIR="${PUBLIC_DIR:-public}"
APP_PUBLIC_STORAGE="${APP_PUBLIC_STORAGE:-storage/app/public}"

if [ -n "$USER_DEFINED_COMPOSER_CMD" ]; then
  COMPOSER_CMD="$USER_DEFINED_COMPOSER_CMD"
elif [ -n "$COMPOSER_BIN" ]; then
  COMPOSER_CMD="$COMPOSER_BIN"
elif command -v composer >/dev/null 2>&1; then
  COMPOSER_CMD="composer"
elif [ -f "$ROOT_DIR/composer.phar" ]; then
  COMPOSER_CMD="$PHP_BIN composer.phar"
else
  echo "ERROR: Composer not found."
  echo "Set COMPOSER_CMD (example: COMPOSER_CMD=\"php composer.phar\") and rerun."
  exit 1
fi

read -r -a COMPOSER_CMD_PARTS <<< "$COMPOSER_CMD"

resolve_path() {
  local path="$1"
  if [[ "$path" = /* ]]; then
    echo "$path"
  else
    echo "$ROOT_DIR/$path"
  fi
}

echo "[1/7] Installing PHP dependencies..."
echo "Using Composer command: $COMPOSER_CMD"
"${COMPOSER_CMD_PARTS[@]}" install --no-interaction --prefer-dist --no-dev --optimize-autoloader

echo "[2/7] Building frontend assets..."
if command -v npm >/dev/null 2>&1; then
  npm ci
  npm run build
else
  if [ ! -f "$(resolve_path "$PUBLIC_DIR")/build/manifest.json" ]; then
    echo "ERROR: npm is not available and $PUBLIC_DIR/build/manifest.json is missing."
    echo "Build assets locally and upload $PUBLIC_DIR/build before running this script."
    exit 1
  fi
  echo "npm not found, using existing prebuilt assets in $PUBLIC_DIR/build."
fi

echo "[3/7] Ensuring storage link (without artisan storage:link)..."
TARGET_PATH="$(resolve_path "$APP_PUBLIC_STORAGE")"
PUBLIC_PATH="$(resolve_path "$PUBLIC_DIR")"
LINK_PATH="$PUBLIC_PATH/storage"

if [ ! -d "$PUBLIC_PATH" ]; then
  echo "ERROR: Public directory not found at $PUBLIC_PATH"
  exit 1
fi

if [ ! -d "$TARGET_PATH" ]; then
  echo "ERROR: Storage public directory not found at $TARGET_PATH"
  exit 1
fi

if [ -L "$LINK_PATH" ]; then
  ln -sfn "$TARGET_PATH" "$LINK_PATH"
elif [ -e "$LINK_PATH" ]; then
  echo "Existing $PUBLIC_DIR/storage is not a symlink. Keeping current directory."
else
  ln -s "$TARGET_PATH" "$LINK_PATH" || true
fi

if [ ! -L "$LINK_PATH" ]; then
  echo "Symlink unavailable. Mirroring files to $PUBLIC_DIR/storage as fallback..."
  mkdir -p "$LINK_PATH"
  cp -a "$TARGET_PATH/." "$LINK_PATH/"
fi

echo "[4/7] Running database migrations..."
"$PHP_BIN" artisan migrate --force

echo "[5/7] Clearing stale caches..."
"$PHP_BIN" artisan optimize:clear

echo "[6/7] Rebuilding production caches..."
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "[7/7] Verifying built assets..."
if [ ! -f "$PUBLIC_PATH/build/manifest.json" ]; then
  echo "ERROR: build manifest is missing after deployment."
  exit 1
fi

echo "Deploy completed successfully."
