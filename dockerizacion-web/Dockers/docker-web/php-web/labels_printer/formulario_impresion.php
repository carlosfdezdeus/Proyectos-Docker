<?php
require_once __DIR__.'/../conf.php';
require_once  __DIR__.'/../debug.php';
include __DIR__.'/../user_login.php';
include __DIR__.'/impresoras.php';

$impresora_selecionada = "";
$div_msg = "";
$serial = "";
$argumento_vacio = false;
function imprimir($impresora_destino, $etiqueta){

    global $impresoras;
    $host= $impresoras[$impresora_destino]['IP'];
    $port = $impresoras[$impresora_destino]['Puerto'];
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket === false) {
        $respuesta = array(
            'Error_id' => -3,
            'Error_msg' => "socket_create() failed. reason: " . socket_strerror(socket_last_error()));
    } else {
        // Socket conectado.
        $conexion = socket_connect($socket, $host, $port);
        // Conexion establecida.
        if ($conexion === false) {
            $respuesta = array(
                'Error_id' => -4,
                'Error_msg' => "socket_connect($conexion) failed. reason: " . socket_strerror(socket_last_error($socket)));
        } else {
            socket_write($socket, $etiqueta, strlen($etiqueta));
            $respuesta = array(
                'Error_id' => 0,
                'Error_msg' => "Impresion correcta");
        }
        if ($socket) {
            socket_close($socket);
        }
    }
    return $respuesta;
}
if (array_key_exists($_POST['impresora'], $impresoras)) {
    $impresora_selecionada = $_POST['impresora'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = "alert alert-danger alert-dismissible";
    $msg = '<strong>Error al imprimir la etiqueta (Avisar a Sistemas)</strong>';
    if (empty($_POST['serial'] )||empty($_POST['impresora'])) {
        $msg =  '<strong>Error:</strong> ';
        $msg .= "El campo serial o impresora está vacio";
    }else{
        $serial = trim($_POST['serial']);
        $impresora_destino = $_POST['impresora'];
        // Conectamos a la DB
        $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
        if ($mysqli->connect_errno) {
            $msg = '<strong>Error:</strong> ';
            $msg .= "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }else{
            $peticion_sql =
                "SELECT LU.serial_number AS serial
                ,mac AS 'mac'
                ,firmware AS 'firmware'
                ,caid AS 'caid'
                , LU.intranet_model_id as ' intranet_model_id'
                ,(
                    SELECT customer_info
                    FROM validator.validator_environment_model
                    WHERE intranet_model_id = LU.intranet_model_id limit 1
                    ) AS 'codigo_sap'
                ,(
                    SELECT validated
                    FROM warehouse.uut_status
                    WHERE logger_uut_id = LU.id limit 1
                    ) AS 'validado'
            FROM warehouse.uut_info AS WUI
            INNER JOIN validator.logger_uut AS LU ON LU.id = WUI.logger_uut_id
            WHERE LU.serial_number = '$serial'";
            $resultado = $mysqli->query($peticion_sql);
            if (empty($resultado)) {
                $msg = '<strong>Peticion vacia.</strong> ';
            }else{
                if ($resultado = $mysqli->query($peticion_sql)) {
                    /* obtener un array asociativo */
                    while ($fila = $resultado->fetch_assoc()) {
                        $array_equipos[]= $fila;
                        unset($fila);
                    }
                    /* liberar el conjunto de resultados */
                    mysqli_free_result($resultado);
                }
                $numero_equipos = count($array_equipos);
                if(empty($numero_equipos)){
                    $msg = '    <strong> No existen equipos con este serial ' .$serial .'</strong>';
                    $msg .= '    <p>';
                    $msg .= '        Por favor, intentalo con otro serial .';
                    $msg .= '    </p>';
                }elseif ($numero_equipos =! 1){
                    $msg = 'Hay mas de un equipo con el mismo serial'. $array_equipos;
                }else{
                    foreach ($array_equipos[0] as $clave => $valor) {
                        if (!(($clave == 'caid') || ($clave == 'firmware'))){
                            if (empty($valor) ) {
                                $argumento_vacio = TRUE;
                                array_push($campos_vacios, $clave);
                            }
                        }
                    }
                    if ($argumento_vacio) {
                        $msg = 'Algunos campos obligatorios estan vacíos (' . implode(",", $campos_vacios) . ')';
                    }else{
                        $mysqli_w = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB_W);
                        if ($mysqli->connect_errno) {
                            echo '<div class="alert alert-danger">';
                            echo '<strong>Error:</strong> ';
                            echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                            echo '</div>';
                            exit();
                        }
                        // Modelos  TECHNICOLOR_DECO_4K,SAGEMCOM_4K_ATV,TECHNICOLOR_DECO_4K_V2
                        $array_modelos_caid = array(2884,3490,3402);
                        $serial= $array_equipos[0]['serial'];
                        $mac= $array_equipos[0]['mac'];
                        $codigo_sap= $array_equipos[0]['codigo_sap'];
                        $fecha_impresion = date("d-m-Y h:i:s");
                        $firmware = $array_equipos[0]['firmware'];
                        $validado = $array_equipos[0]['validado'];
                        $caid= $array_equipos[0]['caid'];
                        $intranet_model_id = $array_equipos[0]['intranet_model_id'];
                        if($validado!=1){
                            $respuesta = array(
                                $msg = ' EL equipo no esta validado');
                        } else {
                            $codigo_etiqueta = 'CT~~CD,~CC^~CT~
^XA~TA000~JSN^LT0^MNW^MTD^PON^PMN^LH0,0^JMA^PR6,6~SD22^JUS^LRN^CI0^XZ
^XA
^MMT
^PW567
^LL0280
^LS0
^BY2,3,35^FT220,25^BCR,,N,N
^FD>:' . $serial .  '^FS
^FT200,25^A0R,20,19^FH\^FDSerial: ' . $serial .  '^FS';
if(in_array($intranet_model_id,$array_modelos_caid)){
    $codigo_etiqueta .= '^BY2,3,35^FT155,25^BCR,,N,N
        ^FD>:' . $caid . '^FS
        ^FT135,25^A0R,20,19^FH\^FDCAID: ' . $caid . '^FS';
}else{
$codigo_etiqueta .= '^BY2,3,35^FT155,25^BCR,,N,N
^FD>:' . $mac . '^FS
^FT135,25^A0R,20,19^FH\^FDMAC: ' . $mac . '^FS';
}
$codigo_etiqueta .= '^BY2,3,35^FT90,25^BCR,,N,N
^FD>:' . $codigo_sap . '^FS
^FT70,25^A0R,20,19^FH\^FDC\A2digo SAP:' . $codigo_sap . '^FS
^FT40,25^A0R,22,20^FH\^FDFirmware: ' . $firmware . '^FS
^FT15,25^A0R,20,19^FH\^FDFecha Impresi\A2n:^FS
^FT15,165^A0R,20,19^FH\^FD'. $fecha_impresion . '^FS
^PQ1,0,1,Y^XZ';
                            $resultado = imprimir($impresora_destino,$codigo_etiqueta);
                            if (empty($resultado)) {
                                $msg = "El resultado de la impresión esta vacio";
                            } elseif ($resultado["Error_id"] == 0) {
                                $class = "alert alert-success alert-dismissible";
                                $msg = date("h:i:s") . ' > Etiqueta  con serial: ' . $serial . ' enviada a la impresora ' . $impresora_destino;
                            } else {
                                $msg = '<strong>Error ' . $resultado["Error_id"] . ': </strong> ' . $resultado["Error_msg"];
                            }
                        }
                    }
                }
            }
        }
    }
    $div_msg = "<div id=\"msg\" class=\"$class\" role=\"alert\">";
    $div_msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
    $div_msg .= $msg;
    $div_msg .= "</div>";
}
?>
<html>
    <head>
            <!-- Bootstrap -->
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/jquery-ui.css">
        <link rel="stylesheet" href="../css/validatorGUI.css">
        <link rel="stylesheet" href="../css/jquery.validationEngine.css">
        <link rel="stylesheet" href="../css/gestion.css">
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
        <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
        <script type="text/javascript" src="/js/libs/jquery.js"></script>
        <script type="text/javascript" src="/js/libs/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="/js/libs/jquery-ui-1.10.3.custom.min.js"></script>
    </head>
    <body>
        <?php
        require_once __DIR__.'/../navbar.php';
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
            <!-- Modificar por esta ruta para utilizar en Tury  action='formulario_impresion.php'  -->
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <form name="formulario_buscar"
                        id="formulario_buscar"
                        action='http://eklabels.turyelectro.com:8080/labels_printer/formulario_impresion.php'
                        method="POST">
                            <legend> <?php echo "Buscador" ?></legend>
                                    <label for="impresora">Impesora:<em>*</em></label>
                                    <select class ="form-control impresoraselecionada"  name="impresora">
                                        <option value="">Selecciona impresora</option>
                                        <?php
                                            foreach ($impresoras as $key => $value){
                                        ?>
                                                <option value="<?php  echo "$key";?>" <?php if ($key == $impresora_selecionada) { echo "SELECTED=SELECTED"; } ?>>
                                            <?php echo $key;?></option>
                                        <?php } ?>
                                    </select>
                                    <br>
                                <div class="form-label-group">
                                        <label for="serial">Serial:<em>*</em></label>
                                        <input name="serial" id="serial" class="form-control" value="" autofocus />
                                </div>
                                <br>
                        <input type="submit" value="Imprimir" id="boton_imprimir" class="btn btn-primary btn-lg center">
                    </form>
                    <script type="text/javascript">
                        $(document).keypress(function (event) {
                            if (event.which == 13) {
                                event.preventDefault();
                                        $("#formulario_buscar").submit();
                            }
                        });

                    </script>
                </div>
            </div>
        </div>
    </body>
</html>