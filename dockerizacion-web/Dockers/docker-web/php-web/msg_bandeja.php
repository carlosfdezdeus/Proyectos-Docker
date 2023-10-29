<?php

include('conf.php');
include('debug.php');

// Conectamos a la DB
$mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
if ($mysqli->connect_errno) {
	echo '<div class="alert alert-danger">';
	echo '<strong>Error:</strong> ';
	echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	echo '</div>';
	exit();
}

if (array_key_exists('ajax', $_REQUEST) && $_REQUEST['ajax'] == 1) {
	if ($_REQUEST['op']=="getShelves") {
		$shelves = Array();
		$sql = "SELECT `id`,`shelf`
				FROM `validator_environment_shelf`
				WHERE `id_environment`= ". $_REQUEST['environment'] ."
				ORDER BY `shelf`;";
		$result = $mysqli->query($sql);
		if ($mysqli->connect_errno) {
			echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		while ($row = $result->fetch_assoc()) {
			$shelves[$row['id']]=$row['id'];
			unset($row);
		}
		mysqli_free_result($result);
		echo "<select id=\"shelf\" name=\"shelf\">		
		<option value='0'>---</option>";
		echo array_to_select($shelves);
		echo "</select>";		
		exit;
	}
}

$txt_bandeja = isset($_REQUEST['txt_bandeja']) ? $_REQUEST['txt_bandeja'] : NULL;
$environment = isset($_REQUEST['environment']) ? $_REQUEST['environment'] : NULL;
$operario = isset($_REQUEST['operario']) ? $_REQUEST['operario'] : NULL;
$shelf = isset($_REQUEST['shelf']) ? $_REQUEST['shelf'] : NULL;
$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : NULL;

$environments = Array();
$sql = "SELECT `id`,`name`
		FROM `validator_environment`
		ORDER BY `name`;";
$result = $mysqli->query($sql);
if ($mysqli->connect_errno) {
	echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
	return;
}
while ($row = $result->fetch_assoc()) {
	$environments[$row['id']]=$row['name'];
	unset($row);
}

// *************** OPERARIO
$operarios = Array();
$sql = "SELECT `id`,`name`
		FROM `login`
		ORDER BY `name`;";
$result = $mysqli->query($sql);
if ($mysqli->connect_errno) {
	echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
	return;
}
while ($row = $result->fetch_assoc()) {
	$operarios[$row['id']]=$row['name'];
	unset($row);
}

// *************** FIN OPERARIO
function array_to_select($array){
	$options="";
	foreach ($array as $key=>$value){
		$options.="<option value='$key'>".$value."</option>";
	}
	return $options;
}
?>

<html>
	<head>
		<title>Mensaje a bandejas</title>
		<link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/jquery-ui.css">
        <link rel="stylesheet" href="/css/interfaz_mensajes.css">
        <link rel="stylesheet" href="/css/jquery.validationEngine.css">
        <link rel="stylesheet" href="/css/theme.blue.css">
        <link rel="stylesheet" href="/css/gestion.css">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
		
		  <!-- Favicons -->
        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="152x152" href="images/apple-touch-icon-152x152.png" />

        <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
        <script type="text/javascript" src="js/libs/jquery.js"></script>
        <script type="text/javascript" src="js/libs/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="js/libs/jquery-ui-1.10.3.custom.min.js"></script>
        
        
		<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
		<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
		<script>
		
			var ajaxObj = null;
			var READY_STATE_COMPLETE=4;

			function inicializaAjax() {
				if (window.XMLHttpRequest) {
					return new XMLHttpRequest();
				}
				else if (window.ActiveXObject) {
					return new ActiveXObject("Microsoft.XMLHTTP");
				}
			}

			function divAjax(seccionAjax) {
				if (ajaxObj.readyState==READY_STATE_COMPLETE) {
					if (ajaxObj.status==200) {
						document.getElementById(seccionAjax).innerHTML=ajaxObj.responseText;
					} else {
						if (ajaxObj.status!=0) {
							document.getElementById(seccionAjax).innerHTML='ERROR';
						}
					}
				}
			}

			function getShelvesAjax(){
				ajaxObj=inicializaAjax();
				var valores="ajax=1&op=getShelves&environment="+document.getElementById('environment').value;
				ajaxObj.open ('GET', 'log_visualizer.php?'+valores, true);
				ajaxObj.onreadystatechange = function(){divAjax("shelf_selector")};
				ajaxObj.send(null);
				return;
			}			
		
		</script>
	</head>
	<body>

<?php

include 'user_login.php';
include 'navbar.php';
 if (!isset($_SESSION['user_id'])) {
        echo '<div class="alert alert-info mt-4" role="alert">';
        echo '    <strong>Es necesario identificarse para usar la aplicaci&oacute;n.</strong>';
        echo '    <p>';
        echo '        Por favor, logueate empleando el formulario de la parte superior izquierda.';
        echo '    </p>';
        echo '</div>';
        exit();
    }
    if ($_SESSION['user_admin'] != 1 && $_SESSION['department_id'] != 1 ) {
        echo '<div class="alert alert-danger mt-4" role="alert">';
        echo '    <strong>No tienes permisos para acceder a esta secci&oacute;n</strong>';
        echo '    <p>';
        echo '       Avisa a Sistemas si necesitas acceder a esta Web.';
        echo '    </p>';
        echo '</div>';
        exit();
    }


?>

<?php if (!array_key_exists('testId', $_REQUEST)) { ?>
	
	</br>
	<h2><label id="label_msg">ENVIAR MENSAJE A BANDEJA</label></h2>
	</br>
		<form>
			<table>	
				<tr>
					<td>Operario</td>
					<td>
						<select id="operario" name="operario" class="form-control">
						    <option value='0'>---</option>
	<?php
			echo array_to_select($operarios);
	?>
				</select>
					</td>
				</tr>
				<tr>
					<td>Environment</td>
					<td>
						<select id="environment" name="environment" class="form-control" onchange="javascript:getShelvesAjax();">
							<option value='0'>---</option>
	<?php
			echo array_to_select($environments);
	?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Shelf</td>
					<td>
						<div id="shelf_selector">
							<select id="shelf" name="shelf" >							   
								<option value='0'>---</option>								
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Mensaje: </td>
					<td>			
						<input type="text" id="txt_bandeja" name="txt_bandeja" size=100 class="form-control" ><br>
					</td>
				</tr>
				
					<td colspan = 2>	
						<input type="submit" value="Enviar" id="boton_enviar_msg" class="btn btn-primary btn-lg center">
					</td>
				</tr>
			</table>
			
	
		</form>
<?php } ?>
<?php


 
// ************  BANDEJA


$bandejas = Array();
$sql =  "SELECT `id`,`shelf`
				FROM `validator_environment_shelf`
				WHERE `id_environment`= ".$environment." and shelf =".$shelf."
				ORDER BY `shelf`;";
		
$result = $mysqli->query($sql);
if ($mysqli->connect_errno) {
	echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
	return;
}
while ($row = $result->fetch_assoc()) {	
	$bandejas[$row['id']]=$row['id'];	
	unset($row);
}

// **************** FIN BANDEJA
        foreach($bandejas as $bandeja){
        	echo $bandeja . "\n";
        }
        echo "<br>";
        echo "  OPERARIO: $operario";
		echo "<br>";
		echo "  ENTORNO: $environment";
		echo "<br>";
		echo "  BANDEJA: $bandeja";
		echo "<br>";
		echo "  TEXTO:   $txt_bandeja";
        echo "<br>";

		
		
?>
	</body>	
	<script src= "/js/ws/fancywebsocket.js"></script>
	<script src= "/js/ws/WebSockets.js"></script>
	<script src= "/js/ws/campana.js"></script>
	<script src= "/js/debug.js"></script>
	
	<script type="text/javascript" >
	  
	    var ws_server = "<?php echo WS_SERVER ?>";
		var ws_ticket_port = "<?php echo WS_TICKET_PORT ?>";
		var ws_msg_port = "<?php echo WS_MSG_PORT ?>";
		var mensajes_web_path = "<?php echo MENSAJES_WEB_PATH ?>";
		
		console.log("ws_server: ", ws_server);
		console.log("ws_ticket_port: ", ws_ticket_port);
		console.log("ws_msg_port: ", ws_msg_port);
		console.log("mensajes_web_path: ", mensajes_web_path);
	
		var entorno = "<?php echo $environment; ?>";
		var bandeja = "<?php echo $bandeja; ?>";
		var txt_bandeja = "<?php echo $txt_bandeja; ?>";
		var operario = "<?php echo $operario; ?>";		
		
		var mensaje = {'bandeja': bandeja,
                     	'texto': txt_bandeja,  'operario': operario};  
      
		var json_info = {'entorno': entorno,
                     'bandeja': bandeja,
                     'texto': txt_bandeja,
        			 'operario': operario};                     
         
         if (txt_bandeja !=""){
         InsertaMsg(mensaje);        
		 payload = JSON.stringify({json_info});
		 conexion = new campanaWebSocket(ws_server, ws_msg_port); 
         conexion.bind('open', function( data ) {	
         conexion.send(payload); 
         })
		}else{
		 alert ("El texto del mensaje esta en blanco"); 	
		}	
		
		function InsertaMsg(mensaje) {	
			var operario = mensaje.operario;
			var bandeja = mensaje.bandeja;
			var mensaje = mensaje.texto;
			
			$.post(mensajes_web_path, {
              "var_1": bandeja,
              "var_2": mensaje,
              "var_3": operario         
            },function(data) {
              console.log('procesamiento finalizado', data);
            });
            return;			
        }
     </script>
</html>
