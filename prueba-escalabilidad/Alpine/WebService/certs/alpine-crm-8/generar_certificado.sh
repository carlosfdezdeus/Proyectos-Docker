#!/bin/ash

# Nombre del archivo de clave privada y certificado
PRIVATE_KEY="clave-privada.key"
CERTIFICATE="certificado-auto-firmado.crt"

# Genera una clave privada RSA
openssl genpkey -algorithm RSA -out "$PRIVATE_KEY"

# Genera un certificado auto-firmado usando la clave privada
openssl req -new -key "$PRIVATE_KEY" -x509 -days 365 -out "$CERTIFICATE" -config openssl.conf

echo "Certificado SSL/TLS auto-firmado generado:"
echo "Clave Privada: $PRIVATE_KEY"
echo "Certificado: $CERTIFICATE"
