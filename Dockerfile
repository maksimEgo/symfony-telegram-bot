# Используем PHP с необходимыми расширениями
FROM ghcr.io/roadrunner-server/roadrunner:2024.2.0 AS roadrunner
FROM php:8.3-cli

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql sockets

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем RoadRunner CLI
RUN composer global require spiral/roadrunner-cli
RUN ~/.composer/vendor/bin/rr get

# Копируем файлы проекта
WORKDIR /app
COPY . .

# Устанавливаем зависимости проекта
RUN composer install --no-dev --optimize-autoloader

# Expose порта для HTTP
EXPOSE 8080

# Команда запуска
CMD ["/rr", "serve", "-c", "rr.yaml"]
