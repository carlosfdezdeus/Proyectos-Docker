FROM centos:centos7

# Instalación de perl-5.16.3 en el contenedor centOS7
RUN yum update -y && \
    yum install -y gcc wget && \
    yum install -y less && \
    yum install -y make && \
    yum install -y perl-5.16.3 && \
    yum install -y cpanminus

RUN cpanm HTTP::Request
RUN cpanm HTTP::Headers
RUN cpanm LWPx::ParanoidAgent
RUN cpanm LWP::UserAgent
RUN cpanm JSON::MaybeXS

# Ejecuto el script de perl con el que voy a obtener los datos
WORKDIR /usr/src/app
CMD ["perl", "router_data.pl", "admin", "63Ykna4R"]
