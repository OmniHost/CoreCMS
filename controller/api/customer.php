<?php

class ControllerApiCustomer extends \Core\Controller\Api {

    public function index() {
        $this->load->language('api/customer');

        // Delete past customer in case there is an error
        unset($this->session->data['customer']);


        if (!$this->testAuth()) {
            return;
        }

        $json = array();
        // Add keys for missing post vars
        $keys = array(
            'customer_id',
            'customer_group_id',
            'firstname',
            'lastname',
            'email',
            'telephone',
        );

        foreach ($keys as $key) {
            if (!isset($this->request->post[$key])) {
                $this->request->post[$key] = '';
            }
        }

        // Customer
        if ($this->request->post['customer_id']) {
            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomer($this->request->post['customer_id']);

            if (!$customer_info || !$this->customer->login($customer_info['email'], '', true)) {
                $json['error']['warning'] = $this->language->get('error_customer');
            }
        }

        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $json['error']['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $json['error']['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email']))) {
            $json['error']['email'] = $this->language->get('error_email');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $json['error']['telephone'] = $this->language->get('error_telephone');
        }

        // Customer Group
        if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $this->request->post['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
					$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}

        if (!$json) {
            $this->session->data['customer'] = array(
                'customer_id' => $this->request->post['customer_id'],
                'customer_group_id' => $customer_group_id,
                'firstname' => $this->request->post['firstname'],
                'lastname' => $this->request->post['lastname'],
                'email' => $this->request->post['email'],
                'telephone' => $this->request->post['telephone'],
                'custom_field' => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : array()
            );

            $json['success'] = $this->language->get('text_success');
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
