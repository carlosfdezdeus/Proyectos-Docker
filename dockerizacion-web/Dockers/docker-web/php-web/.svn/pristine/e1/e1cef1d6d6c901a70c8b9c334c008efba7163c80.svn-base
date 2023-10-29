$(document).on("keydown", "input.input_response", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	var code = e.which;
	if (code == 13) {
		e.preventDefault();
		Response(current_id_shelf, $(this).val());
		$(this).val('');
	}
});

$(document).on("click", ".btn.send_input_response", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	var selected_option = $.trim($('input.input_response[id_shelf='+ current_id_shelf +']').val());
	if (selected_option != '') {
		Response(current_id_shelf, selected_option);
		$('input.input_response[id_shelf='+ current_id_shelf +']').val('');
	}
});

$(document).on("click", ".btn.response", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	var button_text = $(this).text();
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	var response_value = shelf_data.responses[button_text];
	if (typeof response_value==='object' && response_value!==null && !(response_value instanceof Array) && !(response_value instanceof Date)) {
		DebugInfo("La opcion seleccionada es una categoria, cargando sus elementos...");
		shelf_data.responses = response_value;
		buttons_div = $(this).parent('div.form-group');
		buttons_div.empty();
		$.each(response_value, function(index, response) {
			$("<button>")
				.attr({type: "button", class: 'btn response btn-secondary', value: response, id_shelf: current_id_shelf })
				.html(index)
				.appendTo(buttons_div);
		})
		$("<button>")
			.attr({type: "button", class: 'btn response_reset btn-danger', id_shelf: current_id_shelf })
			.html('Volver')
			.appendTo(buttons_div);
		return;
	}
	Response(current_id_shelf, shelf_data.responses[button_text]);
});

$(document).on("click", ".btn.response_reset", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	shelf_data.responses = shelf_data.responses_initial;
	RefreshShelf(current_id_shelf);
});


$(document).on("click", ".btn.send_select_response", function(e) {
	var current_id_shelf = $(this).attr("id_shelf");
	var selected_option = $('.select_response[id_shelf='+ current_id_shelf +']').val();
	Response(current_id_shelf, selected_option);
});

function StyleButtons(id_shelf) {
	DebugLog('StyleButtons');
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	if (!shelf_data.response_options) {
		return;
	}
	$.each(shelf_data.response_options, function (field, value) {
		if (value.style) {
			if ($(".btn.response[id_shelf="+ id_shelf +"][value='"+ field +"']").length != 0) {
				$(".btn.response[id_shelf="+ id_shelf +"][value='"+ field +"']").attr('style',value.style);
			}
		}
	})
}