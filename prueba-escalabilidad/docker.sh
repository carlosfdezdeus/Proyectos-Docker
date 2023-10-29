#!/bin/sh
clear
directorio=$(pwd)

echo "Usted está en: '$directorio', compruebe que el archivo de configuración está en esa carpeta."
echo

echo "Menú Principal:"
echo "1. Creación de la red docker"
echo "2. Lanzamiento de la red en segundo plano"
echo "3. Actualización en tiempo real con ficheros actuales"
echo "4. Actualización en tiempo real con nuevo fichero"
echo "5. Salir"
read -p "Seleccione una opción (1/2/3/4/5): " opcion

case $opcion in
    1)
        # Creo la red docker en base a los ficheros: docker-compose.yml y docker-compose.extend.yml
        docker compose -f docker-compose.yml -f docker-compose.extend.yml up --build
        ;;
    2|3)
        # Lanzo la red docker en segundo plano:
        # En caso de estar ya en funcionamiento -> Actualizo el código
        docker compose -f docker-compose.yml -f docker-compose.extend.yml up -d
        ;;
    4)
        # Pregundo por el nombre del nuevo fichero docker-compose y actualizo en timepo real
        read -p "Introduzca el nombre del fichero docker-compose con las actualizaciones: " nombre_fichero
        docker compose -f docker-compose.yml -f docker-compose.extend.yml -f $nombre_fichero up -d
        ;;
    5)
        echo "Saliendo del menu."
        exit 0
        ;;
    *)
        echo "Opción no válida. Inténtelo de nuevo."
        ;;
esac
