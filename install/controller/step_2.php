<?php

class ControllerStep2 extends \Core\Controller {

    private $error = array();

    public function index() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->redirect($this->url->link('step_3'));
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['action'] = $this->url->link('step_2');

        /* 	$this->data['config_catalog'] = DIR_OpenDeal . 'config.php';
          $this->data['config_admin'] = DIR_OpenDeal . 'admin/config.php'; */

        $this->data['cache'] = DIR_ROOT . '_cache';
        $this->data['logs'] = DIR_ROOT . '_logs';
        $this->data['image'] = DIR_ROOT . 'img';
        $this->data['image_cache'] = DIR_ROOT . 'img/cache';
        $this->data['image_data'] = DIR_ROOT . 'img/uploads';
        $this->data['config'] = DIR_ROOT . 'config.php';
        $this->data['config_admin'] = DIR_ROOT . 'admin/config.php';
        $this->data['modification'] = DIR_MODIFICATION;
        $this->data['upload'] = DIR_SYSTEM . '/upload/';

        $this->data['back'] = $this->url->link('step_1');

        $this->template = 'step_2';
        $this->children = array(
            'header',
            'footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (phpversion() < '5.3') {
            $this->error['warning'] = 'Warning: You need to use PHP5.3 or above for CoreCMS to work!';
        }

        if (!ini_get('file_uploads')) {
            $this->error['warning'] = 'Warning: file_uploads needs to be enabled!';
        }

        if (ini_get('session.auto_start')) {
            $this->error['warning'] = 'Warning: CoreCMS will not work with session.auto_start enabled!';
        }

        if (!extension_loaded('mysql')) {
            $this->error['warning'] = 'Warning: MySQL extension needs to be loaded for CoreCMS to work!';
        }

        if (!extension_loaded('gd')) {
            $this->error['warning'] = 'Warning: GD extension needs to be loaded for CoreCMS to work!';
        }

        if (!extension_loaded('curl')) {
            $this->error['warning'] = 'Warning: CURL extension needs to be loaded for CoreCMS to work!';
        }

        if (!function_exists('mcrypt_encrypt')) {
            $this->error['warning'] = 'Warning: mCrypt extension needs to be loaded for CoreCMS to work!';
        }

        if (!extension_loaded('zlib')) {
            $this->error['warning'] = 'Warning: ZLIB extension needs to be loaded for CoreCMS to work!';
        }

        if (!file_exists(DIR_ROOT . 'config.php')) {
            $this->error['warning'] = 'Warning: config.php does not exist. You need to rename config-dist.php to config.php!';
        } elseif (!is_writable(DIR_ROOT . 'config.php')) {
            $this->error['warning'] = 'Warning: config.php needs to be writable for CoreCMS to be installed!';
        }

        if (!file_exists(DIR_ROOT . 'admin/config.php')) {
            $this->error['warning'] = 'Warning: config.php does not exist. You need to rename admin/config-dist.php to admin/config.php!';
        } elseif (!is_writable(DIR_ROOT . 'admin/config.php')) {
            $this->error['warning'] = 'Warning: admin/config.php needs to be writable for CoreCMS to be installed!';
        }



        if (!is_writable(DIR_ROOT . '_cache')) {
            $this->error['warning'] = 'Warning: Cache directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_ROOT . '_logs')) {
            $this->error['warning'] = 'Warning: Logs directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_ROOT . 'img')) {
            $this->error['warning'] = 'Warning: Image directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_ROOT . 'img/cache')) {
            $this->error['warning'] = 'Warning: Image cache directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_ROOT . 'img/uploads')) {
            $this->error['warning'] = 'Warning: Image data directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_SYSTEM . '/upload')) {
            $this->error['warning'] = 'Warning: System Upload directory needs to be writable for CoreCMS to work!';
        }

        if (!is_writable(DIR_MODIFICATION)) {
            $this->error['warning'] = 'Warning: Modifications directory needs to be writable for CoreCMS to work!';
        }



        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>