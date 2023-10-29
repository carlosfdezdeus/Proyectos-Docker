


var campanaWebSocket = function(ws_server, ws_msg_port) {	
	var callbacks = {};
	var ws_url = 'ws://' + ws_server +':'+ ws_msg_port;	
	this.ws_server = ws_server;
	this.port = ws_msg_port;
	var conn_campana;	

	if ( typeof(MozWebSocket) == 'function' ) {		
		conn_campana = new MozWebSocket(ws_url);
	} else {
		conn_campana = new WebSocket(ws_url);
	}	
	
	conn_campana.onmessage = function(event){
		
		try {
			response = $.parseJSON(event.data);	
			var operario_msg = JSON.stringify(response['json']['json_info']['operario']);
			var entorno_msg = JSON.stringify(response['json']['json_info']['entorno']);
			var bandeja_msg = JSON.stringify(response['json']['json_info']['bandeja']);
			var mensaje_msg = JSON.stringify(response['json']['json_info']['texto']);	
			var id_entorno = JSON.parse(entorno_msg);
			var id_bandeja = JSON.parse(bandeja_msg);
			var id_operario = JSON.parse(operario_msg);
			//var mensaje_user = {'usuario' : id_operario, 'mensaje' : mensaje_msg};
			
			if('"'+user_id + '"' == operario_msg){
				var mensaje_user = {'usuario' : user_id, 'mensaje' : mensaje_msg};		    
				array_msg_users.push(mensaje_user);							
				document.getElementById("campana_button").style.visibility = "visible";
				$("#campana_button").show();
			};
		    
			if((id_bandeja)&&(!mensaje_msg.includes('oculta'))) { 
				var mensaje_bandeja ={'bandeja' : id_bandeja,'mensaje': mensaje_msg};		  
				array_msg_bandejas.push(mensaje_bandeja);
				var todos_botones = document.getElementsByClassName("msg_button");
				Object.entries(todos_botones).forEach(([key, value]) => {		    		
		    		var atributo = value.getAttribute("id_shelf");	
		    		if (atributo == id_bandeja) {		    			
		    			value.style.visibility = "visible";		    			
		    		}
		    	});		    		
		    }
		
		    if((operario_msg == user_id)&&(mensaje_msg.includes('oculta'))){	
		    	$("#campana_button").hide();
		    }
		
		    if((id_bandeja)&&(mensaje_msg.includes('oculta'))){
		    	var todos_botones = document.getElementsByClassName("msg_button");
		    	Object.entries(todos_botones).forEach(([key, value]) => {
		    		var atributo = value.getAttribute("id_shelf");
		    		if (atributo == id_bandeja) {		    			
		    			value.style.visibility = "hidden";		    			
		    		}
		    	});					
		    }
		    
		} catch(err) {
			DebugWarn('WS Error: No se ha podido parsear el JSON (' + event.data +')');
			response = event.data;			
		}
		campana_dispatch("event", response);
	};
	
	
	conn_campana.onclose = function(){campana_dispatch('close', {server: ws_server, port: ws_msg_port})}
	conn_campana.onopen = function(){campana_dispatch('open', {server: ws_server, port: ws_msg_port})}

	this.bind = function(event_name, callback){
		callbacks[event_name] = callbacks[event_name] || [];
		callbacks[event_name].push(callback);
		return this;// chainable
	};
	

	this.send = function(payload){
		DebugInfo('Envio a puerto ' + ws_msg_port + ' de: '+ payload);
		conn_campana.send( payload ); // <= send JSON data to socket server
		conn_campana.onerror = function (error) {
			DebugWarn(error);
		};
		return this;
	};
	
	this.cerrar_campana_user = function(){		
		 var cerrar_campana_ws = new campanaWebSocket(ws_server, ws_msg_port);       
         cerrar_campana_ws.bind('open', function( data ) {       
         var json_info = {'entorno':0,
                     'bandeja':0,
                     'texto': "oculta",
        			 'operario': user_id}   		
		 payload = JSON.stringify({json_info}); 	
         cerrar_campana_ws.send(payload);        
         $("#campana_button").hide();         
         });    	
        }
 
    	
    	
  	this.cerrar_campana_bandeja = function(current_id_shelf){ 
		 var cerrar_campana_ws = new campanaWebSocket(ws_server, ws_msg_port);      
         cerrar_campana_ws.bind('open', function( data ) {      
         var json_info = {'entorno':0,
                     'bandeja':current_id_shelf,
                     'texto': "oculta",
        			 'operario': 0}   		
		 payload = JSON.stringify({json_info}); 
         cerrar_campana_ws.send(payload); 
         });
    	}
  
    
   
    	var campana_dispatch = function(event_name, message){
    		var chain = callbacks[event_name];
    		if(typeof chain == 'undefined') return; // no callbacks for this event
    			for(var i = 0; i < chain.length; i++){
    				chain[i]( message )
    			}
    		}	

      };

/******** CAMPANA DEL USUARIO *********/
$(document).on("click", "#campana_button", function() {
conexion_campana.cerrar_campana_user();
$("#campana_button").hide();
var almacen_usuario = [];
array_msg_users.forEach((almacen) => {  
    if (almacen.usuario == user_id) {    	 	
     	almacen_usuario.push(almacen);  
    }    
});
var ultimo_msg_user = almacen_usuario[almacen_usuario.length-1];

$('#MenuModalMsgUser #ultimo_mensaje_user').val();
$('#MenuModalMsgUser #ultimo_mensaje_user').empty();
$('#MenuModalMsgUser #mensajes_bd_user').val();
$('#MenuModalMsgUser #mensajes_bd_user').empty();
$('#MenuModalMsgUser #ultimo_mensaje_user').append('<li>' + ultimo_msg_user.mensaje + '</li>'); 
$('#MenuModalMsgUser').modal('show');	
});


/******** CAMPANA DE LA BANDEJA *********/
$(document).on("click", ".msg_button", function() {
$('#ModalMsgBandeja #mensajes_bandeja').empty();
var current_id_shelf = $(this).attr("id_shelf");
conexion_campana.cerrar_campana_bandeja(current_id_shelf);
var todos_botones = document.getElementsByClassName("msg_button");
Object.entries(todos_botones).forEach(([key, value]) => {
		var atributo = value.getAttribute("id_shelf");	
		if (atributo == current_id_shelf) {			
			value.style.visibility = "hidden";
		}
});   
var almacen_bandeja = [];
array_msg_bandejas.forEach((almacen) => {
    if (almacen.bandeja == current_id_shelf) {  	
    	almacen_bandeja.push(almacen);    
    } 
});

var ultimo_msg_bandeja = almacen_bandeja[almacen_bandeja.length-1];

$('#ModalMsgBandeja #ultimo_msg_bandeja').val();
$('#ModalMsgBandeja #ultimo_msg_bandeja').empty();
$('#ModalMsgBandeja #msg_bd_bandeja').val(); 
$('#ModalMsgBandeja #msg_bd_bandeja').empty();
$('#ModalMsgBandeja #ultimo_msg_bandeja').append('<li>' + ultimo_msg_bandeja.mensaje + '</li>');
$('#ModalMsgBandeja').modal('show');

$(document).on("click", "#btn_btn_mostrar_msg_bandeja", function() {
		recogeMsgBandeja(current_id_shelf);
});

});

$(document).on("click", "#btn_mostrar_msg_user", function() {	
		recogeMsgUser(user_id);
});

function recogeMsgUser(user) {	
	$.post(mensajes_user_path, {
              "var_1": user,                     
            },function(data) {                       
             $('#MenuModalMsgUser #mensajes_bd_user').html(data)
    });	    
	return;
}

function recogeMsgBandeja(bandeja) {	
	$.post(mensajes_bandeja_path, {
              "var_1": bandeja,                     
            },function(data) {                          
             $('#ModalMsgBandeja #msg_bd_bandeja').html(data)
    });
	return;
}
