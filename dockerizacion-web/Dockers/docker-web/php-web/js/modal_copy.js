$(document).on("hidden.bs.modal", "#MenuModalCopy", function () {
    RefreshShelves();
});

$(document).on("click", "#MenuModalCopy .btn:not(.btn-copy)", function() {
    if ($(this).attr("id") == 'all_button-modal') {
        if ($(this).hasClass("btn-success")) {
            $(this).switchClass("btn-success", "btn-primary");
        } else {
            $(this).switchClass("btn-primary", "btn-success");
        }
    } else {
        if ($(this).hasClass("btn-success")) {
            $(this).switchClass("btn-success", "btn-default");
        } else {
            $(this).switchClass("btn-default", "btn-success");
        }
    }

    if ($(this).hasClass("btn_shelf")) {
        var id_shelf = $(this).attr("id_shelf");
        var shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
		var shelves_group = [];
		if (shelf.shelf_group != null) {
			shelves_group = shelves.filter(bandeja => bandeja.shelf_group == shelf.shelf_group && bandeja.id_shelf != id_shelf);
			$.each(shelves_group, function(index, shelf) {
                shelf_button = $('#MenuModalCopy .btn_shelf[id_shelf="'+ shelf.id_shelf +'"]');
				if (shelf_button.hasClass("btn-success")) {
					shelf_button.switchClass("btn-success", "btn-default");
				} else {
					shelf_button.switchClass("btn-default", "btn-success");
				}
			})
		}
    }

    if ($(this).hasClass("btn_rack")) {
        var rack = $(this).attr("rack");
        rack_shelves = shelves.filter(bandeja => bandeja.rack == rack);
        rack_shelves_ids = ArrayColumn(rack_shelves, 'id_shelf');
        if ($(this).hasClass("btn-success")) {
            $.each(rack_shelves_ids, function(index, id_shelf) {
                $('#MenuModalCopy .btn_shelf[id_shelf="'+ id_shelf +'"]').switchClass("btn-default", "btn-success");
            });
        } else {
            $.each(rack_shelves_ids, function(index, id_shelf) {
                $('#MenuModalCopy .btn_shelf[id_shelf="'+ id_shelf +'"]').switchClass("btn-success", "btn-default");
            });
        }
	}

    if ($(this).attr("id") == 'all_button-modal') {
        if ($(this).hasClass("btn-primary")) {
            $("#MenuModalCopy .btn.btn_shelf").switchClass("btn-success", "btn-default");
        } else {
            $("#MenuModalCopy .btn.btn_shelf").switchClass("btn-default", "btn-success");
        }
    }

    $.each(racks, function(index, rack) {
		if ( $('#MenuModalCopy #rack_'+ rack +' .btn.btn_shelf.btn-default').length > 0 ) {
            $('#MenuModalCopy .btn_rack[rack="'+ rack +'"]').switchClass("btn-success", "btn-default");
        } else {
            $('#MenuModalCopy .btn_rack[rack="'+ rack +'"]').switchClass("btn-default", "btn-success");
        }
    })

    if ( $("#MenuModalCopy .btn_rack.btn-default").length > 0 ) {
        $("#MenuModalCopy #all_button-modal").switchClass("btn-success", "btn-primary");
    } else {
        $("#MenuModalCopy #all_button-modal").switchClass("btn-primary", "btn-success");
	}

})

$(document).on("click", "#MenuModalCopy .btn#btn-copy", function() {
    var selected_shelf_buttons = $("#MenuModalCopy .btn_shelf.btn-success");
    var model_id = $('#MenuModalCopy #model_button-modal').attr('model_id');
    var selected_model = logistics_models.find(modelo => modelo.model_id == model_id);
	$.each(selected_shelf_buttons, function(index, button) {
		var id_shelf = $(this).attr("id_shelf");
        var shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
		if ($("#MenuModalCopy #model_button-modal").hasClass("btn-success")) {
            shelf.model_fields.customer_info = selected_model.customer_info;
            shelf.model_fields.model_id = selected_model.model_id;
            shelf.model_fields.model = selected_model.manufacturer +'_'+ selected_model.model;
            shelf.model_fields.model_with_customer_info = selected_model.model_with_customer_info;
            shelf.logistic_id = selected_model.logistic_id;
            shelf.logistic_name = selected_model.logistic;
		}
		if ($("#MenuModalCopy #invoice_button-modal").hasClass("btn-success")) {
			shelf.invoice = $("#MenuModalCopy #invoice_button-modal").attr('invoice');
        }
        if (serverList[shelf.ws_port].connected) {
            SaveShelf(shelf.id_shelf);
            UpdateShelf(shelf.id_shelf);
        }
	})
	RefreshShelves();
	$( '#MenuModalCopy' ).modal( "hide" );
})