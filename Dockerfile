FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    nginx \
    bash \
    supervisor \
    composer \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    sqlite-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) intl mbstring pdo_sqlite xml

# Create app user
RUN addgroup -g 1000 app && adduser -D -u 1000 -G app app

# Set working directory
WORKDIR /app

# Build arguments
ARG GIT_REPO=https://github.com/AlexandreMonchain/Passphrase.git
ARG GIT_BRANCH=main

# Clone repository
RUN echo "Cloning repository..." && \
    git clone --branch ${GIT_BRANCH} --depth 1 ${GIT_REPO} . && \
    git config --global --add safe.directory /app

# Install PHP dependencies
RUN echo "Installing dependencies..." && \
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Create necessary directories
RUN echo "Creating directories..." && \
    mkdir -p var/cache var/log && \
    chown -R app:app var

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R app:app /app && \
    find /app/public -type d -exec chmod 755 {} \; && \
    find /app/public -type f -exec chmod 644 {} \;

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/index.php || exit 1

# Start services
CMD ["/usr/local/bin/entrypoint.sh"]
