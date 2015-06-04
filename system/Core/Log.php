<?php


namespace Core;
class Log {

    /**
     * Holds the filename for the log
     * @var string 
     */
    private $filename;

    /**
     * 
     * @param string $filename
     */
    public function __construct($filename) {
        $this->filename = $filename;
    }

    /**
     * Write a message to the log file
     * @param string $message
     */
    public function write($message) {
        $file = DIR_LOGS . $this->filename;

        $handle = fopen($file, 'a+');

        fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");

        fclose($handle);
    }



}
