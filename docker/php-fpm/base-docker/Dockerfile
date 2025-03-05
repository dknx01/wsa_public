FROM php:8.3-fpm AS php-main

RUN apt-get update \
    && apt-get install -y git zlib1g-dev libonig-dev libicu-dev libxext6 libxml2-utils libxml2-dev libzip-dev zip \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f apcu
RUN docker-php-ext-enable apcu

FROM php-main AS php-dependencies
# Zeitzone einrichten
ENV TZ=Europe/Berlin
RUN echo $TZ > /etc/timezone && \
    apt-get update && apt-get install -y --no-install-recommends tzdata && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install fileinfo intl mbstring opcache bcmath zip
RUN mkdir /var/www/project /var/www/project/var /var/log/php && chown -R www-data:www-data /var/www/project/var/ && touch /var/log/php/php_errors.log && ln -sf /dev/stderr /var/log/php/php_errors.log

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Local PHP Security Checker
RUN curl -sSL https://github.com/fabpot/local-php-security-checker/releases/download/v2.0.6/local-php-security-checker_2.0.6_linux_amd64 --output local-php-security-checker && chmod +x local-php-security-checker
