<?php
require_once __DIR__.'/../conf.php';
require_once  __DIR__.'/../debug.php';
include __DIR__.'/../user_login.php';
include __DIR__.'/impresoras_zeleris.php';

$impresora_selecionada = "";
$div_msg = "";
$serial = "";
$argumento_vacio = false;
function imprimir($impresora_destino, $etiqueta){

    global $impresoras;
    $host= $impresoras['caja'][$impresora_destino]['IP'];
    $port = $impresoras['caja'][$impresora_destino]['Puerto'];
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
    if (empty($_POST['serial'] )||empty($_POST['impresora'])||empty($_POST['tiposalida'])) {
        $msg =  '<strong>Error:</strong> ';
        $msg .= "El campo serial,impresora  o tipo salida está vacio";
    }else{

        $serial = trim($_POST['serial']);
        $impresora_destino = $_POST['impresora'];
        $tipo_salida = $_POST['tiposalida'];
        // Conectamos a la DB
        $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
        if ($mysqli->connect_errno) {
            $msg = '<strong>Error:</strong> ';
            $msg .= "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }else{
            $peticion_sql =
                "SELECT
                    LU.serial_number AS serial
                    ,mac AS 'mac'
                    ,LU.intranet_model_id AS ' intranet_model_id'
                    ,RIGHT(CAST(YEAR(CURDATE()) AS CHAR),2) AS print_year
                    ,RIGHT(CONCAT('0', CAST(WEEK(CURDATE()) AS CHAR)),2) AS 'print_week'
                    ,(
                        SELECT validated
                        FROM warehouse.uut_status
                        WHERE logger_uut_id = LU.id limit 1
                    ) AS 'validado'
                FROM
                    warehouse.uut_info AS WUI
                        INNER JOIN
                    validator.logger_uut AS LU ON LU.id = WUI.logger_uut_id
                WHERE
                    LU.serial_number = '$serial'";

            $resultado = $mysqli->query($peticion_sql);
            if (empty($resultado)) {
                $msg = '<strong>Peticion vacia.</strong> ';
            }else{
                    /* obtener un array asociativo */
                    while ($fila = $resultado->fetch_assoc()) {
                        $array_equipos[]= $fila;
                        unset($fila);
                    }
                    /* liberar el conjunto de resultados */
                    mysqli_free_result($resultado);
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
                            if (empty($valor) ) {
                                $argumento_vacio = TRUE;
                                array_push($campos_vacios, $clave);
                        }
                    }
                    if ($argumento_vacio) {
                        $msg = 'Algunos campos obligatorios estan vacíos (' . implode(",", $campos_vacios) . ')';
                    }else{
                        $validado = $array_equipos[0]['validado'];
                        $serial= $array_equipos[0]['serial'];
                        $proveedor =   '0800486';
                        $pedido =  '7407507' ;
                        $mac= $array_equipos[0]['mac'];
                        $semana_impresion = $array_equipos[0]['print_week'];
                        $año_impresion = $array_equipos[0]['print_year'];
                        $intranet_model_id = $array_equipos[0]['intranet_model_id'];
                        switch ($intranet_model_id) {
                            case 3850:
                            // Router Smart Wifi (HGU) RTF8115VW
                                $codigo_logistico = "88412979";
                                $codigo_fabricante = "21";
                            case 3852:
                            // Router Smart Wifi (HGU) GPT-2741GNAC
                                $codigo_logistico = "88412977";
                                $codigo_fabricante = "20";
                            case 3854:
                                // MITRASTAR Router Smart WiFi (HGU) GPT-2541GNAC
                                $codigo_logistico = "88412976";
                                $codigo_fabricante = "20";
                            case 3894:
                                // Router Smart Wifi(HGU) RTF3505VW (Nuevo)
                                $codigo_logistico = "88412978";
                                $codigo_fabricante = "21";
                        }
                        $barcode12 = $serial;
                        $barcode32 = $codigo_logistico . $proveedor . $pedido ."000001". $semana_impresion . $año_impresion;
                        $datamatrix = "00" . $codigo_logistico . $codigo_fabricante . "0000" . "0000" . $mac;
                        if($validado!=1){
                            $msg = ' EL equipo no esta validado';
                        }elseif ($serial != $mac ){
                            $msg = 'El campo Serial:' . $serial . 'es distinto de la Mac: ' . $mac . 'en la Base de Datos'  ;
                        }elseif  (!empty($datamatrix) and  strlen($datamatrix) <> 32){
                            $msg = 'El campo datamatrix:' . $datamatrix . 'no tienen 32 digitos de tamaño' ;
                        }elseif  (!empty($barcode32) and  strlen($barcode32) <> 32){
                            $msg = 'El campo barcode32:' . $barcode32 . 'no tienen 32 digitos de tamaño' ;
                        }elseif  (!empty($barcode32) and  strlen($barcode32) <> 32){
                            $msg = 'El campo barcode12:' . $barcode12 . 'no tienen 12 digitos de tamaño' ;
                        }else {
                            $codigo_etiqueta = 'CT~~CD,~CC^~CT~
                            ^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR5,5~SD20^JUS^LRN^CI0^XZ
                            ^XA
                            ^MMT
                            ^PW1122
                            ^LL0531
                            ^LS0
                            ^BY2,3,48^FT63,85^BCN,,N,N
                            ^FD>:' . $barcode12 . '^FS
                            ^FT125,120^A0N,34,33^FH\^FD' . $barcode12 . '^FS
                            ^FT250,215^A0N,54,53^FH\^FD' . $codigo_logistico . '^FS
                            ^FO27,164^GB705,0,3^FS
                            ^FO27,226^GB700,0,3^FS
                            ^BY3,3,80^FT63,316^BCN,,N,N
                            ^FD>;' . $barcode32 . '^FS
                            ^FT124,349^A0N,34,33^FH\^FD' . $barcode32 . '^FS
                            ^BY108,108^FT550,150^BXN,6,200,0,0,1,~
                            ^FH\^FD' . $datamatrix . '^FS
                            ^PQ3,0,1,Y^XZ
                            ';
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
                        action='http://zelerislabels.turyelectro.com:8080/labels_printer/formulario_impresion_caja.php'
                        method="POST">
                            <legend> <?php echo "Buscador" ?></legend>
                                    <label for="impresora">Impesora:<em>*</em></label>
                                    <select class ="form-control impresoraselecionada"  name="impresora">
                                        <option value="">Selecciona impresora</option>
                                        <?php
                                            foreach ($impresoras['caja'] as $key => $value){
                                        ?>
                                                <option value="<?php  echo "$key";?>" <?php if ($key == $impresora_selecionada) { echo "SELECTED=SELECTED"; } ?>>
                                            <?php echo $key;?></option>
                                        <?php } ?>
                                    </select>
                                    <br>
                                    <label for="salida">Tipo de Salida:<em>*</em></label>
                                    <select class ="form-control tiposalidaselecionada"  name="tiposalida">
                                        <option value="1" selected >Renovado</option>
                                    </select>
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