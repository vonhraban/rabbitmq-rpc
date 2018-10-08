FROM php:7.2.10-cli

# This is neccessary evil as bcmath is a required extension for both api and db service
RUN apt-get update && \
    apt-get install -y --no-install-recommends git unzip
RUN docker-php-ext-install  bcmath
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
