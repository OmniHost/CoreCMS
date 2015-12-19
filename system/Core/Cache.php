<?php

namespace Core;
class Cache {

    /**
     * How long should the cache be viable for
     * @var int 
     */
    private $expire = 3600;
    
    
    public function __construct() {
        $files = glob(DIR_CACHE . 'cache.*');

        if ($files) {
            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);
                if ($time < time()) {
                    if (is_file($file)) {
                        @unlink($file);
                        @clearstatcache();
                    }
                }
            }
        }
    }

    /**
     * Gets value from cache
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

        if ($files) {
            $cache = file_get_contents($files[0]);

            $data = unserialize($cache);

            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);

                if ($time < time()) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }

            return $data;
        }
    }

    /**
     * Sets value to cache
     * @param string $key
     * @param mixed $value
     * @param boolean|int $expire (if set sets the specific cache key valid for that period of time) seconds
     */
    public function set($key, $value, $expire = false) {
        $this->delete($key);

        if (!$expire) {
            $expire = $this->expire;
        }

        $file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $expire);

        $handle = fopen($file, 'w');

        fwrite($handle, serialize($value));

        fclose($handle);
    }

    /**
     * Deletes a cache key
     * @param string $key
     */
    public function delete($key) {
        
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Clears the cache folder
     */
    public function clear() {
        $files = glob(DIR_CACHE . 'cache*');
        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        
    }
    /**
     * @non functional
     */
    public function clearImageCache(){
        $files = glob(DIR_IMAGE . 'cache/*');
        if ($files) {
            $this->_deleteImages($files);
        }
    }
    
    public function _deleteImages($files){
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }elseif(is_dir($file)){
                $sfiles = glob($file);
                if($sfiles){
                    $this->_deleteImages($sfiles);
                }
            }
        }
    }

}
