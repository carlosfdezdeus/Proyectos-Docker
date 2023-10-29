


 $(document).on("click", "#MenuModalTicket .btn#btn_enviar_ticket", function() {
 		 
 		 var datos_entorno = $("#entorno_ticket_oculto");
 		 var datos_bandeja = $("#bandeja_ticket_oculto");
 		 var datos_id_bandeja = $("#id_bandeja_ticket_oculto");
 		 var datos_modelo = $("#modelo_ticket_oculto");
 		 var datos_logistica = $("#logistica_ticket_oculto");
 		 var datos_lanzadas = $("#lanzadas_ticket_oculto"); 
 		 var datos_incidencia = $("#incidencia_escogida");
 		 var datos_afectacion = $("#afectacion_escogida");
 		 
 		 var entorno_json = datos_entorno[0].innerText;
 		 var bandeja_json = datos_bandeja[0].innerText;
 		 var id_bandeja_json = datos_id_bandeja[0].innerText;
 		 var modelo_json = datos_modelo[0].innerText;
 		 var logistica_json = datos_logistica[0].innerText;
 		 var nombre_usuario_json = user_name;
 		 var id_usuario_json = user_id;
 		 var incidencia_json = datos_incidencia[0].innerText;
 		 var afectacion_json = datos_afectacion[0].innerText;
 		 var ultimas_lanzadas = datos_lanzadas[0].innerHTML; 
         
         var body = {entorno_json, bandeja_json, id_bandeja_json, modelo_json, logistica_json, nombre_usuario_json, id_usuario_json, incidencia_json, afectacion_json, ultimas_lanzadas};
         ticket_payload = JSON.stringify({body});
         
 		 //ws_server = '192.168.100.99';
         //port = '9313';
         
         socket_ticket = new FancyWebSocket(ws_server, ws_ticket_port);     
         socket_ticket.bind('open', function( data ) {	
         socket_ticket.send(ticket_payload);           
         socket_ticket.recibir_ticket(); 
         });
  
 		 //Es necesario el empty para borrar los valores visibles de la ventana modal
	     $('#MenuModalTicket #entorno_ticket').empty();
	     $('#MenuModalTicket #bandeja_ticket').empty();
	     $('#MenuModalTicket #modelo_ticket').empty();
		 $('#MenuModalTicket #logistica_ticket').empty();
		 $('#MenuModalTicket #usuario_ticket').empty();
			 
		  //Es necesario el empty para borrar el valor oculto del id bandeja
		 $('#MenuModalTicket #id_bandeja_ticket_oculto').empty();
			 
			 
		 $('#MenuModalTicket #entorno_ticket').append("<h1> TICKET ENVIADO </h1>");
		 $('#MenuModalTicket #bandeja_ticket').empty()
		 $('#MenuModalTicket #bandeja_ticket').append("<h3>" + "... por favor, espere  ..." + "</h3>");
			 
		 $("#MenuModalTicket .btn#btn_enviar_ticket").hide();
 		 
 		 
 });	
 	