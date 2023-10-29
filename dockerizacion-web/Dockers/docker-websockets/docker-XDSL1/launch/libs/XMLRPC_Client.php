<?php

/* 
 * Cliente XMLRPC
 */

function consulta_xml_rpc($server, $function, $data) {
    
    if (!$server || !$function || !$data)
        return null;
    $request = xmlrpc_encode_request($function, $data);
    $context = stream_context_create(array('http' => array(
            'method' => "POST",
            'header' => "Content-Type: text/xml\r\nUser-Agent: PHPRPC/1.0\r\n",
            'content' => $request
    )));
    $file = file_get_contents($server, false, $context);
    $context = null;
    $request = null;
    $response = xmlrpc_decode($file);
    return $response;
}