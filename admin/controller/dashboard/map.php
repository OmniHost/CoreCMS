<?php
/**
 * @name Dashboard Widget - Activity Map
 */
class ControllerDashboardMap extends \Core\Controller {

    public function index() {
        $this->load->language('dashboard/map');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_users'] = $this->language->get('text_users');
        $data['text_online'] = $this->language->get('text_online');

        $data['token'] = $this->session->data['token'];

        $this->document->addStyle('view/plugins/jvectormap/jquery-jvectormap-1.2.2.css');
        $this->document->addScript('view/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js');
        $this->document->addScript('view/plugins/jvectormap/jquery-jvectormap-world-mill-en.js');



        $this->template = 'dashboard/map.phtml';
        $this->data = $data;
        return $this->render();
    }

    public function map() {
        $json = array();


        $this->load->model('report/map');
        $results = $this->model_report_map->mapTotalUsers();
        $onlineresults = $this->model_report_map->mapTotalOnlineUsers();
        
        foreach($results as $result){
            $json[strtoupper($result['iso_code_2'])]['users'] = $result['total'];
        }
        foreach($onlineresults as $result){
            $json[strtoupper($result['iso_code_2'])]['online'] = $result['total'];
        }

    /*    $this->load->model('localisation/country');

        $results = $this->model_localisation_country->getCountries();

        foreach ($results as $result) {
            $json[strtolower($result['iso_code_2'])] = array(
                'users' => $this->model_report_map->getTotalUsersByCountry($result['iso_code_2']),
                'online' => $this->model_report_map->getTotalOnlineUsersByCountry($result['iso_code_2'])
            );
        }
     * 
     */

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
