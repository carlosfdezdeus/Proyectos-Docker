<?php

include('conf.php');

$cable_shelf= $_POST['var_1'];
$changes_made = $_POST['var_2'];
$job_operator = $_POST['var_3'];

if (empty($cable_shelf)) { 
  $cable_shelf = 0;
}

if (empty($job_operator)) { 
  $job_operator = 0;
}

// Create connection
$conn = mysqli_connect(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}
 
echo "Connected successfully";


if(isset($_POST['var_1'])) { 
$sql = "INSERT INTO validator.logger_shelf_cables (user, id_shelf, json_changes, date) VALUES ($job_operator, $cable_shelf, '$changes_made', NOW());";
}
if (mysqli_query($conn, $sql)) {
      echo "New record created successfully";
      //shell_exec("/usr/bin/zabbix_sender -z 192.168.40.12 -s \"VALIDATOR_WEB_DEV\" -k cable_notification -o 1");

} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);

?>

