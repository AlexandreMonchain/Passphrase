#!/bin/sh
set -e

echo "🚀 Starting Passphrase..."

# Clear cache
echo "🧹 Warming up cache..."
php bin/console cache:warmup --no-interaction 2>/dev/null || true

# Start supervisord
echo "✅ Services ready!"
exec /usr/bin/supervisord -c /etc/supervisord.conf

