<?php
error_reporting('ALL');
session_start();
include_once('db.php');
require_once '../clases/mysql.php';
require_once '../clases/town.php';
require_once '../clases/users.php';
require_once '../functions/php_func.php';
$user = new User($_SESSION['id']);


$db_towns = new Mysql('towns');
$pr_db = new Mysql('prices');
$msk_db = new Mysql('destinations');
$int_db = new Mysql('intervals');
$db_dest_on = new Mysql('dest_on');
$db_tarifs = new Mysql('tarifs');
$db_depart_points = new Mysql('depart_points');
$db_time_to = new Mysql('time_to_msk');
$db_obl_towns_price = new Mysql('obl_towns_price');
$db_obl_towns = new Mysql('obl_towns');
$db_shedule = new Mysql('schedule');
$db_transfers = new Mysql('new_transfers');
$db_passengers = new Mysql('passengers_data');
$db_pay_control = new Mysql('pay_control');
$db_comision = new Mysql('comisions');
$db_adress_prices = new Mysql('adress_cost');
$db_bag = new Mysql('bag_cost');
$db_pko = new Mysql('pko');
$db_count = new Mysql('count');
    
$sql_dest_cols = $msk_db->getAll();
$price_cols = [];
while ($res = mysqli_fetch_array($sql_dest_cols)) {
    $price_cols[] = $res['name_en'];
}
if (isset($_POST['get_comissions'])) {
    $result = [];
    $sql = $db_comision->getSome2($user->getQIndex(), 'town', $user->getIntId(), 'int_id');
    while ($res = mysqli_fetch_array($sql)) {
        $result[$res['type']] = $res['value'];
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['check_redact'])) {
    $check = $_POST['check_redact'];
    $user->reSetIntIdByTransfer($check);
}
if (isset($_POST['get_ru_dests'])) {
    $en_dests = explode(',', $_POST['get_ru_dests']);
    $result = [];
    foreach ($en_dests as $dest) {
        $sql = $msk_db->getSome($dest, 'name_en');
        $res = mysqli_fetch_assoc($sql);
        $result[] = $res['name_ru'];
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['filter_id_docs'])) {
    $doc_id = $_POST['filter_id_docs'];
    $limit = $_POST['filter_id_docs_limit'];
    $sql_all_pays = $db_pko->getUnionSome2('pko', 'count', $limit, $doc_id, 'ID', $_SESSION['id'], 'user_id_debt');
    $cols =  ["ID", "create_time", "user_id_create", "user_type_create", "transfers", "doc_sum", "user_id_debt", "done", "done_time", "type"];        
    while ($res_all_pays = mysqli_fetch_array($sql_all_pays)) {
        $res = [];
        foreach ($cols as $col) {
            if ($col == 'create_time') {
                $res[$col] = date('d.m.Y / H:i:s', strtotime($res_all_pays[$col]));
            } elseif ($col == 'user_id_debt') {
                $res[$col] = $user->getUserName($res_all_pays[$col]);
            } else {
                $res[$col] = $res_all_pays[$col];
            }
        }
        $result[] = $res;
    }
    if (count($result) == 0) {
        $result = 'none';
        $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $json_result;
    } else {
        $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $json_result;            
    }
    
}
if (isset($_POST['get_pay_docs'])) {
    $result = [];
    $limit = 100;
    $sql_all_pays = $db_pko->getUnionSome('pko', 'count', $limit, $_SESSION['id'], 'user_id_debt');
    $cols =  ["ID", "create_time", "user_id_create", "user_type_create", "transfers", "doc_sum", "user_id_debt", "done", "done_time", "type"];        
    while ($res_all_pays = mysqli_fetch_array($sql_all_pays)) {
        $res = [];
        foreach ($cols as $col) {
            if ($col == 'create_time') {
                $res[$col] = date('d.m.Y / H:i:s', strtotime($res_all_pays[$col]));
            } elseif ($col == 'user_id_debt') {
                $res[$col] = $user->getUserName($res_all_pays[$col]);
            } else {
                $res[$col] = $res_all_pays[$col];
            }
        }
        $result[] = $res;
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;   
}

if (isset($_POST['filter_pay_docs'])) {
    $filter_type = $_POST['filter_pay_docs'];
    $limit = $_POST['filter_pay_docs_limit'];
    $result = [];
    if ($filter_type == 'all') {
        $sql_all_pays = $db_pko->getUnionSome('pko', 'count', $limit, $_SESSION['id'], 'user_id_debt');
        $cols =  ["ID", "create_time", "user_id_create", "user_type_create", "transfers", "doc_sum", "user_id_debt", "done", "done_time", "type"];        
        while ($res_all_pays = mysqli_fetch_array($sql_all_pays)) {
            $res = [];
            foreach ($cols as $col) {
                if ($col == 'create_time') {
                    $res[$col] = date('d.m.Y / H:i:s', strtotime($res_all_pays[$col]));
                } elseif ($col == 'user_id_debt') {
                    $res[$col] = $user->getUserName($res_all_pays[$col]);
                } else {
                    $res[$col] = $res_all_pays[$col];
                }
            }
            $result[] = $res;
        }
        if (count($result) == 0) {
            $result = 'none';
            $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $json_result;
        } else {
            $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $json_result;            
        }
    } elseif ($filter_type == 'pko' || $filter_type == 'count') {
        if ($filter_type == 'pko') {
            $doc_db = $db_pko;
        } elseif ($filter_type == 'count') {
            $doc_db = $db_count;
        }
        $sql_all_pays = $doc_db->getAllOrderLimit('create_time', 'DESC', $limit);
        $cols =  ["ID", "create_time", "user_id_create", "user_type_create", "transfers", "doc_sum", "user_id_debt", "done", "done_time", "type"];
        while ($res_all_pays = mysqli_fetch_array($sql_all_pays)) {
            $res = [];
            foreach ($cols as $col) {
                if ($col == 'create_time') {
                    $res[$col] = date('d.m.Y / H:i:s', strtotime($res_all_pays[$col]));
                } elseif ($col == 'user_id_debt') {
                    $res[$col] = $user->getUserName($res_all_pays[$col]);
                } else {
                    $res[$col] = $res_all_pays[$col];
                }
            }
            $result[] = $res;
        }
        if (count($result) == 0) {
            $result = 'none';
            $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $json_result;
        } else {
            $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $json_result;            
        }
    }
}
if (isset($_POST['add_pay_doc'])) {
    $type = $_POST['add_pay_doc'];
    $data = json_decode($_POST['add_pay_doc_vals']);
    $user_id = $_SESSION['id'];
    $pko_sum = 0;
    foreach ($data as $key => $value) {
        $pko_sum += (int)$value;
    }
    if ($type == 'add_pko') {
        $vals = ['', date('Y-m-d H:i:s'), $_SESSION['id'], 'agent', json_encode($data, JSON_UNESCAPED_UNICODE), $pko_sum, $user_id, 0, '', 'pko'];
        $db_pko->dbOptInsert($vals);
    } elseif ($type == 'add_count') {
        $vals = ['', date('Y-m-d H:i:s'), $_SESSION['id'], 'agent', json_encode($data, JSON_UNESCAPED_UNICODE), $pko_sum, $user_id, 0, '', 'count'];
        $db_count->dbOptInsert($vals);
    }
//    $result = [$type, $data, $user_id];
    $result = [$type, $data, $user_id];
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_user_unpaed_transfers'])) {
    $sql_debt_orders = $user->getDebtTransfers();
    $result = [];
    $cols = ['ID', 'trans_id', 'pay_deadline', 'cost', 'comision', 'for_pay', 'first_pass', 'pass_id', 'add_pay'];
    while ($res_debt_orders = mysqli_fetch_array($sql_debt_orders)) {
        $res = [];
        $for_pay = (int)$res_debt_orders['cost'] - (int)$res_debt_orders['comision'];
        foreach ($cols as $col) {
            if ($col == 'pay_deadline') {
                $res[$col] = date('d.m.Y / H:i:s', strtotime($res_debt_orders['pay_deadline']));
                $res['create_time'] = date('d.m.Y / H:i:s', strtotime($user->getTransferCreateTime($res_debt_orders['trans_id'])));
            } elseif ($col == 'pass_id') {
                $res[$col] = $user->getUserPassenger($res_debt_orders['pass_id'])['fio'];
            } elseif ($col == 'for_pay') {
                $res[$col] = $for_pay;
            } else {
                $res[$col] = $res_debt_orders[$col];
            }
            
        }
        $result[] = $res;
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['get_transfer_logs'])) {
    $trans_id = $_POST['get_transfer_logs'];
    $log = file("logs/" . $trans_id . ".txt");
    $cols = $user->showCols('new_transfers');
    $fields_num = count($cols);
    $result = [];
    $i = 0;
    foreach ($log as $l) {
        $str = trim($l);
        if ($str == 'to_msk') {
            $str = 'в Москву';
        }
        if ($str == 'from_msk') {
            $str = 'из Москвы';
        }
        if ($str == 'noobl') {
            $str = 'Центр';
        }        
        if ($cols[$i-1] == 'u_id') {     
            $u_name = $user->getUserName($str);
            $str = $u_name;
        }
        if ($str == 'log_start') {
            $res = [];
            $i = 0;
        } elseif ($str == 'log_end') {
            $result[] = $res;
        } else {
            $res[$cols[$i-1]] = $str;
        }
        $i++;
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['get_user_pass_data'])) {
    $pass_id = $_POST['get_user_pass_data'];
    $result = $user->getUserPassenger($pass_id);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['get_old_transfer'])) {
    $cols = $user->showCols('new_transfers');
    $old_transfer_id = $_POST['get_old_transfer'];
    $sql_transfer = $user->getTransferById($old_transfer_id);
    $res_transfer = mysqli_fetch_assoc($sql_transfer);
//    $result[$cols[3]] = $res_transfer[$cols[3]];
    $result = [];
    foreach ($cols as $col) {
        $result[$col] = $res_transfer[$col];
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['ajax_test_post'])) {
    $result = $_SESSION['id'];
    
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['add_transfer_order'])) {
    $u_id = $_SESSION['id'];
    $create_time = date('Y-m-d H:i:s', time());
    $redact_mode = $_POST['add_transfer_order_redact_mode'];
    
    $type = $_POST['add_transfer_order'];
    $direction = $_POST['add_transfer_order_direction'];
    $passengers_nums = explode(',', $_POST['add_transfer_order_passengers']);
    $u_comment = $_POST['add_transfer_order_u_comment'];
    $cost = $_POST['add_transfer_order_pay'];
    $num_adlt = $passengers_nums[0];
    $num_chld = $passengers_nums[1];
    $num_chld_seat = $passengers_nums[2];
    $comisions = $user->getUserComisions();
    $add_pay_flag = 0;
    $add_pay = 0;
    $old_cost = 0;
    $old_comision = 0;
    //===============================================================Сравнение цен, доп плата
    if ($redact_mode != 'off') {
        $sql_payed = $db_pay_control->getSome($redact_mode, 'trans_id');
        $res_payed = mysqli_fetch_assoc($sql_payed);
        if ($res_payed['payed'] == 1) {
            $add_pay_flag = 1;
            $old_cost = $res_payed['cost'];
            $old_comision = $res_payed['comision'];
        }
        $user->reSetIntIdByTransfer($redact_mode);
    }
    if ($type == 'ind') {
//===================================IND===================================================
        $ind_type = $_POST['add_transfer_order_ind_type'];
        $ind_dest = $_POST['add_transfer_order_ind_dest'];
        $ind_dest_sub_point = $_POST['add_transfer_order_ind_dest_sub_point'];
        if ($ind_dest_sub_point == 'no_val') {
            $msk_point = $ind_dest;
        } else {
            $msk_point = $ind_dest . '-' . $ind_dest_sub_point;
        }
        
        $air_reis = $_POST['add_transfer_order_air_reis'];
        $msk_adress = 'noadress';
        if (isset($_POST['add_transfer_order_msk_adress'])) {
            $msk_adress = $_POST['add_transfer_order_msk_adress'];
        }
        $obl_town = $_POST['add_transfer_order_ind_type_obl_town'];
        $adress = $_POST['add_transfer_order_ind_type_adress'];
        $dates = $_POST['add_transfer_order_ind_type_dep_arrive_dates'];
        $pass_data = explode(',', $_POST['add_transfer_order_ind_type_pass_data']);
        $dates_arr = explode('_', $dates);
        $dates_format_arr = [];
        foreach ($dates_arr as $item) {
            $format = date('Y-m-d H:i:s', strtotime($item));
            $dates_format_arr[] = $format;
        }
        $deadline = $dates_format_arr[0];
        $depart_date = $dates_format_arr[1];
        $arrive_date = $dates_format_arr[2];
        $pass_db_data = [''];
        $i = 0;
        foreach ($pass_data as $pass) {
            $pass_db_data[] = $pass;
            $i++;
        }
        $pass_db_data[] = 'no_email';
        $pass_db_data[] = '';
        $pass_db_data[] = $u_id;
        $old_passenger = passengerTest($u_id, $pass_data[0], $pass_data[1], $pass_data[2]);
//        $old_passenger = [$u_id, $pass_data[0], $pass_data[1], $pass_data[2]];
        if ($old_passenger == false) {
            $db_passengers->dbOptInsert($pass_db_data);
            $last_pass_id = $db_passengers->getLastId();
        } else {
            $red_pass_fields = ['fio', 'birth_date', 'phone', 'passport'];
            $red_pass_fields_num = count($red_pass_fields);
            for ($i = 0; $i < $red_pass_fields_num; $i++) {
                if ($red_pass_fields[$i] == 'passport'){
                    if ($pass_data[$i] != 'no_value') {
                        $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass_data[$i], 'ID', $old_passenger);
                    }
                } else {
                    $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass_data[$i], 'ID', $old_passenger);
                }
            }
            $last_pass_id = $old_passenger;
        }
        
        
        $transfer_cols = 'ID, u_id, create_time, type, direction, adlt_num, chld_num, u_comment, tarif, msk_point, air_reis, obl_town, adress, deadline, depart_time, passenger';
        $vals = ['', $u_id, date('Y-m-d H:i:s'), $type, $direction, $num_adlt, $num_chld, $num_chld_seat, $u_comment, $ind_type, $msk_point, $air_reis, $obl_town, $adress, $deadline, $depart_date, $last_pass_id, $user->getIntId(), $user->getQIndex(), $msk_adress, 0, $arrive_date, 0];
        
        
        
        if ($redact_mode != 'off') {
            $sql_par_u_id = $db_transfers->getSome($redact_mode, 'ID');
            $res_par_u_id = mysqli_fetch_assoc($sql_par_u_id);
            $par_u_id = $res_par_u_id['u_id'];
            $create_time = $_POST['add_transfer_order_redact_mode_create_time'];
            $vals_r = ['', $par_u_id, $create_time, $type, $direction, $num_adlt, $num_chld, $num_chld_seat, $u_comment, $ind_type, $msk_point, $air_reis, $obl_town, $adress, $deadline, $depart_date, $last_pass_id, $user->getIntId(), $user->getQIndex(), $msk_adress, 0, $arrive_date, 0];
            $db_transfers->dbOptUpdate('ID', $redact_mode, $vals_r);
            $last_transfer_id = $redact_mode;
            if ($add_pay_flag == 1) {
                $old_pay = $old_cost - $old_comision;
                $new_pay = $cost - $comisions['ind'];
                $add_pay = $new_pay - $old_pay;
                if ($add_pay == 0) {
                    $payed = true;
                } else {
                    $payed = false;
                }
            }
            
            $pay_vals = ['', $last_transfer_id, $depart_date, $par_u_id, $payed, $cost, $comisions['ind'], '', '', false, $last_pass_id, $add_pay];
            $db_pay_control->dbOptUpdate('trans_id', $last_transfer_id, $pay_vals);
            
        } else {
            $db_transfers->dbOptInsert($vals);
            $last_transfer_id = $db_transfers->getLastId();
//            $pay_cols = $db_pay_control->showCols();
            
            $pay_vals = ['', $last_transfer_id, $depart_date, $u_id, false, $cost, $comisions['ind'], '', '', false, $last_pass_id];
            $db_pay_control->dbOptInsert($pay_vals);
        }
        
        if ($old_passenger == false) {
            $sql_old_pass = $db_passengers->getSome($last_pass_id, 'ID');
            $res_old_pass = mysqli_fetch_assoc($sql_old_pass);
            $pass_transfers = $last_transfer_id;
            $db_passengers->dbUpdateOne('transfers', $pass_transfers, 'ID', $last_pass_id);
        } else {
            $sql_old_pass = $db_passengers->getSome($old_passenger, 'ID');
            $res_old_pass = mysqli_fetch_assoc($sql_old_pass);
            if ($redact_mode == 'off') {
                $pass_transfers = $res_old_pass['transfers'] . ',' . $last_transfer_id;
                $db_passengers->dbUpdateOne('transfers', $pass_transfers, 'ID', $old_passenger);
            }
        }
        $log = fopen('logs/' . $last_transfer_id . '.txt', "a");
        $cols = $user->showCols('new_transfers');
        $i = 0;
        $write = fwrite($log, 'log_start' . "\r\n");
        foreach ($cols as $col) {
            if ($col == 'ID') {
                $write = fwrite($log, $last_transfer_id . "\r\n");
            } elseif ($col == 'create_time') {
                $write = fwrite($log, date('d.m.Y / H:i:s') . "\r\n");
            } elseif ($col == 'deadline') {
                $write = fwrite($log, date('d.m.Y / H:i:s', strtotime($vals[$i])) . "\r\n");
            }  else {
                $write = fwrite($log, $vals[$i] . "\r\n");
            }
            $i++;
        }
        $write = fwrite($log, 'log_end' . "\r\n");
        fclose($log);
        unset($write);
//        file_put_contents('logs/log1.txt', print_r(date('d.m.Y / H:i'), true) . PHP_EOL, FILE_APPEND);
        $json_result = json_encode($last_transfer_id, JSON_UNESCAPED_UNICODE);
        echo $json_result;
    } elseif ($type == 'group') {
//===================================GROUP==================================================
        $group_dest = $_POST['add_transfer_order_group_dest'];
        $group_dest_sub_point = $_POST['add_transfer_order_group_dest_sub_point'];
        $add_bag = $_POST['add_transfer_order_add_bag'];
//        $group_adress = $_POST['add_transfer_order_group_type_adress'];
        if ($group_dest_sub_point == 'no_val') {
            $msk_point = $group_dest;
        } else {
            $msk_point = $group_dest . '-' . $group_dest_sub_point;
        }
        $air_reis = $_POST['add_transfer_order_air_reis'];
//        $town_point_flag = 0;
//        if ($group_adress == 'noval') {
//            $town_point_flag = 1;
//            $group_town_point = $_POST['add_transfer_order_group_type_town_point'];
//        } else {
//            $group_town_point = $group_adress;
//        }
        
        
        
//        $pass_data = explode(',', $_POST['add_transfer_order_group_type_pass_data']);
        $pass_data = json_decode($_POST['add_transfer_order_group_type_pass_data']);
        $keys = ['fio', 'birth_date', 'phone', 'passport_num'];
        $j = 0;
        $passengers = [];
        $cost_str_arr = explode(',', $_POST['add_transfer_order_pay']);
        $cost = [];
        foreach ($cost_str_arr as $str) {
            $cost[] = (int)$str;
        }
        foreach($pass_data as $pass) {
            if ($j != 0) {
                $add_bag = 0;           
            }
            $single_pass = ['', $pass->fio, $pass->birth_date, $pass->phone, $pass->passport_num, 'no_email', '', $u_id];
            $old_passenger = passengerTest($u_id, $single_pass[1], $single_pass[2], $single_pass[3]);
            $group_town_point = $pass->adress;
            $dates = $pass->dep_arrive;
            $dates_arr = explode('_', $dates);
            $dates_format_arr = [];
            foreach ($dates_arr as $item) {
                $format = date('Y-m-d H:i:s', strtotime($item));
                $dates_format_arr[] = $format;
            }
            $deadline = $dates_format_arr[0];
            $depart_date = $dates_format_arr[1];
            $arrive_date = $dates_format_arr[2];
            if ($old_passenger == false) {
                $db_passengers->dbOptInsert($single_pass);
                $last_pass_id = $db_passengers->getLastId();
                if ($pass->chld == true) {
                    $num_adlt = 0;
                    $num_chld = 1;
                    $cost_g = $cost[1];
                    $first_group_pass = 0;
                    $comis_group = $comisions['groupchild'];
                } else {
                    if ($redact_mode == 'off') {
                        if ($j == 0) {
                            $cost_g = $cost[0] + $cost[2];
                            $first_group_pass = 1;
                            $j++;
                        } else {
                            $cost_g = $cost[0];
                            $first_group_pass = 0;
                        }
                    } else {
                        $sql_first_pass = $db_pay_control->getSome($redact_mode, 'trans_id');
                        $res_first_pass = mysqli_fetch_array($sql_first_pass);
                        $first_pass = $res_first_pass['first_pass'];
                        if ($first_pass == 1) {
                            $cost_g = $cost[0] + $cost[2];
                            $first_group_pass = 1;
                        } else {
                            $cost_g = $cost[0];
                            $first_group_pass = 0;
                        }
                    }
                    $num_adlt = 1;
                    $num_chld = 0;
                    $comis_group = $comisions['groupadlt'];
                }
                
                $vals = ['', $u_id, date('Y-m-d H:i:s'), $type, $direction, $num_adlt, $num_chld, 0, $u_comment, 'group', $msk_point, $air_reis, 'noobl', $group_town_point, $deadline, $depart_date, $last_pass_id, $user->getIntId(), $user->getQIndex(), 'noadr', $add_bag, $arrive_date, 0];
                
                if ($redact_mode != 'off') {
                    $sql_par_u_id = $db_transfers->getSome($redact_mode, 'ID');
                    $res_par_u_id = mysqli_fetch_assoc($sql_par_u_id);
                    $par_u_id = $res_par_u_id['u_id'];
                    $create_time = $_POST['add_transfer_order_redact_mode_create_time'];
                    $vals_r = ['', $par_u_id, $create_time, $type, $direction, $num_adlt, $num_chld, 0, $u_comment, 'group', $msk_point, $air_reis, 'noobl', $group_town_point, $deadline, $depart_date, $last_pass_id, $user->getIntId(), $user->getQIndex(), 'noadr', $add_bag, $arrive_date, 0];
                    $db_transfers->dbOptUpdate('ID', $redact_mode, $vals_r);
                    $last_transfer_id = $redact_mode;
                    $pay_vals = ['', $last_transfer_id, $depart_date, $par_u_id, false, $cost_g, $comis_group, '', '', $first_group_pass, $last_pass_id];
                    $db_pay_control->dbOptUpdate('trans_id', $last_transfer_id, $pay_vals);
                } else {
                    $db_transfers->dbOptInsert($vals);
                    $last_transfer_id = $db_transfers->getLastId();
                    $pay_vals = ['', $last_transfer_id, $depart_date, $u_id, false, $cost_g, $comis_group, '', '', $first_group_pass, $last_pass_id];
                    $db_pay_control->dbOptInsert($pay_vals);
                }
                
                
                $sql_old_pass = $db_passengers->getSome($last_pass_id, 'ID');
                $res_old_pass = mysqli_fetch_assoc($sql_old_pass);
                $pass_transfers = $last_transfer_id;
                $db_passengers->dbUpdateOne('transfers', $pass_transfers, 'ID', $last_pass_id);
                
                $log = fopen('logs/' . $last_transfer_id . '.txt', "a");
                $cols = $user->showCols('new_transfers');
                $i = 0;
                $write = fwrite($log, 'log_start' . "\r\n");
                foreach ($cols as $col) {
                    if ($col == 'ID') {
                        $write = fwrite($log, $last_transfer_id . "\r\n");
                    } elseif ($col == 'create_time') {
                        $write = fwrite($log, date('d.m.Y / H:i:s') . "\r\n");
                    } elseif ($col == 'deadline') {
                        $write = fwrite($log, date('d.m.Y / H:i:s', strtotime($vals[$i])) . "\r\n");
                    }  else {
                        $write = fwrite($log, $vals[$i] . "\r\n");
                    }
                    $i++;
                }
                $write = fwrite($log, 'log_end' . "\r\n");
                fclose($log);
                unset($write);
            } else {
                if ($pass->chld == true) {
                    $num_adlt = 0;
                    $num_chld = 1;
                    $cost_g = $cost[1];
                    $first_group_pass = 0;
                    $comis_group = $comisions['groupchild'];
                } else {
                    if ($redact_mode == 'off') {
                        if ($j == 0) {
                            $cost_g = $cost[0] + $cost[2];
                            $first_group_pass = 1;
                            $j++;
                        } else {
                            $cost_g = $cost[0];
                            $first_group_pass = 0;
                        }
                    } else {
                        $sql_first_pass = $db_pay_control->getSome($redact_mode, 'trans_id');
                        $res_first_pass = mysqli_fetch_array($sql_first_pass);
                        $first_pass = $res_first_pass['first_pass'];
                        if ($first_pass == 1) {
                            $cost_g = $cost[0] + $cost[2];
                            $first_group_pass = 1;
                        } else {
                            $cost_g = $cost[0];
                            $first_group_pass = 0;
                        }
                    }
                    $num_adlt = 1;
                    $num_chld = 0;   
                    $comis_group = $comisions['groupadlt'];
                }
                $red_pass_fields = ['fio', 'birth_date', 'phone', 'passport'];
                $red_pass_fields_num = count($red_pass_fields);
                for ($i = 0; $i < $red_pass_fields_num; $i++) {
                    if ($red_pass_fields[$i] == 'passport'){
                        if ($pass->passport_num != 'no_value') {
                            $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass->passport_num, 'ID', $old_passenger);
                        }
                    }
                    if ($red_pass_fields[$i] == 'fio') {
                        $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass->fio, 'ID', $old_passenger);
                    }
                    if ($red_pass_fields[$i] == 'birth_date') {
                        $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass->birth_date, 'ID', $old_passenger);
                    }
                    if ($red_pass_fields[$i] == 'phone') {
                        $db_passengers->dbUpdateOne($red_pass_fields[$i], $pass->phone, 'ID', $old_passenger);
                    }
                }
                $last_pass_id = $old_passenger;
                $vals = ['', $u_id, date('Y-m-d H:i:s'), $type, $direction, $num_adlt, $num_chld, 0, $u_comment, 'group', $msk_point, $air_reis, 'noobl', $group_town_point, $deadline, $depart_date, $old_passenger, $user->getIntId(), $user->getQIndex(), 'noadr', $add_bag, $arrive_date, 0];
                if ($redact_mode != 'off') {
                    $sql_par_u_id = $db_transfers->getSome($redact_mode, 'ID');
                    $res_par_u_id = mysqli_fetch_assoc($sql_par_u_id);
                    $par_u_id = $res_par_u_id['u_id'];
                    $create_time = $_POST['add_transfer_order_redact_mode_create_time'];
                    $vals_r = ['', $par_u_id, $create_time, $type, $direction, $num_adlt, $num_chld, 0, $u_comment, 'group', $msk_point, $air_reis, 'noobl', $group_town_point, $deadline, $depart_date, $old_passenger, $user->getIntId(), $user->getQIndex(), 'noadr', $add_bag, $arrive_date, 0];
                    $db_transfers->dbOptUpdate('ID', $redact_mode, $vals_r);
                    $last_transfer_id = $redact_mode;
                    $pay_vals = ['', $last_transfer_id, $depart_date, $par_u_id, false, $cost_g, $comis_group, '', '', $first_group_pass, $last_pass_id];
                    $db_pay_control->dbOptUpdate('trans_id', $last_transfer_id, $pay_vals);
                } else {
                    $db_transfers->dbOptInsert($vals);
                    $last_transfer_id = $db_transfers->getLastId();
                    
                    $pay_vals = ['', $last_transfer_id, $depart_date, $u_id, false, $cost_g, $comis_group, '', '', $first_group_pass, $last_pass_id];
                    $db_pay_control->dbOptInsert($pay_vals);
                }
                $sql_old_pass = $db_passengers->getSome($old_passenger, 'ID');
                $res_old_pass = mysqli_fetch_assoc($sql_old_pass);
                if ($redact_mode == 'off') {
                    $pass_transfers = $res_old_pass['transfers'] . ',' . $last_transfer_id;
                    $db_passengers->dbUpdateOne('transfers', $pass_transfers, 'ID', $old_passenger);
                }
                $log = fopen('logs/' . $last_transfer_id . '.txt', "a");
                $cols = $user->showCols('new_transfers');
                $i = 0;
                $write = fwrite($log, 'log_start' . "\r\n");
                foreach ($cols as $col) {
                    if ($col == 'ID') {
                        $write = fwrite($log, $last_transfer_id . "\r\n");
                    } elseif ($col == 'create_time') {
                        $write = fwrite($log, date('d.m.Y / H:i:s') . "\r\n");
                    } elseif ($col == 'deadline') {
                        $write = fwrite($log, date('d.m.Y / H:i:s', strtotime($vals[$i])) . "\r\n");
                    } else {
                        $write = fwrite($log, $vals[$i] . "\r\n");
                    }
                    $i++;
                }
                $write = fwrite($log, 'log_end' . "\r\n");
                fclose($log);
                unset($write);
            }
        }
        
        $json_result = json_encode($passengers, JSON_UNESCAPED_UNICODE);
        echo $json_result;
    }
    
}



if (isset($_POST['get_group_price'])) {
    $dest = $_POST['get_group_price'];
    $bag = $_POST['get_group_price_bag'];
    $adlt = $_POST['get_group_price_adlt'];
    $chld = $_POST['get_group_price_chld'];
    $adress_num = $_POST['get_group_price_adress_num'];
    $result = $user->getGroupPrice($dest, $adlt, $chld, $adress_num, $bag);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_ind_price'])) {
    $all_ind_pass = $_POST['get_ind_price'];
    $dest = $_POST['get_ind_price_dest'];
    $tarif = $_POST['get_ind_price_tarif'];
    $obl_town = $_POST['get_ind_price_obl_town'];
    $adress_num = $_POST['get_ind_price_adress_num'];
    
    $result = $user->getIndPrice($dest, $all_ind_pass, $tarif, $obl_town, $adress_num);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_user_q_index'])) {
    $result;
    $town = $_SESSION['town'];
    $sql_q_index = $db_towns->getSome($town, 'name_en');
    $res_q_index = mysqli_fetch_assoc($sql_q_index);
    $result = $res_q_index['queue_index'];
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_town_indexes'])) {
    $result = [];
    $sql_towns = $db_towns->getAllOrder('queue_index', 'ASC');
    while ($res_towns = mysqli_fetch_array($sql_towns)) {
        if ($res_towns['queue_index'] != 0) {
            $result[] = $res_towns['queue_index'];
        } else {
            array_unshift($result, $res_towns['queue_index']);
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['q_index'])) {
    $result = [];
    $q_indexes = [];
    $sql_towns = $db_towns->getAllOrder('queue_index', 'ASC');
    while ($res_towns = mysqli_fetch_array($sql_towns)) {
        if ($res_towns['queue_index'] != 0) {
            $q_indexes[] = $res_towns['queue_index'];
        } else {
            array_unshift($q_indexes, $res_towns['queue_index']);
        }
    }
    $q_inc = $_POST['q_index'];
    $town_limit = count($q_indexes);
    $q_index = $q_indexes[$q_inc];
    $town = new Town('towns', $q_index);
    $result = array($town->getName(), $town_limit, $q_index);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
    
//    $town_limit = $db_towns->getRowsNum('all');
//    $get_first_q = $db_towns->getAllOrderLimit('queue_index', 'ASC', 1);
//    $res_first_q = mysqli_fetch_assoc($get_first_q);
//    $first_q = $res_first_q['queue_index'];
//    $town = new Town('towns', $_POST['q_index']);
//    $result = array($town->getName(), $town_limit, $first_q);
//    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
//    echo $json_result;
}

if (isset($_POST['int_id_request'])) {
    $sql_int_id = $int_db->getAllOrderLimit('ID', 'DESC', 1);
//    $result = [];
    $int_id = mysqli_fetch_array($sql_int_id);
    $result = $int_id['ID'];
//    }    
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['get_all_dests'])) {
   $sql_dest_cols = $msk_db->getAll();
    $price_cols = [];
    while ($res = mysqli_fetch_array($sql_dest_cols)) {
        $price_cols[] = $res['name_en'];
    }
    $json_result = json_encode($price_cols, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['set_int_dest'])) {
    $cols = $_POST['dest_on'];
    $type = $_POST['set_int_dest'];
    $town_q_index = $_POST['q_index1'];
    $int_id = $_POST['int_id'];
    $vals = ['', $type, $int_id, $town_q_index];
//    $test = json_decode($cols, JSON_UNESCAPED_UNICODE);
    $vals[] = preg_replace('#,#', '.', $cols);    
    $db_dest_on->dbOptInsert($vals);
    $json_result = json_encode($vals, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_old_dest_params'])) {
    $type = $_POST['get_old_dest_params'];
    $int_id = $_POST['int_id_old'];
    $t_index = $_POST['town_index'];
    $sql_dest_gr = $db_dest_on->getSome3OrderLimit($type, 'type', $int_id, 'int_id', $t_index, 'town', 'ID', 'DESC', 1);
    $result = mysqli_fetch_assoc($sql_dest_gr);
    if (empty($result)) {
        $sql_dest_gr = $db_dest_on->getSome2OrderLimit($type, 'type', $t_index, 'town', 'ID', 'DESC', 1);
        $result = mysqli_fetch_assoc($sql_dest_gr);
        if (empty($result)) {
            $result_str = 'empty';
        } elseif (!empty($result)){
            $vals = ['', $type, $int_id, $t_index, $result['dest_str']];
            $db_dest_on->dbOptInsert($vals);
            $result_str = explode('.', $result['dest_str']);
        }
    } elseif (!empty($result)){
        $result_str = explode('.', $result['dest_str']);
    }    
    $json_result = json_encode($result_str, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['set_group_price'])) {
    $int_id = $_POST['set_group_price'];
    $town = $_POST['set_group_price_town'];
    $cols = explode(',', $_POST['set_group_price_dests']);
    $vals_a_str = explode(',', $_POST['set_group_price_vals_a']);
    $vals_c_str = explode(',', $_POST['set_group_price_vals_c']);
    $vals_adlt = ['', 'groupadlt', 'Standard', 1, 1, $town, $int_id];
    $vals_chld = ['', 'groupchld', 'Standard', 1, 1, $town, $int_id];
    $i = 0;
    foreach ($price_cols as $col) {
        if (in_array($col, $cols)) {
            $vals_adlt[] = (int)$vals_a_str[$i];
            $vals_chld[] = (int)$vals_c_str[$i];
            $i++;
        } else {
            $vals_adlt[] = 0;
            $vals_chld[] = 0;
        }       
    }
    
    $pr_db->dbOptInsert($vals_adlt);
    $pr_db->dbOptInsert($vals_chld);    
//    echo $vals_a[0]; 
    $json_result = json_encode($vals_adlt, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_old_gr_prices'])) {
    $type = $_POST['get_old_gr_prices'];
    $points = explode(',', $_POST['get_old_gr_prices_points']);
    $int_id = $_POST['get_old_gr_prices_int_id'];
    $town = $_POST['get_old_gr_prices_town'];    
    
    
    $sql_ind_pr = $pr_db->getSome3OrderLimit($int_id, 'int_id', $town, 'town', $type, 'type', 'ID', 'DESC', 1);
    $result = [];
    $res_ind_pr = mysqli_fetch_assoc($sql_ind_pr);
    if (!empty($res_ind_pr)){
        $result = [$res_ind_pr['ID'], $res_ind_pr['minpass'], $res_ind_pr['maxpass'], $res_ind_pr['class']];
        foreach ($points as $point) {
           $result[$point] = $res_ind_pr[$point];
        }
    }
    if (count($result) == 0) {
        $sql_last_int_id = $pr_db->getSome2OrderLimit($type, 'type', $town, 'town', 'int_id', 'DESC', 1);
        $res_last_int_id = mysqli_fetch_assoc($sql_last_int_id);
        if (!empty($res_last_int_id)) {
            $last_int_id = $res_last_int_id['int_id'];
            
            $sql_ind_pr1 = $pr_db->getSome3OrderLimit($last_int_id, 'int_id', $town, 'town', $type, 'type', 'ID', 'DESC', 1);
            $cols = $pr_db->getCols();
            while ($res_ind_pr1 = mysqli_fetch_array($sql_ind_pr1)) {
                $rec_vals = [''];
                $rec_count = count($cols);
                for ($i = 1; $i < $rec_count; $i++) {
                    if ($cols[$i] == 'int_id') {
                        $rec_vals[] = $int_id;
                    } else {
                        $rec_vals[] = $res_ind_pr1[$cols[$i]];
                    }
                }
                $pr_db->dbOptInsert($rec_vals);
            }
            
            
            $sql_ind_pr = $pr_db->getSome3OrderLimit($int_id, 'int_id', $town, 'town', $type, 'type', 'ID', 'DESC', 1);
            $result = [];
            while ($res_ind_pr = mysqli_fetch_array($sql_ind_pr)) {
                $result = [$res_ind_pr['ID'], $res_ind_pr['minpass'], $res_ind_pr['maxpass'], $res_ind_pr['class']];
                foreach ($points as $point) {
                    $result[$point] = $res_ind_pr[$point];
                }
            }
        } else {
            $result = 'empty';
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;    
    
    
    
    
    
    
    
    
    
//    $result = [];
//    $sql_gr = $pr_db->getSome3OrderLimit($type, 'type', $int_id, 'int_id', $town, 'town', 'ID', 'DESC', 1);
//    $res1 = mysqli_fetch_assoc($sql_gr);
//    if (empty($res1)) {
//        $sql_gr2 = $pr_db->getSome2OrderLimit($type, 'type', $town, 'town', 'ID', 'DESC', 1);
//        $res2 = mysqli_fetch_assoc($sql_gr2);
//        if (empty($res2)) {
//            $result = 'empty';
//        } else {
//            foreach ($points as $point) {
//                $result[] = $res2[$point];
//            }
//        }
//    } else {
//        foreach ($points as $point) {
//            $result[] = $res1[$point];
//        }
//    }
//    $result = $res1[$points[1]];
//    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
//    echo $json_result;    
}
if (isset($_POST['get_ru_tarif'])) {
    $en_tarif = $_POST['get_ru_tarif'];
    $sql = $db_tarifs->getSome($en_tarif, 'name_en');
    $res = mysqli_fetch_assoc($sql);
    $json_result = json_encode($res['name_ru'], JSON_UNESCAPED_UNICODE);
    echo $json_result; 
}
if (isset($_POST['get_old_ind_prices'])) {
    $points = explode(',', $_POST['get_old_ind_prices_points']);
    $int_id = $_POST['get_old_ind_prices_int_id'];
    $town = $_POST['get_old_ind_prices_town'];
    $sql_ind_pr = $pr_db->getSome3Order($int_id, 'int_id', $town, 'town', 'ind', 'type', 'rub_km', 'ASC');
    $result = [];
    while ($res_ind_pr = mysqli_fetch_array($sql_ind_pr)) {
        $ind_price =[$res_ind_pr['ID'], $res_ind_pr['minpass'], $res_ind_pr['maxpass'], $res_ind_pr['class'], $res_ind_pr['rub_km']];
        foreach ($points as $point) {
            $ind_price[] = $res_ind_pr[$point];
        }
        $result[] = $ind_price;
    }
    if (count($result) == 0) {
        $sql_last_int_id = $pr_db->getSome2OrderLimit('ind', 'type', $town, 'town', 'int_id', 'DESC', 1);
        $res_last_int_id = mysqli_fetch_assoc($sql_last_int_id);
        if (!empty($res_last_int_id)) {
            $last_int_id = $res_last_int_id['int_id'];
            
            $sql_ind_pr1 = $pr_db->getSome3Order($last_int_id, 'int_id', $town, 'town', 'ind', 'type', 'rub_km', 'ASC');
            $cols = $pr_db->getCols();
            while ($res_ind_pr1 = mysqli_fetch_array($sql_ind_pr1)) {
                $rec_vals = [''];
                $rec_count = count($cols);
                for ($i = 1; $i < $rec_count; $i++) {
                    if ($cols[$i] == 'int_id') {
                        $rec_vals[] = $int_id;
                    } else {
                        $rec_vals[] = $res_ind_pr1[$cols[$i]];
                    }
                }
                $pr_db->dbOptInsert($rec_vals);
            }
            
            
            $sql_ind_pr = $pr_db->getSome3Order($int_id, 'int_id', $town, 'town', 'ind', 'type', 'rub_km', 'ASC');
            $result = [];
            while ($res_ind_pr = mysqli_fetch_array($sql_ind_pr)) {
                $ind_price = [$res_ind_pr['ID'], $res_ind_pr['minpass'], $res_ind_pr['maxpass'], $res_ind_pr['class'], $res_ind_pr['rub_km']];
                foreach ($points as $point) {
                    $ind_price[] = $res_ind_pr[$point];
                }
                $result[] = $ind_price;
            }
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;    
    
}





if (isset($_POST['get_tarifs'])) {
    $tarifs = [];
    $sql_tarifs = $db_tarifs->getAll();
    while ($tarifs_res = mysqli_fetch_array($sql_tarifs)) {
        $tarifs[] = $tarifs_res['name_en'];
    }
    $json_result = json_encode($tarifs, JSON_UNESCAPED_UNICODE);
    echo $json_result; 
}

if (isset($_POST['set_ind_price'])) {
    $int_id = $_POST['set_ind_price'];
    $town = $_POST['set_ind_price_town'];
    $cols = explode(',', $_POST['set_ind_price_dests']);
    $vals_a_str = explode(',', $_POST['set_ind_price_vals_a']);
    $vals_ind = ['', 'ind', $vals_a_str[2], $vals_a_str[0], $vals_a_str[1], $vals_a_str[3], $town, $int_id];
    $class = $vals_a_str[2];
    $minpass = $vals_a_str[0];
    $maxpass = $vals_a_str[1];
    $sql_ind_pr = $pr_db->getSome3($int_id, 'int_id', $town, 'town', 'ind', 'type');
    $errors = 0;
    while ($res_ind_pr = mysqli_fetch_array($sql_ind_pr)) {
        if ($class == $res_ind_pr['class']) {
            if ($minpass >= $res_ind_pr['minpass'] && $minpass <= $res_ind_pr['maxpass']) {
                $errors++;
            }
            if ($maxpass >= $res_ind_pr['minpass'] && $maxpass <= $res_ind_pr['maxpass']) {
                $errors++;
            }
        }
    }
    $count = count($price_cols);
    if ($errors > 0) {
        $vals_ind = 'error_pass';
    } else {
        for ($i = 0; $i < $count; $i++) {
            if (in_array($price_cols[$i], $cols)) {
                $vals_ind[] = (int)$vals_a_str[$i+4];
            } else {
                $vals_ind[] = 0;
            }
        }

        $pr_db->dbOptInsert($vals_ind);
    }
    
//    echo $vals_a[0]; 
    $json_result = json_encode($vals_ind, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['set_one_price'])) {
    $it_id = $_POST['set_one_price'];
    $it_col = $_POST['set_one_price_col'];
    $it_val = $_POST['set_one_price_val'];
    $pr_db->dbUpdateOne($it_col, $it_val, 'ID', $it_id);
    $json_result = json_encode($it_id, JSON_UNESCAPED_UNICODE);
    echo $json_result;
//    $val = $it_val;
//    $json_result = json_encode($val, JSON_UNESCAPED_UNICODE);
//    echo $json_result;
}
if (isset($_POST['del_one_price'])) {
    $it_id = $_POST['del_one_price'];
    $pr_db->delRow($it_id, 'ID');
    $json_result = json_encode($it_id, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_old_depart_points'])) {
    $cols = $db_depart_points->getCols();
    $town = $_POST['get_old_depart_points_town'];
    $int_id = $_POST['get_old_depart_points_int_id'];
    $sql_get_depart = $db_depart_points->getSome2($town, 'town', $int_id, 'int_id');
    $result = [];
    while ($res_depart = mysqli_fetch_array($sql_get_depart)) {
        $res = [];
        foreach ($cols as $col) {
            $res[$col] = $res_depart[$col];
        }
        $result[] = $res;
    }
    if (count($result) == 0) {
        $sql_last_int = $db_depart_points->getSomeOrderLimit($town, 'town', 'int_id', 'DESC', 1);
        $res_last_int = mysqli_fetch_assoc($sql_last_int);
        $last_int_id = $res_last_int['int_id'];
        $sql_get_depart = $db_depart_points->getSome2($town, 'town', $last_int_id, 'int_id');
        
        
        while ($res_depart = mysqli_fetch_array($sql_get_depart)) {
            $res = [];
            foreach ($cols as $col) {
                if ($col == 'ID') {
                    $res[] = '';
                } elseif ($col == 'int_id') {
                    $res[] = $int_id;
                } else {
                    $res[] = $res_depart[$col];
                }                
            }
            $db_depart_points->dbOptInsert($res);
        }  
        
        $sql_get_depart2 = $db_depart_points->getSome2($town, 'town', $int_id, 'int_id');
        while ($res_depart2 = mysqli_fetch_array($sql_get_depart2)) {
            $res = [];
            foreach ($cols as $col) {
                $res[$col] = $res_depart2[$col];
            }
            $result[] = $res;
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
    
}

if (isset($_POST['set_new_depart_points_int_id'])) {
    $sqlCols = ['name_ru', 'name_en', 'q_index', 'time_plus'];
    $int_id = $_POST['set_new_depart_points_int_id'];
    $town = $_POST['set_new_depart_points_town'];
    $vals = [''];
    foreach ($sqlCols as $col) {
        $vals[] = $_POST['depart-set-'.$col];
    }
    $vals[] = $town;
    $vals[] = $int_id;
    $db_depart_points->dbOptInsert($vals);
    $json_result = json_encode($vals, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['set_one_depart'])) {
    $it_id = $_POST['set_one_depart'];
    $it_col = $_POST['set_one_depart_col'];
    $it_val = $_POST['set_one_depart_val'];
    $db_depart_points->dbUpdateOne($it_col, $it_val, 'ID', $it_id);
    $json_result = json_encode($it_id, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['del_one_depart'])) {
    $it_id = $_POST['del_one_depart'];
    $db_depart_points->delRow($it_id, 'ID');
    $json_result = json_encode($it_id, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_old_time_to'])) {
    $cols = $db_time_to->getCols();
    $town = $_POST['get_old_time_to_town'];
    $int_id = $_POST['get_old_time_to_int_id'];
    
    $sql_null_point = $msk_db->getSome(0, 'queue_index');
    $res_null_point = mysqli_fetch_assoc($sql_null_point);
    $null_point = $res_null_point['name_ru'];
    
    $sql_get_time = $db_time_to->getSome2($town, 'town', $int_id, 'int_id');
    $res_time = mysqli_fetch_assoc($sql_get_time);
    $result = [$null_point];
    if (!empty($res_time)) {
        $result[] = $res_time['time_to'];
        $result[] = $res_time['ID'];
    } else {
        $sql_get_time2 = $db_time_to->getSomeOrderLimit($town, 'town', 'int_id', 'DESC', 1);
        $res_time2 = mysqli_fetch_assoc($sql_get_time2);
        if (!empty($res_time2)) {
            $res = [];
            foreach ($cols as $col) {
                if ($col == 'ID'){
                    $res[] = '';
                } elseif ($col == 'int_id') {
                    $res[] = $int_id;
                } elseif ($col == 'null_point') {
                    $res[] = $null_point;
                } else {
                    $res[] = $res_time2[$col];
                }
            }
            $db_time_to->dbOptInsert($res);
            $sql_get_time3 = $db_time_to->getSome2($town, 'town', $int_id, 'int_id');
            $res_time3 = mysqli_fetch_assoc($sql_get_time3);
            $result[] = $res_time3['time_to'];
            $result[] = $res_time3['ID'];
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['set_time_to'])) {
    $it_id = $_POST['set_time_to'];
    $it_val = $_POST['set_time_to_val'];
    $town = $_POST['set_time_to_town'];
    $int_id = $_POST['set_time_to_int_id'];
    
    $sql_null_point = $msk_db->getSome(0, 'queue_index');
    $res_null_point = mysqli_fetch_assoc($sql_null_point);
    $null_point = $res_null_point['name_en'];
    if ($it_id == 'new') {
        $res = ['', $null_point, $it_val, $town, $int_id];
        $db_time_to->dbOptInsert($res);
    } else {
        $db_time_to->dbUpdateOne('time_to', $it_val, 'ID', $it_id);
    }
}
if (isset($_POST['get_old_obl_index'])) {
    $cols = $db_time_to->getCols();
    $town_index = $_POST['get_old_obl_index_town'];
    $int_id = $_POST['get_old_obl_index_int_id'];
    
    $sql_town = $db_towns->getSome($town_index, 'queue_index');
    $res_town = mysqli_fetch_assoc($sql_town);
    $town = $res_town['name_en'];
    
        
    $sql_get_time = $db_obl_towns_price->getSome2($town, 'town_name_en', $int_id, 'int_id');
    $res_time = mysqli_fetch_assoc($sql_get_time);
    $result = [$town];
    if (!empty($res_time)) {
        $result[] = $res_time['rub_km'];
        $result[] = $res_time['ID'];
    } else {
        $sql_get_time2 = $db_obl_towns_price->getSomeOrderLimit($town, 'town_name_en', 'int_id', 'DESC', 1);
        $res_time2 = mysqli_fetch_assoc($sql_get_time2);
        if (!empty($res_time2)) {
            $res = [];
            foreach ($cols as $col) {
                if ($col == 'ID'){
                    $res[] = '';
                } elseif ($col == 'int_id') {
                    $res[] = $int_id;
                } elseif ($col == 'town_name_en') {
                    $res[] = $town;
                } else {
                    $res[] = $res_time2[$col];
                }
            }
            $db_obl_towns_price->dbOptInsert($res);
            $sql_get_time3 = $db_obl_towns_price->getSome2($town, 'town_name_en', $int_id, 'int_id');
            $res_time3 = mysqli_fetch_assoc($sql_get_time3);
            $result[] = $res_time3['rub_km'];
            $result[] = $res_time3['ID'];
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['set_obl_index'])) {
    $it_id = $_POST['set_obl_index'];
    $it_val = $_POST['set_obl_index_val'];
    $town_index = $_POST['set_obl_index_town'];
    $int_id = $_POST['set_obl_index_int_id'];
    $sql_town = $db_towns->getSome($town_index, 'queue_index');
    $res_town = mysqli_fetch_assoc($sql_town);
    $town = $res_town['name_en'];
    
    if ($it_id == 'new') {
        $res = ['', $it_val, $town, $int_id];
        $db_obl_towns_price->dbOptInsert($res);
    } else {
        $db_obl_towns_price->dbUpdateOne('rub_km', $it_val, 'ID', $it_id);
    }
}

if (isset($_POST['get_cat_indexes'])) {
    $int_id = $_POST['get_cat_indexes'];
    $town = $_POST['get_cat_indexes_town'];
    $sql_cat_indexes = $pr_db->getSome3Order($int_id, 'int_id', $town, 'town', 'ind', 'type', 'minpass', 'ASC');
    $result = [];
    while ($res_cat = mysqli_fetch_array($sql_cat_indexes)) {
        $category = $res_cat['minpass'] . '-' . $res_cat['maxpass'];
        $cat_index = $res_cat['rub_km'];
        $cat = [$category, $cat_index];
        $result[] = $cat;
    }
    
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
    
}

if (isset($_POST['get_obl_towns'])) {
    $result = [];
    $town_index = $_POST['get_obl_towns_town'];
    $cols = $db_obl_towns->getCols();
    $sql_town = $db_towns->getSome($town_index, 'queue_index');
    $res_town = mysqli_fetch_assoc($sql_town);
    $town = $res_town['name_en'];
    $sql_obl_towns = $db_obl_towns->getSome2($town, 'center', 0, 'del_value');
    while ($res_obl_towns = mysqli_fetch_array($sql_obl_towns)) {
        $res = [];
        foreach ($cols as $col) {
            $res[$col] = $res_obl_towns[$col];
        }
        $result[] = $res;
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}

if (isset($_POST['new_obl_town'])) {
    $result = [''];
    $town_index = $_POST['new_obl_town'];
    $sql_town = $db_towns->getSome($town_index, 'queue_index');
    $res_town = mysqli_fetch_assoc($sql_town);
    $town = $res_town['name_en'];
    $sql_cols = ['name_ru', 'name_en', 'distance', 'time_plus'];
    $result[] = $_POST['new_obl_town-name_ru'];
    $result[] = $_POST['new_obl_town-name_en'];
    $result[] = $_POST['new_obl_town-distance'];
    $result[] = $_POST['new_obl_town-time_plus'];
    
//    foreach($sql_cols as $col) {
//        $a = 'new_obl_town-' . $col;
//        $result[] = $_POST[$a];
//    }
    $result[] = $town;
    $result[] = 0;
    $db_obl_towns->dbOptInsert($result);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
    
}
if (isset($_POST['del_obl_town'])) {
    $it_id = $_POST['del_obl_town'];
    $db_obl_towns->dbUpdateOne('del_value', 1, 'ID', $it_id);
    $result = 'done';
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_qdests'])) {
    $dests = explode(',', $_POST['get_qdests']);
    $result = [];
    $sql_q_dests = $msk_db->getAllOrder('queue_index', 'ASC');
    while ($res_q_dests = mysqli_fetch_array($sql_q_dests)) {
        $check = $res_q_dests['name_en'];
        if (in_array($check, $dests)) {
            $result[] = $check;
        }
    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['get_shedule'])) {
    $dests = explode(',', $_POST['get_shedule']);
    $time_plus = [];
    $time_plus_r = [];
    $sql_q_dests = $msk_db->getAllOrder('queue_index', 'ASC');
    while ($res_q_dests = mysqli_fetch_array($sql_q_dests)) {
        $check = $res_q_dests['name_en'];
        if (in_array($check, $dests)) {
            $time_plus[] = $res_q_dests['time_plus'];
            array_unshift($time_plus_r, $res_q_dests['time_plus']);
        }
    }
    
    $int_id = $_POST['get_shedule_int_id'];
    $town = $_POST['get_shedule_town'];
    $sql_get_time = $db_time_to->getSome2($town, 'town', $int_id, 'int_id');
    $res_time = mysqli_fetch_assoc($sql_get_time);
    $time_to = strtotime($res_time['time_to']);
    $sql_get_null_time = $db_time_to->getSome2(0, 'town', $int_id, 'int_id');
    $res_null_time = mysqli_fetch_assoc($sql_get_null_time);
    $null_time = $res_null_time['time_to'];
    $result = [];
    $sql_shedule = $db_shedule->getSomeOrder($int_id, 'int_id', 'reis', 'ASC');
    $shedule = [];
    $to_msk = [];
    $from_msk = [];
    $to_i = 0;
    $from_i = 0;
    $is_it_int = 0;
    while ($res_shedule = mysqli_fetch_array($sql_shedule)) {
        if ($res_shedule['type'] == 'to_msk') {
            $to_msk[] = $res_shedule['reis'];
            $to_i++;
        } elseif ($res_shedule['type'] == 'from_msk') {
            $from_msk[] = $res_shedule['reis'];
            $from_i++;
        }
        $is_it_int++;
    }
    
    if ($is_it_int == 0) {
        $sql_shedule2 = $db_shedule->getAllOrderLimit('int_id', 'DESC', 1);
        $res_last_int_id = mysqli_fetch_assoc($sql_shedule2);
        $last_int_id = $res_last_int_id['int_id'];
        $sql_shedule3 = $db_shedule->getSomeOrder($last_int_id, 'int_id', 'reis', 'ASC');
        $shedule_cols = $db_shedule->getCols();
        
        while ($res_shedule3 = mysqli_fetch_array($sql_shedule3)) {
            $vals_sh = [''];
            foreach ($shedule_cols as $col) {
                if ($col != 'ID' && $col != 'int_id') {
                    $vals_sh[] = $res_shedule3[$col];
                } elseif ($col == 'int_id') {
                    $vals_sh[] = $int_id;
                }
            }
            $db_shedule->dbOptInsert($vals_sh);
        }
        $sql_shedule4 = $db_shedule->getSomeOrder($int_id, 'int_id', 'reis', 'ASC');
        while ($res_shedule4 = mysqli_fetch_array($sql_shedule4)) {
            if ($res_shedule4['type'] == 'to_msk') {
                $to_msk[] = $res_shedule4['reis'];
                $to_i++;
            } elseif ($res_shedule4['type'] == 'from_msk') {
                $from_msk[] = $res_shedule4['reis'];
                $from_i++;
            }
        }
    } 
    
    if ($to_i > 0 || $from_i > 0) {
        $tr_len = max($to_i, $from_i);
        for ($i = 0; $i < $tr_len; $i++) {
            $reis = [];
            if (isset($to_msk[$i])) {
                if ($town == 0) {
                    $reis[] = date('H:i', strtotime($to_msk[$i]));
                } elseif ($town > 0) {
                    $start = strtotime($to_msk[$i]) + strtotime($null_time) - $time_to;
                    $reis[] = date('H:i', $start);
                } elseif ($town < 0) {
                    $start = strtotime($to_msk[$i]) + strtotime($null_time) - $time_to;
                    $reis[] = date('H:i', $start);
                }
                
//                $reis[] = date('H:i', $time_to + $to_msk[0] );
                foreach ($time_plus as $time) {
                    if ($town == 0) {
                        $time2 = strtotime($to_msk[$i]) + $time_to + $time*60 - strtotime('00:00:00');
                    } elseif ($town > 0) {
                        $time2 = $start + $time_to + $time*60 - strtotime('00:00:00');
                    } elseif ($town < 0) {
                        $time2 = $start + $time_to + $time*60 - strtotime('00:00:00');
                    }
                    
                    $reis[] = date('H:i', $time2);
                }
            } else {
                $reis[] = '';
                foreach ($time_plus as $time) {
                    $reis[] = '';
                }
            }
            if (isset($from_msk[$i])) {                
                foreach ($time_plus_r as $time) {
                    $time2 = strtotime($from_msk[$i]) - $time*60;
                    $reis[] = date('H:i', $time2);
                }
                $reis[] = date('H:i', strtotime($from_msk[$i]) + $time_to - strtotime('00:00:00'));
            } else {                
                foreach ($time_plus_r as $time) {
                    $reis[] = '';
                }
                $reis[] = '';
            }
            $result[] = $reis;
        }
    }
    
//    if (count($shedule != 0)) {
//        $to_msk = [];
//        $from_msk = [];
//        foreach ($shedule as $shed)
//    }
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['set_new_reis'])) {
    $type = $_POST['set_new_reis'];
    $int_id = $_POST['set_new_reis_int_id'];
    $val = $_POST['set_new_reis_val'];
    $result = ['', $type, $val, $int_id];
    $db_shedule->dbOptInsert($result);
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;
}
if (isset($_POST['del_shedule'])) {
    $reis = date('H:i:s', strtotime($_POST['del_shedule']));
    if ($_POST['del_shedule_type'] == 0) {
        $type = 'to_msk';
    } else {
        $type = 'from_msk';
    }
    
    $int_id = $_POST['del_shedule_int_id'];
    $db_shedule->delRow3($int_id, 'int_id', $type, 'type', $reis, 'reis');
    
    $result = [$int_id, $type, $reis];
    $json_result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $json_result;    
}
if (isset($_POST['now_int_id_request'])) {
    $now = date('Y-m-d');    
    $sql_int = $int_db->compBetween($now, 'time_start', 'time_end');    
    $res = mysqli_fetch_assoc($sql_int);
    $now_id = $res['ID'];
    $json_result = json_encode($now_id, JSON_UNESCAPED_UNICODE);
    echo $json_result;    
}
