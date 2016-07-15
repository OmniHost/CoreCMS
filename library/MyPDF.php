<?php

Class MyPDF {

    public $mode = '';
    public $format = 'A4';
    public $default_font_size = 0;
    public $default_font = '';
    public $mgl = 15;
    public $mgr = 15;
    public $mgt = 16;
    public $mgb = 16;
    public $mgh = 9;
    public $mgf = 9;
    public $orientation = 'P';

    /**
     *
     * @var \mPDF;
     */
    protected $mpdf;

    public function init() {
        $this->mpdf = new \mPDF($this->mode, $this->format, $this->default_font_size, $this->default_font, $this->mgl, $this->mgr, $this->mgt, $this->mgb, $this->mgh, $this->mgf, $this->orientation);
        return $this->mpdf;
    }

    /**
     * 
     * @param type $name
     * @param type $arguments
     * @return type.
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->mpdf, $name), $arguments);
    }

}
