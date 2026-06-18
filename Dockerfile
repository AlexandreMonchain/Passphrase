FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    nginx \
    bash \
    supervisor \
    composer \
    unzip \
    su-exec \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    sqlite-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mbstring \
        pdo_sqlite \
        pdo_mysql \
        xml

WORKDIR /app

ARG GIT_REPO=https://github.com/AlexandreMonchain/Passphrase.git
ARG GIT_BRANCH=dev

RUN echo "Cloning repository..." \
    && git clone --branch "${GIT_BRANCH}" --depth 1 "${GIT_REPO}" . \
    && git config --global --add safe.directory /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Symfony Runtime exige actuellement la présence de /app/.env.
# Les vraies valeurs sensibles seront fournies par Portainer.
RUN if [ ! -f /app/.env ]; then \
        printf 'APP_ENV=prod\nAPP_DEBUG=0\n' > /app/.env; \
    fi

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --no-progress

RUN mkdir -p /app/var/cache /app/var/log \
    && chown -R www-data:www-data /app/var \
    && chmod -R ug+rwX /app/var

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /app

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost/ || exit 1

CMD ["/usr/local/bin/entrypoint.sh"]