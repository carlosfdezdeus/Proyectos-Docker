$(document).on("hidden.bs.modal", "#MenuModalLaunch", function () {
    RefreshShelves();
});

$(document).on('shown.bs.modal', "#MenuModalLaunch", function (e) {
	$('#MenuModalLaunch .shelf_data#serial').select();
	if (allow_loop) {
		$('#lanzar_en_bucle').show();
	}
})

$(document).on("click", ".shelf_data", function() {
	$(this).select();
});

$(document).on("click", "#MenuModalLaunch #btn-launch", function() {
	$("#MenuModalLaunch #launch_form").validate().settings.ignore = ":hidden";
	$("#MenuModalLaunch #launch_form .shelf_data").valid();
	if ($("#MenuModalLaunch #launch_form").valid() == false) {
		DebugWarn('Datos incorrectos en formulario');
		$(validator.errorList[0].element).select();
		return;
	}
	$.each($("#MenuModalLaunch #launch_form .shelf_data"), function (field, value) {
		DebugLog($.trim($(this).val()));
		if ($.trim($(this).val()) == '') {
			DebugWarn('Datos incorrectos en el campo '+ $(this).attr('id') +' del formulario');
			return;
		}
	})
	shelf2launch = $("#shelf2launch").val();
	index_next_shelf2launch = $('input[id^=launch_button-]').index($('input[id^=launch_button-'+ shelf2launch +']')) + 1;
	next_shelf2launch_button = $('input[id^=launch_button-]')[index_next_shelf2launch];
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == shelf2launch);
	var shelf_launch_data = Object.assign({}, shelf_data.model_fields);
	shelf_launch_data.logistic_id = shelf_data.logistic_id;
	shelf_launch_data.logistic_name = shelf_data.logistic_name;
	shelf_launch_data.invoice = shelf_data.invoice;
	shelf_launch_data.loops = $('#MenuModalLaunch #loops').val();
	if ($(".btn.test_in_hand_response.btn-info").length == 0) {
		alert("Error: no se ha seleccionado Test in Hand");
		return;
	}
	shelf_launch_data.test_in_hand = $(".btn.test_in_hand_response.btn-info").val();
	shelf_data.last_launch_info_datetime = new Date().toString();
	var campos_nulos = false;
	$.each(shelf_launch_data, function (field, value) {
		if (($.trim(value) == '') && ($.inArray( $.trim(field), [ 'customer_info', 'logistic_name', 'invoice' ] ) == -1)) {
			DebugWarn('Datos incorrectos en el campo '+ field +' del formulario (no opcionales)');
			campos_nulos = true;
		}
	})
	if (campos_nulos) return;
	$("#MenuModalLaunch #launch_form .shelf_data").valid();
	if ($("#MenuModalLaunch #launch_form").valid() == false) {
		$(validator.errorList[0].element).select();
	} else {
		if ($.active == 0) {
			DebugInfo("Lanzamiento de bandeja "+ shelf2launch);
			var payload = JSON.stringify({id_user: user_id, id_shelf: shelf2launch, event:"launch", data: shelf_launch_data});
			var shelf_data = shelves.find(bandeja => bandeja.id_shelf == shelf2launch);
			serverList[shelf_data.ws_port].socket.send( payload );
			shelf_data.s_from_launch = 0;
            shelf_data.msg_seq = 0;
			RefreshTimeFromLastActivity();
			shelf_data.shelf_number_color = 'dodgerblue';
		} else {
			DebugInfo('Esperando a que terminen las consultas Ajax actuales ('+ $.active +')');
			$(document).one('ajaxStop', function () {
				DebugInfo("Lanzamiento retrasado de bandeja "+ shelf2launch);
				var payload = JSON.stringify({id_user: user_id, id_shelf: shelf2launch, event:"launch", data: shelf_launch_data});
				var shelf_data = shelves.find(bandeja => bandeja.id_shelf == shelf2launch);
				serverList[shelf_data.ws_port].socket.send( payload );
				shelf_data.s_from_launch = 0;
                shelf_data.msg_seq = 0;
				RefreshTimeFromLastActivity();
				shelf_data.shelf_number_color = 'dodgerblue';
			});
		}
		$('#MenuModalLaunch #loops').val(1);
	}
	SaveShelf(shelf2launch);
    UpdateShelf(shelf2launch);
    $('#MenuModalLaunch').modal('hide');
	// Que salte a la siguiente bandeja sin lanzar
	//setTimeout(function() { $('#'+ $(next_shelf2launch_button).attr('id')).trigger('click'); }, 2000);
});

$(document).on("click", "#MenuModalLaunch #btn-close", function() {
	$("#shelf2launch").val('');
	$('#MenuModalLaunch #loops').val(1);
	RefreshShelves();
});

$(document).on("change", "#MenuModalLaunch .shelf_data", function() {
	var current_id_shelf = $(this).attr("id_shelf");
	var current_field = $(this).attr("id");
	var new_value = $(this).val();
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	DebugInfo("Cambio en "+ current_field +" de bandeja "+ current_id_shelf +" a '"+ new_value +"'");
	if (current_field != 'loops') {
		shelf_data.model_fields[current_field] = new_value;
	}
});

$(document).on("keydown", "#MenuModalLaunch .shelf_data", function(event) {
	if (event.keyCode === 13) {
		event.preventDefault();
		var current_field = $(this).attr("id");
		var current_id_shelf = $(this).attr("id_shelf");
		$(this).trigger('change');
		// No comprobamos la regex entre tickados
		if ($("#MenuModalLaunch #launch_form .shelf_data").filter(function() {return !this.value;}).length == 0) {
			// Si no hay inputs vacios, intentamos lanzar el equipo
			if ($.active == 0) {
				$("#MenuModalLaunch #launch_form .shelf_data").valid();
				$("#MenuModalLaunch #btn-launch").trigger("click");
			} else {
				DebugInfo('Esperando a que terminen las consultas Ajax actuales ('+ $.active +')');
				$(document).one('ajaxStop', function () {
					$("#MenuModalLaunch #launch_form .shelf_data").valid();
					$("#MenuModalLaunch #btn-launch").trigger("click");
				});
			}
		} else {
            if ($("#MenuModalLaunch #launch_form").validate().settings.ignore == ":hidden") {
				$(validator.errorList[0].element).select();
			} else {
				var inputs = $(this).closest('form').find(':input:text:visible');
				inputs.eq( inputs.index(this) + 1 ).select();
			}
		}
	}
});

$(document).on("click", ".btn.test_in_hand_group", function(e) {
	var current_id_shelf = $(this).attr('id_shelf');
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
    var selected_model = logistics_models.find(modelo => modelo.model_id == shelf_data.model_fields.model_id);
	// TODO: filtrar por customer_id
    var intranet_id = selected_model.intranet_model_id;
	var tests_in_hand = GiveMeBarcodes(intranet_id, shelf_data);
	var tests_in_hand_fails = tests_in_hand.filter(test => (test.priority == 2) );
	$("#MenuModalLaunch #test_in_hand").empty();
	$.each(tests_in_hand_fails.reverse(), function (index, test) {
		if ($("#MenuModalLaunch #test_in_hand button[value='"+ test.barcode +"']").length == 0) {
			$("<button>")
				.attr({type: "button", class: 'btn test_in_hand_response btn-secondary col', value: test.barcode, id_shelf: current_id_shelf })
				.html(test.operator_show)
				.appendTo('#MenuModalLaunch #test_in_hand');
		}
	})
	$("<br>")
		.appendTo('#MenuModalLaunch #test_in_hand');
	$("<button>")
		.attr({type: "button", class: 'btn test_in_hand_response_reset btn-danger col', id_shelf: current_id_shelf })
		.html('Volver')
		.appendTo('#MenuModalLaunch #test_in_hand');
});

$(document).on("click", ".btn.test_in_hand_response", function(e) {
	$(".btn.test_in_hand_response").switchClass( "btn-info", "btn-secondary" );
	$(this).switchClass( "btn-secondary", "btn-info" );
});

$(document).on("click", ".btn.test_in_hand_response_reset", function(e) {
	var id_shelf = $(this).attr('id_shelf');
	TestInHandReset(id_shelf);
});

function TestInHandReset(id_shelf) {
	DebugLog('TestInHandReset (id_shelf = '+ id_shelf +')');
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	var selected_model = logistics_models.find(modelo => modelo.logistic_id == shelf_data.logistic_id && modelo.manufacturer +'_'+ modelo.model == shelf_data.model_fields.model );
	// TODO: filtrar por customer_id
	var intranet_id = selected_model.intranet_model_id;
    tests_in_hand = GiveMeBarcodes(intranet_id, shelf_data);	
	var tests_in_hand_actions = tests_in_hand.filter(test => (test.priority == 0) || (test.priority == 1) );
	$(".btn.test_in_hand_response").switchClass( "btn-info", "btn-secondary" );
	$("#MenuModalLaunch #test_in_hand").empty();
	$.each(tests_in_hand_actions, function (index, test) {
		if ($("#MenuModalLaunch #test_in_hand button[value='"+ test.barcode +"']").length == 0) {
			var button = $("<button>")
				.attr({type: "button", class: 'btn test_in_hand_response btn-secondary', value: test.barcode, id_shelf: id_shelf })
				.html(test.operator_show)
				.appendTo('#MenuModalLaunch #test_in_hand');
			if (test.priority == 0) {
				button.switchClass( "btn-secondary", "btn-info" );
			}
		}
	});
	$("<button>")
		.attr({type: "button", class: 'btn test_in_hand_group btn-secondary', id_shelf: id_shelf })
		.html('FALLO')
		.appendTo('#MenuModalLaunch #test_in_hand');
	validator = $("#MenuModalLaunch #launch_form").validate({
		debug: false,
		success: "valid"
	});
}

function GiveMeBarcodes(intranet_id, shelf_data){	
	
	var selected_model = logistics_models.find(modelo => modelo.logistic_id == shelf_data.logistic_id && modelo.manufacturer +'_'+ modelo.model == shelf_data.model_fields.model );	
	var tests_in_hand = test_in_hand.filter(test => (test.model_id == selected_model.intranet_model_id || test.model_id == null));
	var technology_set = models_interfaces;	
	var intranet_technology_model = technology_set.filter(set => (set.intranet_model_id == selected_model.intranet_model_id));     
    //comprobar que existe json tecnologia en base de datos    
    var size = Object.keys(intranet_technology_model).length;    
    if(size != 0){   
    	var model_technology = JSON.parse(intranet_technology_model[0].interfaces);  
    	//Modificamos TECHNOLOGY en Json       	      	       	      	  
    	const new_technology = model_technology.TECHNOLOGY;              
    	delete model_technology.TECHNOLOGY;             
    	model_technology[new_technology] = '1'; 
    	//Rellenamos array interfaces    
    	var set_interfaces = [];   
    	for (var key in model_technology) {              	  
    		if (model_technology[key] > 0) {
    			set_interfaces.push(key);             
    		}    		
    	} 
    	//buscamos y eliminamos duplicados
    	const duplicate_search = tests_in_hand.reduce((acc, tests_in_hand) => {				
				acc[tests_in_hand.barcode] = ++acc[tests_in_hand.barcode] || 0;				
				return acc;
		}, {});		
		const duplicates = tests_in_hand.filter( filtro => {
				return duplicate_search[filtro.barcode];	   
	 	});	 	 	
	 	var to_remove = duplicates.filter(duplicado => duplicado.model_id == null);  
	 	var tests_in_hand_actions = [];
		for (i in tests_in_hand){			
			if(to_remove.includes(tests_in_hand[i])){
				//nothing	
			}else		     
			tests_in_hand_actions.push(tests_in_hand[i])		     
		}		
		//BARCODES ESPECIFICOS	 
		var set_barcodes = [];	 
		for(let i = 0; i < tests_in_hand_actions.length; i++) { 	 	
			if ((set_interfaces.includes (tests_in_hand_actions[i].interface)) ||  tests_in_hand_actions[i].interface == null || tests_in_hand_actions[i].model_id == intranet_id){ 
				set_barcodes.push(tests_in_hand_actions[i]);      
			} 
        } 
        
        return set_barcodes;        
    }else{  
    	return tests_in_hand;
    }    
}

