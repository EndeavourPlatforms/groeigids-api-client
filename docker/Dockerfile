FROM php:8.3-cli as base

USER root

RUN mkdir -p "/opt/phpstorm-coverage" && \
    chmod -R 777 "/opt/phpstorm-coverage"

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./docker/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM base as composer-deps
WORKDIR /var/www/html

# Install Composer globally
COPY --link --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --link composer.json composer.json
RUN composer install

FROM base as final
WORKDIR /var/www/html

COPY --link --from=composer-deps var/www/html/vendor var/www/html/vendor
COPY --link --from=composer-deps /usr/bin/composer /usr/bin/composer
COPY --link --chown=nobody . /var/www/html

USER nobody
