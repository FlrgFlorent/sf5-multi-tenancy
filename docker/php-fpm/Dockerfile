FROM phpdockerio/php74-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# make sure apt is up to date
RUN apt-get update --fix-missing

RUN apt-get install -y apt-utils

# Install selected extensions and other stuff
RUN apt-get update && apt-get -y --no-install-recommends install php-memcached php7.4-mysql php7.4-intl php-yaml

# Install git
RUN apt-get update && apt-get -y install git

RUN apt-get update && \
  apt-get install -y build-essential make gcc g++ && \
  apt-get clean && \
  apt-get install -y libgs-dev && \
  rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer