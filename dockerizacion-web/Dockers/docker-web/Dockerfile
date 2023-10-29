#****************** DOCKERFILE WEB *****************#
FROM php:7.4.33-alpine3.15

# ConfiguraciÃ³n del apache:
RUN apk add --no-cache apache2 && \
    apk add --no-cache php7-apache2 && \
    apk add --no-cache php7-mysqli && \   
    apk add --no-cache php7-sockets && \
    apk add --no-cache php7-pdo_mysql && \
    apk add --no-cache php7-opcache && \
    apk add --no-cache php7-session && \
    apk add --no-cache php7-json

# Instalación dependencias web php
RUN docker-php-ext-install mysqli opcache pdo_mysql sockets

# Configuración y actualizaciÃ³n del docker
RUN apk update && \
    apk add --no-cache net-tools iproute2 bind-tools mtr nano openssl-dev && \
    rm -rf /var/cache/apk/* && \
    docker-php-ext-install ftp

EXPOSE 80

WORKDIR /var/www/localhost/htdocs/validator-web

CMD ["httpd", "-D", "FOREGROUND"]
