FROM php:7.4.33-alpine3.15
#Si se desea ejecutar en alpine: php:7.3.33-alpine

RUN docker-php-ext-install sockets

RUN apk update && \
    apk add --no-cache net-tools iproute2 && \
    apk add --no-cache netcat-openbsd

RUN mkdir -p /websocket

WORKDIR /websocket
#CMD ["php", "launch_ws_servers_SAT2.php"]
# En docker-compose.yml