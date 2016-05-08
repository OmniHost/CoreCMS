<?php

class ModelUpgrade extends \Core\Model {

    public $db;
    
    public function mysql() {

      

        $config = $this->config;
        
  
        
        $this->db = $db = new \Core\Db($config->get('DB_DRIVER'), $config->get('DB_HOSTNAME'), $config->get('DB_USERNAME'), $config->get('DB_PASSWORD'), $config->get('DB_DATABASE'), $config->get('DB_PREFIX'));

       // $k = $db->query("select value from #__setting where `code`='_version_' and `key`='_version_");
        
        $k = $db->query("select value from #__setting where `code`='_version_' and `key`='_version_'");

        
        $version = isset($k->row['value']) ? $k->row['value'] : '1.0.1';
   

        while (VERSION != $version) {
         
            $file = DIR_APPLICATION . 'upgrades/' . $version . '.sql';
     
            if (is_file($file)) {
                $lines = file($file);

                if ($lines) {
                    $sql = '';

                    foreach ($lines as $line) {
                        if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                            $sql .= $line;

                            if (preg_match('/;\s*$/', $line)) {

                                $db->query($sql);
                              

                                $sql = '';
                            }
                        }
                    }
                   
                }
            }else{
                DIE("UPGRADE FILE MISSING");
            }
             $k = $this->db->query("select `value` from #__setting where `code`='_version_' and `key`='_version_'");
                    $version = $k->row['value'];
        }
    }

}
