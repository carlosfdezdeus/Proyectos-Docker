<?php

require_once './libs/tailCustom.php';

$debug_files_base_path = '/var/log/validator-web/';
$debug_files_max_lines = 5000;
$msg_seq = 0;

// Preparamos las variables a partir de los argumentos
if ($argc < 3) {
    exit("Uso: php ws_server.php [IP] [puerto]\n");
}

$ws_server_IP = $argv[1];
$ws_server_PORT = $argv[2];
$buffer_msgs = [];

exec("ps aux | grep -i 'php ws_server.php $ws_server_IP $ws_server_PORT' | grep -v grep", $pids);
if (count($pids) > 1) {
    echo "Ya existe un ws_server en el puerto $ws_server_PORT\n";
    exit;
}

$log_file = '/var/log/validator-web/ws_server_'.$ws_server_PORT.'.log';

// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)

$PATH_Servidor = dirname(__DIR__);

require $PATH_Servidor.'./libs/class.PHPWebSocket.php';

/*
 * Funcion que almacena mensajes de depuracion en los logs
 */

function WriteLog($text, $erase = false) {
    global $log_file, $debug_files_max_lines;
    if ($text) {
        $log_lines_max = 500;
        $date = date('m-d-Y').'-'.date('H:i:s');
        $log = "<hr>\n";
        $log .= "\"$date\" $text \n";

        file_put_contents($log_file, tailCustom($log_file, $debug_files_max_lines) . $log, LOCK_EX);
    }
}

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
    global $server, $ws_server_PORT, $buffer_msgs, $msg_seq;
    $ip = long2ip($server->wsClients[$clientID][6]);
    // check if message length is 0
    if ($messageLength == 0) {
        $server->wsClose($clientID);
        return;
    }

    $json = json_decode($message, true);

    if ($json['event'] == 'clients_count') {
        $clients_count_total = sizeof($server->wsClients);
        $clients_count_browser = 0;
        foreach ($server->wsClients as $id => $client) {
            if ($client[12] == 'browser') {
                $clients_count_browser++;
            }
        }
        $clients_count_undefined = $clients_count_total - $clients_count_browser;
        WriteLog("<h2>Hay $clients_count_browser clientes identificados como navegadores de $clients_count_total clientes en total</h2>'");
        $id_shelf = $json['id_shelf'];
        $json = Array();
        $json['event'] = 'clients_count';
        $json['id_shelf'] = $id_shelf;
        $json['ws_port'] = intval($ws_server_PORT);
        $json['clients_count_total'] = $clients_count_total;
        $json['clients_count_browser'] = $clients_count_browser;
        $json['clients_count_undefined'] = $clients_count_undefined;
        $message = json_encode(['ws_client_id' => $clientID, 'ws_client_ip' => $ip, 'json' => $json]);
        $server->wsSend($id, "$message");
        return;
    }

    if ($json['event'] == 'my_ws_client_id') {
        # Send client its ID
        $id_shelf = $json['id_shelf'];
        $json = Array();
        $json['event'] = 'your_ws_client_id';
        $json['id_shelf'] = $id_shelf;
        $json['your_ws_client_id'] = $clientID;
        $message = json_encode(['ws_client_id' => $clientID, 'ws_client_ip' => $ip, 'json' => $json]);
        $server->wsSend($clientID, "$message");
        return;
    }

    if ($json['event'] == 'load_buffer') {
        # Mark client as browser
        $server->wsClients[$clientID][12] = 'browser';
        WriteLog("<font color='blue'>$ip [$clientID] (". $server->wsClients[$clientID][12] .") marcado como navegador.</font>");
        foreach ((array_column($buffer_msgs, 'json')) as $msg) {
            $server->wsSend($clientID, "$msg");
            $json = json_decode($msg, true);
            WriteLog("<font color='teal'>Enviado msg[".$json['msg_seq']."] desde buffer a $ip [$clientID] (". $server->wsClients[$clientID][12] ."):</font><p><pre>'".print_r(json_decode($msg, true), true)."'</pre>");
        }
        return;
    }

    if ($json['event'] != 'operator_answer') {
        # Answers not saved in buffer because browser ignore them
        $buffer_msgs[] = Array(
            'event' => $json['event'],
            'json' => json_encode(['ws_client_id' => $clientID, 'ws_client_ip' => $ip, 'msg_seq' => $msg_seq, 'json' => $json])
        );
        if ($json['event'] == 'write_log') {
            # on a write_log event count the amount of write_log events in buffer
            $buffer_by_event = array_column($buffer_msgs, 'event');
            if (array_count_values($buffer_by_event)['write_log'] > 5) {
                # if the amount of write_log events are more than 5 we delete de oldest
                $index_of_oldest = array_search('write_log', $buffer_by_event);
                array_splice($buffer_msgs, $index_of_oldest, 1);
            }
        }
    }

    if (in_array($json['event'], Array('launch','relaunch'))) {
        # Reset msg sequence in new launch
        $msg_seq = 0;
    }

    $msg_seq++;
    WriteLog("$ip [$clientID] (". $server->wsClients[$clientID][12] .") dijo:<p><pre>'".print_r($json, true)."'</pre>");

    if (in_array($json['event'], Array('shelf_update','relaunch','launch'))) {
        # Update is send after saving in DB. The old msg are not longer necessary for keep the browser update
        $buffer_msgs = [];
        WriteLog("<h2>Evento shelf_update recibido y Buffer reseteado</h2>'", true);
    }

    //The speaker is the only person in the room. Don't let them feel lonely.
    if (sizeof($server->wsClients) == 1) {
        $server->wsSend($clientID, "Estas hablando solo en el servidor. tu mensaje fue '$message' ");
    } else {
        //Send the message to everyone but the person who said it
        //$server->log( "! Cliente $clientID ($ip) dijo: '$message'" );
        $message = json_encode(['ws_client_id' => $clientID, 'ws_client_ip' => $ip, 'msg_seq' => $msg_seq, 'json' => $json]);
        foreach ($server->wsClients as $id => $client) {
            if ((int) $id != (int) $clientID) {
                $ip_destino = long2ip($server->wsClients[$id][6]);
                $server->wsSend($id, "$message");
                WriteLog("Enviado msg[$msg_seq] de $ip [$clientID] (". $server->wsClients[$clientID][12] .") a $ip_destino [$id] (". $server->wsClients[$id][12] .")");
            }
        }
    }
}

// when a client connects
function wsOnOpen($clientID) {
    global $server;
    $ip = long2ip($server->wsClients[$clientID][6]);

    $server->wsClients[$clientID][12] = 'unidentified';

    //$server->log( "$ip ($clientID) se ha conectado." );
    WriteLog("<font color='green'>$ip [$clientID] (". $server->wsClients[$clientID][12] .") se ha conectado.</font>");

    //Send a join notice to everyone but the person who joined
//	foreach ( $server->wsClients as $id => $client )
//		if ( $id != $clientID )
//			$server->wsSend($id, "Cliente $clientID ($ip) se ha unido al servidor.");
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
    global $server;
    $ip = long2ip($server->wsClients[$clientID][6]);

    //$server->log( "$ip ($clientID) se ha desconectado." );
    WriteLog("<font color='red'>$ip [$clientID] (". $server->wsClients[$clientID][12] .") se ha desconectado.</font>");

    //Send a user left notice to everyone in the room
//	foreach ( $server->wsClients as $id => $client )
//		$server->wsSend($id, "Cliente $clientID ($ip) se ha desconectado.");
}

// start the server
$server = new PHPWebSocket();
$server->bind('message', 'wsOnMessage');
$server->bind('open', 'wsOnOpen');
$server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
echo 'Arrancando servidor en '.$ws_server_IP.':'.$ws_server_PORT."...\n";
WriteLog('<h2>Arrancando servidor en '.$ws_server_IP.':'.$ws_server_PORT.'...</h2>');
$server->wsStartServer($ws_server_IP, $ws_server_PORT);