<?php

/*
 * Funcion que lanza una peticion XMLRPC desde CLI
 * Sintaxis:
 * 
 *      Forked_print <URL_servidor> <funcion-metodo> <argumentos serializados>
 * 
 */

require "libs/XMLRPC_Client.php";

if (isset($argv)) {
    $URL_Servidor = $argv[1];
    $funcion = $argv[2];
    $datos_XMLRPC = unserialize($argv[3]);
} else {
    return;
}    

consulta_xml_rpc($URL_Servidor, $funcion, $datos_XMLRPC);