<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Newsletter Send Handler
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerMarketingNewsletterSend extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->load->language('marketing/newsletter/send');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('marketing/newsletter');
        $this->load->model('marketing/newsletter/group');

        $data['groups'] = array();
        $data['groups'][0] = $this->language->get('text_all_members');

        $groups = $this->model_marketing_newsletter_group->getGroups();
        foreach ($groups as $group) {
            $data['groups'][$group['group_id']] = $group['group_name'];
        }


        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');

        $data['action'] = $this->url->link('marketing/newsletter/send', 'newsletter_id=' . (int) $this->request->get['newsletter_id'] . '&token=' . $this->session->data['token'] . $url);

        $data['dont_send_duplicate'] = 0;
        $data['group_ids'] = array();
        $data['error_group_send_to'] = '';



        if (isset($this->request->post['send_later'])) {
            $data['send_later'] = $this->request->post['send_later'];
        } else {
            $data['send_later'] = DATE("Y-m-d H:i:s", time());
        }


        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (!isset($this->request->post['dont_send_duplicate'])) {
                $data['dont_send_duplicate'] = 0;
            }
            $data['group_ids'] = !empty($this->request->post['group_ids']) ? $this->request->post['group_ids'] : array();

            if (empty($data['group_ids'])) {
                $data['error_group_send_to'] = $this->language->get('error_group_send_to');
            } else {
                $post = array(
                    'group_ids' => $data['group_ids'],
                    'dont_send_duplicate' => $data['dont_send_duplicate'],
                    'send_later' => strtotime($data['send_later'])
                );
                if ($post['send_later'] < time()) {
                    $post['send_later'] = time();
                }

                $newsletter_id = $this->request->get['newsletter_id'];

                if ($this->model_marketing_newsletter->createSend($newsletter_id, $post)) {

                    $this->redirect($this->url->link('marketing/newsletter/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                } else {
                    $data['error_group_send_to'] = $this->language->get('error_group_no_members');
                }
            }
        }


        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');

        $this->response->setOutput($this->render('marketing/newsletter/send.phtml', $data));
    }

}
