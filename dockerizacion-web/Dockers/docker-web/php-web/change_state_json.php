<?php
require_once __DIR__.'/conf.php';

$opciones_caja = Array('LIM','CAR');
$logger_test_id = $_POST['logger_id'];
$accion = strtoupper($_POST['accion']);
$caja = strtoupper($_POST['caja']);

if (isset($accion) and isset($logger_test_id) ) {
    $sql_changes = ",`action_if_validate` = '$accion'";
    if(in_array($caja,$opciones_caja)){
        if($caja == "CAR"){
            $sql_changes .= ",`box` = 'LIM'";
        } elseif($caja == "LIM"){
            $sql_changes .= ",`box` = 'CAR'";
        }
    }
    $sql = "UPDATE `validator`.`logger_test` SET `info_extra` = 'CAMBIO GENERADO MANUALMENTE' $sql_changes WHERE (`id` = '".$logger_test_id."');";
    $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
    if ($mysqli->connect_errno) {
        $msg = '<strong>Error:</strong> ';
        $msg .= "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        $msg = "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
        return;
    }
$msg = 'Equipo modificado correctamente.';
}

echo json_encode($msg);
?>