Cuando se ejecuta: ./Dockers/docker-web/php-web/ws_server/gestion_server_procesos.php, ya se levantan los websockets en la 172.16.0.X
siendo X el número del environment a levantar. De todas formas, esta líneas (29 a 34) están comentadas para poder levantar los websockets
deseados mediante el uso de sus respectivos contenedores.

OJO:
Para el correcto funcionamiento, se debería modificar la página web y la base de datos.
En cuanto a la BD, se debe añadir una columna con las URLs de cada environment.
En cuanto a la página web, se debería coger la constante: "WS_SERVER" de la base de datos
En esta prueba, se observa que solo el environment STB1 funciona, el resto NO. Ya que se ha modificado la WS_SERVER a su ip.
Si se quieren probar otros, dejo aqui su IP:
    - STB1:     172.16.0.7      V
    - ANDRES:   172.16.0.13     V
    - BENITO:   172.16.0.12     V
    - 24R1:     172.16.0.14     V
    - CPE1:     172.16.0.100    V
    - CPE2:     172.16.0.5      V
    - SAT1:     172.16.0.6      V
    - SAT2:     172.16.0.19     V
    - STB3:     172.16.0.20     V
    - DEV:      172.16.0.21     V
    - FERNANDO: 172.16.0.11     V
    - IRIA:     172.16.0.18     V
    - JLUIS:    172.16.0.22     V
    - RUBEN:    172.16.0.17     V
    - TEST1:    172.16.0.8      V 
    - XDSL1:    172.16.0.4      V

APUNTE:
Se ha dejado la carpeta "Dockers_no_terminados", solo habría que copiar cambiando las ips