#!/bin/sh

certbot certonly --manual --email desarrollos@turyelectro.com --manual-auth-hook ./authenticator.sh --manual-cleanup-hook ./cleanup.sh --domain *.turyelectro.com --preferred-challenges dns --agree-tos --non-interactive