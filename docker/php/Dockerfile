FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
  bash \
  git \
  curl \
  zip \
  unzip \
  libzip-dev \
  icu-dev \
  oniguruma-dev \
  autoconf \
  make \
  g++ \
  postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql pgsql intl mbstring zip bcmath

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN addgroup -g 1000 www && adduser -D -u 1000 -G www www
RUN chown -R www:www /var/www/html

USER www

EXPOSE 9000

CMD ["php-fpm"]
