<?php
require_once '../conf.php';
require_once '../debug.php';

include '../user_login.php';

$acciones = [0, 1];
if (!empty($_GET)) {
    if (empty($_GET['server_port'])) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo 'El valor de server_port es incorrecto debes pones ALL o el numero de puerto';
        echo '</div>';
        exit();
    } elseif (!in_array($_GET['estado'], $acciones)) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo 'El argumento estado no tiene el valor correcto  los valores son'.print_r($acciones, 1);
        echo '</div>';
        exit();
    }
    $numero_puertos = strtoupper($_GET['server_port']);
    $array_puertos_seleccionados = explode(',', $numero_puertos);
    $estado = $_GET['estado'];
    header("Location: gestion_server_procesos.php?server_port=$numero_puertos&estado=$estado");
    exit();
}

// Conectamos a la DB
$mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
if ($mysqli->connect_errno) {
    echo '<div class="alert alert-danger">';
    echo '<strong>Error:</strong> ';
    echo 'Fall&oacute; la conexi&oacute;n a MySQL: ('.$mysqli->connect_errno.') '.$mysqli->connect_error;
    echo '</div>';
    exit();
}

$sql =
            "SELECT ves.id
            FROM
                    validator_environment_shelf ves
            LEFT JOIN
                    validator_environment_shelf_launcher vesl
            ON
                    ves.id= vesl.validator_environment_shelf_id
            WHERE
                    vesl.validator_environment_shelf_id is null
            ORDER BY ves.id";

if (empty($resultado = $mysqli->query($sql))) {
    echo '<strong>Peticion vacia:</strong> ';
    exit();
}

if ($resultado = $mysqli->query($sql)) {
    /* obtener un array asociativo */
    while ($fila = $resultado->fetch_assoc()) {
        $array_enviroment[] = $fila;
        unset($fila);
    }
    /* liberar el conjunto de resultados */
    mysqli_free_result($resultado);
}


if(!empty($array_enviroment)){
    $peticion_sql =  "Insert Into validator_environment_shelf_launcher (validator_environment_shelf_id)
    $sql";
    if (empty($resultado = $mysqli->query($peticion_sql))) {
        echo '<strong>Peticion vacia:</strong> ';
        exit();
    }
    if ($resultado = $mysqli->query($peticion_sql)) {
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);
    }
}

$peticion_sql =
            'SELECT v.id_environment
                    ,v.shelf
                    ,v.ws_port
                    ,ve.name
            FROM validator_environment_shelf v
            LEFT JOIN validator_environment ve
            ON v.id_environment = ve.id
            ORDER BY ve.name
                ,v.shelf';
if (empty($resultado = $mysqli->query($peticion_sql))) {
    echo '<strong>Peticion vacia:</strong> ';
    exit();
}
if ($resultado = $mysqli->query($peticion_sql)) {
    /* obtener un array asociativo */
    while ($fila = $resultado->fetch_assoc()) {
        $array_puertos[] = $fila;
        unset($fila);
    }
    /* liberar el conjunto de resultados */
    mysqli_free_result($resultado);
}
 // Comprobamos que hay algún  puerto lanzado
$cmd = "ps aux | grep -i 'php ws_server.php ".WS_SERVER."'| grep -v grep";
$pids = shell_exec($cmd);
$patron = '/'.WS_SERVER.'\s*(\d+)/';
$matriz_fl = preg_match_all($patron, $pids, $coincidencias, PREG_PATTERN_ORDER);
$puertos_activos = $coincidencias[1];

function crear_tabla($array_puertos, $array_puertos_activos)
{
    $html = '';
    $t1 = "\t";
    $t2 = "\t\t";
    $t3 = "\t\t\t";
    $t4 = "\t\t\t\t";
    $t5 = "\t\t\t\t\t";
    $t6 = "\t\t\t\t\t\t";
    $t7 = "\t\t\t\t\t\t\t";
    $t8 = "\t\t\t\t\t\t\t\t";
    $html .= "$t1<thead>\n";
    $html .= "$t2<tr>\n";
    $html .= "$t3";
    $html .= '<th>
                <input type="checkbox" id="marcar_todos" name="marcar_todos">
            </th>';
    $html .= "\n";
    $html .= "$t3<th>Environment</th>\n";
    $html .= "$t3<th>Shelf</th>\n";
    $html .= "$t3<th>Puerto</th>\n";
    $html .= "$t3<th>Estado</th>\n";
    $html .= "$t2</tr>\n";
    $html .= "$t1</thead>\n";
    $html .= "$t1<tbody>\n";
    foreach ($array_puertos as $key => $value) {
        $puerto = $value['ws_port'];
        $environment = $value['name'];
        $estado = (in_array($puerto, $array_puertos_activos)) ? 'activado' : 'desactivado';
        $shelf = $value['shelf'];
        $html .= "$t2<tr>\n";
        $html .= "$t3<td class='$estado'><input type='checkbox' class= 'puertos' name='puertos[]' value=".$puerto." ></td>\n";
        $html .= "$t3<td align ='center' class='$estado'>".$environment."</td>\n";
        $html .= "$t3<td align ='center' class='$estado'>".$shelf."</td>\n";
        $html .= "$t3<td align ='center' class='$estado'>".$puerto."</td>\n";

        $html .= "$t3<td  class='$estado' align ='center'> Puerto $estado </td>\n";
        //  $html .= "$t3<td  class='$estado' align ='center' numero_puerto=". $puerto ."> Puerto $estado  </td>\n";

        $html .= "$t2</tr>\n";
    }
    $html .= "\t</tbody>\n";

    return $html;
}

?>

<html>
    <head>
        <title>"WS SERVER"</title>

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
    </head>
    <body>
<?php

    include '../navbar.php';

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

    if ($_SESSION['user_admin'] != 1) {
        echo '<div class="alert alert-danger mt-4" role="alert">';
        echo '    <strong>El usuario no tiene permisos para acceder a esa página</strong>';
        echo '    <p>';
        echo '        Por favor, pongase en contacto con Sistemas.';
        echo '    </p>';
        echo '</div>';
        exit;
    }

?>
        <form name="formulario_on_off_puertos"
            id="formulario_on_off_puertos"
            class="formateado center"
            action="gestion_server_procesos.php"
            method="POST">
            <div class="flotante_izquierda" >
                <input type="submit"  class="btn btn-success btn-lg on_off_puerto" name="boton" value="Lanzar"  >
                <input type="submit"  class="btn btn-danger btn-lg on_off_puerto" name="boton" value="Deshabilitar">
            </div>
            <div class=flotante_derecha>
            <a class = ir_arriba> <img src = "/images/flecha.png"></a>
            </div>
            <div class = "contenido">
                <table id="groups" class="tablesorter">
                    <?php echo crear_tabla($array_puertos, $puertos_activos); ?>
                </table>
            </div>
            <div class= pie_pagina>
            </div>
        </form>
        <script>
            $( document ).ready(function() {
                $("input.tablesorter-filter[data-column='0']").hide();
                $( document ).on("click",".flotante_derecha", function() {
                    $('body, html').animate({
                        scrollTop: '0px'
                    }, 300);
                });
                $(window).scroll(function(){
                if( $(this).scrollTop() > 0 ){
                    $('.ir-arriba').slideDown(300);
                } else {
                    $('.ir-arriba').slideUp(300);
                }
            });

            });
            $( document ).on("click","#marcar_todos", function() {
                $(".puertos:visible").prop("checked", this.checked);
            });
            $( document ).on("click",".puertos", function() {
                if ($(".puertos").length == $(".puertos:checked").length) {
                    $("#marcar_todos").prop("checked", true);
                } else {
                    $("#marcar_todos").prop("checked", false);
                }
            });
        </script>
    </body>
</html>