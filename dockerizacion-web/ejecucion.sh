#!/bin/ash

# Función para verificar la existencia de un programa
check_program() {
    command -v "$1" >/dev/null 2>&1
}

# Verificar si Docker está instalado
if ! check_program "docker"; then
    echo "Docker no está instalado. Por favor, instálalo para continuar."
    exit 1
fi

# Verificar si Docker Compose está instalado
if ! check_program "docker-compose"; then
    echo "Docker Compose no está instalado. Por favor, instálalo para continuar."
    exit 1
fi

# Función para verificar la existencia de una interfaz de red
check_interface() {
    if ip link show "$1" &> /dev/null; then
        echo "La interfaz $1 existe."
    else
        echo "La interfaz $1 no existe."
    fi
}

# Verificar las interfaces eth1.10 y eth1.11
check_interface "eth1.10"
check_interface "eth1.11"

# Ir al directorio donde se encuentra el archivo docker-compose.yml
cd /ruta/a/tu/directorio

# Ejecutar docker-compose up
docker-compose up