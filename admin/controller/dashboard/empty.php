<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 *  @name Dashboard Widget - Activity
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
 

class ControllerDashboardEmpty extends \Core\Controller {

    public function index() {
        $this->load->language('dashboard/empty');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['token'] = $this->session->data['token'];

        $data['activities'] = array();

        

        $this->template = 'dashboard/empty.phtml';
        $this->data = $data;
        return $this->render();
    }

}
