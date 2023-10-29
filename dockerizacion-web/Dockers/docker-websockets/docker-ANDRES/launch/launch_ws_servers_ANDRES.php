<?php
    $SERVIDOR = '172.16.0.13';
    $timeout = 0.5;
    echo "Iniciando servidores...\n";

    function is_port_open($puerto) {
        global $SERVIDOR;
        exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
        return $returnCode === 0;
    }

    // ANDRES
    if (!is_port_open(9181)) {
        exec("php ws_server.php $SERVIDOR 9181 > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }

    if (!is_port_open(9327)) {
        exec("php ws_server.php $SERVIDOR 8327 > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }

    for ($i = 9342; $i <= 9343; $i++) {
        if (!is_port_open($i)) {
            exec("php ws_server.php $SERVIDOR $i > /dev/null 2>&1 &");
            echo "\nLevanto los WebSockets\n";
            usleep(100000);
        }
    }
?>