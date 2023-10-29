#!/bin/sh

# Nombre de usuario de Dinahosting
USER=turyelectro

# Contraseña con la que acceder al área de clientes
PASSWORD=Tury3xt3rn02018

# Nombre de dominio contratado con Dinahosting
DOMAIN=turyelectro.com

curl "https://dinahosting.com/special/api.php?AUTH_USER=$USER&AUTH_PWD=$PASSWORD&domain=$DOMAIN&hostname=_acme-challenge.$CERTBOT_DOMAIN&text=$CERTBOT_VALIDATION&command=Domain_Zone_AddTypeTXT"

sleep 10
