<?php

    include('user_login.php');

    $response = '<h1>Permisos insuficientes.</h1>';
    if ($_SESSION['user_admin'] == 1) {
        $response = '<h1>No se ha proporcionado un puerto.</h1>';
        $port = $_GET['port'];
        if (!$port) {
            echo $response;
            exit;
        }
        $file = '/var/log/validator-web/ws_server_'. $port .'.log';
        $response = '<h1>No se ha encontrado log para el puerto '. $port .'</h1>';
        if (file_exists($file)) {
            $response = file_get_contents( $file );
            $response = str_replace("\n", "<br>", $response);
        }
    }
    echo $response;

?>