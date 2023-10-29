var serverList = {};
var user_id;

function Response(id_shelf, response) {
	var payload = JSON.stringify({id_user: user_id, id_shelf: id_shelf, event:'operator_answer', data: {response: response}});
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	serverList[shelf_data.ws_port].socket.send( payload );
}

function CheckConnectedClients(id_shelf) {
    var shelf_data = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
    DebugLog( 'Asking for ClientID of id_shelf = '+ id_shelf);
    var payload = JSON.stringify({id_user: user_id, id_shelf: id_shelf, event:'my_ws_client_id'});
    serverList[shelf_data.ws_port].socket.send( payload );
    // Deshabilitamos la comprobacion de clientes por las desconexiones del script de python (#33735)
	//DebugLog( 'Asking for clients connected to '+ serverList[shelf_data.ws_port].url +':'+ shelf_data.ws_port +' ...');
	//var payload = JSON.stringify({id_user: user_id, id_shelf: id_shelf, event:'clients_count'});
	//serverList[shelf_data.ws_port].socket.send( payload );
    return;
}

function Connect2WSServer(ws_url, port) {
	serverList[port] = {};
	serverList[port].url = ws_url;
	serverList[port].connected = false;
    serverList[port].socket = new FancyWebSocket(ws_server, port);

	DebugLog('Connecting to '+ ws_server +':'+ port +' ...');
    serverList[port].socket.bind('open', function( data ) {
		DebugLog( 'Connected to '+ data.server +':'+ data.port +' ...');
        serverList[port].connected = true;
        DebugLog( 'Asking for buffer to '+ data.server +':'+ data.port +' ...');
        var payload = JSON.stringify({id_user: user_id, event:'load_buffer'});
        serverList[port].socket.send( payload );
        var shelf_data = shelves.find(bandeja => bandeja.ws_port == port);
        CheckConnectedClients(shelf_data.id_shelf);
	});
	serverList[port].socket.bind('close', function( data ) {
		DebugWarn( 'Disconnected from '+ data.server +':'+ data.port +' ...');
		serverList[port].connected = false;
		var ws_server_shelves = shelves.filter(bandeja => bandeja.ws_port == port);
		$.each(ws_server_shelves, function(index, shelf_data) {
			RefreshShelf(shelf_data.id_shelf);
		});
	});

	serverList[port].socket.bind('event', function( payload ) {
		if (!payload.hasOwnProperty('json')){
			DebugWarn( 'Evento sin JSON (' + payload.data +')');
			return;
		}
		if (!payload.json) {
			return;
		}
		DebugInfo(payload.json);
		/*
		if (payload.json.id_user == user_id) {
			DebugInfo('ignorado mensaje propio');
			return;
		}
        */
		if (!payload.json.hasOwnProperty('id_shelf')){
			DebugWarn( 'Evento sin id_shelf.' );
			return;
		}
		var shelf_data = shelves.find(bandeja => bandeja.id_shelf == payload.json.id_shelf);
		if (!shelf_data) {
			//DebugWarn( "Bandeja '"+ payload.json.id_shelf +"' no se encuentra en la vista actual." );
			return;
		}
		if (shelf_data.ws_port != port) {
			DebugInfo('ignorado por ser de otro puerto ['+ port +' (current) <> '+ shelf_data.ws_port +' (incomming)]');
			return;
		}
        if (!("msg_seq" in shelf_data)) {
            DebugInfo('msq_seq puesto a 0');
            shelf_data.msg_seq = 0;
        }
		if ((shelf_data.msg_seq >= payload.msg_seq) && (payload.json.event != 'shelf_update')) {
			DebugInfo('rechazado msg por ser mas antiguo que el ultimo recibido ['+ shelf_data.msg_seq +' (current) >= '+ payload.msg_seq +' (incomming)]');
			return;
		}
		DebugInfo('aceptado msg en puerto ('+ port +') ['+ shelf_data.msg_seq +' (current) < '+ payload.msg_seq +' (incomming)]');
		if (payload.hasOwnProperty('msg_seq')){
            shelf_data.msg_seq = payload.msg_seq;
        }
		switch(payload.json.event) {
			case 'launch':
				DebugLog('! launch event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.shelf_number_color = 'dodgerblue';
				shelf_data.last_launch_info_datetime = new Date().toString();
				shelf_data.s_from_launch = 1;
				shelf_data.s_from_last_server_activity_datetime = null;
				RefreshTimeFromLastActivity();
				break;
			case 'relaunch':
				DebugLog('! relaunch event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.shelf_number_color = 'darkorange';
				shelf_data.last_launch_info_datetime = new Date().toString();
				shelf_data.s_from_launch = 1;
				shelf_data.s_from_last_server_activity_datetime = null;
				RefreshTimeFromLastActivity();
				break;
			case 'end_launch':
				DebugLog('! end_launch event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.shelf_number_color = 'black';
				shelf_data.log = '';
				shelf_data.last_launch_info = payload.json.data;
				$.each(shelf_data.model_fields, function (field, value) {
                    if ($.inArray( $.trim(field), [ 'model_id', 'model', 'customer_info', 'invoice' ] ) == -1) {
						shelf_data.model_fields[field] = '';
					}
				});
				shelf_data.current_view = 'msg';
				shelf_data.msg = '<h3>Bandeja finalizada</h3>';
				shelf_data.last_launch_info_datetime = '';
				shelf_data.s_from_launch = null;
				shelf_data.s_from_last_server_activity_datetime = null;
				RefreshTimeFromLastActivity();
				break;
			case 'write_log':
				DebugLog('! write_log event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.s_from_last_server_activity_datetime = 0;
				shelf_data.current_view = 'log';
				var new_log = '[' + new Date().toLocaleTimeString() + '] ';
				new_log += payload.json.data.msg + '<br>';
				shelf_data.log = new_log + shelf_data.log;
				break;
			case 'operator_question':
				DebugLog('! operator_question event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.s_from_last_server_activity_datetime = 0;
				shelf_data.current_view = 'question';
				shelf_data.msg = payload.json.data.msg;
				shelf_data.response_type = payload.json.data.type;
				shelf_data.responses = payload.json.data.responses;
				shelf_data.response_options = payload.json.data.response_options;
				shelf_data.responses_initial = payload.json.data.responses;
				var array_responses = {};
				// formateamos un diccionario con las respuestas si es un array lo convertimos
				if (Array.isArray(payload.json.data.responses)) {
					$.each(payload.json.data.responses, function (response, value) {
						array_responses[value] = value;
					});
					shelf_data.responses = array_responses;
				}
				break;
			case 'full_msg':
				DebugLog('! full_msg event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.s_from_last_server_activity_datetime = 0;
				shelf_data.current_view = 'msg';
                shelf_data.msg = payload.json.data.msg;
				break;
			case 'load_data':
				DebugLog('! load_data event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				shelf_data.s_from_last_server_activity_datetime = 0;
				shelf_data.model_fields = payload.json.data.device_data;
				$('#MenuModalLaunch').modal('hide');
				break;
			case 'shelf_update':
				DebugLog('! shelf_update event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
				$('#MenuModalLaunch').modal('hide');
				var index = shelves.findIndex(bandeja => bandeja.id_shelf == payload.json.id_shelf);
				shelves[index] = payload.json.data;
				RefreshTimeFromLastActivity();
                break;
                case 'clients_count':
                    DebugLog('! clients_count event detected (shelf='+ payload.json.id_shelf +' ws_client_id='+ payload.ws_client_id +')');
                    DebugLog('clients_count_total='+ payload.json.clients_count_total);
                    DebugLog('clients_count_browser='+ payload.json.clients_count_browser);
                    DebugLog('clients_count_undefined='+ payload.json.clients_count_undefined);
                    var index = shelves.findIndex(bandeja => bandeja.id_shelf == payload.json.id_shelf);
                    if (parseInt(payload.json.clients_count_undefined) < 1) {
                        DebugWarn( 'No se ha encontrado script conectado en el puerto: '+ payload.json.ws_port +'.');
                        var ws_server_shelves = shelves.filter(bandeja => bandeja.ws_port == port);
                        $.each(ws_server_shelves, function(index, shelf_data) {
                            shelf_data.current_view = 'msg';
                            shelf_data.shelf_color = 'red';
                            //shelf_data.shelf_number_color = 'black';
                            shelf_data.msg = "No se ha encontrado script conectado. <br><h2>Avisar a Sistemas</h2>";
                            RefreshShelf(shelf_data.id_shelf);
                        });
                        $('#MenuModalLaunch').modal('hide');
                        DebugInfo('Cierra el modal de lanzar porque script no esta conectado en el puerto '+port);
                    }
                    break;
			case 'your_ws_client_id':
                if (payload.json.hasOwnProperty('your_ws_client_id')) {
                    DebugLog( 'ClientID set as :'+ payload.json.your_ws_client_id +'.');
                    shelf_data.ws_client_id = payload.json.your_ws_client_id;
                }
				break;
			default:
				DebugWarn( "Evento '"+ payload.json.event +"' no soportado." );
				return;
        }
        if (payload.json.hasOwnProperty('data')) {
            if (payload.json.data.hasOwnProperty('shelf_color')) {
                DebugInfo('shelf_color = '+ payload.json.data.shelf_color);
                shelf_data.shelf_color = payload.json.data.shelf_color;
            }
            if (payload.json.data.hasOwnProperty('shelf_text_color')) {
                shelf_data.shelf_text_color = payload.json.data.shelf_text_color;
            }
            if (payload.json.data.hasOwnProperty('flow_color')) {
                shelf_data.flow_color = payload.json.data.flow_color;
            }
            if (payload.json.data.hasOwnProperty('flow_text_color')) {
                shelf_data.flow_text_color = payload.json.data.flow_text_color;
            }
            if (payload.json.data.hasOwnProperty('warning_color')) {
                shelf_data.warning_color = payload.json.data.warning_color;
            }
            if (payload.json.data.hasOwnProperty('warning_text_color')) {
                shelf_data.warning_text_color = payload.json.data.warning_text_color;
            }
            if (payload.json.data.hasOwnProperty('warning')) {
                shelf_data.warning = payload.json.data.warning;
            }
        }
        RefreshShelf(shelf_data.id_shelf);
        if ($.inArray( payload.json.event, [ 'operator_question', 'full_msg' ] ) != -1) {
            SaveShelf(shelf_data.id_shelf);
        }
		UpdateVisibilityDefaultAnswerAllButtons();
	});
}

$(document).on("click", ".lanzador_puertos", function() {
	var port = $(this).attr("port");
	var id_shelf =  $(this).attr("id_shelf");
	Launch_port(port,id_shelf);
});