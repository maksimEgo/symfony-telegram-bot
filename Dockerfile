# Используем PHP с необходимыми расширениями
FROM ghcr.io/roadrunner-server/roadrunner:2024.2.0 AS roadrunner
FROM php:8.3-cli

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql sockets

RUN pecl channel-update pecl.php.net

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer global require spiral/roadrunner-cli
RUN ~/.composer/vendor/bin/rr get

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY config/xDebug/xDebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["/rr", "serve", "-c", "rr.yaml"]
