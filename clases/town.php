<?php
require_once('mysql.php');
class Town extends Mysql {
    private $name;
    private $q_index;
    private $prices = 'prices';
//    private $
    public function __construct($table, $index) {
        parent::__construct($table);
        $this->name = $this->showOne($index, 'queue_index', 'name_ru');
        $this->q_index = $index;
        
    }
    
    
    public function getName() {
        return $this->name;
    }
    
    public function getGrAdltPrices($int_id) {
        return $this->getSome3FromOtherTable($this->prices, $this->q_index, 'town', $int_id, 'int_id', 'groupadlt', 'type');
    }
    public function getGrChldPrices($int_id) {
        return $this->getSome3FromOtherTable($this->prices, $this->q_index, 'town', $int_id, 'int_id', 'groupchld', 'type');
    }
    public function getIndPrices($int_id) {
        return $this->getSome3FromOtherTable($this->prices, $this->q_index, 'town', $int_id, 'int_id', 'ind', 'type');
    }
}