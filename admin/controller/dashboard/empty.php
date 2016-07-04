<?php

/**
 * @name Dashboard Widget - Empty Sample
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
