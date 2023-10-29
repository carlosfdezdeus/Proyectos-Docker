

var id_shef_wiring = 0;


var todas_incidencias = [
  {
    "id": 1,
    "incidencia": "Imposibilidad de lanzar"
   },
  {
    "id": 2,
    "incidencia": "Cable cambiado y sigue sin funcionar"
   },
	
    {
    "id": 3,
    "incidencia": "Bandeja no valida con diferentes errores"
   },
   
     {
    "id": 4,
    "incidencia": "Bandeja parada por falta de cableado"
   },
   
     {
    "id": 5,
    "incidencia": "Problema con la fuente"
   },
   
      {
    "id": 6,
    "incidencia": "Muere bandeja por SCRIPT DIE"
   },
   
   
      {
    "id": 7,
    "incidencia": "INCIDENCIA SIN DEFINIR"
   }]
   



$(document).on("click", ".warning", function() {
	var current_id_shelf = $(this).attr('id_shelf');
	id_shelf_wiring = current_id_shelf;
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == current_id_shelf);
	
	$('#MenuModalLanzadas').modal('show');
	
	$('#MenuModalLanzadas #titulo').val("");
	$('#MenuModalLanzadas #titulo').empty();
	$('#MenuModalLanzadas #titulo').append(shelf_data.warning.info_title);
	$('#MenuModalLanzadas #info_text').val("");
	$('#MenuModalLanzadas #info_text').empty();
	$('#MenuModalLanzadas #info_text').append(shelf_data.warning.info_text);
	
	$('#MenuModalLanzadas #datos_bandeja').val("");
	$('#MenuModalLanzadas #datos_bandeja').empty();
	$('#MenuModalLanzadas #datos_bandeja').append("<b><h5>Configuracion bandeja</h5></b>");
	$('#MenuModalLanzadas #datos_bandeja').append("<b>" + shelf_data.environment_name + "</b>");
	$('#MenuModalLanzadas #datos_bandeja').append("<b>&nbsp;" + shelf_data.shelf + "</b>");
	
	
	$('body').find('[data-toggle="popover"]').popover('hide');      
	
	
	
	$(document).on('click', '#btn_ticket_popover', function(){	
				
			$("[data-toggle=popover]").popover('hide');
			$('#MenuModalLanzadas').modal('hide');
			$('#MenuModalTicket #incidencia_escogida').val("");
			$('#MenuModalTicket #incidencia_escogida').empty();
			$('#MenuModalTicket #afectacion_escogida').val("");
			$('#MenuModalTicket #afectacion_escogida').empty();
		    $('#MenuModalTicket #afectacion').hide();
		    $("#MenuModalTicket #btn_enviar_ticket").hide();
		    $('#MenuModalTicket .box1').css("background-color", "#FFFFFF");
		    $('#MenuModalTicket .box2').css("background-color", "#FFFFFF");			
		
			CrearBotonesIncidencia(current_id_shelf);	
			
			//Es necesario el empty para borrar los valores visibles de la ventana modal
	         $('#MenuModalTicket #entorno_ticket').empty();
	         $('#MenuModalTicket #bandeja_ticket').empty();	      
			 $('#MenuModalTicket #modelo_ticket').empty();
			 $('#MenuModalTicket #logistica_ticket').empty();
			 $('#MenuModalTicket #usuario_ticket').empty();
			 
			 //Es necesario el empty para borrar los valores invisibles de la ventana modal
			 $('#MenuModalTicket #entorno_ticket_oculto').empty();
			 $('#MenuModalTicket #bandeja_ticket_oculto').empty();
			 $('#MenuModalTicket #id_bandeja_ticket_oculto').empty();
			 $('#MenuModalTicket #modelo_ticket_oculto').empty();
			 $('#MenuModalTicket #logistica_ticket_oculto').empty();
			 $('#MenuModalTicket #usuario_ticket_oculto').empty();
			 $('#MenuModalTicket #lanzadas_ticket_oculto').empty();	
			 
			$("[data-toggle=popover]").popover('hide');
			
			//primero limpiamos campos de la ventana modal
			$('#MenuModalTicket #entorno_ticket').val("");
			$('#MenuModalTicket #bandeja_ticket').val("");
			$('#MenuModalTicket #modelo_ticket').val("");
			$('#MenuModalTicket #logistica_ticket').val("");
			$('#MenuModalTicket #usuario_ticket').val("");
			//$('#MenuModalTicket #lanzadas_ticket').val("");
			
			//añadimos los campos con los datos de la bandeja
			$('#MenuModalTicket #entorno_ticket').append("<b>Entorno: </b>&nbsp;&nbsp;&nbsp;" + shelf_data.environment_name);
			$('#MenuModalTicket #bandeja_ticket').append("<b>Bandeja: </b>&nbsp;&nbsp;&nbsp;" + shelf_data.shelf);
			$('#MenuModalTicket #modelo_ticket').append("<b>Modelo: </b>&nbsp;&nbsp;&nbsp;" + shelf_data.model_fields.model + ' ' + '<br />');
			$('#MenuModalTicket #logistica_ticket').append("<b>Logistica: </b>&nbsp;" + shelf_data.logistic_name + '<br />');
			$('#MenuModalTicket #usuario_ticket').append("<b>Usuario: </b>&nbsp;&nbsp;&nbsp;" + user_id + ' ' + user_name + '<br />');
			//$('#MenuModalTicket #lanzadas_ticket').append(shelf_data.warning.info_text + '<br />');
			
			//primero limpiamos campos ocultos de la ventana modal
			$('#MenuModalTicket #entorno_ticket_oculto').val("");
			$('#MenuModalTicket #bandeja_ticket_oculto').val("");
			$('#MenuModalTicket #id_bandeja_ticket_oculto').empty();
			$('#MenuModalTicket #modelo_ticket_oculto').val("");
			$('#MenuModalTicket #logistica_ticket_oculto').val("");
			$('#MenuModalTicket #usuario_ticket_oculto').val("");
			$('#MenuModalTicket #lanzadas_ticket_oculto').val("");
			
			
			//añadimos los campos ocultos con los datos de la bandeja
			$('#MenuModalTicket #entorno_ticket_oculto').append(shelf_data.environment_name);
			$('#MenuModalTicket #bandeja_ticket_oculto').append(shelf_data.shelf);
			$('#MenuModalTicket #id_bandeja_ticket_oculto').append(current_id_shelf);
			$('#MenuModalTicket #modelo_ticket_oculto').append(shelf_data.model_fields.model);
			$('#MenuModalTicket #logistica_ticket_oculto').append(shelf_data.logistic_name);
			$('#MenuModalTicket #usuario_ticket_oculto').append(user_name);
			$('#MenuModalTicket #lanzadas_ticket_oculto').append(shelf_data.warning.info_text);
			
			
			$('#MenuModalTicket').modal('show');
			
	});
	
	
	$(document).on('click', '#btn_change_wiring', function(){
	
	$('#MenuModalCables #cables').empty();
	$('#MenuModalCables #selected_cables').empty();
	$('#MenuModalCables #requested_cables').empty();   
    
    $("[data-toggle=popover]").popover('hide');
	$('#MenuModalLanzadas').modal('hide');
	$('#MenuModalCables #cables').val("");
	$('#MenuModalCables #cables').empty();
	$('#MenuModalCables #selected_cables').val("");
	$('#MenuModalCables #selected_cables').empty();
	$('#MenuModalCables #requested_cables').val("");
	$('#MenuModalCables #requested_cables').empty();
	ComCableChange(current_id_shelf);		
	});
	
	$(document).on('click', '#btn_cerrar_warning', function(){
	$("[data-toggle=popover]").popover('hide');
	});
	
	
});


function CrearBotonesIncidencia(id_shelf) {		
	
	var shelf_data = shelves.find(bandeja => bandeja.id_shelf == id_shelf);	
	$("#MenuModalTicket #botones_incidencia").empty();
	$.each(todas_incidencias, function (index, incidencia) {
		if ($("#MenuModalTicket#botones_incidencia button[value='"+ incidencia.id +"']").length == 0) {
			var button = $("<button>")
				.attr({type: "button", class: 'btn btn_incidencia btn-primary', value: incidencia.id, id_shelf: id_shelf })
				.html(incidencia.incidencia)
				.appendTo('#MenuModalTicket #botones_incidencia');	
			}
				
		$("<br>")
		.appendTo('#MenuModalTicket #botones_incidencia');		
		
	});
}


$(document).on("click", "#MenuModalTicket .btn_incidencia", function() {
		
	$('#MenuModalTicket #incidencia_escogida').val("");
	$('#MenuModalTicket #incidencia_escogida').empty();
	$('#MenuModalTicket .box1').css("background-color", "#CFC");
		
	var boton_seleccionado = $(this).attr('value');
	
	$.each(todas_incidencias, function (index, incidencia) {
		if (boton_seleccionado == incidencia.id) {			
					$('#MenuModalTicket #incidencia_escogida').append(incidencia.incidencia);
			}		
		
	});
	
	$("#MenuModalTicket #botones_incidencia").empty();
	$("#MenuModalTicket #afectacion").show();

	
	
});


$(document).on("click", "#MenuModalTicket .btn_afectacion", function() {
		
	$('#MenuModalTicket #afectacion_escogida').val("");
	$('#MenuModalTicket #afectacion_escogida').empty();
	$('#MenuModalTicket .box2').css("background-color", "#CFC");
		
	var boton_seleccionado = $(this).attr('value');
	
	$('#MenuModalTicket #afectacion_escogida').append(boton_seleccionado);
	
	$("#MenuModalTicket #botones_incidencia").empty();
	$("#MenuModalTicket #afectacion").hide();
    $("#MenuModalTicket #btn_enviar_ticket").show();
	
});

function ComCableChange(id_shelf) {		
	
	var shelf_data = shelves.find(cable_shelf => cable_shelf.id_shelf == id_shelf);	
	
	$('#MenuModalLanzadas').modal('hide');	
	$('#MenuModalCables #selected_cables').val("");
	$('#MenuModalCables #selected_cables').empty();
	$('#MenuModalCables #requested_cables').val("");
	$('#MenuModalCables #requested_cables').empty();
  
	var all_cables = shelf_plates;		
	
  if(shelf_data.id_shelf_interfaces_type == null) {  	
  	  alert("ESTA BANDEJA NO TIENE CABLES. LLAMAR A SISTEMAS");
  	}
  $('#MenuModalLanzadas').modal('hide');
  $('#MenuModalCables').modal('show'); 
  var shelf_cables = all_cables.filter(cables => (cables.id == shelf_data.id_shelf_interfaces_type)); 
  var json_cables_shelf = JSON.parse(shelf_cables[0].json_interfaces);
  var set_cables = [];   
    	for (var key in json_cables_shelf) {              	  
    		if (json_cables_shelf[key] > 0) {
    			set_cables.push(key);             
    		}    		
    	} 
    
  
  $.each(set_cables, function (index, cable) {		
			var button = $("<button>")
				.attr({type: "button", class: 'btn btn-primery btn-cables no_seleccionado', value: cable, id_shelf: id_shelf})
				.html("<img src=../img/cables/" + cable + ".jpg  height=100 width=150 />")	
				.appendTo('#MenuModalCables #cables');
  });
  
  $("<br>")
  		.appendTo('#MenuModalCables #cables');
  		
  $("#cable_shelf_number").html("<b>Bandeja: </b>" + " " + shelf_data.shelf);
  $("#cable_environment_name").html("<b>Entorno: </b>" + " " + shelf_data.environment_name);
  $("#cable_user_number").html("<b>Usuario: </b>" + " " + user_name);
  
  $("#btn_confirm_cables").hide();
  $("#btn_send_cables").show();  

 $(document).on("click", "#btn_send_cables", function(e) { 
  var contador = 0;  
  	
	$('#MenuModalCables #selected_cables').val("");
	$('#MenuModalCables #selected_cables').empty();
	
  
  $.each($("#MenuModalCables .btn.btn-cables.seleccionado"), function () {
  		$('#MenuModalCables #selected_cables').append("<b>" + $(this).val() + "</b>");
  		$('#MenuModalCables #selected_cables').append("<br>");
  		$(this).prop("disabled", true).css('opacity',1);
  		contador += 1;  		
  });	
  
 
  
  if (contador == 0) {  	  
  	 alert("NADA SELECCIONADO");	  
  }else{  	  
  	  $.each($("#MenuModalCables .btn.btn-cables.no_seleccionado"), function () {
  			$(this).remove();  
			$("#btn_send_cables").hide();
			$("#btn_confirm_cables").show();			
  });	
  	  
  	  $("#btn_send_cables").hide();
	  $("#btn_confirm_cables").show();
  }
 
});


 $(document).on("click", "#btn_confirm_cables", function(e) { 
	e.stopImmediatePropagation();	
	var array_cambios = [];
  
	 $.each($("#MenuModalCables .btn.btn-cables.seleccionado"), function () {  		
  		array_cambios.push($(this).val());
  	 });	
  	 
  	$('#MenuModalCables #requested_cables').val("");
	$('#MenuModalCables #requested_cables').empty();
	$('#MenuModalCables #requested_cables').append("<br>");
	$('#MenuModalCables #requested_cables').append("<p style='color:blue'><b>CAMBIO DE CABLEADO NOTIFICADO</b></p><br>");	
	$("#btn_confirm_cables").hide();
	
	var id_cables_shelf = id_shelf_wiring;  
    var shelf = shelf_data.shelf;  
   
    var json_changes =  JSON.stringify(array_cambios); 
	
	var changed_cables = {'id_cables_shelf':id_cables_shelf,		             
						'json_changes':json_changes};
	
	
	InsertChanges(changed_cables);
	
	
});
 

	
}


function InsertChanges(changed_cables) {	
 	 
			var id_selected_shelf = changed_cables.id_cables_shelf;
			var json_changes = changed_cables.json_changes;
			var operario = user_id;		
							
			$.post('http://new-validator.turyelectro.com/check_wiring.php', {
              "var_1": id_selected_shelf,
              "var_2": json_changes,
              "var_3": operario
            },function(data) {
              console.log('procesamiento finalizado', data);
            });
                      
 }
 
 $(document).on("click", ".btn.btn-cables", function(e) { 
   
	var id_shelf = $(this).attr('id_shelf');
	var type_cable = $(this).attr('value');	
	
	$(this).html("<img src=../img/cables/" + type_cable + ".jpg  height=100 width=150 />");
	$(this).toggleClass('no_seleccionado');
	$(this).toggleClass('seleccionado');
   
    if ($(this).hasClass("seleccionado")) {
    	$(this).html("<img src=../img/cables/" + type_cable + "_seleccionado.jpg  height=100 width=150 />");      
    }else{
    	$(this).html("<img src=../img/cables/" + type_cable + ".jpg  height=100 width=150 />");    	
    }
	
  });

 