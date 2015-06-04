<?php

class ControllerCommonCaptcha extends \Core\Controller {

    public function index() {
        $this->load->library('captcha');

        $captcha = new Captcha();


        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

}
