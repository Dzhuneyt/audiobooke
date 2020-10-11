FROM yiisoftware/yii2-php:7.3-apache

WORKDIR /app

# Install dependencies
COPY composer.json composer.json
RUN composer global require hirak/prestissimo && \
    composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Add source code to container
COPY . /app

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

# Fix directory permission issues
RUN mkdir -p /app/common/runtime && chmod -R 777 /app/common/runtime && \
    mkdir -p /app/rest/runtime/logs && mkdir -p /app/rest/web/assets && \
    chmod -R 777 /app/rest/runtime && \
    chmod -R 777 /app/rest/runtime/logs && \
    chmod -R 777 /app/rest/web/assets && \
    chmod +x -R /app/scripts/*

# Point HTTP requests to Yii entrypoint
ENV APACHE_DOCUMENT_ROOT /app/rest/web
RUN sed -ri -e 's!/app/web!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/app/web/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite

# Helps to wait for MySQL to initialize in a multi container Swarm environment
ENV DOCKERIZE_VERSION v0.6.1
RUN apt-get -qq update && apt-get install --no-install-recommends -y -qq wget \
    && wget -q https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && apt-get remove -y wget \
    && rm -rf /var/lib/apt/lists/*


EXPOSE 80
