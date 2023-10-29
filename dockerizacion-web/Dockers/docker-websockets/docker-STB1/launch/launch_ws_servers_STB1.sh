#!/bin/bash
#cd "$(dirname "$0")"

#SERVIDOR='new-validator.turyelectro.com';

SERVIDOR='172.16.0.7';
timeout=0.5
echo "Iniciando servidores..."

function is_port_open() {
    local PUERTO="$1"
    nc -z -v -w5 $SERVIDOR "$PUERTO"
    return $?
}

# STB1
for i in {9120..9136} ;
do
    #Compruebo si los ws están levantados: me contecto a ellos con el necat
    #nc -z -v -w5 $SERVIDOR $i
    if ! is_port_open "$i"; then
        php ws_server.php $SERVIDOR $i &
        echo "Levanto los WebSockets";
        sleep 0.1
    fi
done
for i in {9153..9176} ;
do
    #Compruebo si los ws están levantados: me contecto a ellos con el necat
    #nc -z -v -w5 $SERVIDOR $i
    if ! is_port_open "$i"; then
        php ws_server.php $SERVIDOR $i &
        echo "Levanto los WebSockets";
        sleep 0.1
    fi
done

#Compruebo si los ws están levantados: me contecto a ellos con el necat
#nc -z -v -w5 $SERVIDOR $i
if ! is_port_open "$i"; then
    php ws_server.php $SERVIDOR 9230 &
    echo "Levanto los WebSockets";
    sleep 0.1
fi

for i in {9295..9310} ;
do
    #Compruebo si los ws están levantados: me contecto a ellos con el necat
    #nc -z -v -w5 $SERVIDOR $i
    #if [ $? -ne 0 ]; then
    #    php ws_server.php $SERVIDOR $i &
    #fi
    if ! is_port_open "$i"; then
        php ws_server.php $SERVIDOR $i &
        echo "Levanto los WebSockets";
        sleep 0.1
    fi
done