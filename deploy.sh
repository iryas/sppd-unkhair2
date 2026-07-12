#!/usr/bin/env bash
#
# Script update/deploy SPPD-UNKHAIR di server produksi.
# Cara pakai (dari root project):  bash deploy.sh
#
set -euo pipefail
cd "$(dirname "$0")"

BRANCH="SPPD2"

echo "==> [1/6] Aktifkan mode maintenance"
php artisan down --retry=15 || true
# apapun yang terjadi, pastikan aplikasi kembali online saat script selesai
trap 'php artisan up || true' EXIT

echo "==> [2/6] Ambil update terbaru dari GitHub (branch ${BRANCH})"
git fetch --depth 1 origin "${BRANCH}"
git reset --hard "origin/${BRANCH}"

echo "==> [3/6] Update dependency (produksi, hemat memori)"
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "==> [4/6] Jalankan migrasi database (bila ada yang baru)"
php artisan migrate --force

echo "==> [5/6] Bersihkan cache (config, route, view, cache)"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "==> [6/6] Selesai"
# trap EXIT akan otomatis menjalankan: php artisan up
echo "✅ Deploy selesai. Aplikasi kembali online."
