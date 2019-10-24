FROM php:7.3.3-alpine
# FROM php:7.2-fpm

RUN apk add --no-cache \
    wget \
    curl \
    # git \
    shadow \
    build-base \
    autoconf \
    # hiredis \
    libxml2-dev \
    zlib-dev \
    libevent \
    libevent-dev \
    openssl-dev \
    gmp-dev \
    icu-dev

RUN docker-php-ext-install \
        pcntl \
        mbstring \
        pdo \
        pdo_mysql \
        tokenizer \
        xml \
        sockets \
        gmp \
        bcmath \
        intl

        # Libevent
RUN pecl install event

RUN apk update

# Cleanup
# apk del .build-deps && \
RUN rm -rf /var/cache/apk/* && \
    rm -rf /tmp/*

RUN mkdir -p /var/www && mkdir -p /etc/ssl_crt

COPY laravel_pusher /var/www/html
RUN groupmod -g 1000 www-data && \
    usermod -u 1000 www-data

USER www-data

WORKDIR /var/www/html

EXPOSE 6001

CMD ["php", "artisan", "websockets:serve"]