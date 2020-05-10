FROM wordpress:latest

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    libbz2-dev \
    unzip wget

# Install various PHP extensions
RUN docker-php-ext-configure soap --enable-soap \
  && docker-php-ext-install \
    soap \
  && docker-php-ext-install opcache \
  && docker-php-ext-enable opcache

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# Copy xdebug configration for remote debugging
COPY ./docker/configs/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
