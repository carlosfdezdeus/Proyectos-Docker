<?php

    include('conf.php');
    include('user_login.php');
    include('debug.php');
    include('libs/mysqli_result_2_table.php');

    $filtro_sql = " AND (login.id = ". $_SESSION['user_id'] .") ";
    if ($_SESSION['user_admin'] == 1) {
        $filtro_sql = ' AND login.name is not null ';
    }

    // Conectamos a la DB
	$mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
	if ($mysqli->connect_errno) {
		echo '<div class="alert alert-danger">';
		echo '<strong>Error:</strong> ';
		echo "Fall&oacute; la conexi&oacute;n a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		echo '</div>';
		exit();
    }

	$sql = "SELECT login.name AS USUARIO
                ,LU.serial_number AS SERIAL
                ,VEM.model AS MODELO
                ,LT.timestamp_init AS HORA_DE_LANZADA
                ,LT.timestamp_finish AS FINAL_DE_LANZADA
                ,VE.name AS ENTORNO
                ,LT.validator_environment_shelf AS BANDEJA
                ,LT.commited AS VALIDADO
                ,IF (LT.commited=1,ifnull(VS.name,'VALIDADO'),ifnull(VS.name,'UNCOMPLETE')) as ERROR
                ,ifnull(VERR.error_message, '-----') AS MENSAJE_ERROR
                ,LT.relaunched AS RELANZADO_POR_SCRIPT
                ,ifnull(TIMESTAMPDIFF(MINUTE, LT.timestamp_init, LT.timestamp_finish), 'UNCOMPLETE') AS TIEMPO_LANZADA
            FROM validator.logger_test AS LT
            LEFT JOIN login ON LT.validator_user_id = login.id
            LEFT JOIN logger_uut AS LU ON LU.id = LT.logger_uut_id
            LEFT JOIN validator_subtest AS VS ON VS.id = LT.subtest_id
            LEFT JOIN validator_environment AS VE ON VE.id = LT.validator_environment_id
            LEFT JOIN validator_error_messages AS VERR ON VERR.id = LT.error_message_id
            LEFT JOIN validator_environment_model AS VEM ON VEM.intranet_model_id = LU.intranet_model_id
            WHERE LT.id >= (
                    SELECT MIN(id)
                    FROM validator.logger_test
                    WHERE DATE (timestamp_init) = CURDATE() limit 1
                    )
                " . $filtro_sql . "
                AND VEM.logistic_id = 1
            ORDER BY timestamp_init DESC;";
    $results = $mysqli->query($sql);
    $tabla_statistics = mysqli_result_2_table($results);

    $sql = "SELECT login.name as USUARIO,
				VL.name as LOGISTICA,
				VEM.model as MODELO,
				count(logger_uut_id) - count(DISTINCT logger_uut_id)  as TOTAL
			FROM validator.logger_test as LT
				left join login as login on LT.validator_user_id = login.id
				left join logger_uut as LU on LU.id = LT.logger_uut_id
				left join validator_environment_model as VEM on VEM.intranet_model_id = LU.intranet_model_id
				left join validator_logistic as VL on VL.id = VEM.logistic_id
			WHERE LT.id>= (SELECT MIN(id) FROM validator.logger_test where DATE(timestamp_init) = CURDATE() limit 1)
			AND VEM.logistic_id=1
			AND logger_uut_id in(
				SELECT logger_uut_id FROM validator.logger_test as LT left join login as login on LT.validator_user_id = login.id
				where LT.id >= (SELECT MIN(id) FROM validator.logger_test where DATE(timestamp_init) = CURDATE() limit 1)
				AND (login.id = ". $_SESSION['user_id'] .")
			)
			AND (login.id = ". $_SESSION['user_id'] .")
			GROUP by VEM.model WITH ROLLUP";
    $results = $mysqli->query($sql);
    $tabla_relaunch = mysqli_result_2_table($results);

	$sql = "SELECT login.name as USUARIO,
                VEM.model as MODELO,
            count(VEM.model) as TOTAL
            FROM validator.logger_test as LT
            left join login as login on LT.validator_user_id = login.id
            left join logger_uut as LU on LU.id = LT.logger_uut_id
            left join validator_environment_model as VEM on VEM.intranet_model_id = LU.intranet_model_id
            left join validator_logistic as VL on VL.id = VEM.logistic_id
            WHERE LT.id>=(SELECT MIN(id) FROM validator.logger_test where DATE(timestamp_init) = CURDATE() limit 1) AND VEM.logistic_id=1
                AND LT.id in (
                SELECT MAX(id) FROM validator.logger_test
                where id >= (SELECT MIN(id) FROM validator.logger_test where DATE(timestamp_init) = CURDATE() limit 1)
                group by logger_uut_id
                )
                " . $filtro_sql . "
            GROUP by USUARIO,MODELO WITH ROLLUP;";
    $results = $mysqli->query($sql);
    $tabla_resumen = mysqli_result_2_table($results);

?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="es"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="es"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="es"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="es"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="GUI para Validator">
        <meta name="author" content="Oscar Zapata">

        <title>Validator GUI</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/validatorGUI.css">
        <link rel="stylesheet" href="css/jquery.validationEngine.css">

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
    </head>
    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
    <script type="text/javascript" src="js/libs/jquery.js"></script>
    <script type="text/javascript" src="js/libs/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/libs/jquery-ui-1.10.3.custom.min.js"></script>
    <body>

<?php

    include('navbar.php');

    if (!isset($_SESSION['user_id'])) {
        echo '<div class="alert alert-info mt-4" role="alert">';
        echo '    <strong>Es necesario identificarse para usar la aplicaci&oacute;n.</strong>';
        echo '    <p>';
        echo '        Por favor, logueate empleando el formulario de la parte superior izquierda.';
        echo '    </p>';
        echo '</div>';
        exit();
    }

?>
        <p>
        <section id="tabs" class="project-tab">
            <div class="container-flow">
                <div class="row">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Resumen</a>
                                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile-rel" role="tab" aria-controls="nav-profile" aria-selected="false">Relanzadas</a>
                                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Detalle</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
<?php
    echo $tabla_resumen;
?>                          </div>
							<div class="tab-pane fade" id="nav-profile-rel" role="tabpanel" aria-labelledby="nav-profile-tab">
<?php
    echo $tabla_relaunch;
?>                          </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
<?php
    echo $tabla_statistics;
?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
