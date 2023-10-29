<?php

    include 'conf.php';
    include 'debug.php';
    $selected_accion = "Crear";
    $selected_name_user= '';
    $selected_user = '';
    $password="";
    $selected_admin=0;
    $usuario_seleccionado="";
    $id_departamento_selecionado= Array();
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
    if ($_SESSION['user_admin'] != 1 && $_SESSION['department_id'] != 1 ) {
        echo '<div class="alert alert-danger mt-4" role="alert">';
        echo '    <strong>No tienes permisos para acceder a esta secci&oacute;n</strong>';
        echo '    <p>';
        echo '       Avisa a Sistemas si necesitas acceder a esta Web.';
        echo '    </p>';
        echo '</div>';
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
if ($_SESSION['department_id'] == 1) {
    $peticion_sql = 'SELECT id,department FROM department;';
} else {
    $peticion_sql = 'SELECT id,department FROM department WHERE id = '.$_SESSION['department_id'].';';
}
$result = $mysqli->query($peticion_sql);
if ($result = $mysqli->query($peticion_sql)) {
    /* obtener un array asociativo */
    while ($fila = $result->fetch_assoc()) {
        $departamentos[] = $fila;
        unset($fila);
    }
    /* liberar el conjunto de resultados */
    mysqli_free_result($result);
}


$peticion_sql = 'SELECT v.id,v.name ,v.user,d.department,v.admin FROM validator.login  v
    LEFT JOIN department d
    ON v.department_id = d.id;';
$result = $mysqli->query($peticion_sql);
if ($result = $mysqli->query($peticion_sql)) {
    /* obtener un array asociativo */
    while ($fila = $result->fetch_assoc()) {
        $datos_user_totales[] = $fila;
        unset($fila);
    }
    /* liberar el conjunto de resultados */
    mysqli_free_result($result);
}


if ($_SESSION['department_id'] == 1) {
    $datos_usuarios = $datos_user_totales;
} else {
    $peticion_sql = 'SELECT v.id, v.name ,v.user,d.department,v.admin  FROM validator.login  v
    LEFT JOIN department d
    ON v.department_id = d.id WHERE department_id = '.$_SESSION['department_id'].';';
    $result = $mysqli->query($peticion_sql);
    if ($result = $mysqli->query($peticion_sql)) {
        /* obtener un array asociativo */
        while ($fila = $result->fetch_assoc()) {
            $datos_usuarios[] = $fila;
            unset($fila);
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($result);
    }
}
$array_user_totales = $array_user = array_combine(array_column($datos_user_totales, 'id'), array_column($datos_user_totales, 'user'));
$array_user = array_combine(array_column($datos_usuarios, 'id'), array_column($datos_usuarios, 'user'));
$array_datos_user = array_combine(array_column($datos_usuarios, 'id'), $datos_usuarios);
$array_departamentos = array_combine(array_column($departamentos, 'id'), array_column($departamentos, 'department'));

if (!empty($_GET)) {
    $usuario_seleccionado = (array_key_exists($_GET['id_usuario'], $array_user)) ? $_GET['id_usuario'] : "";
    if(in_array($usuario_seleccionado,array_keys($array_datos_user))){

        $selected_accion = "Modificar";
        $selected_name_user = $array_datos_user[$usuario_seleccionado]['name'];
        $selected_user = $array_datos_user[$usuario_seleccionado]['user'];
        $selected_admin = $array_datos_user[$usuario_seleccionado]['admin'];
        $id_departamento_selecionado = array_keys($array_departamentos,$array_datos_user[$usuario_seleccionado]['department']);
        $id_departamento_selecionado = empty($id_departamento_selecionado)? Array(2) :$id_departamento_selecionado;
        if ($_SESSION['department_id'] != 1) {
            $array_departamentos = Array($array_datos_user[$usuario_seleccionado]['department']);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($_POST['selector'])){
        if (empty($_POST['name']) || empty($_POST['user']) || empty($_POST['password']) || is_int($_POST['department'])) {
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong> Faltan datos obligatorios para crear el usuario.</strong>';
            echo '    <p>';
            echo '        Por favor, intentalo de nuevo.';
            echo '    </p>';
            echo '</div>';
            exit;
        }
        $user = $_POST['user'];
        $name_user = $_POST['name'];
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 12]);
        $department_id = $_POST['department'];
        $admin = ($_POST['admin'] != null) ? 1 : 0;
        if(in_array($user,array_values($array_user_totales))){
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong> Ya existe un usuario con ese nombre usuario.</strong>';
            echo '    <p>';
            echo '        Por favor, intentalo de nuevo.';
            echo '    </p>';
            echo '</div>';
            exit;
        }
        $peticion_sql = "INSERT INTO login (name,user,password,department_id,admin) VALUES('$name_user','$user','$hash',$department_id,$admin);";
        $result = $mysqli->query($peticion_sql);
        if($mysqli->error){
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong> No se ha podido añadir usuario.</strong>';
            echo '    <p>';
            echo '        Por favor, Avisa a Sistemas.';
            echo '    </p>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-success mt-4" role="alert">';
            echo '    <strong>Usuario creado con &eacute;xito.</strong>';
            echo '</div>';
        }
        mysqli_free_result($result);
    } else {
        $id_usuario=$_POST['selector'];
        $user = $_POST['user'];
        $name_user = $_POST['name'];
        $department_id = $_POST['department'];
        $admin=(!empty($_POST['admin']) && ($_SESSION['department_id'] != 1)) ? 0 : $_POST['admin'];
        $admin = ($_POST['admin'] != null) ? 1 : 0;
        if(!empty($_POST['password'])){
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 12]);
            $sql=",password='$hash'";
        }

        $id_user = array_search($user,$array_user_totales);
        if((!empty($id_user)) && ($id_user!= $id_usuario)){
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo '    <strong> No se a podido modificar el usuario.</strong>';
            echo '    <p>';
            echo '        Por favor, intentalo con otro nombre de Usuario  .';
            echo '    </p>';
            echo '</div>';
        }else{
            $peticion_sql ="UPDATE login
                            SET name='$name_user',user='$user',department_id=$department_id, admin=$admin $sql
                            WHERE id=$id_usuario;";
            $result = $mysqli->query($peticion_sql);
            if($mysqli->error){
                echo '<div class="alert alert-danger mt-4" role="alert">';
                echo '    <strong> No se ha podido modificar  usuario.</strong>';
                echo '    <p>';
                echo '        Por favor, Avisa a Sistemas.';
                echo '    </p>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-success mt-4" role="alert">';
                echo '    <strong>Usuario modificado con &eacute;xito.</strong>';
                echo '</div>';
            }
            mysqli_free_result($result);
        }
    }
    $_POST['name'] = '';
    $_POST['user'] = '';
    $_POST['password'] = '';
}
?>
        <p>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                    <form
                        class="form-signin"
                        action="user_management.php"
                        method="POST"
                    >
                        <label for="user">Acci&oacuten<em>*</em></label>
							<select class="form-control usuario_selecionado" id="selector" name="selector">
                                <option value="">Nuevo usuario</option>
<?php foreach ($array_user as $id => $user) { ?>
                                <option value="<?php echo "$id";?>" <?php echo (($id != $usuario_seleccionado) ?: "SELECTED=SELECTED"); ?> ><?php echo $user; ?></option>
<?php } ?>
							</select>
                        <div class="form-label-group">
                                <ol>
                                    <li>
                                        <label for="name">Nombre<em>*</em></label>
                                        <input name="name" id="name" class="form-control" value="<?php echo $selected_name_user; ?>" required/>
                                    </li>
                                    <li>
                                        <label for="user">Usuario<em>*</em></label>
                                        <input name="user" id="user" class="form-control" value="<?php echo $selected_user; ?>" required/>
                                    </li>
                                    <li>
                                        <label for="password">Contraseña</label>
                                        <input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>"
                                        <?php echo (!empty($usuario_seleccionado)?: "required")?>/>
                                    </li>
                                    <li>
                                        <label for="department">Departamento<em>*</em></label>
                                        <select class="form-control" id="department" name="department" >
<?php foreach ($array_departamentos as $id => $departmet) { ?>
                                    <option value="<?php echo $id; ?>"<?php echo (($id != $id_departamento_selecionado[0]) ?: "SELECTED=SELECTED"); ?>>
                                    <?php echo $departmet; ?></option>
<?php } ?>
                                        </select>
                                    </li>
<?php if ($_SESSION['department_id'] == 1) { ?>
                                    <li>
                                    <label> Administrador</label>
                                    <input type="checkbox" name="admin" value="1"  <?php echo (($selected_admin != 1) ?: "checked"); ?>> <br>
                                    </li>
<?php } ?>
                                </ol>
                        </div>
                        <br>
                        <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit"><?php echo $selected_accion; ?></button>
                    </form>
                </div>
            </div>
        </div>
        <script>
        $(document).on('change', ".usuario_selecionado", function() {
                var id_accion = $(this).val();
                console.log(id_accion);
                window.location.href = "user_management.php?id_usuario=" + id_accion;
            })
        </script>
    </body>
</html>