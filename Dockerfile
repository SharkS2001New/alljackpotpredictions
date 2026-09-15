# AllJackpotPredictions — PHP + Apache (same stack pattern as baopredictions)
FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    pdo_mysql \
    mysqli \
    gd

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/logging.ini /usr/local/etc/php/conf.d/logging.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Flat site: document root is the project root (not /public)
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers deflate expires

RUN sed -i 's/Listen 80/Listen 5500/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:5500/' /etc/apache2/sites-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN printf '%s\n' \
  'KeepAlive On' \
  'MaxKeepAliveRequests 100' \
  'KeepAliveTimeout 5' \
  > /etc/apache2/conf-available/ajp-perf.conf \
  && a2enconf ajp-perf

ENV LOG_CHANNEL=stderr
ENV APACHE_LOG_DIR=/var/log/apache2

VOLUME /var/www/html

COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction

RUN mkdir -p /var/www/html/storage/cache /var/www/html/storage/framework/cache/data \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html

EXPOSE 5500

CMD ["apache2-foreground"]
