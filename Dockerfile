FROM php:8.4-apache-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    APACHE_DOCUMENT_ROOT=/app/public \
    APP_ENV=prod \
    APP_DEBUG=0

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
    && docker-php-ext-install \
        intl \
        mbstring \
        xml \
        zip \
        ctype \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

ARG GIT_REPO=https://github.com/AlexandreMonchain/Passphrase.git
ARG GIT_BRANCH=main
ARG CACHE_BUST=1

RUN git clone --branch "${GIT_BRANCH}" --depth 1 "${GIT_REPO}" .

RUN printf 'APP_ENV=prod\nAPP_DEBUG=0\n' > /app/.env

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --no-progress

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /app \
    && chmod -R ug+rwX /app/var

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost/ || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
