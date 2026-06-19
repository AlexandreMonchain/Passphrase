FROM php:8.4-apache-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    APACHE_DOCUMENT_ROOT=/app/public \
    APP_ENV=prod \
    APP_DEBUG=0

WORKDIR /app

# Dépendances système + extensions PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libsqlite3-dev \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo_sqlite \
        pdo_mysql \
        xml \
        zip \
        ctype \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer officiel
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Apache : pointer sur /app/public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Clone du dépôt
ARG GIT_REPO=https://github.com/AlexandreMonchain/Passphrase.git
ARG GIT_BRANCH=dev

RUN git clone --branch "${GIT_BRANCH}" --depth 1 "${GIT_REPO}" .

# .env minimal pour Symfony Runtime (les vraies valeurs viennent de Portainer)
RUN printf 'APP_ENV=prod\nAPP_DEBUG=0\n' > /app/.env

# Dépendances Composer
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --no-progress

# Permissions
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