$(document).on("click", ".default_answer_all_button", function() {
    var current_rack = $(this).attr('rack');
    DebugInfo('Click en responder por defecto todas las preguntas del rack ' + current_rack);
    var rack_shelves = shelves.filter(bandeja => bandeja.rack == current_rack);
    var default_response = 'COD000';
    $.each(rack_shelves, function(index, shelf) {
        default_response = 'COD000';
        if (shelf.current_view == 'question') {
            if (shelf.hasOwnProperty('response_options')) {
                if (shelf.response_options.hasOwnProperty('default_value')) {
                    default_response = shelf.response_options.default_value;
                }
            }
            Response(shelf.id_shelf, default_response);
        }
	})
});

function UpdateVisibilityDefaultAnswerAllButtons() {
	DebugLog('UpdateVisibilityDefaultAnswerAllButtons');
    $.each(racks, function(index, rack) {
        if (shelves.filter(bandeja => bandeja.rack == rack && bandeja.current_view == 'question').length == 0) {
            $('.default_answer_all_button[rack='+ rack +']').hide();
        } else {
            $('.default_answer_all_button[rack='+ rack +']').show();
        }
    });
}