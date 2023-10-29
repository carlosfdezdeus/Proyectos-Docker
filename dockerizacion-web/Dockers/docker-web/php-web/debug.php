<?php

/*
 * Funcion que almacena mensajes de depuracion en los logs
 */

function debug($file, $text) {
    if($file && $text){
        $date = date("m-d-Y")."-". date("H:i:s");
        $log = "\"$date\" $text \n";
        $filename = DEBUG_FILES_PATH . $file . ".log";
        file_put_contents($filename, $log, FILE_APPEND | LOCK_EX);
    }
}