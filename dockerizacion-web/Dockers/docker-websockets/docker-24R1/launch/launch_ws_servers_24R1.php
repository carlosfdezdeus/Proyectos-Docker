<?php
    $SERVIDOR = '172.16.0.14';
    $timeout = 0.5;
    echo "Iniciando servidores...\n";

    function is_port_open($puerto) {
        global $SERVIDOR;
        exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
        return $returnCode === 0;
    }

    // 24R1
    if (!is_port_open(9313)) {
        exec("php ws_server.php $SERVIDOR 9313 > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
?>