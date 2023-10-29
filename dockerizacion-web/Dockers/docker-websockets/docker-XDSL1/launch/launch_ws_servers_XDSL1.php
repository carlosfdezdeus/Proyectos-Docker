<?php
$SERVIDOR = '172.16.0.4';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array(
    "9031", "9032", "9033", "9034", "9035", "9036", "9037", "9038", "9039", "9040",
    "9041", "9042", "9043", "9044", "9045", "9046"
);

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>

