FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y git zlib1g-dev libonig-dev libicu-dev libxrender1 libfontconfig1 libxext6 libmagickwand-dev libfreetype6 libjpeg-dev libpng-dev libfreetype6-dev libxml2-utils libxml2-dev libzip-dev zip sqlite3 \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f xdebug apcu pcov imagick
RUN docker-php-ext-enable xdebug apcu pcov imagick

# Zeitzone einrichten
ENV TZ Europe/Berlin
RUN echo $TZ > /etc/timezone && \
    apt-get update && apt-get install -y tzdata && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install fileinfo intl mbstring opcache pdo_mysql bcmath zip xml dom gd
RUN mkdir /var/log/php && touch /var/log/php/php_errors.log && ln -sf /dev/stderr /var/log/php/php_errors.log

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Local PHP Security Checker
RUN curl -sSL https://github.com/fabpot/local-php-security-checker/releases/download/v2.1.3/local-php-security-checker_linux_386 --output local-php-security-checker && chmod +x local-php-security-checker
