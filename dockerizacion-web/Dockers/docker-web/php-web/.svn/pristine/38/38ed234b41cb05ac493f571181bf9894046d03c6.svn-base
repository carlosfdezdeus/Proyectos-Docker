<?php

include('conf.php');

$bandeja= $_POST['var_1'];
$mensaje = $_POST['var_2'];
$operario = $_POST['var_3'];

if (empty($bandeja)) { 
  $bandeja = 0;
}

if (empty($operario)) { 
  $operario = 0;
}

// Create connection
$conn = mysqli_connect(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}
 
echo "Connected successfully";


if(isset($_POST['var_1'])) { 
$sql = "INSERT INTO logger_web_chat (user, bandeja, mensaje, fecha) VALUES ($operario, $bandeja, '$mensaje', NOW());";
}
if (mysqli_query($conn, $sql)) {
      echo "New record created successfully";
} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);

?>

