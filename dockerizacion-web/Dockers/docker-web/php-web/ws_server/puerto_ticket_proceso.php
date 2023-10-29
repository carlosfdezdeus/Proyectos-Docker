<?php

$path_servidor = dirname(__DIR__);
require_once $path_servidor.'/conf.php';
require_once $path_servidor.'/debug.php';
$html = 1;
$array_puertos_seleccionados = [];
if (php_sapi_name() == 'cli') {	
    if (empty($argv[1]) or is_int($argv[2])) {
        echo 'Los argumentos que se enviar no son correctos /n';
        exit;
    } else {
        $array_puertos_seleccionados = explode(',', $argv[1]);
        $estado = $argv[2];
    }
}

// Compruebo si pasamos valores por GET o POST
if (!empty($_GET)) {
    $numero_puertos = strtoupper($_GET['server_port']);
    $array_puertos_seleccionados = explode(',', $numero_puertos);
    $estado = $_GET['estado'];
    $html = is_null($_GET['html']) ?: $_GET['html'];
}
if (!empty($_POST)) {
    $estado = ($_POST['boton'] == 'Lanzar') ? 1 : 0;
    $array_puertos_seleccionados = $_POST['puertos'];
}
$cmd = "ps aux | grep -i 'php ws_server.php ".WS_SERVER."'| grep -v grep";
$pids = shell_exec($cmd);
$patron = '/'.WS_SERVER.'\s*(\d+)/';

$matriz_fl = preg_match_all($patron, $pids, $coincidencias, PREG_PATTERN_ORDER);
$puertos_activos = $coincidencias[1];
// Conectamos a la DB
$mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
if ($mysqli->connect_errno) {
    $msg = ' Error: Fall&oacute; la conexi&oacute;n a MySQL: ('.$mysqli->connect_errno.') '.$mysqli->connect_error;
    respuesta_error($msg, $html);
    exit();
}
$peticion_sql =
            'select
                ws_port
            from
                validator_websockets';

if (empty($resultado = $mysqli->query($peticion_sql))) {
    echo '<strong>Peticion vacia:</strong> ';
    exit();
}

if ($resultado = $mysqli->query($peticion_sql)) {
    /* obtener un array asociativo */
    while ($fila = $resultado->fetch_assoc()) {
        $array_puertos[] = $fila;
        unset($fila);
    }
    /* liberar el conjunto de resultados */
    mysqli_free_result($resultado);
}


if ((!in_array('ALL', $array_puertos_seleccionados)) and (!empty(array_diff($array_puertos_seleccionados, array_column($array_puertos, 'ws_port'))))) {
    $msg = 'Error : El valor del server port  es incorrecto';
    respuesta_error($msg, $html);
    exit();
}



if ((!in_array('ALL', $array_puertos_seleccionados)) and $estado == 1) {
    //Activa Selecionados  
    $array_puertos_temporal = array_diff($array_puertos_seleccionados, $puertos_activos);
} elseif (in_array('ALL', $array_puertos_seleccionados) and $estado == 1) {
    //Activa Todos los puertos   
    $array_puertos_temporal = array_diff(array_column($array_puertos, 'ws_port'), $puertos_activos);
} elseif ((!in_array('ALL', $array_puertos_seleccionados)) and $estado == 0) {
    //Desactiva Selecionados   
    $array_puertos_temporal = array_intersect($array_puertos_seleccionados, $puertos_activos);
} elseif (in_array('ALL', $array_puertos_seleccionados) and $estado == 0) {
    //Desactiva todos los puertos   
    $array_puertos_temporal = $puertos_activos;
}

$resultado = Lanzar_Matar_puerto($array_puertos_temporal, $estado);
if ($html == 0) {
    echo $resultado;
    exit();
}
if (php_sapi_name() != 'cli') {
    header('Location: puerto_ticket.php');
}

function Lanzar_Matar_puerto($array_puertos, $accion)
{
    if ($accion == true) {
        foreach ($array_puertos as  $value) {
            $cmd2 = 'php ws_server.php '.WS_SERVER." $value > /dev/null &";
            passthru($cmd2);
        }

        return 'OK. Puerto lanzado'.implode(',', $array_puertos);
    } else {
        foreach ($array_puertos as  $value) {
            $cmd = "ps aux | grep -i 'php ws_server.php ".WS_SERVER.' '.$value."'| grep -v grep |  awk '{print $2}'";
            $pids = shell_exec($cmd);
            $cmd2 = "kill -9 $pids";
            $kill = passthru($cmd2);
        }

        return 'OK.Puertos Deshabilidatos';
    }
}
function respuesta_error($msg, $html = 1)
{
    if ($html != 0) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo   $msg;
        echo '</div>';
    } else {
        echo $msg;
    }
}
