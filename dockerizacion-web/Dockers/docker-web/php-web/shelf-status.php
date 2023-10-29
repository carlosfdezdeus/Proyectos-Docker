<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="es"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="es"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="es"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="es"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>SHELF STATUS GUI</title>
        <meta name="description" content="GUI para estado de las bandejas">
        <meta name="author" content="Fernando Fernández-Valdés">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/shelf-status.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <!-- Favicons
        ================================================== -->
        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="152x152" href="images/apple-touch-icon-152x152.png" />
        <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
	<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
        <script>
            $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd-mm-yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
            };
            $(function() {
            $.datepicker.setDefaults( $.datepicker.regional[ 'es' ] );
            $( ".datepicker" ).datepicker( $.datepicker.regional[ 'es' ] );
            });
	</script>

    </head>
    <body>

        <?php

// Cargamos los parametros
        include_once 'conf.php';
        include_once 'shelf-status_json.php';
	$context=TRUE;
        if (empty($_GET['environment'])) {
            $environment='CPE1';
            $shelf=1;
        }else{
            $shelf=$_GET['shelf'];
            $environment=strtoupper($_GET['environment']);
            $initData=$date = str_replace('/', '-', $_GET['initData']);
            $finishData=str_replace('/', '-',$_GET['finishData']);
            if($finishData==0 || $initData>$finishData){$finishData=$initData;}
        }
// Consultamos la DB para averiguar la distribucion de las bandejas solicitadas
        $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
        if ($mysqli->connect_errno) {
            echo "Falló la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            exit();
        }

// Obtenemos los datos
        $data_shelf = getShelfData($mysqli, $environment, $shelf, $initData, $finishData);
        $data_summary = getShelfDataSummary($mysqli, $environment, $shelf, $initData, $finishData);
        $data_diag_summary = getShelfDiagnosticSummary($mysqli, $environment, $shelf, $initData, $finishData);
        $environments= getEnvironments($mysqli);
        $environmentShelfs=getEnvironmentShelfs($mysqli,$environment);

?>


        <br>
        <form id="dataForm">
            <input type="hidden" id="action" name="action" value=""/>
            <div class="container">
                <div class='centrado col-md-10'>
                        <?php echo "<h4><b>$environment - $shelf</b></h4>" ?>
                </div>
            </div>
            <br>
            <div class="container">
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group' id='envpicket'>
                            <span class="input-group-addon">Environment
                            </span>
                            <select onchange="this.form.submit()" class="form-control " name="environment" id="envSelect">
                                <?php  while ($fila = $environments->fetch_assoc()) {
                                    if($fila['environment']){
                                        echo "<option ";
                                        if($environment==$fila['environment']){echo "selected";}
                                        echo">".$fila['environment']."</option>";
                                    }
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group' id='envpicket'>
                            <span class="input-group-addon">Shelf
                            </span>
                            <select onchange="this.form.submit()" class="form-control" name="shelf" id="shelfSelect">
                                <?php  while ($fila = $environmentShelfs->fetch_assoc()) {
                                    if($fila['shelf']){
                                        echo "<option ";
                                        if($shelf==$fila['shelf']){echo "selected";}
                                        echo">".$fila['shelf']."</option>";
                                    }
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn-block btn btn-success" id="exportCSV">
                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"> CSV</span>
                    </button>
                </div>
            </div>

            <div class="container">
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepickerInit'>
                            <input id="initData" onchange="this.form.submit()" name="initData" type='text' class="datepicker form-control" placeholder="Fecha o Fecha inicio" value="<?php echo "$initData"?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepickerFinish'>
                            <input id="finishData" onchange="this.form.submit()" name="finishData" type='text' class="datepicker form-control" placeholder="Fecha fin" value="<?php echo "$finishData"?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn-block btn btn-warning" id="today">
                        <span aria-hidden="true">TODAY</span>
                    </button>
                </div>
            </div>
        </form>



        <table class="table table-hover">
<?php
            $shelfsShown=array();
            $filaNum=0;
            $lastDate=0;
            $lastSerial="";
            while ($fila = $data_shelf->fetch_assoc()) {
                $newShelf=0;
                $filaNum++;

                #si cambiamos de día, creamos una nueva tabla. poniendo como cabecera la fecha
                if($lastDate!=date('d - m - Y', strtotime($fila['timestamp_init']))){
                    $lastDate=date('d - m - Y', strtotime($fila['timestamp_init']));
                    if($filaNum>1){echo "<tr><th colspan='10'/></tr>";}
                    echo "<tr><th colspan='9' class='centrado' style='border:0'>$lastDate</th></tr>";
                    if($filaNum==1){
                        echo "<tr>
                                <th>INIT</th>
                                <th>FINISH</th>";
                        if(!$shelf){echo "<th class='centrado'>SHELF</th>";}
                        echo "<th>MODEL</th>
                                <th>SERIAL</th>
                                <th></th>
                                <th colspan='2'>DIAGNOSTIC</th>
                                <th>STATUS</th>
                                <th>CUSTOMER</th>
                                <th></th>
                            </tr>";
                    }
                }


                $errorMessage=$fila['error'];
                if(!in_array($fila['shelf'], $shelfsShown)){
                    $newShelf=1;
                    array_push($shelfsShown, $fila['shelf']);
                }

                if($fila['validated']==1){$subtest="OK";$classRow="success";}
                elseif ($fila['subtest']!=null) {$subtest=$fila['subtest'];$classRow="danger";}
                else{
                    $subtest=$fila['subtest'];
                    $classRow="";
                    if(!$newShelf){$classRow="active";}
                    elseif($fila['timestamp_finish']==0){
                        $errorMessage="running...";
                        $subtest="<i class='fa fa-spinner fa-spin' style='font-size:24px'></i>";
                    }
                }
                if($fila['timestamp_finish']==0){$endTimestamp="";}
                else{$endTimestamp=date('H:i:s', strtotime($fila['timestamp_finish']));}

                $relaunchText="";
                if($fila['relaunched']==1){
                    if($lastSerial==$fila['serial']){$relaunchText="<span title='Relaunched OK' class='glyphicon glyphicon-registration-mark' ></span><span title='Relaunched OK' class='glyphicon glyphicon-ok green' ></span>";}
                    else{$relaunchText="<span title='NOT Relaunched' class='glyphicon glyphicon-registration-mark' ></span><span title='NOT Relaunched' class='glyphicon glyphicon-remove red'></span>";}
                }

                echo "<tr class='$classRow'>
                        <td>".date('H:i:s', strtotime($fila['timestamp_init']))."</td>
                        <td>".$endTimestamp."</td>";
                if(!$shelf){echo "<td>".$fila['shelf']."</td>";}
                echo "<td>".$fila['model']."</td>
                        <td>".$fila['serial']."</td>
                        <td>".$relaunchText."</td>
                        <td>".$subtest."</td>
                        <td>".$errorMessage."</td>
                        <td>".$fila['uut_status']."</td>
                        <td>".$fila['intranet_customer_name']."</td>
                        <td><a href='log_visualizer.php?model=0&serial=&environment=0&shelf=0&date=&limit=1&testId=".$fila['id']."'>
                                <span title='log' class='glyphicon glyphicon-list-alt' ></span>
                            </a>
                        </td>
                    </tr>";
                $lastSerial=$fila['serial'];
            }
            $data_shelf->data_seek(0);  #Reinitialize pointer
?>
        </table>



        <div class="container">
        <table class="table" style="border:1px solid">
            <caption class="centrado">DIAGNÓSTICO REALIZADOS</caption>
            <tr class="active">
                <th style="border-top:1px solid;">MODEL</th>
                <th style="border-top:1px solid;">CUSTOMER</th>
                <th style="border-top:1px solid;">STATUS</th>
                <th style="border-top:1px solid;">OK</th>
                <th style="border-top:1px solid;">FAIL</th>
            </tr>
<?php
            $last_model="";
            while ($fila = $data_summary->fetch_assoc()) {
                if($fila['model']!=$last_model){$style=" style='border-top:1px solid' ";$last_model=$fila['model'];}
                else{$style="";}
                 $validated=$fila['num_validated'];
                 $fails=$fila['num_fails'];
                 $validated_percent=number_format((float)$validated/($validated+$fails)*100, 2, '.', '');
                 $fails_percent=number_format((float)$fails/($validated+$fails)*100, 2, '.', '');
                 echo "<tr>
                        <td ".$style.">".$fila['model']."</td>
                        <td ".$style.">".$fila['intranet_customer_name']."</td>
                        <td ".$style.">".$fila['uut_status']."</td>
                        <td ".$style.">".$validated." (".$validated_percent."%)</td>
                        <td ".$style.">".$fails." (".$fails_percent."%)</td>
                    </tr>";
             }
?>
        </table>
        </div>




        <div class="container">
        <table class="table" style="border:1px solid">
            <caption class="centrado">DETALLE DE AVERÍAS EQUIPOS DIAGNOSTICADOS</caption>
            <tr class="active">
                <th style="border-top:1px solid;">MODEL</th>
                <th style="border-top:1px solid;">CUSTOMER</th>
                <th style="border-top:1px solid;">STATUS</th>
                <th style="border-top:1px solid;">DIAGNOSTIC</th>
                <th style="border-top:1px solid;">QTY</th>
            </tr>
<?php
             $last_model="";
             while ($fila = $data_diag_summary->fetch_assoc()) {
                 if($fila['model']!=$last_model){$style=" style='border-top:1px solid' ";$last_model=$fila['model'];}
                 else{$style="";}
                 $fails=$fila['num_fails'];
                 echo "<tr>
                        <td ".$style.">".$fila['model']."</td>
                        <td ".$style.">".$fila['intranet_customer_name']."</td>
                        <td ".$style.">".$fila['uut_status']."</td>
                        <td ".$style.">".$fila['subtest']." - ".$fila['error']."</td>
                        <td ".$style.">".$fails."</td>
                    </tr>";
             }
?>
        </table>
        </div>






    </body>
</html>

<script>

$('#exportCSV').click(function() {
    $('#action').val('exportCSV');
    form=document.getElementById('dataForm')
    form.action="shelf-status_json.php";
    form.method='POST';
    $('#dataForm').submit();
    form.action="shelf-status.php";
    form.method='GET';
    $('#action').val('');
});

$('#today').click(function() {
    form=document.getElementById('dataForm')
    document.getElementById('initData').value="";
    document.getElementById('finishData').value="";
    $('#dataForm').submit();
});
</script>

