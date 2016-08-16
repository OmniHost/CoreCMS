<?php



namespace Core;

abstract class Model {

   
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

    //Utilities :!-)

    public function factory($table, $primary_key = 'id') {
        require_once('Model/Factory.php');
        $model = new \Core\Model\Factory($table, $primary_key);
        return $model;
    }
    
    public function reset(){
        return false;
    }

}
