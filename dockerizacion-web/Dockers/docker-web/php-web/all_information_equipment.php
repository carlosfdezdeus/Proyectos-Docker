<?php
	require_once 'conf.php';
    require_once 'debug.php';



    // Conectamos a la DB
    $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
    if ($mysqli->connect_errno) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong> ';
        echo 'Fall&oacute; la conexi&oacute;n a MySQL: ('.$mysqli->connect_errno.') '.$mysqli->connect_error;
        echo '</div>';
        exit();
    }

    $sql = "SELECT DISTINCT
                VMI.image_name      as IMAGEN
                , VMI.display_name    as MODELO
                , VMI.interfaces      as INTERFACES
                , VMI.voltage         as VOLTAJE
                , VMI.current         as CORRIENTE
                , VMI.connector       as CONECTOR_FUENTE
                , VMI.manual_reset    as RESETEO
                , VEM.mngmnt_lanip    as IP
                , VF.firmware_version as FIRMWARE

            FROM
                validator_model_information as VMI
                left join
                    validator_environment_model as VEM
                    on
                        VEM.intranet_model_id = VMI.intranet_model_id
                left join
                    validator_logistic as VL
                    on
                        VL.id = VEM.logistic_id
                left join
                    validator_firmware as VF
                    on
                        VF.intranet_model_id = VMI.intranet_model_id";
          
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()) {
        $array_equipos[]= $row;
        unset($row);
    }
    mysqli_free_result($result);


function crear_tabla($array_equipos){
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
    $html .= "$t3<th width='100' >Imagen</th>\n";
    $html .= "$t3<th width='200'>Modelo</th>\n";
    $html .= "$t3<th width='200' >Interfaces</th>\n";
	$html .= "$t3<th >Voltaje</th>\n";
	$html .= "$t3<th >Corriente</th>\n";
	$html .= "$t3<th>Conector Fuente</th>\n";
	$html .= "$t3<th >Reseteo</th>\n";
	$html .= "$t3<th>IP</th>\n";
	$html .= "$t3<th>Firware</th>\n";
    $html .= "$t2</tr>\n";
    $html .= "$t1</thead>\n";
    $html .= "$t1<tbody>\n";
    foreach ($array_equipos as  $equipos) {
        $interfaces= json_decode($equipos['INTERFACES'],true);
        //unset($interfaces['TECHNOLOGY_ID']);
        $reseteo= utf8_encode ($equipos['RESETEO']);
        $nombre_imagen= $equipos['IMAGEN'];
        $html .= "$t2<tr>\n";
        $html .= "$t3<td align ='center'>
        <img src='/img/model_info/$nombre_imagen' width='100 height='100'>";
		$html .= "$t3<td align ='center'>".$equipos['MODELO']."</td>\n";
		$html .= '<td>';
        foreach($interfaces as $key=>$value){
            if($value != 0 ||$value != "0" ){
                $html .=  " $key  => $value </br>" ;
            }
        }
		$html .= '</td>';
		$html .= "$t3<td align ='center'>".$equipos['VOLTAJE']."</td>\n";
		$html .= "$t3<td align ='center'>".$equipos['CORRIENTE']."</td>\n";
		$html .= "$t3<td align ='center'>".$equipos['CONECTOR_FUENTE']."</td>\n";
		$html .= "$t3<td align ='center'>".$reseteo."</td>\n";
		$html .= "$t3<td align ='center'>".$equipos['IP']."</td>\n";
		$html .= "$t3<td align ='center'>".$equipos['FIRMWARE']."</td>\n";
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

        <!-- Tablesorter -->
        <script src= "/js/libs/jquery.tablesorter.combined.js"></script>
        <script src= "/js/libs/jquery.tablesorter.custom.grouping.js"></script>
    </head>
    <body>
            <div class = "contenido">
                <table id="groups" class="tablesorter informacion_equipos" >
                    <?php echo crear_tabla($array_equipos); ?>
                </table>
            </div>
            <div class= pie_pagina>
            </div>
    </body>
</html>