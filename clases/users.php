<?php
class User {
    
    private $host = '';
    private $user = '';
    private $password = '';
    private $dbname = '';
    private $db;
    private $table = 'users';
    
    private $id;
    private $res_row;
    private $res_town;
    private $town;
    private $q_index;
    private $int_id;
    private $dest_on_group;
    private $dest_on_ind;
    private $dest_on_group_ru;
    private $dest_on_ind_ru;
    private $obl_towns_list;
    private $town_points = [];
    private $ind_types = [];
    private $date_time_messages = [];
    private $time_to_msk;
    private $schedule = [];
    private $max_pass = [];
    
    public function __construct($u_id) {
        $this->setDb();
        $this->id = $u_id;
        $sql_row = mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `ID` = '$this->id'");
        $this->res_row = mysqli_fetch_assoc($sql_row);
        $this->town = $this->res_row['town'];
        $sql_town = mysqli_query($this->db, "SELECT * FROM `towns` WHERE `name_en` = '$this->town'");
        $this->res_town = mysqli_fetch_assoc($sql_town);
        $this->setIntId();
        $this->setQIndex();
        $this->setDests();
        $this->setOblTownsList();
        $this->setTownPoints();
        $this->setIndTypes();
        $this->setDateTimeMassages();
        $this->setTimeToMsk();
        $this->setSchedule();
        $this->setMaxPass();
    }
    public function getUserSql() {
        return $this->res_row;
    }
    public function getTransferPayStatus($trans_id) {
        $sql_price = mysqli_query($this->db, "SELECT * FROM `pay_control` WHERE `trans_id` = '$trans_id' AND `user_id` = '$this->id'");
        $res_price = mysqli_fetch_assoc($sql_price);
        return $res_price['payed'];
    }
    public function getTransferCreateTime($trans_id) {
        $sql_trans = mysqli_query($this->db, "SELECT `create_time` FROM `new_transfers` WHERE `ID` = '$trans_id'");
        $res_trans = mysqli_fetch_assoc($sql_trans);
        return $res_trans['create_time'];
    }
    public function getPriceOfTransfer($trans_id) {
        $sql_price = mysqli_query($this->db, "SELECT * FROM `pay_control` WHERE `trans_id` = '$trans_id' AND `user_id` = '$this->id'");
        $res_price = mysqli_fetch_assoc($sql_price);
        return $res_price['cost'];
    }
    public function getDebtTransfers() {
        return mysqli_query($this->db, "SELECT * FROM `pay_control` WHERE `payed` = '0' AND `user_id` = '$this->id'");
    }
    public function getUserDebt() {
        $sql_all_user_debt = mysqli_query($this->db, "SELECT * FROM `pay_control` WHERE `payed` = '0' AND `user_id` = '$this->id'");
        $all_debt = 0;
        while ($res_all_user_debt = mysqli_fetch_array($sql_all_user_debt)) {
            if ($res_all_user_debt['add_pay'] == 0) {
                $debt = $res_all_user_debt['cost'] - $res_all_user_debt['comision'];
            } else {
                $debt = $res_all_user_debt['add_pay'];
            }
            
            $all_debt += $debt;
        }
        return $all_debt;
    }
    public function getUserName($u_id) {
        $sql_row = mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `ID` = '$u_id'");
        $sql_res = mysqli_fetch_assoc($sql_row);
        return $sql_res['company'];
    }
    public function getUserComisions() {
        $result = [];
        $sql_comisions = $this->getSome2Order('comisions', $this->q_index, 'town', $this->int_id, 'int_id', 'ID', 'ASC');
        while ($res_comisions = mysqli_fetch_array($sql_comisions)) {
            $result[$res_comisions['type']] = $res_comisions['value'];
        }
        return $result;
    }
    public function getTransferById($t_id) {
        return mysqli_query($this->db, "SELECT * FROM `new_transfers` WHERE `u_id` = '$this->id' AND `ID` = '$t_id'");
    }
    public function getUserTransfers() {
        return mysqli_query($this->db, "SELECT * FROM `new_transfers` WHERE `u_id` = '$this->id' ORDER BY `depart_time` DESC");
//        return $this->getSomeOrder('new_transfers', $this->id, 'u_id', 'depart_time', 'ASC');
    }
    public function getUserTransfersASC5() {
        return mysqli_query($this->db, "SELECT * FROM `new_transfers` WHERE `u_id` = '$this->id' AND `depart_time` >= CURRENT_TIME AND `deleted` = '0' ORDER BY `depart_time` ASC LIMIT 5");
//        return $this->getSomeOrder('new_transfers', $this->id, 'u_id', 'depart_time', 'ASC');
    }
    public function getUserPassenger($pass_id) {
        $cols = $this->showCols('passengers_data');
        $sql_pass = $this->getSomeOrder('passengers_data', $pass_id, 'ID', 'ID', 'ASC');
        $result = [];
        while ($res_pass = mysqli_fetch_array($sql_pass)) {
            foreach ($cols as $col) {
                $result[$col] = $res_pass[$col];
            }
        }
        return $result;
    }
    public function showCols($table) {
        $sql = mysqli_query($this->db, "SHOW COLUMNS FROM `$table`");
        $result = [];
        while ($res = mysqli_fetch_assoc($sql)) {
            $result[] = $res['Field'];
        }
        return $result;
    }
    public function getBagCost() {
        $sql = $this->getSome2Order('bag_cost', $this->q_index, 'town', $this->int_id, 'int_id', 'ID', 'DESC');
        $res = mysqli_fetch_assoc($sql);
        return (int)$res['cost'];
    }
    
    public function getAdressCost($type) {
        $sql_adress = $this->getSome2Order('adress_cost', $this->q_index, 'town', $this->int_id, 'int_id', 'ID', 'DESC');
        $res_adress = mysqli_fetch_assoc($sql_adress);
        return (int)$res_adress[$type . '_type'];
    }
    
    public function getGroupPrice($dest, $adlt, $chld, $adress_num, $bag) {
        $sql_get_price_adlt = mysqli_query($this->db, "SELECT * FROM `prices` WHERE `int_id` = '".$this->int_id."' AND `town` = '".$this->q_index."' AND `type` = 'groupadlt'");
        $res_adlt = mysqli_fetch_assoc($sql_get_price_adlt);
        $adlt_price = $res_adlt[$dest];
        
        $sql_get_price_chld = mysqli_query($this->db, "SELECT * FROM `prices` WHERE `int_id` = '".$this->int_id."' AND `town` = '".$this->q_index."' AND `type` = 'groupchld'");
        $res_chld = mysqli_fetch_assoc($sql_get_price_chld);
        $chld_price = $res_chld[$dest];
        
        $full_price_adlt = $adlt * $adlt_price;
        $full_price_chld = $chld * $chld_price;
        $adlt_price .= ' руб. (за человека)';
        $chld_price .= ' руб. (за человека)';
        $adress_price = $this->getAdressCost('group');
        $adress_cost = $adress_num * $adress_price;
//        $adress_num = (int)$adress_num;
        if ($adress_num == 1) {
            $adress_num .= ' адрес';
        } elseif ($adress_num >= 2 && $adress_num <= 4){
            $adress_num .= ' адреса';
        } else {
            $adress_num .= ' адресов';
        }
        
        $bag_cost = $this->getBagCost();
        $bag_price = $bag_cost * $bag;
        if ($bag == 1) {
            $bag .= ' дополнительный багаж';
        } elseif ($bag >= 2 && $bag <= 4){
            $bag .= ' дополнительных багажа';
        } else {
            $bag .= ' дополнительных багажей';
        }
        
        $result = [[$adlt_price , $chld_price , $adress_num, $bag], [$full_price_adlt, $full_price_chld, $adress_cost, $bag_price]];
        return $result;
    }
    
    public function getIndPrice($dest, $all_pass, $tarif, $obl_town, $adres_num) {
        $sql_get_price = mysqli_query($this->db, "SELECT * FROM `prices` WHERE `int_id` = '".$this->int_id."' AND `town` = '".$this->q_index."' AND `type` = 'ind' AND `class` = '".$tarif."' AND `minpass` <= '".$all_pass."' AND `maxpass` >= '".$all_pass."'");       
        $res_get_price = mysqli_fetch_assoc($sql_get_price);
        $obl_cost = 0;
        $adress_cost = 0;
        $category = $res_get_price['minpass'] . ' - ' . $res_get_price['maxpass'];
        if ($res_get_price['maxpass'] >= 2 && $res_get_price['maxpass'] <= 4) {
            $category .= ' человека';
        } else {
            $category .= ' человек';
        }
        if ($obl_town != 'off') {
            $obl_town_arr = explode('_', $obl_town);
            $distance = (int) $obl_town_arr[0];
            $obl_cost = $distance * $res_get_price['rub_km'];
        }
        if ($adres_num > 2) {
            $adress_price = $this->getAdressCost('ind');
            $add_adr_num = $adres_num - 2;
            $adress_cost = $add_adr_num * $adress_price;
        }
        $result = [[$category, $distance, $adres_num], [(int) $res_get_price[$dest], $obl_cost, $adress_cost]];
//        $result = "SELECT * FROM `prices` WHERE `int_id` = '".$this->int_id."' AND `town` = '".$this->q_index."' AND `type` = 'ind' AND `class` = '".$tarif."' AND `minpass` <= '".$allpass."' AND `maxpass` >= '".$allpass."'";
        return $result;
    }
    private function setMaxPass() {
        $result = [];
        $sql_set_max_pass = $this->getSome3Order('prices', $this->int_id, 'int_id', $this->q_index, 'town', 'ind', 'type', 'class', 'DESC');
//        $type_flag = 'none';
//        $key;
        $vals = [];
        while ($res_max_pas = mysqli_fetch_array($sql_set_max_pass)) {
                $vals[$res_max_pas['class']][] = $res_max_pas['maxpass'];
                $vals[$res_max_pas['class']][] = $res_max_pas['minpass'];
        }
        foreach ($vals as $key=>$val) {
            $min = min($val);
            $max = max($val);
            $result[$key] = [$min, $max];
        }
        $this->max_pass = $result;
        return $this;
    }
    public function getMaxTest() {
        $result = [];
        $sql_set_max_pass = $this->getSome3Order('prices', $this->int_id, 'int_id', $this->q_index, 'town', 'ind', 'type', 'class', 'DESC');
//        $type_flag = 'none';
//        $key;
        $vals = [];
        while ($res_max_pas = mysqli_fetch_array($sql_set_max_pass)) {
                $vals[$res_max_pas['class']][] = $res_max_pas['maxpass'];
                $vals[$res_max_pas['class']][] = $res_max_pas['minpass'];
        }
        foreach ($vals as $key=>$val) {
            $min = min($val);
            $max = max($val);
            $result[$key] = [$min, $max];
        }
        return $result;
    }
    public function getSome3OrderT($u_table, $some, $where, $some2, $where2, $some3, $where3, $order, $sc) {
        return "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."' ORDER BY `$order` $sc";
    }
    public function getMaxPass() {
        return $this->max_pass;
    }
    
    public function getSchedule() {
        return $this->schedule;
    }
    
    private function setSchedule() {
        $sql_set_schedule = $this->getSomeOrder('schedule', $this->int_id, 'int_id', 'type', 'ASC');
        $result = [];
        while ($res_schedule = mysqli_fetch_array($sql_set_schedule)) {            
            $res = [$res_schedule['type'], $res_schedule['reis']];
            $result[] = $res;
        }
        if ($this->q_index != 0) {
            $sql_null_time = $this->getSome2Order('time_to_msk', 0, 'town', $this->int_id, 'int_id', 'ID', 'DESC');
            $res_null_time = mysqli_fetch_assoc($sql_null_time);
            $null_time = $res_null_time['time_to'];
            
            $sql_town_time = $this->getSome2Order('time_to_msk', $this->q_index, 'town', $this->int_id, 'int_id', 'ID', 'DESC');
            $res_town_time = mysqli_fetch_assoc($sql_town_time);
            $town_time = $res_town_time['time_to'];
            
            $change_to_msk = [];
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i][0] == 'to_msk') {
                    $start = date('H:i:s', strtotime($result[$i][1]) + strtotime($null_time) - strtotime($town_time));
                    $result[$i] = ['to_msk', $start];
                }
            }
        }
        $this->schedule = $result;
        return $this;
    }
    
    private function setTimeToMsk() {
        $sql_time_to_msk = $this->getSome2Order('time_to_msk', $this->int_id, 'int_id', $this->q_index, 'town', 'ID', 'DESC');
        $res_time_to_msk = mysqli_fetch_assoc($sql_time_to_msk);
        $this->time_to_msk = $res_time_to_msk['time_to'];
        return $this;
    }
    
    public function getTimeToMsk() {
        return $this->time_to_msk;
    }
    
    private function setDateTimeMassages() {
        $sql_ind_types = $this->getSomeOrder('dest_types', $this->int_id, 'int_id', 'ID', 'ASC');
        $rr = [];
        while ($res_types = mysqli_fetch_array($sql_ind_types)) {
            $rr = [$res_types['name_en'], $res_types['message_to'], $res_types['time_plus'], $res_types['time_wait'], $res_types['message_from']];
            $this->date_time_messages[] = $rr;
        }
        
        return $this; 
    }
    
    public function getDateTimeMessages() {
        return $this->date_time_messages;
    }
    
    private function setIndTypes() {
        $sql_ind_types = $this->getSome2Order('tarifs', $this->int_id, 'int_id', $this->q_index, 'town', 'ID', 'ASC');
        $rr = [];
        while ($res_types = mysqli_fetch_array($sql_ind_types)) {
            $rr[$res_types['name_en']] = $res_types['name_ru'];
        }
        
        $sql_true_clases = $this->getSome3Order('prices', $this->int_id, 'int_id', $this->q_index, 'town', 'ind', 'type', 'class', 'DESC');
        $true_clases = [];
        while ($res_true_clases = mysqli_fetch_assoc($sql_true_clases)) {
            $true_clases[$res_true_clases['class']] = $rr[$res_true_clases['class']];
        }
        $this->ind_types = $true_clases;
        return $this; 
    }
    
    public function getIndTypes(){
        return $this->ind_types;
    }
    
    private function setTownPoints() {
        $sql_points = $this->getSome2Order('depart_points', $this->int_id, 'int_id', $this->q_index, 'town', 'time_plus', 'ASC');
        $rr = [];
        while ($res_points = mysqli_fetch_array($sql_points)) {
            $this->town_points[] = [$res_points['name_en'], $res_points['name_ru'], $res_points['time_plus']];
        }
        return $this;        
    }
    public function getTownPoints() {
        return $this->town_points;
    }
    private function setQIndex() {
        $this->q_index = $this->res_town['queue_index'];
        return $this;
    }
    public function getQIndex() {
        return $this->q_index;
    }
    public function setOblTownsList() {
        $sqlOblTowns = $this->getSome2Order('obl_towns', $this->town, 'center', 0, 'del_value', 'distance', 'ASC');
        $result = [];
        while ($res_obl_towns = mysqli_fetch_array($sqlOblTowns)) {
            $res = [$res_obl_towns['distance'], $res_obl_towns['name_ru'], $res_obl_towns['time_plus'], $res_obl_towns['name_en']];
            $result[] = $res;
        }
        $this->obl_towns_list = $result;
    }
    
    public function getOblTownsList() {
        return $this->obl_towns_list;
    }
    public function testSql() {
        $sql_dests_g = $this->getSome3OrderLimit('dest_on', $this->int_id, 'int_id', 'group', 'type', $this->q_index, 'town', 'ID', 'DESC', 1);
        $res_g = mysqli_fetch_assoc($sql_dests_g);
        return explode('.', $res_g['dest_str']);
//        return $this->testDest('dest_on', $this->int_id, 'int_id', 'group', 'type', $this->q_index, 'town', 'ID', 'DESC', 1);
    }
    public function testDest($u_table, $some, $where, $some2, $where2, $some3, $where3, $order, $sc, $limit){
        return "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."' ORDER BY `$order` $sc LIMIT $limit";
    }
    public function getIndDestsRu() {
        $sql_all_dests = mysqli_query($this->db, "SELECT * FROM `destinations` ORDER BY `queue_index` ASC");
        $result = [];
        while ($res_all = mysqli_fetch_array($sql_all_dests)) {
            $res = [];
            if (in_array($res_all['name_en'], $this->dest_on_ind)) {
                $res = [$res_all['name_en'], $res_all['name_ru'], $res_all['type'], $res_all['time_plus'], $res_all['sub_points']];
                $result[] = $res;
            }
        }
        $this->dest_on_ind_ru = $result;
        return $this->dest_on_ind_ru;
    }
    public function getGroupDestsRu() {
        $sql_all_dests = mysqli_query($this->db, "SELECT * FROM `destinations` ORDER BY `queue_index` ASC");
        $result = [];
        while ($res_all = mysqli_fetch_array($sql_all_dests)) {
            $res = [];
            if (in_array($res_all['name_en'], $this->dest_on_group)) {
                $res = [$res_all['name_en'], $res_all['name_ru'], $res_all['type'], $res_all['time_plus'], $res_all['sub_points']];
                $result[] = $res;
            }
        }
        $this->dest_on_group_ru = $result;
        return $this->dest_on_group_ru;
    }
    
    private function setDests() {
        $sql_dests_i = $this->getSome3OrderLimit('dest_on', $this->int_id, 'int_id', 'ind', 'type', $this->q_index, 'town', 'ID', 'DESC', 1);
        $res_i = mysqli_fetch_assoc($sql_dests_i);
        $this->dest_on_ind = explode('.', $res_i['dest_str']);
        
        $sql_dests_g = $this->getSome3OrderLimit('dest_on', $this->int_id, 'int_id', 'group', 'type', $this->q_index, 'town', 'ID', 'DESC', 1);
        $res_g = mysqli_fetch_assoc($sql_dests_g);
        $this->dest_on_group = explode('.', $res_g['dest_str']);
        return $this;
    }
    public function getGroupDests(){
        return $this->dest_on_group;
    }
    public function getIndDests(){
        return $this->dest_on_ind;
    }
    private function setDb() {
        $db = mysqli_connect($this->host, $this->user, $this->password, $this->dbname) or die ('Ошибка : ('. mysqli_connect_error($db) . ')');
        $this->db = $db;
        return $this;
    }
    private function getSome3Order($u_table, $some, $where, $some2, $where2, $some3, $where3, $order, $sc) {
        return mysqli_query($this->db, "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."' ORDER BY `$order` $sc");
    }
    private function getSome2Order($u_table, $some, $where, $some2, $where2, $order, $sc) {
        return mysqli_query($this->db, "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' ORDER BY `$order` $sc");
    }
    private function getSomeOrder($u_table, $some, $where, $order, $sc) {
        return mysqli_query($this->db, "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' ORDER BY `$order` $sc");
    }
    private function getSome2OrderLimit($u_table, $some, $where, $some2, $where2, $order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' ORDER BY `$order` $sc LIMIT $limit");
    }
    private function getSome3OrderLimit($u_table, $some, $where, $some2, $where2, $some3, $where3, $order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$u_table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."' ORDER BY `$order` $sc LIMIT $limit");
    }
    public function getId() {
        return $this->id;
    }
    private function setIntId() {
        $now = date('Y-m-d');    
        $sql_int = $this->compBetweenInt($now, 'time_start', 'time_end');    
        $res = mysqli_fetch_assoc($sql_int);
        $this->int_id = $res['ID'];
        return $this;
    }
    public function reSetIntId($id) {
        $this->int_id = $id;
        return $this;
    }
    public function reSetIntIdByTransfer($id) {
        $sql = $this->getSomeOrder('new_transfers', $id, 'ID', 'ID', 'DESC');
        $res = mysqli_fetch_assoc($sql);
        if (empty($res)) {
            return false;
        } 
        $this->int_id = $res['int_id'];
        return $this;
        
    }
    public function getIntId() {
        return $this->int_id;
    }
    public function getTown() {        
        return $this->town;
    }
    public function getTownRu() {        
        $town_ru = $this->res_town['name_ru'];
        return $town_ru;
    }
    public function compBetweenInt($val, $com1, $com2) {
        return mysqli_query($this->db, "SELECT * FROM `intervals` WHERE '$val' >= `$com1` AND '$val' <= `$com2`");
    }
}