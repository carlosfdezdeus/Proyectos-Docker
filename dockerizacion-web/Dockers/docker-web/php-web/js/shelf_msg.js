$(document).on("click", ".row .msg", function() {
	var current_id_shelf = $(this).attr('id_shelf');
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	DebugInfo('Click en msg de bandeja ' + current_id_shelf);
	shelf_data.s_from_launch == 1;
	if (shelf_data.s_from_launch == null) {
		shelf_data.current_view = "launcher";
	}
	RefreshShelf(current_id_shelf);
    $('.launch_button[id_shelf='+ current_id_shelf +']').trigger('click');
});