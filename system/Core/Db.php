<?php

namespace Core;

class Db {

    /**
     * holds the current database driver instance
     * @var database class 
     */
    private $driver;

    /**
     * holds collection of database connections
     * @var array 
     */
    private $drivers = array();
    private $prefix = '';
    private $prefixes = array();
    private $queries = array();
    public $profile = false;

    /**
     * Constructs an instance of the db class
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($driver, $hostname, $username, $password, $database, $prefix = '', $profile = false) {
        $driver = ucfirst(strtolower($driver));
        $file = __DIR__ . '/Db/' . $driver . '.php';
        if (file_exists($file)) {
            require_once($file);

            $class = '\\Core\\Db\\' . $driver;

            $this->drivers['default'] = new $class($hostname, $username, $password, $database);
            $this->driver = $this->drivers['default'];
            $this->prefix = $prefix;
            $this->prefixes['default'] = $prefix;
        } else {
            exit('Error: Could not load database driver type ' . $driver . '!');
        }
        $this->profile = $profile;
    }

    /**
     * Creates a connection to a secondary database and if activate = true sets it as the current connection
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $identifier
     * @param boolean $activate
     */
    public function addConnection($driver, $hostname, $username, $password, $database, $identifier, $prefix = '', $activate = false) {
        $driver = ucfirst(strtolower($driver));
        $file = __DIR__ . '/Db/' . $driver . '.php';
        if (file_exists($file)) {
            require_once($file);

            $class = '\\Core\\Db\\' . $driver;

            $this->drivers[$identifier] = new $class($hostname, $username, $password, $database);
            $this->prefixes[$identifier] = $prefix;
            if ($activate) {
                $this->selectConnection($identifier);
            }
        } else {
            exit('Error: Could not load database driver type ' . $driver . '!');
        }
    }

    /**
     * switch between different database connections
     * @param string $identifier
     */
    public function selectConnection($identifier = 'default') {
        if (!isset($this->drivers[$identifier])) {
            exit('Error: ' . $identifier . ' Connection not found');
        }
        $this->driver = $this->drivers[$identifier];
        $this->prefix = $this->prefixes[$identifier];
    }

    /**
     * check if there is a database connection
     * @param string $identifier
     */
    public function hasConnection($identifier = 'default') {
        return isset($this->drivers[$identifier]);
    }

    /**
     * wrapper to connections query method
     * @param string $sql
     * @return \stdClass|boolean
     */
    public function query($sql) {

        if ($this->profile) {
            $query_time = (time() + microtime());
        }
        $sql = $this->prepair($sql);
        $res = $this->driver->query($sql);
        if ($this->profile) {
            $exec_time = (time() + microtime());
            $exec_time = round($exec_time - $query_time, 4);
            $trace = debug_backtrace();

            $filename = (isset($trace[0]['file'])) ? str_replace(DIR_ROOT, '', $trace[0]['file']) . ' - Line ' . $trace[0]['line'] : '---';
            $this->queries[] = array(
                'query' => $sql,
                'file' => $filename,
                'time' => $exec_time
            );
        }
        return $res;
    }

    /**
     * wrapper to connection escaped string method
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return $this->driver->escape($value);
    }

    /**
     * wrapper to return number of affected rows from the connection
     * @return int
     */
    public function countAffected() {
        return $this->driver->countAffected();
    }

    /**
     * Wrapper to return the last insert id from the connection
     * @return int
     */
    public function getLastId() {
        return $this->driver->getLastId();
    }

    public function insertId() {
        return $this->driver->getLastId();
    }

    /**
     * quotes and optionally escapes the variable for the database query
     * @param string $text
     * @param boolean $escape
     * @return string
     */
    public function quote($text, $escape = true) {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = $this->quote($v, $escape);
            }

            return $text;
        } else {
            return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
        }
    }

    /**
     * updates the query to replace the prefix placeholder with the prefix on database table names
     * @param string $sql
     * @return string
     */
    public function prepair($sql) {
        return str_replace("#__", $this->prefix, $sql);
    }

    public function fetchRow($sql) {
        $query = $this->query($sql);
        return $query->row;
    }

    public function fetchAll($sql) {
        $query = $this->query($sql);
        return $query->rows;
    }

    /**
     * Profiler
     */
    public function profile() {
        if (count($this->queries) && BASE_REQUEST_TYPE != 'ajax') {
            if (BASE_REQUEST_TYPE == 'cli' || !$this->profile) {
                //      print_r($this->queries);
            } else {

                echo '<div class="table-responsive"><table class="table table-responsive table-condensed table-striped">';
                echo '<thead><tr><th>Query</th><th>File</th><th>Time</th></thead>';
                echo '<tbody>';
                $time = 0;
                foreach ($this->queries as $q) {
                    echo '<tr><td>' . $q['query'] . '</td><td>' . $q['file'] . '</td><td>' . $q['time'] . '</td></tr>';
                    $time += $q['time'];
                }
                echo '<tr><td>Total Queries: ' . count($this->queries) . '</td><td>Total Time: ' . $time . '</td><td></td></tr>';
                echo '</tbody></table></div>';
            }
        }
    }

}
