<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Modules - Admin Links on Front
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerModuleFeTlb extends \Core\Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/fe_tlb');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('fe_tlb', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['style1'] = $this->language->get('style1');
		$data['style2'] = $this->language->get('style2');
		$data['style3'] = $this->language->get('style3');
		$data['fun'] = $this->language->get('fun');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_style'] = $this->language->get('entry_style');
		$data['entry_links'] = $this->language->get('entry_links');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_mobile'] = $this->language->get('entry_mobile');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
				
		$this->load->language('common/menu');
		if (isset($this->request->post['fe_tlb_clink'])) {
			$data['fe_tlb_clink'] = $this->request->post['fe_tlb_clink'];
		} elseif ($this->config->has('fe_tlb_clink')) {
		   	$data['fe_tlb_clink'] = $this->config->get('fe_tlb_clink');
		} else {
			$data['fe_tlb_clink'] = array();
		}
                $data['fe_tlb_links'] = array();
               
		
		$data['fe_tlb_links'][] = array(
			'text'  => $this->language->get('Modules'),
			'value' => 'module'
		);
		$data['fe_tlb_links'][] = array(
			'text'  => $this->language->get('Users'),
			'value' => 'customer'
		);
		$data['fe_tlb_links'][] = array(
			'text'  => $this->language->get('Settings'),
			'value' => 'setting'
		);
		
                $data['fe_tlb_links'][] = array(
			'text'  => $this->language->get('Pages'),
			'value' => 'cms_page'
		);
                
                $data['fe_tlb_links'][] = array(
			'text'  => $this->language->get('Blog'),
			'value' => 'blog'
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['style'])) {
			$data['error_style'] = $this->error['style'];
		} else {
			$data['error_style'] = '';
		}
		
		if (isset($this->error['links'])) {
			$data['error_links'] = $this->error['links'];
		} else {
			$data['error_links'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/fe_tlb', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/fe_tlb', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['fe_tlb_style'])) {
			$data['fe_tlb_style'] = $this->request->post['fe_tlb_style'];
		} else {
			$data['fe_tlb_style'] = $this->config->get('fe_tlb_style');
		}
		

		if (isset($this->request->post['fe_tlb_status'])) {
			$data['fe_tlb_status'] = $this->request->post['fe_tlb_status'];
		} else {
			$data['fe_tlb_status'] = $this->config->get('fe_tlb_status');
		}

		if (isset($this->request->post['fe_tlb_mobile'])) {
			$data['fe_tlb_mobile'] = $this->request->post['fe_tlb_mobile'];
		} else {
			$data['fe_tlb_mobile'] = $this->config->get('fe_tlb_mobile');
		}
                if (isset($this->request->post['fe_tlb_fun'])) {
			$data['fe_tlb_fun'] = $this->request->post['fe_tlb_fun'];
		} else {
			$data['fe_tlb_fun'] = $this->config->get('fe_tlb_fun');
		}

		$data['header'] = $this->getChild('common/header');
		$data['footer'] = $this->getChild('common/footer');

		$this->response->setOutput($this->render('module/fe_tlb.phtml', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/fe_tlb')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['fe_tlb_style']) {
			$this->error['style'] = $this->language->get('error_style');
		}
		if (!$this->request->post['fe_tlb_clink']) {
			$this->error['link'] = $this->language->get('error_link');
		}

		return !$this->error;
	}
}