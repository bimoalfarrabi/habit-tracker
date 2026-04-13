# Shared Hosting Deploy Guide

This guide is for shared hosting environments where:
- `php artisan storage:link` fails because `exec()` is disabled
- frontend assets may disappear after `git pull` and page becomes plain HTML

## 1. One-time server setup

Go to project root, then make deploy script executable:

```bash
chmod +x scripts/deploy-shared-hosting.sh
```

If your host uses a custom PHP path, export it once per session:

```bash
export PHP_BIN=/usr/local/bin/php
```

If your host uses a custom Composer path:

```bash
export COMPOSER_BIN=/usr/local/bin/composer
```

If your web root is outside repo (example: `public_html/ritme`), set `PUBLIC_DIR` to absolute path:

```bash
export PUBLIC_DIR=/home/USERNAME/public_html/ritme
```

## 2. Deploy routine after each pull

```bash
git pull origin main
bash scripts/deploy-shared-hosting.sh
```

The script will:
1. install Composer dependencies in production mode
2. build Vite assets (or verify existing prebuilt assets if npm is unavailable)
3. create `public/storage` link manually without `artisan storage:link`
4. fallback to file copy if symlink is blocked by hosting policy
5. run database migrations
6. clear and rebuild Laravel caches
7. verify `public/build/manifest.json` exists

## 3. If npm is not available on hosting

Build assets locally:

```bash
npm ci
npm run build
```

Upload `public/build` to server, then rerun:

```bash
bash scripts/deploy-shared-hosting.sh
```

If your build output must be uploaded to external web root (`public_html/ritme/build`), upload it there.

## 4. Quick verification checklist

Run:

```bash
php artisan about
ls -lah public/build/manifest.json
ls -lah public/storage
```

If using external web root, replace `public/...` with your `PUBLIC_DIR` path.

Expected:
- `public/build/manifest.json` exists
- `public/storage` is a symlink, or a populated fallback directory
- pages load with proper CSS/JS (not plain native HTML)
