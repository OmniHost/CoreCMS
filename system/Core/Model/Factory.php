<?php

namespace Core\Model;

class Factory {

    protected $table;
    protected $primary_key = 'id';
    protected $alias = '';
    protected $sql;
    protected $where = array();
    protected $order = '';
    protected $query;
    protected $join;
    protected $group;
    protected $limit = '';

    public function __construct($table, $primary_key = 'id') {
        $this->table = $table;
        $this->primary_key = $primary_key;
        $this->select();
    }

    /**
     * Returns object from registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    /**
     * Sets object to the registry
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    public function setAlias($key) {
        $this->alias = $key;
        return $this;
    }

    public function join($table, $on, $type = 'LEFT') {
        $this->join[] = $type . ' JOIN ' . $table . ' on ' . $on;
        return $this;
    }

    public function reset() {
        $this->sql = '';
        $this->alias = '';
        $this->where = array();
        $this->order = '';
        $this->limit = '';
        $this->join = array();
        return $this;
    }

    public function select($columns = '*', $where = array()) {
        $this->sql = "Select " . $columns . " from #__" . $this->table . " ";
        if ($this->alias) {
            $this->sql .= $this->alias . " ";
        }

        if ($this->where) {
            if (is_array($this->where)) {
                foreach ($this->where as $k => $v) {
                    $this->where($k, $v);
                }
            }
        }
        return $this;
    }

    public function orderBy($order) {
        $this->order = $order;
        return $this;
    }

    public function where($col, $val = false, $type = 'AND') {
        
        if($val === false){
            $this->where[] = $type .' ' . $col;
            return $this;
        }
        
        $colp = explode(" ", $col);

        $column = explode(".", $colp[0]);
        $col = implode("`.`", $column);

        $q = $type . " `" . $col . "` ";
        if (!empty($colp[1])) {
            $q .= $colp[1] . " " . $this->db->quote($val);
        } else {
            $q .= ' = ' . $this->db->quote($val);
        }

        $this->where[] = $q;
        return $this;
    }

    public function whereIn($col, $vals = array(), $type = 'AND') {
        if(empty($vals)){
           return $this;
        }
        $vals = $this->db->quote($vals);
        $colp = explode(" ", $col);

        $column = explode(".", $colp[0]);
        $col = implode("`.`", $column);

        $q = $type . "`" . $col . "` in (" . implode(",",$vals) . ") ";
        $this->where[] = $q;
        return $this;
    }

    public function limit($number, $start = 0) {
        $this->limit = " limit $start, $number";
        return $this;
    }

    public function find($val){
        if($this->alias){
            $this->where($this->alias . '.' . $this->primary_key, $val);
        }else{
            $this->where($this->primary_key, $val);
        }
        
       
       $this->limit(1);
       $this->findAll();
       return $this;
    }
    
    public function findAll() {
        $this->query = $this->db->query($this->__build());
        return $this;
    }

    public function getData() {
        return $this->query->rows;
    }
    
    public function getDataPair($key){
        $rows = $this->getData();
        $return = array();
        foreach($rows as $row){
            $return[$row[$key]] = $row;
        }
        return $return;
    }
    
    public function groupBy($groupby){
        $this->group = $groupby;
        return $this;
    }

    public function getRow() {
        return $this->query->row;
    }

    protected function __build() {
        $sql = $this->sql;

        if ($this->join) {
            $sql .= implode(" ", $this->join);
        }

        if ($this->where) {
            $sql .= ' WHERE 1 ';
            $sql .= implode(" ", $this->where);
        }
        
        if($this->group){
            $sql .= " group by " . $this->group . " ";
        }
        
        if ($this->order) {
            $sql .= " ORDER BY " . $this->order;
        }
        if ($this->limit) {
            $sql .= $this->limit;
        }


        return $sql;
    }

    //Utilities :!-)
}
