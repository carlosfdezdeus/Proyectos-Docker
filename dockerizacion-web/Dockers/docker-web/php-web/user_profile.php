<?php

    include 'conf.php';
    include 'debug.php';

    $context = true;
    foreach (['environment', 'shelves'] as $campo) {
        if (empty($_GET['environment'])) {
            $context = false;
        }
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

        <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
        <script type="text/javascript" src="js/libs/jquery.js"></script>
        <script type="text/javascript" src="js/libs/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="js/libs/jquery-ui-1.10.3.custom.min.js"></script>
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['password'])) {
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong>No se ha establecido nueva contrase&ntilde;a.</strong>';
            echo '    <p>';
            echo '        Por favor, intentalo de nuevo.';
            echo '    </p>';
            echo '</div>';
            exit;
        }
        if ($_POST['password'] != $_POST['password2']) {
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong>Las contrase&ntilde;as no coinciden.</strong>';
            echo '    <p>';
            echo '        Por favor, intentalo de nuevo.';
            echo '    </p>';
            echo '</div>';
        } else {
            $hash = password_hash($_POST['password2'], PASSWORD_DEFAULT, ['cost' => 12]);
            $sql = "UPDATE validator.login SET password = '".$hash."' WHERE id = ".$_SESSION['user_id'].';';
            $result = $mysqli->query($sql);
            mysqli_free_result($result);
            $_POST['password'] = '';
            $_POST['password2'] = '';
            echo '<div class="alert alert-success mt-4" role="alert">';
            echo '    <strong>Contrase&ntilde;a modificada con &eacute;xito.</strong>';
            echo '</div>';
        }
    }

?>
        <p>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <form class="form-signin" method="POST">
                        <div class="form-label-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Nueva contrase&ntilde;a" value="<?php echo $_POST['password']; ?>" required autofocus>
                        </div>
                        <br>
                        <div class="form-label-group">
                            <input type="password" id="password2" name="password2" class="form-control" placeholder="Repetir nueva contrase&ntilde;a" value="<?php echo $_POST['password2']; ?>" required>
                        </div>
                        <br>
                        <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Cambiar contrase&ntilde;a</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>