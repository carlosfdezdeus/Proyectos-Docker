function BootstrapDisableSanitizer() {
	DebugLog('BootstrapDisableSanitizer');
	$.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    $.fn.popover.Constructor.Default.whiteList.div = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
	$.fn.popover.Constructor.Default.whiteList.thead = [];
}

dic_process = {};

function RefreshShelf(id_shelf) {
	DebugLog('RefreshShelf (id_shelf = '+ id_shelf +')');
	BootstrapDisableSanitizer();
	var shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	if ($('.card[id_shelf='+ id_shelf +']').length == 0) {
		DebugInfo('Refresh anulado por bandeja no visible');
		return;
	}
	if (serverList[shelf.ws_port].connected == false) {
		shelf.current_view = 'msg';
		shelf.msg = '<h3>Se ha perdido la conexi&oacute;n con el servidor.<br>('+ shelf.ws_port +') </h3>';
		if(user_admin_status){
			shelf.msg += "<button class='btn btn-info lanzador_puertos' id_shelf='";
			shelf.msg += id_shelf;
			shelf.msg += "' port='";
			shelf.msg += shelf.ws_port;
			shelf.msg += "'>Pulsa aqu&iacute; para lanzar puerto</button>";
		}
		shelf.shelf_color = 'red';
		tiempo_espera = (dic_process[id_shelf] == undefined) ? 5000 : 60000;
		window.timer = setTimeout(() => {
			Launch_port(shelf.ws_port,id_shelf);
		}, tiempo_espera);
		dic_process[id_shelf] = window.timer;
	}
	$('.card[id_shelf='+ id_shelf +']').html($.templates("#shelf_template").render(shelf));
	$("[data-toggle=popover]").popover();
	$("[data-toggle=popover]").on('inserted.bs.popover', function(){
		// Format Tables in popover
		$(".popover-body table:not([class*='table'])").addClass("table");
		$(".popover-body table:not([class*='table-bordered'])").addClass("table-bordered");
		$(".popover-body table:not([class*='table-striped'])").addClass("table-striped");
	});
	$.each($(".last_launch_info"), function() {
		$(this).css('color', $(this).attr('text_color'))
	});
	$.each($(".warning"), function() {
		$(this).css('color', $(this).attr('text_color'))
	});
	$(".dragscroll").attachDragger();
	StyleButtons(id_shelf);
	if ($.isFunction(window.ShowWSports)) {
        ShowWSports();
	}
	if (shelves.length == 1) {
		$('.input_response').select();
	}
};

function RefreshShelves() {
	DebugLog('RefreshShelves');
	BootstrapDisableSanitizer();
	DebugInfo('Refresh de todas las bandejas.');
	racks = $.unique(ArrayColumn(shelves, 'rack')).sort();
	$.each(racks, function(index, rack) {
		shelves_rack = shelves.filter(function (shelf) {
			return shelf.rack == rack
		});
		$.templates("#shelf_template").link("#panel_rack_"+ rack, shelves_rack);
	});
	$("[data-toggle=popover]").popover();
	$("[data-toggle=popover]").on('inserted.bs.popover', function(){
		// Format Tables in popover
		$(".popover-body table:not([class*='table'])").addClass("table");
		$(".popover-body table:not([class*='table-bordered'])").addClass("table-bordered");
		$(".popover-body table:not([class*='table-striped'])").addClass("table-striped");
	});
	$.each($(".last_launch_info"), function() {
		$(this).css('color', $(this).attr('text_color'))
	});
	$.each($(".warning"), function() {
		$(this).css('color', $(this).attr('text_color'))
	});
	$(".dragscroll").attachDragger();
	$.each(shelves, function(index, shelf) {
		StyleButtons(shelf.id_shelf);
	})
	if ($.isFunction(window.ShowWSports)) {
        ShowWSports();
    }
	if (shelves.length == 1) {
		$('.input_response').select();
	}
};

function SaveShelf(id_shelf) {
	DebugLog('SaveShelf (id_shelf = '+ id_shelf +')');
	// Guardamos el estado del DOM en la DB para restaurar al recargar la Web
	var selected_shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	var shelf_old_index = shelves_old.findIndex(bandeja => bandeja.id_shelf == id_shelf);
	shelf_info = Object.assign({}, selected_shelf);
	shelf_info.log = (shelf_info.log) ? shelf_info.log.split('<br>')[0] : '';
	$("[id$='-"+ id_shelf +"'].shelf_data").toggleClass("loading");
	$.post(
		"index_json.php",
		{
			updateDB: id_shelf,
			shelves_data: {shelf_info},
		},
		function(data) {
			DebugLog(data);
			$("[id$='-"+ id_shelf +"'].shelf_data").toggleClass("loading");
			shelves_old[shelf_old_index] = Object.assign({}, shelf_info);
			RefreshShelf(id_shelf);
		}
	)
	.fail(function() {
		selected_shelf.shelf_color = 'red';
		RefreshShelf(id_shelf);
	})
}

function UpdateShelf(id_shelf) {
	DebugLog('UpdateShelf (id_shelf = '+ id_shelf +')');
	var selected_shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	var shelf_old_index = shelves_old.findIndex(bandeja => bandeja.id_shelf == id_shelf);
	shelves_old[shelf_old_index] = Object.assign({}, selected_shelf);
	var payload = JSON.stringify({id_user: user_id, id_shelf: id_shelf, event:"shelf_update", data: selected_shelf});
	serverList[selected_shelf.ws_port].socket.send( payload );
}

function UpdateGroup(id_shelf) {
	DebugLog('UpdateGroup (id_shelf = '+ id_shelf +')');
	// Guardamos el estado del DOM en la DB para restaurar al recargar la Web
	var selected_shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	// Si la bandeja tiene grupo guardamos todas las del grupo
	var selected_group_shelves = shelves.filter(bandeja => ((bandeja.id_shelf == selected_shelf.id_shelf || bandeja.shelf_group != null) && bandeja.shelf_group == selected_shelf.shelf_group));
	selected_group_shelves_info = Object.assign({}, selected_group_shelves);
	$.each(selected_group_shelves_info, function(index, shelf) {
		$("[id$='-"+ selected_group_shelves[index].id_shelf +"'].shelf_data").toggleClass("loading");
		selected_group_shelves_info.log = shelf_info.log.split('<br>')[0];
	});
	$.post(
		"index_json.php",
		{
			updateDB: id_shelf,
			shelves_data: selected_group_shelves_info,
		},
		function(data) {
			DebugLog(data);
			$.each(selected_group_shelves, function(index, shelf) {
				$("[id$='-"+ shelf.id_shelf +"'].shelf_data").toggleClass("loading");
				UpdateShelf(shelf.id_shelf);
				RefreshShelf(shelf.id_shelf);
			});
		}
	)
	.fail(function() {
		$.each(selected_group_shelves, function(index, shelf) {
			selected_group_shelves[index].shelf_color = 'red';
			RefreshShelf(selected_group_shelves[index]);
		});
	})
}

function Launch_port (port, id_shelf){
	DebugLog('Launch_port (port = '+ port +' / id_shelf = '+ id_shelf +')');
	var shelf_old = shelves_old.find(bandeja => bandeja.id_shelf == id_shelf );
	var shelf = shelves.find(bandeja => bandeja.id_shelf == id_shelf);
	var shelf_index = shelves.findIndex(bandeja => bandeja.id_shelf == id_shelf);
	$.get("/ws_server/gestion_server_procesos.php", {server_port: port, estado: 1, html:0}, function(respuesta){
		if (!respuesta.includes('Error')) {
			Connect2WSServer(ws_server, port);
			serverList[port].connected = true;
			shelves[shelf_index] = Object.assign({}, shelf_old);
			delete (dic_process[id_shelf]);
		}
		RefreshShelf(id_shelf);
	})
	.fail(function() {
		alert( "Error no se ha podido lanzar el puerto " );
	})
}