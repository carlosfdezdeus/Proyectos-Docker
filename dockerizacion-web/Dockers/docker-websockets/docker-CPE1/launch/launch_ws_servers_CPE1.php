<?php
$SERVIDOR = '172.16.0.100';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array(
    "9001", "9002", "9003", "9004", "9005", "9006", "9007", "9008", "9009", "9010",
    "9011", "9012", "9013", "9014", "9015", "9016", "9020", "9057", "9058", "9059",
    "9060", "9061", "9062", "9063", "9064", "9065", "9066", "9067", "9068", "9069",
    "9070", "9071", "9072", "9101", "9271", "9272", "9273", "9274", "9275", "9276",
    "9277", "9278", "9279", "9280", "9281", "9282", "9283", "9284", "9285", "9286"
);

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>