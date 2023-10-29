<?php
$SERVIDOR = '172.16.0.5';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array(
    "9249", "9256", "9257", "9258", "9259", "9260", "9261", "9262",
    "9287", "9288", "9289", "9290", "9291", "9292", "9293", "9294"
);

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>