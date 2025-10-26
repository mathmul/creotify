FROM php:8.4-fpm-alpine AS base

RUN sed -i 's/dl-cdn.alpinelinux.org/dl-3.alpinelinux.org/g' /etc/apk/repositories \
 && apk add --no-cache \
    bash git curl unzip icu-dev oniguruma-dev libzip-dev zlib-dev \
    autoconf make g++ linux-headers pkgconfig shadow su-exec ca-certificates \
    postgresql-dev postgresql-libs freetype-dev libjpeg-turbo-dev libpng-dev

RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j"$(nproc)" intl bcmath pcntl opcache zip \
 && docker-php-ext-install pdo_pgsql pgsql \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" gd

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/app/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data /var/www/html

COPY docker/app/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]
