<?php

class ControllerModulePage extends \Core\Controller {

    public function index($setting) {

        $this->load->model('cms/page');

        $page = $this->model_cms_page->loadPageObject($setting['ams_page_id'])->toArray();

        if (!$page || !$page['status']) {
            return '';
        }
        $this->load->model('setting/rights');
        $allowed = $this->model_setting_rights->getRight($setting['ams_page_id'], 'ams_page');
        if (!$allowed) {
            return '';
        }

        $this->model_cms_page->updateViews();
        $this->language->load('cms/page');
        $this->event->trigger('ams.page.name', $page['name']);
        $page['content'] = html_entity_decode($page['content'], ENT_QUOTES, 'UTF-8');
        $this->event->trigger('ams.page.content', $page['content']);

        $this->data['page'] = $page;

        $this->data['setting'] = $setting;
        $this->template = 'module/page.phtml';
        return $this->render();
    }

}
