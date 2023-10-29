<?php

include('conf.php');

$usuario= $_POST['var_1'];

$sql = "SELECT mensaje, fecha
                  FROM logger_web_chat
                  WHERE user = '".$usuario."'
                  ORDER BY id DESC LIMIT 5";
                 

$mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

// Perform query
if ($result = $mysqli -> query($sql)) {
 while( $rows = mysqli_fetch_assoc($result) ) { 
 echo "<tr>";
 echo "<td>";
 echo $rows["mensaje"];
 echo "</td>";
 echo " &nbsp;";
 echo "<td>";
 echo $rows["fecha"];
 echo "</td>";
 echo "</tr>";
 echo "<br>";
  }
  // Free result set
  $result -> free_result();
}

$mysqli -> close();


?>


