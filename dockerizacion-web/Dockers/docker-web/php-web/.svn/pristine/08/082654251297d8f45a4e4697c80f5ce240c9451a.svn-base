/*
The MIT License (MIT)
Copyright (c) 2014 Ismael Celis
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
-------------------------------*/
/*
Simplified WebSocket events dispatcher (no channels, no users)
var socket = new FancyWebSocket();
// bind to server events
socket.bind('some_event', function(data){
	alert(data.name + ' says: ' + data.message)
});
// broadcast events to all connected users
socket.send( 'some_event', {name: 'ismael', message : 'Hello world'} );
*/

var FancyWebSocket = function(ws_server, port) {
	var callbacks = {};
	var ws_url = 'ws://' + ws_server +':'+ port;
	this.ws_server = ws_server;
	this.port = port;
	var conn;

	if ( typeof(MozWebSocket) == 'function' ) {
		conn = new MozWebSocket(ws_url);
	} else {
		conn = new WebSocket(ws_url);
	}

	conn.onerror = function (error) {
		DebugWarn('Error: al conectar con el servidor WS ('+ port +').');		
		if(port == ws_ticket_port){
			  $('#MenuModalTicket #entorno_ticket').empty();
	          $('#MenuModalTicket #entorno_ticket').val();
	          $('#MenuModalTicket #bandeja_ticket').empty()
		      $('#MenuModalTicket #bandeja_ticket').append("<h3> ... HA OCURRIDO UN ERROR. POR FAVOR LLAME AL 123 ... </h3>");
		}
		ws_server_shelves = shelves.filter(bandeja => bandeja.ws_port == port);
		$.each(ws_server_shelves, function(index, shelf_data) {
			shelf_data.current_view = 'msg';
			shelf_data.msg = '<h3>No se ha podido conectar al servidor.<br>('+ port +')</h3>';
            shelf_data.shelf_color = 'red';
			RefreshShelf(shelf_data.id_shelf);
		});
	};

	conn.onmessage = function(event){
		try {
			response = $.parseJSON(event.data);
		} catch(err) {
			DebugWarn('WS Error: No se ha podido parsear el JSON (' + event.data +')');
			response = event.data;
		}
		dispatch("event", response);
	};

	conn.onclose = function(){dispatch('close', {server: ws_server, port: port})}
	conn.onopen = function(){dispatch('open', {server: ws_server, port: port})}

	this.bind = function(event_name, callback){
		callbacks[event_name] = callbacks[event_name] || [];
		callbacks[event_name].push(callback);
		return this;// chainable
	};

	this.send = function(payload){
		DebugInfo('Envio a puerto ' + port + ' de: '+ payload);
		conn.send( payload ); // <= send JSON data to socket server
		conn.onerror = function (error) {
			DebugWarn(error);
		};
		return this;
	};
	
	this.recibir_ticket = function(){
		conn.onmessage = function(s) {	
		    try{	
		        var ticket_recibido = $.parseJSON(s.data);	
		        if (typeof ticket_recibido.json.event !== 'undefined'){
		        var shelf_data = shelves.find(bandeja => bandeja.id_shelf == ticket_recibido.json.id_shelf);
	            if (typeof shelf_data !== "undefined") {   
		        if(ticket_recibido.json.id_shelf == shelf_data.id_shelf){
		        $('#MenuModalTicket #bandeja_ticket').empty()
		        $('#MenuModalTicket #bandeja_ticket').append("<h3>" + ticket_recibido.json.data.numero_ticket + " </h3>");
		        conn.close();
		                   }
		                 }
 		               }	    
	         } catch(e) {	
	           $('#MenuModalTicket #entorno_ticket').empty();
	           $('#MenuModalTicket #entorno_ticket').val();
	           $('#MenuModalTicket #bandeja_ticket').empty()
		       $('#MenuModalTicket #bandeja_ticket').append("<h3> ... HA OCURRIDO UN ERROR. POR FAVOR LLAME AL 123 ... </h3>");
			   conn.close();
		     }
		  }
 	  };


	this.disconnect = function() {
		conn.close();
	};

	var dispatch = function(event_name, message){
		var chain = callbacks[event_name];
		if(typeof chain == 'undefined') return; // no callbacks for this event
		for(var i = 0; i < chain.length; i++){
			chain[i]( message )
		}
	}
};