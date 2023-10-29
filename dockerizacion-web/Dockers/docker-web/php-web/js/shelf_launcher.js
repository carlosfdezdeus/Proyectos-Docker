$('.popover-dismiss').popover({
	trigger: 'focus'
});

$(document).on("change blur", ".shelf_data", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	SaveShelf(current_id_shelf);
    UpdateShelf(current_id_shelf);
});

$(document).on("shown.bs.modal", "#MenuModalLaunch", function() {
    // Comprobamos que exista un script escuchando
    CheckConnectedClients(current_id_shelf);
});

$(document).on("click", "input.launch_button", function() {	
	current_id_shelf = $(this).attr("id_shelf");
	var json_sede = JSON.parse(site_data[0].site_json);
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);	
	if(json_sede.enable_albaran == "1"){		
		GetDeliveryNote(json_sede, current_id_shelf, shelf_data.invoice);
	}
	var selected_model = logistics_models.find(modelo => modelo.model_id == shelf_data.model_fields.model_id );
	var selected_model_fields = models_fields.filter(test => test.model_id == selected_model.model_id);
    var customer_info = '';
    if (shelf_data.model_fields.customer_info) {
        customer_info = '('+ shelf_data.model_fields.customer_info +')'
    }
    $.templates("#modal_launch_form").link("#modal_launch_needed_fields", shelf_data);
    $('#MenuModalLaunch').modal('show');
	$('#MenuModalLaunch #titulo').html(shelf_data.shelf +' - Campos obligatorios para lanzar');
	input = '<label for="model" class="col-sm-3 col-form-label text-center"><b>model</b></label>';
	input += '<div class="col-sm-9">';
	input += '    <input class="form-control shelf_data" id="shelf-'+ current_id_shelf +'_model"';
	input += '        value="'+ shelf_data.model_fields.model + ' ' + customer_info + '"';
	input += '        id_shelf="'+ current_id_shelf +'"';
	input += '        disabled="disabled">';
	input += '</div>';
	$('#MenuModalLaunch #model_fields').append(input);
	shelf_data.model_fields = {
        'model_id': shelf_data.model_fields.model_id,
        'model': shelf_data.model_fields.model,
        'customer_info': shelf_data.model_fields.customer_info
    };
	if (shelf_data.only_test == "1") {
		var selected_model_field_serial = models_fields.find(test => test.model_id == selected_model.model_id && test.name == 'serial');
		var serial_regex = ''
		if (selected_model_field_serial) {
			serial_regex = selected_model_field_serial.reg_exp;
		}
		input = '<label for="serial" class="col-sm-3 col-form-label text-center"><b>serial</b></label>';
		input += '<div class="col-sm-9">';
		input += '    <input class="form-control shelf_data required"';
		input += '        id="serial"';
		input += '        name="serial"';
		input += '        value=""';
		input += '        id_shelf="'+ current_id_shelf +'"';
		input += '        order_to_ask="NULL"';
		input += '        regex="'+ serial_regex +'">';
		input += '</div>';
		$('#MenuModalLaunch #model_fields').append(input);
	} else {
		// Arreglo para evitar campos duplicados
		$.each(selected_model_fields, function (i, field) {
			if (!$('#MenuModalLaunch #model_fields input[name="'+ field.name +'"]').length) {
				if ( !(field.name in shelf_data.model_fields) ) {
					shelf_data.model_fields[field.name] = '';
				}
				input = '<label for="'+ field.name +'" class="col-sm-3 col-form-label text-center"><b>'+ field.name +'</b></label>';
				input += '<div class="col-sm-9">';
				input += '    <input class="form-control shelf_data required"';
				input += '        id="'+ field.name +'"';
				input += '        name="'+ field.name +'"';
				input += '        value="'+ shelf_data.model_fields[field.name] +'"';
				input += '        id_shelf="'+ current_id_shelf +'"';
				input += '        order_to_ask="'+ field.order_to_ask +'"';
				input += '        regex="'+ field.reg_exp +'">';
				input += '</div>';
				$('#MenuModalLaunch #model_fields').append(input);
			}
		});
	}
	$('#MenuModalLaunch .relaunch_button').remove();
	if (shelf_data.last_launch_info !== undefined) {
		if ((shelf_data.last_launch_info.model !== undefined) && ($('#MenuModalLaunch .relaunch_button').length == 0)) {
			if (shelf_data.environment_name.includes("STB")) {
				input = '<button type="button" class="btn btn-primary mr-auto relaunch_button no_fw" id_shelf='+ current_id_shelf + '>ReLanzar sin FW</button>';
				$('#MenuModalLaunch .modal-footer').html(input + $('#MenuModalLaunch .modal-footer').html());
			}
			input = '<button type="button" class="btn btn-primary mr-auto relaunch_button" id_shelf='+ current_id_shelf + '>ReLanzar</button>';
			$('#MenuModalLaunch .modal-footer').html(input + $('#MenuModalLaunch .modal-footer').html());
		}
	}
	TestInHandReset(current_id_shelf);
	$.each($('#MenuModalLaunch input.shelf_data:not([disabled])[id$="_check"]'), function (i, field) {
		var field2check = $(this).attr("id").split('_check')[0];
		$('#MenuModalLaunch input.shelf_data#'+ field2check +'_check').each(function () {
			$(this).rules('add', {
				equalTo: '#'+field2check,
				messages: {
					equalTo: 'No coincide con el campo '+field2check
				}
			});
		});
	});
    $("#MenuModalLaunch #launch_form").validate().settings.ignore = "*";
});

$(document).on("click", ".launch_without_data_button", function() {
	var current_id_shelf = $(this).attr("id_shelf");
	$("#shelf2launch").val(current_id_shelf);
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	var shelf_launch_data = Object.assign({}, shelf_data.model_fields);
	shelf_launch_data.logistic_id = shelf_data.logistic_id;
	shelf_launch_data.logistic_name = shelf_data.logistic_name;
	shelf_launch_data.invoice = shelf_data.invoice;
	DebugInfo("Lanzamiento de bandeja "+ current_id_shelf +" sin datos.");
	var payload = JSON.stringify({id_user: user_id, id_shelf: current_id_shelf, event:"launch", data: shelf_launch_data});
	serverList[shelf_data.ws_port].socket.send( payload );
	shelf_data.s_from_launch = 0;
    shelf_data.msg_seq = 0;
	RefreshTimeFromLastActivity();
	shelf_data.shelf_number_color = 'dodgerblue';
	shelf_data.last_launch_info_datetime = new Date().toString();
	SaveShelf(current_id_shelf);
    UpdateShelf(current_id_shelf);
	$('#MenuModalLaunch').modal('hide');
});

$(document).on("click", ".relaunch_button", function() {
	var current_id_shelf = $(this).attr("id_shelf");
	$("#shelf2launch").val(current_id_shelf);
	DebugInfo("Relanzamiento de bandeja "+ current_id_shelf);
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
    // Comprobamos que exista un script escuchando
    CheckConnectedClients(shelf_data.id_shelf);
	shelf_data.last_launch_info_datetime = new Date().toString();
	if ($(this).hasClass( "no_fw" )) {
		shelf_data.last_launch_info.no_fw = 1;
		DebugWarn("Relanzamiento bandeja "+ current_id_shelf +" sin FW");
	}
	if (shelf_data.last_launch_info) {
		var payload = JSON.stringify({id_user: user_id, id_shelf: current_id_shelf, event:"relaunch", data: shelf_data.last_launch_info});
		serverList[shelf_data.ws_port].socket.send( payload );
		shelf_data.s_from_launch = 0;
        shelf_data.msg_seq = 0;
		RefreshTimeFromLastActivity();
		shelf_data.shelf_number_color = 'dodgerblue';
        shelf_data.last_launch_info_datetime = new Date().toString();
        // Comprobamos que exista un script escuchando
        CheckConnectedClients(shelf_data.id_shelf);
	} else {
		$('.relaunch_button[id_shelf='+ current_id_shelf +']').hide();
		DebugWarn("Intento de ReLanzar bandeja "+ current_id_shelf +" sin datos");
	}
	SaveShelf(current_id_shelf);
    UpdateShelf(current_id_shelf);
	$('#MenuModalLaunch').modal('hide');
});

$(document).on("click", "input.copy_button", function() {
	var current_id_shelf = $(this).attr("id_shelf");
    var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
    var customer_info = '';
    if (shelf_data.model_fields.customer_info) {
        customer_info = '('+ shelf_data.model_fields.customer_info +')'
    }
	$('#MenuModalCopy #model_button-modal').val("Modelo: "+ shelf_data.model_fields.model + ' ' + customer_info);
	$('#MenuModalCopy #model_button-modal').attr("model_id", shelf_data.model_fields.model_id);
	$('#MenuModalCopy #invoice_button-modal').val("Albaran: "+ shelf_data.invoice);
	$('#MenuModalCopy #invoice_button-modal').attr("invoice", shelf_data.invoice);
	$.templates("#modal_copy_racks").link("#MenuModalCopy #racks", racks);
	$.each(racks, function(index, rack) {
		shelves_rack = shelves.filter(function (shelf) {
			return shelf.rack == rack
		});
		$.templates("#modal_copy_shelves").link("#MenuModalCopy #panel_rack_"+ rack, shelves_rack);
	});
	$('#MenuModalCopy').modal('show');
	RefreshShelves();
});

$(document).on("click", "input.model", function() {
	current_id_shelf = $(this).attr("id_shelf");
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	$('#MenuModalSelect #titulo').html('Seleccionar Log&iacute;stica');
	$.templates("#modal_select_form").link("#MenuModalSelect #modal_options", {'option_type':'logistics', 'options': logistics});
	$('#MenuModalSelect').modal('show');
});

$(document).on("change", "input.invoice", function() {
	var current_id_shelf = $(this).attr("id_shelf");
	var new_value = $(this).val();
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	DebugInfo("Cambio de albaran en bandeja "+ current_id_shelf +" a '"+ new_value +"'");
	shelf_data.invoice = new_value;
	SaveShelf(shelf_data.id_shelf);
    UpdateShelf(shelf_data.id_shelf);
});

function GetDeliveryNote(datos_sede, current_shelf, current_invoice) {	
	var remaining_on_invoice = 88888888;	
	$('#MenuModalLaunch #invoice_alert').val();
	$('#MenuModalLaunch #invoice_alert').empty();
	$('#MenuModalLaunch #current_invoice').val();
	$('#MenuModalLaunch #current_invoice').empty();	
	$('#MenuModalLaunch #current_invoice').html('<b>albaran: </b>'+current_invoice);	
	$('#MenuModalLaunch #remaining_on_invoice').val();
	$('#MenuModalLaunch #remaining_on_invoice').empty();
	
	if(current_invoice){
	const intranet_invoice_link = intranet_invoice_path + current_invoice;	
	$.get(intranet_invoice_link,function(data) {
    const invoice_data =  $.parseJSON(data);
    if (invoice_data.length > 0){   	
         remaining_on_invoice = invoice_data[0].previstos_restantes;
    	 $('#MenuModalLaunch #remaining_on_invoice').html('<b>restantes previstos: </b>'+ remaining_on_invoice);    	 
    	 if(remaining_on_invoice <= 0){    	 	 
    	 	 $('#MenuModalLaunch #invoice_alert').html('<b>ALBARAN COMPLETO</b>');
    	 }    	 
       }	
    });	
   }
}
