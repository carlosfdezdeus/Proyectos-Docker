#*************** DOCKERFILE ROCKY-LINUX *************************#
FROM rockylinux:9.2

# Actualizo el rocky + descargas:
RUN dnf update -y && dnf upgrade -y && \
    dnf -y install yum-utils && \
    dnf -y install nano && \
    dnf -y install wget

# Habilito los repositorios REMI:
RUN dnf install -y http://rpms.remirepo.net/enterprise/remi-release-9.rpm && \
    dnf update -y

# Instalación Apache: 
RUN dnf install -y httpd httpd-tools mod_ssl

# Habilito el módulo antes de instalar PHP 8.2:
RUN dnf module list php && \
    dnf module enable php:remi-8.2 -y 

# Instalo PHP 8.2
RUN dnf install php \
                php-gd \
                php-common \
                php-fpm \
                php-pdo \
                php-opcache \
                php-zip \
                php-phar \
                php-iconv \
                php-cli \
                php-curl \
                php-openssl \
                php-mbstring \
                php-fileinfo \
                php-json \
                php-xml \
                php-xmlreader \
                php-xmlwriter \
                php-simplexml \
                php-dom \
                php-pdo_mysql \
                php-pdo_pgsql \
                php-pdo_sqlite \
                php-tokenizer \
                php-pecl-redis -y

# Habilito el php-fpm.service:
RUN mkdir /run/php-fpm && \
    systemctl enable php-fpm.service && \
    php-fpm

# Instalo el certbot
RUN yum install -y certbot 

# Instalación composer:
RUN wget https://getcomposer.org/installer -O composer-installer.php && \
    php composer-installer.php --filename=composer --install-dir=/usr/local/bin
    
# Instalación Laravel:
RUN composer global require laravel/installer

# Instalación Node 18.17.0:
RUN curl -sL https://rpm.nodesource.com/setup_18.x | bash - && \
    dnf -y install nodejs && \
    npm install -g npm@9.8.1

# Direccionamos a /var/www/html/crm
ADD ./crm /var/www/html/crm
WORKDIR /var/www/html/crm

# Permisos de la carpeta crm
RUN chown -R apache:apache /var/www/html/crm/
RUN chmod -R 775 /var/www/html/crm/

RUN npm install 
RUN composer install
RUN npm run build

EXPOSE 80
EXPOSE 443

ADD ./httpd/conf.d /etc/httpd/conf.d
ADD ./certs /etc/pki/tls/certs/docker-crm.turyelectro.com

CMD ["httpd", "-D", "FOREGROUND"]
#CMD ["sleep", "infinity"]