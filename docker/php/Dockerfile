FROM php:8.1-fpm 
RUN apt-get update -y && apt-get install -y sendmail libpng-dev zlib1g-dev libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install mysqli pdo_mysql gd intl
