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
			$shelves[$row['shelf']]=$row['shelf'];
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

$model = isset($_REQUEST['model']) ? $_REQUEST['model'] : NULL;
$serial = isset($_REQUEST['model']) ? trim($_REQUEST['serial']) : NULL;
$environment = isset($_REQUEST['environment']) ? $_REQUEST['environment'] : NULL;
$shelf = isset($_REQUEST['shelf']) ? $_REQUEST['shelf'] : NULL;
$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : NULL;
$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 1;
$loop = isset($_REQUEST['loop']) ? $_REQUEST['loop'] : 0;
$testId = isset($_REQUEST['testId']) ? $_REQUEST['testId'] : NULL;
$checked = array(0=>'',1=>'',2=>'',5=>'');
$checked["$limit"] = 'checked="checked"';

$modelos = Array();
$sql = "SELECT `id`,`model`
		FROM `validator_environment_model`
		ORDER BY `model`;";
$result = $mysqli->query($sql);
if ($mysqli->connect_errno) {
	echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
	return;
}
while ($row = $result->fetch_assoc()) {
	$modelos[$row['id']]=$row['model'];
	unset($row);
}

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

function array_to_select($array){
	$options="";
	foreach ($array as $key=>$value){
		$options.="<option value='$key'>".$value."</option>";
	}
	return $options;
}

function obtener_log($mysqli, $model = NULL, $serial = NULL, $environment = NULL, $shelf = NULL, $date = NULL, $rows = NULL, $loop = 0, $testId = NULL){
	$where="";
	$innerjoin="";
	$limit="";
	$tabla = "";
	$logline = "logger_logline";
	if ($loop == 1) {
		$logline = "logger_loops_logline";
	}
	if ($testId) {
		$where.=" AND logger_test.id=$testId";
	}
	if ($model) {
		$where.=" AND validator_environment_model.id=$model";
	}
	if ($serial) {
		$where.=" AND logger_uut.serial_number like '%$serial%'";
	}
	if ($environment) {
		$where.=" AND logger_test.validator_environment_id=$environment";
	}
	if ($shelf) {
		$where.=" AND logger_test.validator_environment_shelf=$shelf"; //cuando ese puntero este bien
	}
	if ($date) {
		$innerjoin.=" inner join $logline on logger_test.id=$logline.logger_test_id";
		$where.=" AND $logline.timestamp > TIMESTAMP(STR_TO_DATE('$date', '%m/%d/%Y')) AND $logline.timestamp <= TIMESTAMP(STR_TO_DATE('$date', '%m/%d/%Y'),'24:00:00')";
	}
	if ($rows>0) {
		$limit="LIMIT $rows";
	} elseif (($serial=="")&&($date=="")) {
		return "ERROR";
	}
	if ($where!=""){
		$where=preg_replace('/AND/', 'WHERE', $where, 1);
	} //sustituir and por where en el primero
	if (($innerjoin=="")&&($where=="")) {
		return "ERROR";
	}
	$sql = "SELECT DISTINCT `logger_test`.`id`
				,model
				,timestamp_init
				,serial_number
				,intranet_customer_name
				,logger_test.validator_environment_shelf AS shelf_number
				,validator_environment.name AS environment
			FROM `logger_test`
			INNER JOIN validator_environment ON validator_environment.id = logger_test.validator_environment_id
			INNER JOIN logger_uut ON logger_test.logger_uut_id = logger_uut.id
			INNER JOIN validator_environment_model ON validator_environment_model.intranet_model_id = logger_uut.intranet_model_id
			INNER JOIN validator_environment_shelf ON validator_environment_shelf.shelf = logger_test.validator_environment_shelf
			".$innerjoin." ".$where."
			ORDER BY `logger_test`.`id` DESC ".$limit.";";
	$result = $mysqli->query($sql);
	if ($mysqli->connect_errno) {
		echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}
	$captions = Array();
	while ($row = $result->fetch_assoc()) {
		$captions[$row['id']] = $row['environment']."-".$row['shelf_number']." / ".$row['model']." S/N: ".$row['serial_number']." (".$row['intranet_customer_name'].") ";
		unset($row);
	}
	if (sizeof($captions)==0){
		return "NO RESULTS";
	}
	foreach ($captions as $id=>$caption) {
		$sql = "SELECT logger_test.loop AS bucle
				FROM validator.logger_test
				WHERE id = $id;";
		$result = $mysqli->query($sql);
		if ($mysqli->connect_errno) {
			echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		while ($row = $result->fetch_assoc()) {
			$bucle = $row['bucle'];
			unset($row);
		}
		$loop = "- (LOOP TEST)";
		if ($bucle == 0){
			$loop = "";
		}
		$i = 0;
		$tabla .= "<caption align=\"left\" style=\"text-align:left;font-weight:bold;\">".$caption.$loop."</caption><table style=\"border:1px solid black\">";
		if (($bucle==1) && (($logline == "logger_logline") || ($logline == "logger_loops_logline"))) {
			# Muestro cabecera con los datos del test en bucle
			$sql = "(
						SELECT LT.timestamp_init
							,count(VEM.error_message) AS count
							,VS.name
							,VEM.error_message
						FROM validator.logger_loops_results AS LLT
						LEFT JOIN validator.validator_subtest AS VS ON VS.id = LLT.validator_subtest_id
						LEFT JOIN validator.validator_error_messages AS VEM ON VEM.id = LLT.validator_error_message_id
						INNER JOIN validator.logger_test AS LT ON LT.id = LLT.logger_test_id
						WHERE LLT.logger_test_id = $id
						GROUP BY VEM.error_message
					)
					UNION
					(
						SELECT LT.timestamp_init
							,count(LLT.logger_test_id) AS count
							,VS.name
							,VEM.error_message
						FROM validator.logger_loops_results AS LLT
						LEFT JOIN validator.validator_subtest AS VS ON VS.id = LLT.validator_subtest_id
						LEFT JOIN validator.validator_error_messages AS VEM ON VEM.id = LLT.validator_error_message_id
						INNER JOIN validator.logger_test AS LT ON LT.id = LLT.logger_test_id
						WHERE LLT.logger_test_id = $id
							AND VS.name IS NULL
							AND VEM.error_message IS NULL
						GROUP BY LLT.logger_test_id
					);";
			$result = $mysqli->query($sql);
			if ($mysqli->connect_errno) {
				echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
				return;
			}
			while ($row = $result->fetch_assoc()) {
				if ($i==0){
					$tabla .= "<tr><td><pre>-------------------</pre></td>";
					$tabla .= "<td><pre></pre></td><td><pre>-----</pre></td>";
					$tabla .= "<td><pre>-----------------------------------------------------------------------------------------</pre></td></tr>";
					$tabla .= "<tr><td><pre>".$row["timestamp_init"]."</pre></td>";
					$tabla .= "<td><pre></pre></td><td><pre>LOOP</pre></td><td><pre>RESUME LOOP RESULTS:</pre></td></tr>";
					$i++;
				}
				if ($row["count"] != 0){
					if (!isset ($row["name"])){
						$tabla .= "<tr><td><pre></pre></td><td><pre></pre></td><td><pre>LOOP</pre></td>";
						$tabla .= "<td><pre>OK : ".$row["count"]."</pre></td></tr>";
					} else {
						$tabla .= "<tr><td><pre></pre></td><td><pre></pre></td><td><pre>LOOP</pre></td>";
						$tabla .= "<td><pre>".$row["name"]." - ".$row["error_message"]." : ".$row["count"]."</pre></td></tr>";
					}
				}
				unset($row);
			}
			$tabla .= "<tr><td><pre>-------------------</pre></td><td><pre></pre></td><td><pre>-----</pre></td><td>";
			$tabla .= "<pre>-----------------------------------------------------------------------------------------</pre></td></tr>";
		} elseif (($bucle == 0) && (($logline == "logger_logline"))) {
			# Muestro diagnostico
			$sql = "SELECT LT.timestamp_init
						,LT.timestamp_finish
						,VS.name
						,VEM.error_message
						,LT.validated
					FROM validator.logger_test AS LT
					LEFT JOIN validator.validator_subtest AS VS ON VS.id = LT.subtest_id
					LEFT JOIN validator.validator_error_messages AS VEM ON VEM.id = LT.error_message_id
					WHERE LT.id = $id;";
			$result = $mysqli->query($sql);
			if ($mysqli->connect_errno) {
				echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
				return;
			}
			while ($row = $result->fetch_assoc()) {
				if ($i == 0){
					$tabla .= "<tr><td><pre>-------------------</pre></td>";
					$tabla .= "<td><pre></pre></td><td><pre>-----</pre></td>";
					$tabla .= "<td><pre>-----------------------------------------------------------------------------------------</pre></td></tr>";
					$tabla .= "<tr><td><pre>".$row["timestamp_init"]."</pre></td>";
					$tabla .= "<td><pre></pre></td><td><pre>DIAG</pre></td><td><pre>LAUNCHED RESULTS:</pre></td></tr>";
					$i++;
				}
				if ($row["validated"]){
					$tabla .= "<tr><td><pre>".$row["timestamp_finish"]."</pre></td><td><pre></pre></td><td>";
					$tabla .= "<pre>DIAG</pre></td><td><pre>VALIDATED</pre></td></tr>";
				} else {
					if ((isset($row["name"])) && (isset($row["error_message"]))){
						$tabla .= "<tr><td><pre></pre></td><td><pre></pre></td><td><pre>DIAG</pre></td>";
						$tabla .= "<td><pre>".$row["name"]." - ".$row["error_message"]."</pre></td></tr>";
					} else {
						$tabla .= "<tr><td><pre></pre></td><td><pre></pre></td><td><pre>DIAG</pre></td><td><pre>SCRIPT WITHOUT RESULT</pre></td></tr>";
					}
				}
				unset($row);
			}
			$tabla .= "<tr><td><pre>-------------------</pre></td><td><pre></pre></td><td><pre>-----</pre></td>";
			$tabla .= "<td><pre>-----------------------------------------------------------------------------------------</pre></td></tr>";
		} else {
			echo "Fall&oacute; Bucle <> (0,1) in logger_test [id = $id]";
			return;
		}

		$sql = "SELECT `$logline`.`Timestamp`,`logger_logtype`.`name` as 'Tipo',`logger_loglevel`.`name` as 'Nivel',`$logline`.`Line`
				FROM `$logline`
				LEFT JOIN `logger_logtype` ON `$logline`.`logger_logtype_id`=`logger_logtype`.`id`
				LEFT JOIN `logger_loglevel` ON `$logline`.`logger_loglevel_id`=`logger_loglevel`.`id`
				WHERE `logger_test_id` = $id
				ORDER BY `$logline`.`id`;";
		$result = $mysqli->query($sql);
		if ($mysqli->connect_errno) {
			echo "Fall&oacute; MySQL: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		while ($row = $result->fetch_assoc()) {
			if ($result==""){
				$head="<tr>";
				$row="<tr>";
				foreach($row as $key=>$value){
					$head.="<th>$key</th>";
					$row.="<td><pre>$value</pre></td>";
				}
				$head.="</tr>";
				$row.="</tr>";
				$tabla .= $head.$row."\n";
			} else {
				$tabla .= "<tr>";
				foreach($row as $key=>$value){
					$tabla .= "<td><pre>$value</pre></td>";
				}
				$tabla .= "</tr>\n";
			}
			unset($row);
		}
		$tabla .= "</table><br/>";
	}
	$tabla = preg_replace( '/#([0-9]{1,10})\b/i','<a target=_blank href="http://chili.turyelectro.com/issues/$1">#$1</a>',$tabla);
	return $tabla;
}

?>

<html>
	<head>
		<title>Log Visualizer</title>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
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

			$(function() {
				$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
				$( "#datepicker" ).datepicker( $.datepicker.regional[ "es" ] );
			});
		</script>
	</head>
	<body onload="document.getElementById('serial').focus();">
<?php if (!array_key_exists('testId', $_REQUEST)) { ?>
		<h1>Visualizador de logs</h1>
		<form>
			<table>
				<tr>
					<td>Model</td>
					<td>
						<select id="model" name="model">
							<option value='0'>---</option>
	<?php
			echo array_to_select($modelos);
	?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Serial</td>
					<td>
						<input type=text id="serial" name="serial"/>
					</td>
				</tr>
				<tr>
					<td>Environment</td>
					<td>
						<select id="environment" name="environment" onchange="javascript:getShelvesAjax();">
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
							<select id="shelf" name="shelf">
								<option value='0'>---</option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Fecha</td>
					<td>
						<input type=text id="datepicker" name="date"/>
					</td>
				</tr>
				<tr>
					<td>Resultados</td>
					<td>
						<input type="radio" name="limit" value="1" <?php echo $checked[1]; ?>>1</input>
						<input type="radio" name="limit" value="2" <?php echo $checked[2]; ?>>2</input>
						<input type="radio" name="limit" value="5" <?php echo $checked[5]; ?>>5</input>
						<input type="radio" name="limit" value="0" <?php echo $checked[0]; ?>>TODOS</input>
						<input type="checkbox" name="loop" value="1">loop</input>
					</td>
				</tr>
				<tr>
					<td colspan=2 style='text-align:right'>
						<input type="submit"/>
					</td>
				</tr>
			</table>
		</form>
<?php } ?>
<?php
		echo obtener_log($mysqli, $model ,$serial ,$environment ,$shelf ,$date ,$limit , $loop ,$testId );
?>
	</body>
</html>
