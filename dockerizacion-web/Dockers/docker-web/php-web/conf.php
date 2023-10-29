<?php

//Selector de entorno
require('entorno.php');

$sede = "Tury";

//Base de datos
switch ($entorno) {
    case 'Produccion':
        // Logs
        define('DEBUG_FILES_PATH', "/var/log/validator-web/");
        // WS
        define('WS_SERVER', "new-validator.turyelectro.com");
        //define('WS_SERVER', "192.168.100.36");
        //define('WS_PYTHON_SERVER', "192.168.100.99");
        define('WS_SERVER_PORT', 9300);
        define('WS_TICKET_PORT', 11001);
        define('WS_MSG_PORT', 11002);
        // MySQL
        define('MySQL_SERVER', "validator.turyelectro.com");
        define('MySQL_USER', "root");
        define('MySQL_PASSWORD', "validator00");
        define('MySQL_DB', "validator");
        // STB
        define('STB_SERVER_XMLRPC_TIMEOUT_COM', 30); // segundos
        define('STB_SERVER_XMLRPC_TIMEOUT_CHANGING', 180); // segundos
        define('STB_SERVER_XMLRPC_PORT', 38400);
         //MENSAJES WEB
        define('MENSAJES_WEB_PATH',"http://new-validator.turyelectro.com/insertar_mensaje.php");
        define('MENSAJES_USER_PATH',"http://new-validator.turyelectro.com/leo_msg_user.php");
        define('MENSAJES_BANDEJA_PATH',"http://new-validator.turyelectro.com/leo_msg_bandeja.php");
        // ALBARAN
        define('INTRANET_INVOICE_PATH',"http://test-intranet.turyelectro.com/intranet/albaranes_entrada/previstos_restantes.asp?invoice=");  
        
        break;

    case 'Desarrollo':
        // Logs
        define('DEBUG_FILES_PATH', "/var/log/validator-web/");
        // WS
        //define('WS_SERVER', "test-validator.turyelectro.com");
        define('WS_SERVER', "172.16.0.7");
        //define('WS_PYTHON_SERVER', "192.168.100.99");
        define('WS_SERVER_PORT', 9001);
        define('WS_TICKET_PORT', 11001);
        define('WS_MSG_PORT', 11002);
        // MySQL
        #define('MySQL_SERVER', "test-validator.turyelectro.com");
        #define('MySQL_USER', "validator-web");
        #define('MySQL_PASSWORD', "Validator.00");
        define('MySQL_SERVER', "192.168.30.52");
        define('MySQL_USER', "logger");
        define('MySQL_PASSWORD', "logger00");
        define('MySQL_DB', "validator");
        define('MySQL_DB_W', "warehouse");
        // STB
        define('STB_SERVER_XMLRPC_TIMEOUT_COM', 30); // segundos
        define('STB_SERVER_XMLRPC_TIMEOUT_CHANGING', 180); // segundos
        define('STB_SERVER_XMLRPC_PORT', 38400);
           //MENSAJES WEB
        define('MENSAJES_WEB_PATH',"http://test-validator.turyelectro.com/insertar_mensaje.php");
        define('MENSAJES_USER_PATH',"http://test-validator.turyelectro.com/leo_msg_user.php");
        define('MENSAJES_BANDEJA_PATH',"http://test-validator.turyelectro.com/leo_msg_bandeja.php");       
        // ALBARAN
        define('INTRANET_INVOICE_PATH',"http://test-intranet.turyelectro.com/intranet/albaranes_entrada/previstos_restantes.asp?invoice=");  
        
        break;
}
?>
