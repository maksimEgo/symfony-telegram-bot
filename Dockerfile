FROM ghcr.io/roadrunner-server/roadrunner:2024.2.0 AS roadrunner

FROM php:8.3-cli

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

ENV GO_VERSION=1.21.1
RUN curl -OL https://golang.org/dl/go$GO_VERSION.linux-amd64.tar.gz \
    && tar -C /usr/local -xzf go$GO_VERSION.linux-amd64.tar.gz \
    && rm go$GO_VERSION.linux-amd64.tar.gz
ENV PATH=$PATH:/usr/local/go/bin

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql sockets

RUN pecl channel-update pecl.php.net && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer global require spiral/roadrunner-cli \
    && ~/.composer/vendor/bin/rr get

WORKDIR /app

COPY . .

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --verbose

EXPOSE 8080

CMD ["/rr", "serve", "-c", "rr.yaml"]

