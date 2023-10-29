#!/bin/bash
cd "$(dirname "$0")"

SERVIDOR='new-validator.turyelectro.com';

echo "Iniciando servidores..."

# SSEGOVIA
for i in {9689..9693} ;
do
    php ws_server.php $SERVIDOR $i &
done

for i in {9722..9740} ;
do
    php ws_server.php $SERVIDOR $i &
done



