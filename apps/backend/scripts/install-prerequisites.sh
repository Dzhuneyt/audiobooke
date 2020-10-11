#!/usr/bin/env bash

# Prevent asking for promts
DEBIAN_FRONTEND=noninteractive

apt-get -qq update
apt-get --assume-yes install -y -qq software-properties-common curl
add-apt-repository -y ppa:ondrej/php
apt-get install -y -qq php7.3 php7.3-cli php7.3-fpm php7.3-json php7.3-pdo php7.3-mysql php7.3-zip php7.3-gd  php7.3-mbstring php7.3-curl php7.3-xml php7.3-bcmath php7.3-json
php -v

#apt -qq install -y zlib1g-dev libsqlite3-dev python3-pip virtualenvwrapper && docker-php-ext-install zip

curl -sS https://getcomposer.org/installer -o composer-setup.php && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php

composer -v
