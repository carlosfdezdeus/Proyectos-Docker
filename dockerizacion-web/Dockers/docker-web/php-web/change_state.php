<?php
require_once __DIR__.'/conf.php';
require_once  __DIR__.'/debug.php';
require_once __DIR__.'/user_login.php';

$html_table = "";
$div_msg = "";
$serial = "";
$msg = "";
$msg_class = "";
$html_table = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    gestionar_POST();
    if ($msg <> "") {
        $div_msg = "<div id=\"msg\" class=\"$msg_class\" role=\"alert\">";
        $div_msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
        $div_msg .= $msg;
        $div_msg .= "</div>";
    }
}

function assoc_array_to_table($rs, $style ='table-hover table-condensed') {
    $arr = $rs->fetch_all(MYSQLI_ASSOC);
    $html_table = "<table class='table tablesorter' style='".$style."'><thead>";
    $keys = array_keys($arr[0]);
    foreach($keys as $key){
        $html_table .= "<th>".$key."</th>";
    }
    $html_table .= "</thead><tbody>";
    foreach($arr as $arr2) {
        $html_table .= "<tr>";
        foreach($arr2 as $value){
            $html_table .= "<td>".$value."</td>";
        }
        $html_table .= "</tr>";
    }
    $html_table .= "</tbody></table>";
    return $html_table;
}

function gestionar_POST() {
    global $msg, $msg_class, $html_table,$array_datos_equipo;
    $msg_class = "alert alert-danger alert-dismissible";
    $msg = '<strong>Error al buscar equipo (Avisar a Sistemas)</strong>';
    if (empty($_POST['serial'] )) {
        $msg =  '<strong>Error:</strong> ';
        $msg .= "El campo serial est&aacute; vac&iacute;o";
        return;
    } else {
        $serial = trim($_POST['serial']);
        // Conectamos a la DB
        $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
        if ($mysqli->connect_errno) {
            $msg = '<strong>Error:</strong> ';
            $msg .= "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        } else {
            # Mostramos el historial
            $peticion_sql = "SELECT (
                                    SELECT model
                                    FROM validator.validator_environment_model
                                    WHERE intranet_model_id = LU.intranet_model_id LIMIT 1
                                    ) AS 'MODELO'
                                , (
                                    SELECT customer_info
                                    FROM validator.validator_environment_model
                                    WHERE intranet_model_id = LU.intranet_model_id LIMIT 1
                                    ) AS 'SAP'
                                , LU.serial_number AS 'NUMERO DE SERIE'
                                , WUS.logger_test_id AS 'LOGGER TEST ID'
                                , (
                                    SELECT label_value
                                    FROM validator.validator_relaunch_label
                                    WHERE logger_uut_id = LU.id
                                        AND validator_label_id = 4 limit 1
                                    ) AS 'MAC'
                                , WUS.TIMESTAMP AS 'HORA DE FINAL' ";
            $peticion_sql .= "  , LT.intranet_customer_name AS CLIENTE
                                , WUS.validated AS ESTADO
                                ,IF(
                                    WUS.need_relaunch = 0
                                    , 'SI'
                                    , 'PDTE'
                                    ) AS 'EQUIPO TERMINADO'
                                , VEM.error_message AS 'MENSAJE DE ERROR'
                                , LT.box AS CAJA
                                , LT.actiON_if_validate AS 'ACCION SI VALIDA'
                                , (
                                    SELECT STATUS
                                    FROM warehouse.invoice
                                    WHERE id = WUS.invoice_id limit 1
                                    ) AS 'INVOICE STATUS'
                            FROM warehouse.uut_status AS WUS
                            LEFT JOIN validator.logger_test AS LT ON LT.id = WUS.logger_test_id
                            LEFT JOIN validator.logger_uut AS LU ON LU.id = WUS.logger_uut_id
                            LEFT JOIN validator.validator_error_messages AS VEM ON LT.error_message_id = VEM.id ";
            $peticion_sql .= "WHERE LU.serial_number = '$serial'
                            ORDER BY WUS.TIMESTAMP DESC";
            $resultado = $mysqli->query($peticion_sql);
            if (($resultado->num_rows) == 0) {
                $msg = '<strong>Equipo no encontrado en DB.</strong> ';
                $msg_class = "alert alert-danger alert-dismissible";
                return;
            }
            if ($resultado = $mysqli->query($peticion_sql)) {

                /* obtener un array asociativo > tabla HTML */
                $html_table = assoc_array_to_table($resultado);


            }
            if ($resultado = $mysqli->query($peticion_sql)) {
                while ($row = $resultado->fetch_assoc()) {
                    $array_datos_equipo[]= $row;
                    unset($row);
                }
                /* liberar el conjunto de resultados */
                mysqli_free_result($resultado);
                $msg = "";
            }
        }
    }
}

?>
<html>
    <head>
        <title>Buscar equipo</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/jquery-ui.css">
        <link rel="stylesheet" href="/css/validatorGUI.css">
        <link rel="stylesheet" href="/css/jquery.validationEngine.css">
        <link rel="stylesheet" href="/css/theme.blue.css">
        <link rel="stylesheet" href="/css/gestion.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

        <!-- Favicons -->
        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="152x152" href="images/apple-touch-icon-152x152.png" />

        <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
        <script type="text/javascript" src="/js/libs/jquery.js"></script>
        <script type="text/javascript" src="/js/libs/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="/js/libs/jquery-ui-1.10.3.custom.min.js"></script>

        <!-- Tablesorter -->
        <script src= "/js/libs/jquery.tablesorter.combined.js"></script>
        <script src= "/js/libs/jquery.tablesorter.custom.grouping.js"></script>

        <style type="text/css">
            form.formateado {
                width: 400px;
            }
            form.formateado fieldset {
                width: 850px;
            }
            form input#boton_imprimir {
                width: 450px;
            }
            form select#impresora {
                width: 175px;
            }
        </style>
    </head>
    <body>
        <?php
        require_once __DIR__.'/navbar.php';
        echo $div_msg;
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-info mt-4" role="alert">';
            echo '    <strong>Es necesario identificarse para usar la aplicaci&oacute;n.</strong>';
            echo '    <p>';
            echo '        Por favor, logueate empleando el formulario de la parte superior izquierda.';
            echo '    </p>';
            echo '</div>';
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) && !$context) {
                echo '<div class="alert alert-danger mt-4" role="alert">';
                echo '    <strong>Email o Password incorrectos</strong>';
                echo '    <p>';
                echo '        Por favor, intentalo de nuevo.';
                echo '    </p>';
                echo '</div>';
                exit;
            }
        }
        ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <form name="formulario_buscar" id="formulario_buscar" method="POST">
                        <div class="form-label-group">
                            <label for="serial">Serial:<em>*</em></label>
                            <input name="serial" id="serial" class="form-control" value="" autofocus />
                        </div>
                        <br>
                        <input type="submit" value="Buscar" id="boton_imprimir" class="btn btn-primary btn-lg center">
                    </form>
                </div>
            </div>
        </div>
        <div class = "contenido">
            <?php echo $html_table ?>
        </div>
<?php if ($array_datos_equipo[0]['INVOICE STATUS']==1 and $array_datos_equipo[0]['EQUIPO TERMINADO']=='SI' and ($array_datos_equipo[0]['ACCION SI VALIDA']=='CARCASA' or  $array_datos_equipo[0]['ACCION SI VALIDA']=='LIMPIEZA' )) : ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <div class="form-label-group">
                        <input
                            type="submit" value="Cambiar a "
                            id="boton_imprimir"
                            class="btn btn-info btn-lg center cambiar_estado"
                            estado ="<?php echo $array_datos_equipo[0]['CAJA']; ?>"
                            logger_id = "<?php echo $array_datos_equipo[0]['LOGGER TEST ID']; ?>"
                        >
                        <select nombre="estado_destino" class="form-select">
<?php if ($array_datos_equipo[0]['ACCION SI VALIDA'] == 'CARCASA'){  ?>
                            <option  class = 'selector' value="limpieza"  caja="<?php echo $array_datos_equipo[0]['CAJA']; ?>">Limpieza</option>
<?php }elseif ($array_datos_equipo[0]['ACCION SI VALIDA'] == 'LIMPIEZA') {  ?>
                            <option class = 'selector' value="carcasa"   caja="<?php echo $array_datos_equipo[0]['CAJA']; ?>">Carcasa</option>
<?php }else {  ?>
    <option value="-">-</option>
<?php }?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
<?php endif; ?>
    </body>
    <script type="text/javascript">
        $(document).keypress(function (event) {
            if (event.which == 13) {
                event.preventDefault();
                $("#formulario_buscar").submit();
            }
        });
        $(document).on('click', '.cambiar_estado', function () {
            var logger_id =  $(this).attr("logger_id");
            var accion  = $(".form-select").val();
            var caja = caja = $('.selector').attr('caja');
            console.log(caja);
            $.ajax({
                    url: 'change_state_json.php',
                    data: {
                        logger_id: logger_id,
                        caja: caja,
                        accion: accion

                    },
                    type: "POST",
                    success: function (respuesta) {
                        location.reload();
                    },
                    error: function () {
                        alert("No se ha podido modificar el estado del equipo");
                    }
                });
        });
    </script>
</html>