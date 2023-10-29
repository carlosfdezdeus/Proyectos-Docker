FROM nginx:alpine
 
COPY nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /etc/ssl/alpine/
COPY ./certs/alpine/alpine-crm.key /etc/ssl/alpine/alpine-crm.key
COPY ./certs/alpine/alpine-crm.crt /etc/ssl/alpine/alpine-crm.crt

RUN mkdir -p /etc/ssl/rocky/
COPY ./certs/rocky/rocky-crm.key /etc/ssl/rocky/rocky-crm.key
COPY ./certs/rocky/rocky-crm.crt /etc/ssl/rocky/rocky-crm.crt

RUN mkdir -p /etc/ssl/alma/
COPY ./certs/alma/alma-crm.key /etc/ssl/alma/alma-crm.key
COPY ./certs/alma/alma-crm.crt /etc/ssl/alma/alma-crm.crt