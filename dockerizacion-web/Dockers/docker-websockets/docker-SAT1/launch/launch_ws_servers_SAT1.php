<?php
$SERVIDOR = '172.16.0.6';
$timeout = 0.5;
echo "Iniciando servidores...\n";

function is_port_open($puerto) {
    global $SERVIDOR;
    exec("nc -z -v -w5 $SERVIDOR $puerto", $output, $returnCode);
    return $returnCode === 0;
}

$puertos = array(
    "9078", "9079", "9080", "9102", "9103", "9104", "9105", "9106", "9107", "9108",
    "9109", "9137", "9138", "9139", "9140", "9141", "9142", "9143", "9144", "9145",
    "9146", "9147", "9148", "9149", "9150", "9226", "9227", "9228", "9229", "9234",
    "9235", "9236", "9237", "9238", "9239", "9240", "9241", "9242", "9243", "9244",
    "9245", "9246", "9247", "9248"
);

foreach ($puertos as $puerto) {
    if (!is_port_open($puerto)) {
        exec("php ws_server.php $SERVIDOR $puerto > /dev/null 2>&1 &");
        echo "\nLevanto los WebSockets\n";
        usleep(100000);
    }
}
?>
