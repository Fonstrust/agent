<?php
class Mysql {
    private $host = '';
    private $user = '';
    private $password = '';
    private $dbname = '';
    private $db;
    private $cols;
    private $all;
    private $table;
    private $rows_num;
    
    public function __construct($table) {
        $this->setDb();
        $this->setTable($table);
    }
    public function setDb() {
        $db = mysqli_connect($this->host, $this->user, $this->password, $this->dbname) or die ('Ошибка : ('. mysqli_connect_error($db) . ')');
        $this->db = $db;
        return $this;
    }
    public function getLastId(){
        return mysqli_insert_id($this->db);
    }
    public function testH() {
        return 'work';
    }
    public function getDb() {
        return $this->db;
    }
    public function getOne($one, $some, $where) {
        return mysqli_query($this->db, "SELECT `$one` FROM `$this->table` WHERE `$where` = '".$some."'");
    }
    
    public function getUnion($t1, $t2, $limit) {
        return mysqli_query($this->db, "SELECT * FROM  
        (SELECT * FROM `$t1`
        UNION ALL
        SELECT * FROM `$t2`) AS `all`
        ORDER BY `create_time` DESC LIMIT $limit");
    }
    public function getUnionSome($t1, $t2, $limit, $some, $where) {
        return mysqli_query($this->db, "SELECT * FROM  
        (SELECT * FROM `$t1`
        UNION ALL
        SELECT * FROM `$t2`) AS `all`
        WHERE `$where` = '$some'
        ORDER BY `create_time` DESC LIMIT $limit");
    }
    public function getUnionSome2($t1, $t2, $limit, $some, $where, $some2, $where2) {
        return mysqli_query($this->db, "SELECT * FROM  
        (SELECT * FROM `$t1`
        UNION ALL
        SELECT * FROM `$t2`) AS `all`
        WHERE `$where` = '$some' AND `$where2` = '$some2'
        ORDER BY `create_time` DESC LIMIT $limit");
    }
    
    public function dbClose() {
        return mysqli_close($this->db);
    }
    
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }
    
    public function getTable() {
        return $this->table;
    }
    
    public function showCols() {
        $sql = mysqli_query($this->db, "SHOW COLUMNS FROM `$this->table`");
        $result = [];
        while ($res = mysqli_fetch_assoc($sql)) {
            $result[] = $res['Field'];
        }
        $this->cols = $result;
        return $this;
    }
    
    public function getCols() {
        $this->showCols();
        return $this->cols;
    }
    public function addColText60($col, $after) {
        return mysqli_query($this->db, "ALTER TABLE `$this->table` ADD `$col` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `$after`");
    }
    public function setAll() {
        $sql = mysqli_query($this->db, "SELECT * FROM `$this->table`");
        $this->all = $sql;
        return $this;
    }
    
    public function getAllOrder($order, $sc) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` ORDER BY `$order` $sc");      
    }
    public function getAllOrderLimit($order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` ORDER BY `$order` $sc LIMIT $limit");      
    }
    public function getColVals($col) {
        $sql = $this->getAll();
        $result = [];
        while ($res = mysqli_fetch_array($sql)) {
            $result[] = $res[$col];
        }
        return $result;
    }
    public function getAll() {
        $this->setAll();
        return $this->all;
    }
    
    public function getSome($some, $where) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."'");
    }
    public function getSome2($some, $where, $some2, $where2) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."'");
    }
    public function getSome3($some, $where, $some2, $where2, $some3, $where3) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."'");
    }
    public function getSome3FromOtherTable($table, $some, $where, $some2, $where2, $some3, $where3) {
        return mysqli_query($this->db, "SELECT * FROM `$table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."'");
    }
    public function getSomeOrderLimit($some, $where, $order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."' ORDER BY `$order` $sc LIMIT $limit");
    }
    public function getSome2OrderLimit($some, $where, $some2, $where2, $order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' ORDER BY `$order` $sc LIMIT $limit");
    }
    public function getSome3OrderLimit($some, $where, $some2, $where2, $some3, $where3, $order, $sc, $limit) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '".$some."' AND `$where2` = '".$some2."' AND `$where3` = '".$some3."' ORDER BY `$order` $sc LIMIT $limit");
    }
    public function getString($string) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` $string");
    }
    
    public function getSomeOrder($some, $where, $order, $sc){
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '$some' ORDER BY `$order` $sc");
    }
    public function getSome3Order($some, $where, $some2, $where2, $some3, $where3, $order, $sc){
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE `$where` = '$some' AND `$where2` = '$some2' AND `$where3` = '$some3' ORDER BY `$order` $sc");
    }
    public function compBetween($val, $com1, $com2) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE '$val' >= `$com1` AND '$val' <= `$com2`");
    }
    public function compBetweenEq2($val, $com1, $com2, $col, $equall, $equall2, $order, $sc) {
        return mysqli_query($this->db, "SELECT * FROM `$this->table` WHERE $val >= `$com1` AND $val <= `$com2` AND `$col` = '$equall' OR `$col` = '$equall2' ORDER BY $order $sc");
    }
    
    public function showOne($some, $where, $one) {
        $sql = $this->getSome($some, $where);
        $res = mysqli_fetch_assoc($sql);
        return $res[$one];
    }
    
    public function getRowsNum($method, $some = 'n', $where = 'n') {
        $this->setRowsNum($method, $some, $where);
        return $this->rows_num;
    }
    
    public function dbOptInsert($vals) {
        $fields = $this->getCols();
        $count = count($fields);
        $fields_str = '';
        $values = '';
        for ($i = 1; $i < $count; $i++) {
            if ($i < $count - 1){
                $fields_str .= '`' . $fields[$i] . '`, ';
                 $values .= "'" . preg_replace('#<.+?>|\'+#', '', $vals[$i])."', ";
            } else {
                $fields_str .= '`' . $fields[$i] . '`';
                $values .= "'" . preg_replace('#<.+?>|\'+#', '', $vals[$i]) . "'";
            }
        }
        
        return mysqli_query($this->db, "INSERT INTO `$this->table` ($fields_str) VALUES ($values)");      
         
    }
    
    public function dbInsert($subname = '') {
        $fields = $this->getCols();
        $count = count($fields);
        $fields_str = '';
        $values = '';
        for ($i = 1; $i < $count; $i++) {
            if ($i < $count - 1){
                $fields_str .= '`' . $fields[$i] . '`, ';
                 $values .= "'" . preg_replace('#<.+?>|\'+#', '', $_POST[$subname . $fields[$i]])."', ";
            } else {
                $fields_str .= '`' . $fields[$i] . '`';
                $values .= "'" . preg_replace('#<.+?>|\'+#', '', $_POST[$subname . $fields[$i]]) . "'";
            }
        }
        
        return mysqli_query($this->db, "INSERT INTO `$this->table` ($fields_str) VALUES ($values)");        
    }
    
    public function dbUpdateOne($field, $val, $some, $row) {
        return mysqli_query($this->db, "UPDATE `$this->table` SET `$field`  = '$val' WHERE `$some` = '$row'");
//        return "UPDATE `$this->table` SET `$field`  = '$val' WHERE `$some` = '$row'";
    }
        
    public function dbUpdate($some, $val) {
        $fields = $this->getCols();
        $count = count($fields);
        $fields_str = '';
        $values = '';
        for ($i = 1; $i < $count; $i++) {
            if ($i < $count - 1){
                $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '', $_POST['u_' . $fields[$i]]).'", ';
            } else {
                $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '', $_POST['u_' . $fields[$i]]).'"';
            }
        }
        return mysqli_query($this->db, "UPDATE `$this->table` SET $fields_str WHERE `$some` = '$val'");
    }
    
    public function dbOptFieldsUpdate($some, $val, $fields, $vals) {
       $count = count($fields);
       $fields_str = '';
       $values = '';
       for ($i = 1; $i < $count; $i++) {
           if ($i < $count - 1){
               $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '', $vals[$i]).'", ';
           } else {
               $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '',  $vals[$i]).'"';
           }
       }
       return mysqli_query($this->db, "UPDATE `$this->table` SET $fields_str WHERE `$some` = '$val'");
   }
    
     public function dbOptUpdate($some, $val, $vals) {
        $fields = $this->getCols();
        $count = count($fields);
        $fields_str = '';
        $values = '';
        for ($i = 1; $i < $count; $i++) {
            if ($i < $count - 1){
                $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '', $vals[$i]).'", ';
            } else {
                $fields_str .= '`' . $fields[$i] . '` = "' . preg_replace('#<.+?>|\'+#', '',  $vals[$i]).'"';
            }
        }
        return mysqli_query($this->db, "UPDATE `$this->table` SET $fields_str WHERE `$some` = '$val'");
    }
    public function delRow3($some, $where, $some2, $where2, $some3, $where3) {
        return mysqli_query($this->db, "DELETE FROM `$this->table` WHERE `$where` = '$some' AND `$where2` = '$some2' AND `$where3` = '$some3'");
    }
    public function delRow($some, $where) {
        return mysqli_query($this->db, "DELETE FROM `$this->table` WHERE `$where` = '$some'");
    }
        
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    private function setRowsNum($method, $some = 'n', $where = 'n') {
        if ($method == 'all'){
            $sql = $this->getAll();
        } elseif ($method = 'some') {
            $sql = $this->getSome($some, $where);            
        }
        
        $this->rows_num = mysqli_num_rows($sql);
        return $this;
    }

}