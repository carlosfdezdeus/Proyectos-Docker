$(document).on("dblclick", ".shelf_number", function(event) {
	var current_id_shelf = $(this).attr('id_shelf');
	DebugLog('click derecho en '+ current_id_shelf);
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	msg = 'Confirmar terminar lanzada de bandeja '+ shelf_data.shelf +'<br>('+ shelf_data.id_shelf +')';
	$( "#dialog-message #msg" ).html(msg);
	$( "#dialog-message" ).dialog({
		resizable: true,
		height: 300,
		width: 550,
		modal: true,
		title: "Confirmar finalizar lanzada",
		buttons: {
			"Terminar": function() {
				var serial = shelf_data.serial;
				var relaunch_data = {
					'serial': shelf_data.serial,
                    'model_id': shelf_data.model_fields.model_id,
                    'model': shelf_data.model_fields.model,
                    'customer_info': shelf_data.model_fields.customer_info,
					'info': 'Finalizada manualmente',
					'flow': 'ENDED',
					'flow_color': 'white',
					'flow_text_color': 'orange'
				};
				var payload = JSON.stringify({id_user: user_id, id_shelf: shelf_data.id_shelf, event:"end_launch", data: relaunch_data});
				serverList[shelf_data.ws_port].socket.send( payload );
				DebugLog('Bandeja '+ shelf_data.shelf +'<br>('+ shelf_data.id_shelf +') finalizada.');
				$( this ).dialog( "close" );
				shelf_data.shelf_number_color = 'black';
				shelf_data.log = '';
				shelf_data.last_launch_info = {};
				$.each(shelf_data.model_fields, function (field, value) {
					if ($.inArray( $.trim(field), [ 'model_id', 'model', 'customer_info', 'invoice' ] ) == -1) {
						shelf_data.model_fields[field] = '';
					}
				});
				shelf_data.current_view = 'msg';
				shelf_data.msg = '<h3>Bandeja finalizada manualmente</h3>';
				shelf_data.serial = serial;
				shelf_data.last_launch_info.info = 'Finalizada manualmente';
				shelf_data.last_launch_info.flow = 'ENDED';
				shelf_data.flow_color = 'white';
				shelf_data.flow_text_color = 'orange';
				shelf_data.last_launch_info_datetime = '';
				shelf_data.s_from_launch = null;
				shelf_data.s_from_last_server_activity_datetime = null;
				$('.relaunch_button[id_shelf='+ shelf_data.id_shelf +']').hide();
				RefreshTimeFromLastActivity();
				SaveShelf(shelf_data.id_shelf);
                UpdateShelf(shelf_data.id_shelf);
				return;
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
				return;
			}
		}
	})
});