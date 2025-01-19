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

ENV APP_ENV=prod
ENV APPLICATION_ENV=prod
ENV MATOMO_ENABLED=true
ENV MATOMO_NOSCRIPT_IMG=exampple
ENV MATOMO_SITE_ID=example
ENV MATOMO_COOKIE_DOMAIN=https://www.erikwitthauer.de/piwik/
ENV MATOMO_DOMAIN=https://www.erikwitthauer.de/piwik/
ENV MATOMO_TRACKER_URL=matomo.php
ENV MATOMO_API_TOKEN=example
COPY . /var/www/project
RUN cd /var/www/project && COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev && chmod +x ./rebuild.prod.sh ./bin/console && ./rebuild.prod.sh
COPY . /var/www/project
#RUN mkdir -p /var/www/project/public_backup && cp -R /var/www/project/public/* /var/www/project/public_backup/*
#
#ENTRYPOINT entrypoint.php.sh
#CMD["php-fpm"]
