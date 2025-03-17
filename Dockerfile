#base
FROM public.ecr.aws/docker/library/php:8.3.4-fpm-bullseye AS base
LABEL maintainer="KOKI YASUMOTO <yasu.mahjong0808@gmail.com>"

RUN apt-get update \
    && apt-get install -y \
        git \
        zip \
        unzip \
        libpq-dev \
    && pecl install apcu \
    && docker-php-ext-install opcache pdo_pgsql \
    && docker-php-ext-enable apcu \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=public.ecr.aws/docker/library/composer:2.7.6 /usr/bin/composer /usr/bin/composer

# for development
FROM base AS development

COPY . /var/www/html

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

RUN composer install

VOLUME /var/www/html

EXPOSE 9000

RUN chmod +x startup.sh

CMD ["/bin/sh", "-c", "./startup.sh"]

# for production
FROM base AS production

COPY . /var/www/html

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader

VOLUME /var/www/html

EXPOSE 9000

RUN chmod +x startup.sh

CMD ["/bin/sh", "-c", "./startup.sh"]
