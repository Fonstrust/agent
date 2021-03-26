<?php
error_reporting('ALL');
session_start();
include_once('db.php');
require_once '../clases/mysql.php';
echo '!';
//require_once '../functions/php_func.php';



$db_passengers_data = new Mysql('passengers_data');
function passengerTest($u_id, $fio, $birth_date, $phone) {
    global $db_passengers_data;
//    return 'ХУЙ';
//    $arr = $db_passengers_data->testH();
    $arr = $db_passengers_data->getTable();
//    $arr = '523664';
//    $sql_u_pass = $db_passengers_data->getSome($u_id, 'agent_id');
//    while ($res_u_pass = mysqli_fetch_array($sql_u_pass)) {
//        $arr[] = $res_u_pass['fio'];
//    }
//    while ($res_u_pass = mysqli_fetch_array($sql_u_pass)) {
//        if ($res_u_pass['fio'] == $fio && $res_u_pass['birth_date'] == $birth_date) {
//            return $res_u_pass['ID'];
//        }
//        if ($res_u_pass['fio'] == $fio && $res_u_pass['phone'] == $phone) {
//            return $res_u_pass['ID'];
//        }
//        if ($res_u_pass['birth_date'] == $birth_date && $res_u_pass['phone'] == $phone) {
//            return $res_u_pass['ID'];
//        }
//    }
//    return false;
    return $arr;
}
//$r = passengerTest(3, 1, 1, 1);
//print_r($r);
$t = passengerTest(3,1,1,1);
echo $t;