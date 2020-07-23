FROM php:fpm-alpine

RUN apk --update --no-cache add git

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pcntl

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
&& pecl install -o -f redis \
&& pecl install xdebug-2.9.4 \
&& rm -rf /tmp/pear \
&& docker-php-ext-enable redis \
&& apk del pcre-dev ${PHPIZE_DEPS}

WORKDIR /var/www

CMD php bin/job.php

EXPOSE 9000
