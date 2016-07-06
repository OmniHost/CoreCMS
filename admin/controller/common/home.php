<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Administration Dashboard Page
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 *  @visibility private
 */
 

class ControllerCommonHome extends \Core\Controller {

    public function index() {
        $this->language->load('common/home');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => 'fa fa-dashboard'
        );


        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }



        $this->load->model('user/user');
        $panels = $this->model_user_user->getDashboard($this->user->getId());
        if (!$panels) {
            $panels = '{dashleft:"",dashcntr:"",dashright:"",dashhide:""}';
        }
        $this->data['panels'] = $panels;

        $this->data['widgets'] = array();
        $files = glob(DIR_APPLICATION . 'controller/dashboard/*.php');
        if ($files) {

            foreach ($files as $file) {
                $extension = basename($file, '.php');

                if ($this->user->hasPermission('access', 'dashboard/' . $extension)) {


                    $this->load->language('dashboard/' . $extension);
                    $this->data['widgets'][slug($extension)] = $this->load->controller('dashboard/' . $extension);
                }
            }
        }

        $this->event->trigger('admin.dashboard.widget', $this->data['widgets']);

        $this->template = 'common/home.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    public function positions() {
        $this->load->model("user/user");
        $this->model_user_user->saveDashboard($this->user->getId(), html_entity_decode($this->request->post['panels'], ENT_QUOTES, 'UTF-8'));
    }

}
