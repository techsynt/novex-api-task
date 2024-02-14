FROM php:8.2-fpm

#Set the working dir
WORKDIR /srv/app


# Install system dependencies and required tools
# Install system dependencies and required tools
RUN apt-get update && \
    apt-get install -y libpq-dev

# Install PHP extensions
RUN  docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
     docker-php-ext-install pdo_pgsql pgsql opcache
