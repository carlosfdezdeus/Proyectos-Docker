<?php
    $SERVIDOR = '172.16.0.7';
    $timeout = 0.5;
    echo "Iniciando servidores...\n";

    function is_port_open($puerto) {
        global $SERVIDOR;
        exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
        return $returnCode === 0;
    }

    // STB1
    for ($i = 9120; $i <= 9136; $i++) {
        if (!is_port_open($i)) {
            exec("php ws_server.php $SERVIDOR $i > /dev/null 2>&1 &");
            echo "\nLevanto los WebSockets\n";
            usleep(100000);
        }
    }

    for ($i = 9153; $i <= 9176; $i++) {
        if (!is_port_open($i)) {
            exec("php ws_server.php $SERVIDOR $i > /dev/null 2>&1 &");
            echo "\nLevanto los WebSockets\n";
            usleep(100000);
        }
    }

    if (!is_port_open(9230)) {
        exec("php ws_server.php $SERVIDOR 9230 > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }

    for ($i = 9295; $i <= 9310; $i++) {
        if (!is_port_open($i)) {
            exec("php ws_server.php $SERVIDOR $i > /dev/null 2>&1 &");
            echo "\nLevanto los WebSockets\n";
            usleep(100000);
        }
    }
?>