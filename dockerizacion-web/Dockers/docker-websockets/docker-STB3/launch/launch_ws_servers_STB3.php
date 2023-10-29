<?php
$SERVIDOR = '172.16.0.20';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array("9322", "9323", "9324", "9325");

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>
