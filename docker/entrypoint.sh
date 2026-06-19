#!/bin/sh
set -eu

cd /app

echo "🚀 Starting Passphrase..."

mkdir -p var/cache var/log
chown -R www-data:www-data var
chmod -R ug+rwX var

echo "🧹 Clearing Symfony cache..."

php bin/console cache:clear \
    --env=prod \
    --no-debug \
    --no-interaction

php bin/console cache:warmup \
    --env=prod \
    --no-debug \
    --no-interaction

echo "✅ Services ready!"

exec "$@"