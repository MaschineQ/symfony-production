FROM php:8.1-fpm

RUN apt update && apt install -y wget zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

COPY php.ini /usr/local/etc/php/conf.d/
ARG WWW_DATA_UID=1000
ARG WWW_DATA_GID=1000
RUN usermod -u $WWW_DATA_UID www-data
RUN groupmod -g $WWW_DATA_GID www-data
RUN chown $WWW_DATA_UID:$WWW_DATA_UID /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && apt install symfony-cli

USER www-data
WORKDIR /var/www/html

