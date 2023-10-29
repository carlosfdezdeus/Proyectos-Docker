$(document).on("hidden.bs.modal", "#MenuModalSelect", function () {
    RefreshShelves();
});

$(document).on("click", "button[option_type=logistics]", function() {
	var manufacturers = logistics_models.filter(modelo => modelo.logistic_id == $(this).val());
	selected_logistic_id = $(this).val();

	manufacturers = ArrayCombine(ArrayColumn(manufacturers, 'manufacturer'),ArrayColumn(manufacturers, 'manufacturer'));
	$('#MenuModalSelect #titulo').html('Seleccionar Fabricante');
	$.templates("#modal_select_form").link("#MenuModalSelect #modal_options", {'option_type':'manufacturers', 'options': manufacturers});
	if (manufacturers.length == 1) {
		$('#MenuModalSelect button[value="'+ manufacturers[0] +'"]').trigger('click');
	}
});

$(document).on("click", "button[option_type=manufacturers]", function() {
	var models = logistics_models.filter(modelo => modelo.logistic_id == selected_logistic_id && modelo.manufacturer == $(this).val());
    //models = $.unique(ArrayColumn(models, 'model')).sort();
    models = ArrayCombine(ArrayColumn(models, 'model_with_customer_info'),ArrayColumn(models, 'model_id'));
	$('#MenuModalSelect #titulo').html('Seleccionar Modelo de '+ $(this).val());
	$.templates("#modal_select_form").link("#MenuModalSelect #modal_options", {'option_type':'models', 'options': models});
	if (models.length == 1) {
		$('#MenuModalSelect button[value="'+ models[0] +'"]').trigger('click');
	}
});

$(document).on("click", "button[option_type=models]", function() {
	var selected_model_id = $(this).val();
	var selected_model = logistics_models.find(modelo => modelo.model_id == selected_model_id);
	DebugLog(selected_model);
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	DebugLog("Actualizando modelo de bandeja "+ shelf_data.id_shelf );
	$('#MenuModalSelect').modal('hide');
	// Comprobamos si la bandeja pertenece a un grupo
	group_shelves = shelves.filter(bandeja => (bandeja.id_shelf == shelf_data.id_shelf || bandeja.shelf_group != null) && bandeja.shelf_group == shelf_data.shelf_group);
	if (group_shelves.length > 1) {
		group_shelves_str = $.unique(ArrayColumn(shelves.filter(bandeja => bandeja.shelf_group == shelf_data.shelf_group), 'shelf')).sort();
		if (group_shelves.length) {
			msg = "El cambio de modelo se extender&aacute; a las bandejas:";
			msg += "<h2>" + group_shelves_str.join() + "</h2>";
			msg += "(Se detendr&aacute;n los scripts en estas bandejas)";
			$( "#dialog-message #msg" ).html(msg);
			$( "#dialog-message" ).dialog({
				resizable: true,
				height: 300,
				width: 550,
				modal: true,
				title: "Cambio de modelo en bloque",
				buttons: {
					"Cambiar": function() {
						$( this ).dialog( "close" );
						$.each(group_shelves, function(index, shelf) {
                            group_shelves[index].model_fields.model_id = selected_model_id;
							group_shelves[index].model_fields.model = selected_model.manufacturer +'_'+ selected_model.model;
                            group_shelves[index].model_fields.customer_info = selected_model.customer_info;
							group_shelves[index].logistic_id = selected_model.logistic_id;
							group_shelves[index].logistic_name = selected_model.logistic;
						});
						UpdateGroup(shelf_data.id_shelf);
					},
					"Cancelar": function() {
						$( this ).dialog( "close" );
						return;
					}
				}
			})
		}
	} else {
		shelf_data.model_fields.model_id = selected_model_id;
		shelf_data.model_fields.model = selected_model.manufacturer +'_'+ selected_model.model;
		shelf_data.model_fields.customer_info = selected_model.customer_info;
		shelf_data.logistic_id = selected_model.logistic_id;
		shelf_data.logistic_name = selected_model.logistic;
		shelf_data.launch_with_data = selected_model.launch_with_data;
		shelf_data.launch_without_data = selected_model.launch_without_data;
		SaveShelf(shelf_data.id_shelf);
        UpdateShelf(shelf_data.id_shelf);
	}
});