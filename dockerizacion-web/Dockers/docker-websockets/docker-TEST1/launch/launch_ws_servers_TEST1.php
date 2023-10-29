<?php
$SERVIDOR = '172.16.0.8';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array(
    "9182", "9183", "9184", "9185", "9186", "9187", "9188", "9189", "9190", "9191",
    "9192", "9193", "9194", "9195", "9196", "9197", "9198", "9199", "9200", "9201",
    "9202", "9203", "9204", "9205", "9206", "9207", "9208", "9209", "9210", "9211",
    "9212", "9213", "9214", "9215", "9216", "9217", "9218", "9219", "9220", "9221",
    "9222", "9223", "9224", "9225"
);

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>

