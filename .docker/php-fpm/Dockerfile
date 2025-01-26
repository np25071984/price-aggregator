FROM php:8.3-fpm

# COPY wait-for-it.sh /usr/bin/wait-for-it
# RUN chmod +x /usr/bin/wait-for-it

RUN apt update
RUN apt install -y --no-install-recommends libssl-dev zlib1g-dev curl \
    git unzip libxml2-dev libpq-dev libzip-dev libpng-dev sendmail
RUN pecl install apcu

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql
RUN docker-php-ext-enable apcu pdo_pgsql sodium
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

RUN apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD composer install ; wait-for-it db:5432 -- php artisan migrate ; php-fpm

EXPOSE 9000