<?php

    $shelves_data = array();
    $logistics_models = array();
    $test_in_hand = array();
    $models_fields = array();
    $models_interfaces = array();
    $shelf_plates= array();
    $site_data = array();


//Recogemos json datos sede
  
   $sql = "SELECT site_name, site_json
            FROM validator.validator_environment_shelf_launcher_sites
            WHERE site_name= '$sede'";     
     
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $site_data[]= $row;
        unset($row);
    }
    mysqli_free_result($result);


// Obtenemos el listado de modelos por logistic
    $sql = "SELECT A.id AS logistic_id
                , A.name AS logistic
                , A.launch_data AS launch_with_data
                , A.launch_no_data AS launch_without_data
                , SUBSTRING_INDEX(B.model,'_', 1) AS manufacturer
                , B.id as model_id
                , SUBSTRING_INDEX(B.model,CONCAT(SUBSTRING_INDEX(B.model,'_', 1),'_'), -1) AS model
                , B.intranet_model_id AS intranet_model_id
                , B.customer_info
                , CASE WHEN (B.customer_info IS NULL)
                    THEN SUBSTRING_INDEX(B.model,CONCAT(SUBSTRING_INDEX(B.model,'_', 1),'_'), -1)
                    ELSE CONCAT(SUBSTRING_INDEX(B.model,CONCAT(SUBSTRING_INDEX(B.model,'_', 1),'_'), -1), ' (', B.customer_info, ')')
                END  AS model_with_customer_info
                FROM validator_environment_model B
            LEFT JOIN validator_logistic A ON A.id = B.logistic_id
            ORDER BY A.name, B.model;";
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $logistics_models[]= $row;
        unset($row);
    }
    mysqli_free_result($result);

//Recogemos json interfaces de todos los modelos
  
     $sql = "SELECT intranet_model_id, interfaces
             FROM validator_model_information;";
     
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $models_interfaces[]= $row;
        unset($row);
    }
    mysqli_free_result($result);

 //Recogemos interfaces de las bandejas 
 
    $sql = "SELECT *
            FROM validator_shelf_interfaces_types;";
            
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $shelf_plates[]= $row;
        unset($row);
    }
    mysqli_free_result($result);
    
   



// Test in hand
    $sql = "SELECT *
            FROM validator_barcode
            WHERE `step`='IN_HAND'
                AND operator_show IS NOT NULL
                AND operator_show <> ''
            ORDER BY barcode ASC, model_id DESC, customer_id DESC, text, operator_show DESC;";
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $test_in_hand[]= $row;
        unset($row);
    }
    mysqli_free_result($result);

// Obtenemos la configuracion del entorno
    $sql = "SELECT
				id_environment,
                validator_environment.name AS environment_name,
                rack,
				validator_environment_shelf.id AS id_shelf,
                shelf,
                shelf_type,
                shelf_group,
                stb_server_ip,
                configuring_shelf,
                configuring_shelf_init_time,
                logistic_id,
                model,
                invoice,
                COALESCE(web_last_status_JSON, '') AS web_last_status_JSON,
                ws_port,
                only_test,
                id_shelf_interfaces_type
            FROM
                validator_environment_shelf
            LEFT JOIN validator_environment
				ON validator_environment_shelf.id_environment = validator_environment.id
            LEFT JOIN validator_environment_shelf_launcher
				ON validator_environment_shelf.id = validator_environment_shelf_id
		WHERE id_environment = " . $_GET['environment'] . "
				AND CAST(shelf AS SIGNED) IN (" . $_GET['shelves'] . ")
        ORDER BY shelf ASC;";
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $shelves_data[$i] = json_decode($row['web_last_status_JSON'], true);
        $shelves_data[$i]['id_environment'] = $row['id_environment'];
        $shelves_data[$i]['environment_name'] = $row['environment_name'];
        $shelves_data[$i]['rack'] = $row['rack'];
        $shelves_data[$i]['id_shelf'] = $row['id_shelf'];
        $shelves_data[$i]['shelf'] = $row['shelf'];
        $shelves_data[$i]['only_test'] = $row['only_test'];
        $shelves_data[$i]['id_shelf_interfaces_type'] = $row['id_shelf_interfaces_type'];
        $shelves_data[$i]['ws_port'] = isset($row['ws_port']) ? $row['ws_port'] : null;
        $shelves_data[$i]['shelf_type'] = $row['shelf_type'];
        $shelves_data[$i]['shelf_group'] = $row['shelf_group'];
        $shelves_data[$i]['stb_server_ip'] = $row['stb_server_ip'];
        $shelves_data[$i]['configuring_shelf'] = $row['configuring_shelf'];
        $shelves_data[$i]['configuring_shelf_init_time'] = $row['configuring_shelf_init_time'];
        $shelves_data[$i]['s_from_launch'] = isset($row['s_from_launch']) ? $row['s_from_launch'] : null;
        $shelves_data[$i]['logistic_id'] = $row['logistic_id'];
        $shelves_data[$i]['model_fields']['model'] = $row['model'];
        $shelves_data[$i]['model_fields']['loops'] = 1;
        $shelves_data[$i]['invoice'] = $row['invoice'];

        $datetime_now = new DateTime("now");
        if (isset($shelves_data[$i]['last_launch_info_datetime'])) {
            $datetime_last_launch = new DateTime($shelves_data[$i]['last_launch_info_datetime']);
            $s_from_launch = ($datetime_now->getTimestamp() - $datetime_last_launch->getTimestamp());
        }
        if (isset($shelves_data[$i]['last_server_activity_datetime'])) {
            $datetime_last_server_activity_datetime = new DateTime($shelves_data[$i]['last_server_activity_datetime']);
            $s_from_last_server_activity_datetime = ($datetime_now->getTimestamp() - $datetime_last_server_activity_datetime->getTimestamp());
        }
        $shelves_data[$i]['s_from_launch'] = isset($shelves_data[$i]['last_launch_info_datetime']) ? (String)$s_from_launch : null;
        $shelves_data[$i]['s_from_last_server_activity_datetime'] = isset($shelves_data[$i]['last_server_activity_datetime']) ? (String)$s_from_last_server_activity_datetime : null;

        $shelves_data[$i]['last_launch_info_datetime'] = isset($shelves_data[$i]['last_launch_info_datetime']) ? $shelves_data[$i]['last_launch_info_datetime'] : '';
        $shelves_data[$i]['current_view'] = isset($shelves_data[$i]['current_view']) ? $shelves_data[$i]['current_view'] : 'launcher';
        $shelves_data[$i]['response_type'] = isset($shelves_data[$i]['response_type']) ? $shelves_data[$i]['response_type'] : 'input';
        $shelves_data[$i]['shelf_color'] = isset($shelves_data[$i]['shelf_color']) ? $shelves_data[$i]['shelf_color'] : 'white';
        $shelves_data[$i]['shelf_text_color'] = isset($shelves_data[$i]['shelf_text_color']) ? $shelves_data[$i]['shelf_text_color'] : 'black';
        $shelves_data[$i]['flow_color'] = isset($shelves_data[$i]['flow_color']) ? $shelves_data[$i]['flow_color'] : 'white';
        $shelves_data[$i]['flow_text_color'] = isset($shelves_data[$i]['flow_text_color']) ? $shelves_data[$i]['flow_text_color'] : 'black';
        $shelves_data[$i]['log'] = isset($shelves_data[$i]['log']) ? $shelves_data[$i]['log'] : '';
        $shelves_data[$i]['model_info'] = "No hay informacion de este modelo.";

        if (is_null($shelves_data[$i]['ws_port'])) {
            $shelves_data[$i]['current_view'] = 'msg';
            $shelves_data[$i]['msg'] = '<h3>No se ha podido conectar al servidor.<br>(server port not defined)</h3>';
            $shelves_data[$i]['shelf_color'] = 'red';
        }

        $i++;
        unset($row);
    }
    mysqli_free_result($result);

// Obtenemos los campos y su orden
    $sql = "SELECT DISTINCT VEM.intranet_model_id AS intranet_model_id
                , VEM.id as model_id
                , VEM.model
                , VL.name
                , VLRE.reg_exp
                , VLO.order_to_ask
            FROM validator.validator_label_reg_exp AS VLRE
            INNER JOIN validator.validator_environment_model AS VEM ON VEM.intranet_model_id = VLRE.intranet_model_id
            INNER JOIN validator.validator_label AS VL ON VL.id = VLRE.validator_label_id
            LEFT JOIN validator.validator_label_order AS VLO ON VLRE.id = VLO.validator_label_reg_exp_id
            WHERE (
                    VLO.time_to_ask = 'begin_first'
                    OR VLRE.customer_id IS NULL
                    AND VL.name = 'serial'
                    )
            ORDER BY VEM.model
                ,VLO.order_to_ask ASC;";
    $result = $mysqli->query($sql);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        echo '</div>';
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $models_fields[]= $row;
        unset($row);
    }
    mysqli_free_result($result);

?>

<div id="page"></div>
        <div class="container-fluid">
            <div id="pageContent" class="row"></div>
        </div>
        <div id="dialog-message" title="">
            <div id="msg" class="text-center"></div>
		</div>

<?php
    include('templates/modal_launch.html');
    include('templates/modal_copy.html');
    include('templates/modal_cables.html');
    include('templates/modal_select.html'); 
    include('templates/modal_msg_user.html');
    include('templates/modal_msg_bandeja.html');
    include('templates/modal_ticket.html');
    include('templates/modal_ultimas_lanzadas.html');
?>
    </body>

<?php
    include('templates/rack.html');
    include('templates/shelf.html');
    include('templates/modal_launch_form.html');
    include('templates/modal_copy_form.html');
    include('templates/modal_select_form.html');
?>
    <script type="text/javascript">
        window.debug = false;
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="js/libs/jquery.js"></script>
    <script type="text/javascript" src="js/libs/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/libs/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="js/libs/jquery.bsAlerts.min.js"></script>
    <script type="text/javascript" src="js/libs/firebugx.js"></script>
    <script type="text/javascript" src="js/libs/html5shiv.js"></script>

    <script type="text/javascript" src="js/libs/jsrender.min.js"></script>
    <script type="text/javascript" src="js/libs/jsviews.min.js"></script>
    <script type="text/javascript">
        $.views.settings.debugMode('AVISAR A SISTEMAS');
    </script>

    <script type="text/javascript" src="js/libs/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/libs/jquery.validate.messages_es.min.js"></script>
    <script type="text/javascript" src="js/libs/jquery.validate.additional-methods.min.js"></script>

    <script type="text/javascript" src="js/ws/fancywebsocket.js"></script>
    <script type="text/javascript" src="js/ws/WebSockets.js"></script>
    <script type="text/javascript" src="js/ws/campana.js"></script>

    <script type="text/javascript" src="js/CapsLock.js"></script>
    <script type="text/javascript" src="js/clock.js"></script>
    <script type="text/javascript" src="js/debug.js"></script>
    <script type="text/javascript" src="js/modal_copy.js"></script>
    <script type="text/javascript" src="js/modal_launch.js"></script>
    <script type="text/javascript" src="js/modal_model_selection.js"></script>
    <script type="text/javascript" src="js/rack.js"></script>
    <script type="text/javascript" src="js/shelf_launcher.js"></script>
    <script type="text/javascript" src="js/shelf_log.js"></script>
    <script type="text/javascript" src="js/shelf_msg.js"></script>
    <script type="text/javascript" src="js/shelf_number.js"></script>
    <script type="text/javascript" src="js/shelf_question.js"></script>
    <script type="text/javascript" src="js/shelf_update.js"></script>
    <script type="text/javascript" src="js/validation.js"></script>
    <script type="text/javascript" src="js/shelf_warning.js"></script>
    <script type="text/javascript" src="js/modal_ticket.js"></script>

<?php if ($_SESSION['user_admin'] == 1) { ?>
    <script type="text/javascript" src="js/shelf_end_launch.js"></script>
<?php  }    ?>

	<script type="text/javascript">

        var entorno = "<?php echo $entorno ?>";
        var sede = "<?php echo $sede ?>";
        var shelves_old = <?php echo json_encode($shelves_data); ?>;
        var shelves = <?php echo json_encode($shelves_data); ?>;
		var ws_server = "<?php echo WS_SERVER ?>";
        var ws_server_ports = $.unique($.grep(ArrayColumn(shelves, 'ws_port'), function(n, i){ return (n !== "" && n != null); })).sort();
        var intranet_invoice_path = "<?php echo INTRANET_INVOICE_PATH ?>";
        var mensajes_web_path = "<?php echo MENSAJES_WEB_PATH ?>";
        var mensajes_user_path = "<?php echo MENSAJES_USER_PATH ?>";
        var mensajes_bandeja_path = "<?php echo MENSAJES_BANDEJA_PATH ?>";
		var ws_ticket_port = "<?php echo WS_TICKET_PORT ?>";
        var ws_msg_port = "<?php echo WS_MSG_PORT ?>";
        var logistics_models = <?php echo json_encode($logistics_models); ?>;
        //var logistics = $.unique(ArrayColumn(logistics_models, 'logistic').sort());
        var logistics = ArrayCombine(ArrayColumn(logistics_models, 'logistic'),ArrayColumn(logistics_models, 'logistic_id'));
        var test_in_hand = <?php echo json_encode($test_in_hand); ?>;
        var models_fields = <?php echo json_encode($models_fields); ?>;
        var models_interfaces = <?php echo json_encode($models_interfaces); ?>;
        var shelf_plates = <?php echo json_encode($shelf_plates); ?>;
        var site_data = <?php echo json_encode($site_data); ?>;
        var user_id = <?php echo $_SESSION['user_id']; ?>;
        var user_name = '<?php echo $_SESSION['user_name']; ?>';
        var user_admin_status = false;
        var allow_loop = false;
        var array_msg_bandejas = [];
        var array_msg_users = [];
<?php if ($_SESSION['user_admin'] == 1) { ?>
        user_admin_status = true;
<?php } ?>
<?php if ($_SESSION['allow_loop'] == 1) { ?>
        allow_loop = true;
        console.log('! Habilitado lanzar en bucle para el usuario.');
<?php } ?>

		var current_environment = {
			id: shelves[0].id_environment,
			name: shelves[0].environment_name,
			racks: $.unique(ArrayColumn(shelves, 'rack')).sort()
		};

		$( document ).ready(function() {

			$.templates("#rack_template").link("#pageContent", current_environment);
			RefreshShelves();
            UpdateVisibilityDefaultAnswerAllButtons();

            // arreglo provisional para popover
            //$('body').on('click','.close',function(){$('.popover').hide()});

            $.each(ws_server_ports, function (port, value) {
                Connect2WSServer(ws_server, value);
            });

            conexion_campana = new campanaWebSocket(ws_server, ws_msg_port); 

        });

<?php if ($_SESSION['user_admin'] == 1) { ?>
        function ShowWSports() {
            //Mostramos los puertos de las bandejas
            $.each(shelves, function(index, shelf) {
                var link = '<b>Puerto:</b> <a href="../log_viewer.php?port='+ shelf.ws_port +'" target="_blank">'+ shelf.ws_port +'</a>';
                link += ' ['+ shelf.ws_client_id +']';
                link += ' <a href="../log_visualizer.php?model=0&serial=&environment='+shelf.id_environment+'&shelf='+shelf.shelf+'&date=&limit=1" target="_blank" class="shelflog" style="text-decoration:none">&#9409;</a>';
                $('span.ws_port[id_shelf='+ shelf.id_shelf +']').html(link);
            })
        }
<?php  }    ?>

	</script>