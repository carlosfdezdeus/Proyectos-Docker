<?php
/*
 * Gestiona las solicitudes AJAX
 */

// Importa los parametros de entorno
require 'conf.php';

// Arreglo de encode_json para la version 5.1 de apache
require 'jsonwrapper/jsonwrapper.php';

// Obtener un JSON con el estado de las bandejas
if (!empty($_POST['action']) && $_POST['action']=="exportCSV") {
    //The name of the CSV file that will be downloaded by the user.
    $shelf=$_POST['shelf'];
    $environment=strtoupper($_POST['environment']);
    $initData=$date = str_replace('/', '-', $_POST['initData']);
    $finishData=str_replace('/', '-',$_POST['finishData']);
    if($finishData==0 || $initData>$finishData){$finishData=$initData;}
    
    $mysqli = new mysqli(MySQL_SERVER, MySQL_USER, MySQL_PASSWORD, MySQL_DB);
        if ($mysqli->connect_errno) {
            echo "Falló la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            exit();
        }
    $data_shelf = getShelfData($mysqli, $environment, $shelf, $initData, $finishData);
    $mysqli->close();
    //Set the Content-Type and Content-Disposition headers.
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$_POST['environment'].'-'.$_POST['shelf'].'.csv');


    //Open up a PHP output stream using the function fopen.
    $fp = fopen('php://output', 'w');
    fputcsv($fp,array("TEST_ID","VALIDATED","DAY","TIMESTAMP_INIT","TIMESTAMP_FINISH","SHELF","MODEL","SERIAL","RELAUNCHED","SUBTEST","ERROR","STATUS","CUSTOMER_ID","CUSTOMER"),";");

    while ($fila = $data_shelf->fetch_assoc()) {
        fputcsv($fp, $fila,";");
    }

    //Close the file handle.
    fclose($fp);
}


function getEnvironmentShelfs($mysqli,$environment){
    $sql="SELECT distinct shelf
            FROM validator.validator_environment_shelf as ES
            INNER JOIN validator.validator_environment as VE
            ON VE.id=ES.id_environment
            where VE.name='".$environment."'";
    return($mysqli->query($sql));
}
function getEnvironments($mysqli){
    $sql="SELECT distinct 
                name as environment
            FROM validator.validator_environment_shelf as ES
            INNER JOIN validator.validator_environment as VE
            ON VE.id=ES.id_environment;";
    return($mysqli->query($sql));
}
function getShelfDiagnosticSummary($mysqli,$environment,$shelf,$initData,$finishData){
    $sql_time_condition=getTimeCondition($initData,$finishData);
    $sql_shelf=getShelfCondition($shelf);
    $sql="SELECT distinct
                VM.model,
                LT.intranet_customer_name,
                US.uut_status AS uut_status,
                ST.interface as subtest, 
                EM.error_message as error,
                count(*) as num_fails
            FROM validator.logger_test as LT
                inner join validator.logger_uut as LU
                on LU.id=LT.logger_uut_id
                inner join validator.validator_environment_model AS VM
                on LU.intranet_model_id=VM.intranet_model_id
                left join validator.validator_error_messages as EM
                on LT.error_message_id=EM.id
                left join validator.validator_subtest AS ST
                on LT.subtest_id=ST.id
                inner join validator.validator_uut_status as US
                on LT.uut_status_id=US.id
                inner join validator.validator_environment AS VE
                on LT.validator_environment_id=VE.id
            where VE.name='$environment'
                and LT.relaunched=0
                and validated=0 and ST.interface is not null ";
    $sql.=$sql_shelf;
    $sql.=$sql_time_condition;
    $sql.="group by model,intranet_customer_id,uut_status,subtest,error
            having (num_fails)>0
            order by model,intranet_customer_name,uut_status,subtest,error";
    return($mysqli->query($sql));
}

function getShelfDataSummary($mysqli,$environment,$shelf,$initData,$finishData){
    $sql_time_condition=getTimeCondition($initData,$finishData);
    $sql_shelf=getShelfCondition($shelf);
    $sql="SELECT distinct
                VM.model,
                LT.intranet_customer_name,
                US.uut_status AS uut_status,
                count(CASE when validated=1 then 1 end) as num_validated,
                count(CASE when LT.subtest_id is not null then 1 end) as num_fails
            FROM validator.logger_test as LT
                inner join validator.logger_uut as LU
                on LU.id=LT.logger_uut_id
                inner join validator.validator_environment_model AS VM
                on LU.intranet_model_id=VM.intranet_model_id
                inner join validator.validator_uut_status as US
                on LT.uut_status_id=US.id
                inner join validator.validator_environment AS VE
                on LT.validator_environment_id=VE.id
            where VE.name='$environment'
                and LT.relaunched=0";
    $sql.=$sql_shelf;
    $sql.=$sql_time_condition;
    $sql.="group by model,intranet_customer_id,uut_status
            having (num_validated+num_fails)>0
            order by model,intranet_customer_name,uut_status";
    return($mysqli->query($sql));
    
}

function getShelfData($mysqli,$environment,$shelf,$initData,$finishData){
    $sql_time_condition=getTimeCondition($initData,$finishData);
    $sql_shelf=getShelfCondition($shelf);
    $sql = "SELECT distinct
                LT.id,
                LT.validated,
                date(LT.timestamp_init) as day,
                LT.timestamp_init,
                LT.timestamp_finish,
                LT.validator_environment_shelf as shelf,
                VM.model,
                LU.serial_number as serial,
                LT.relaunched,
                ST.interface as subtest, 
                EM.error_message as error,
                US.uut_status AS uut_status,
                LT.intranet_customer_id, 
                LT.intranet_customer_name
            FROM validator.logger_test as LT
                inner join validator.logger_uut as LU
                on LU.id=LT.logger_uut_id
                inner join validator.validator_environment_model AS VM
                on LU.intranet_model_id=VM.intranet_model_id
                left join validator.validator_error_messages as EM
                on LT.error_message_id=EM.id
                left join validator.validator_subtest AS ST
                on LT.subtest_id=ST.id
                inner join validator.validator_uut_status as US
                on LT.uut_status_id=US.id
                inner join validator.validator_environment AS VE
                on LT.validator_environment_id=VE.id
            where VE.name='$environment'";
    $sql.=$sql_shelf;
    $sql.=$sql_time_condition;
    $sql.=" order by LT.id desc limit 1000";
    return($mysqli->query($sql));
}

function getShelfCondition($shelf){
    if($shelf){$sql_shelf=" and validator_environment_shelf=$shelf ";}
    else{$sql_shelf="";}
    return $sql_shelf;
}

function getTimeCondition($initData,$finishData){
    if($initData==0){
        $sql_time_condition="and date(timestamp_init)=CURDATE() ";
    }else{
        $sql_time_condition="and date(timestamp_init)>='".date('Y-m-d', strtotime($initData))."'
                and date(timestamp_init)<='".date('Y-m-d', strtotime($finishData))."'";
    }
    return($sql_time_condition);
}

?>

