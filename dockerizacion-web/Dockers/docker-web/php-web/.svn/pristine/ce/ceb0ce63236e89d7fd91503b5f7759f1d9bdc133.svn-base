<?php
/*
 * Gestiona las solicitudes AJAX
 */

// Importa los parametros de entorno
require 'conf.php';

// Arreglo de encode_json para la version 5.1 de apache
require 'libs/jsonwrapper/jsonwrapper.php';

// Guardar estado de la bandeja en la DB
if (!empty($_POST['updateDB']) && !empty($_POST['shelves_data'])) {
    $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
    if ($mysqli->connect_errno) {
        echo "Falla la conexion a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        exit();
    }

    foreach ($_POST['shelves_data'] as $shelf_data) {
        if (empty($shelf_data['s_from_launch'])) {
            $shelf_data['last_launch_info_datetime'] =  null;
            $shelf_data['s_from_launch'] = 0;
        } else {
            $shelf_data['last_launch_info_datetime'] = date_format(new DateTime(date("m/d/Y h:i:s", time() - $shelf_data['s_from_launch'])), 'Y-m-d H:i:s');
        }
        if (empty($shelf_data['s_from_last_server_activity_datetime'])) {
            $shelf_data['last_server_activity_datetime'] =  null;
            $shelf_data['s_from_launch'] = 0;
        } else {
            $shelf_data['last_server_activity_datetime'] = date_format(new DateTime(date("m/d/Y h:i:s", time() - $shelf_data['s_from_last_server_activity_datetime'])), 'Y-m-d H:i:s');
        }

    }

    $query = 'UPDATE validator_environment_shelf_launcher SET ';
    $query .= "model = '" . $shelf_data['model_fields']['model'] . "', ";
    $query .= "invoice = '" . $shelf_data['invoice'] . "', ";
    $query .= 'logistic_id = ' . $shelf_data['logistic_id'] . ', ';
    $query .= 'configuring_shelf = 1, ';
    $query .= 'configuring_shelf_init_time = now(), ';
    $query .= "web_last_status_JSON = '" . json_encode($shelf_data) . "' ";
    $query .= 'WHERE validator_environment_shelf_id = ' . $shelf_data['id_shelf'] . ';';
    if ($result = $mysqli->query($query)) {
        $pl_sp = ((count($_POST['shelves_data']) == 1) ? ' ' : 's ');
        echo "Bandeja". $pl_sp . implode(', ',array_column($_POST['shelves_data'], 'id_shelf')) . ' actualizada'. $pl_sp .'con exito en la DB.';
    } else {
        echo "Error: " . $mysqli->error;
    }
    $mysqli->close();
}

// Solicitar cambio de configuracion en el servidor
if (!empty($_GET['change2model']) && !empty($_GET['server']) && !empty($_GET['groups'])) {
    $Forked_script_PATH = getcwd()."/XMLRPC_Forked.php";
    $modelo = $_GET['change2model'];
    $grupos = array_unique(explode(",", $_GET['groups']));
    $peticion_XMLRPC = array($modelo, $grupos);
    passthru("/usr/bin/php $Forked_script_PATH http://".$_GET['server'].":".STB_SERVER_XMLRPC_PORT." arrancarServidor ".escapeshellarg(serialize($peticion_XMLRPC))." 2>&1 &");
    echo "Peticion de cambio de los grupos ".$_GET['groups']." al modelo $modelo enviada a http://{$_GET['server']}:".STB_SERVER_XMLRPC_PORT;
}

?>

