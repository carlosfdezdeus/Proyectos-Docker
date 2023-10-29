FROM alpine:3.18 

RUN apk update && \
    apk upgrade && \
    apk add nano

# Configuraci�n apache:
RUN apk add --no-cache apache2 && \
    apk add --no-cache apache2-ssl && \
    apk add --no-cache php82-apache2 && \
    apk add --no-cache apache2-proxy

RUN mkdir -p /etc/apache2-virtualhosts

# Instalaci�n php:
RUN apk add --no-cache  php82 \
                        php82-gd \
                        php82-common \
                        php82-fpm \
                        php82-pdo \
                        php82-opcache \
                        php82-zip \
                        php82-phar \
                        php82-iconv \
                        php82-cli \
                        php82-curl \
                        php82-openssl \
                        php82-mbstring \
                        php82-fileinfo \
                        php82-json \
                        php82-xml \
                        php82-xmlreader \
                        php82-xmlwriter \
                        php82-simplexml \
                        php82-dom \
                        php82-pdo_mysql \
                        php82-pdo_pgsql \
                        php82-pdo_sqlite \
                        php82-tokenizer \
                        php82-pecl-redis

RUN ln -s /usr/bin/php82 /usr/bin/php

RUN apk add certbot

# Instalaci�n composer
RUN apk add curl
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Instalaci�n Laravel:
RUN composer global require laravel/installer

# Instalaci�n Node y npm
RUN apk add --no-cache nodejs npm

# Direccionamos a /var/www/html/crm
ADD ./crm /var/www/localhost/htdocs/crm
WORKDIR /var/www/localhost/htdocs/crm

# Permisos de la carpeta crm
RUN chown -R apache:apache /var/www/localhost/htdocs/crm/
RUN chmod -R 775 /var/www/localhost/htdocs/crm/
RUN ls

# Instalo el NPM y el COMPOSER: 
#RUN npm install 
#RUN composer install
#RUN npm run build

#EXPOSE 80
EXPOSE 443

RUN mkdir -p /etc/ssl/certs/atc.turyelectro.com

CMD ["httpd", "-D", "FOREGROUND"]
#CMD ["sleep", "infinity"]
