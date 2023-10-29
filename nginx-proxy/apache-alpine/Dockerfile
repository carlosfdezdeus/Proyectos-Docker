FROM httpd:alpine

COPY ./apache-config/httpd.conf /usr/local/apache2/conf/
COPY ./apache-config/httpd-ssl.conf /usr/local/apache2/conf/extra/httpd-ssl.conf
COPY ./apache-config/httpd-ssl.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf

COPY ./apache-certs/server.pem /etc/ssl/apache2/server.pem
COPY ./apache-certs/server.key /etc/ssl/apache2/server.key

RUN mkdir -p /etc/ssl/certs/alpine-crm.turyelectro.com

COPY ./apache-certs/alpine-crm.crt  /etc/ssl/certs/alpine-crm.turyelectro.com/alpine-crm.crt
COPY ./apache-certs/alpine-crm.key  /etc/ssl/certs/alpine-crm.turyelectro.com/alpine-crm.key
RUN mkdir -p /var/www/localhost/htdocs/crm

COPY ./crm/index.html /usr/local/apache2/docs/crm/index.html
