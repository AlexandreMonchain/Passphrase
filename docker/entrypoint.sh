#!/bin/sh
set -eu

cd /app

echo "🚀 Starting Passphrase..."

mkdir -p var/cache var/log
chown -R www-data:www-data var
chmod -R ug+rwX var

echo "🧹 Clearing Symfony cache..."

su-exec www-data php bin/console cache:clear \
    --env=prod \
    --no-debug \
    --no-interaction

su-exec www-data php bin/console cache:warmup \
    --env=prod \
    --no-debug \
    --no-interaction

echo "✅ Services ready!"

exec /usr/bin/supervisord -c /etc/supervisord.conf