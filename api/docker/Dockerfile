FROM php:8.1-apache

RUN apt-get update

RUN apt-get install -y \
    pkg-config  \
    libzip-dev \
    iproute2 \
    libpcre3-dev \
    supervisor \
    vim

RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    zip \
    pcntl \
    sockets

RUN pecl install redis
RUN docker-php-ext-enable redis pcntl sockets

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite headers
RUN service apache2 restart

# Create directory for supervisor configs
RUN mkdir -p /etc/supervisor/conf.d

# Add supervisor configuration for WebSocket server
COPY websocket-supervisor.conf /etc/supervisor/conf.d/

# Expose WebSocket port
EXPOSE 6003

# Start supervisor (which will manage the WebSocket server)
CMD ["supervisord", "-n"]
