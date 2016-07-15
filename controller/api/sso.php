<?php

class ControllerApiSso extends Core\Controller\Api {

    public function index() {
        $json = array();
        $json['name'] = '';
        $json['photourl'] = '';
        $this->load->model('api/api');
        $this->load->model('tool/image');
        $this->load->model('account/customer_group');
        $sso = $this->model_api_api->getCredentials($this->config->get('config_sso_id'));

        if ($sso['username'] == $this->request->get['client_id']) {
            if ($this->customer->getId()) {
                $json['uniqueid'] = $this->customer->getId();
                $json['name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
                $json['email'] = $this->customer->getEmail();

                if ($this->customer->getProfileImg()) {
                    $json['photourl'] = $this->config->get('config_url') . $this->model_tool_image->resize($this->customer->getProfileImg(), 150, 150);
                } else {
                    $json['photourl'] = $this->config->get('config_url') . $this->model_tool_image->resize('no_photo.jpg', 150, 150);
                }
                $roles = array();
                //GET USER GROUPS
                $groups = $this->customer->getGroupId();
                foreach ($groups as $group) {
                    $roles[] = $this->model_account_customer_group->getCustomerGroup($group)['name'];
                }
                $json['roles'] = implode(",", $roles);
            } else {
                
            }
            $sigarr = array_change_key_case($json);
            ksort($sigarr);
            $String = http_build_query($sigarr, NULL, '&');
            $Signature = $this->model_api_api->JsHash($String . $sso['password'], TRUE);
            $json['client_id'] = $sso['username'];
            $json['signature'] = $Signature;
        } else {
            $json['error'] = 'Incorrect client id';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($this->renderJSON($json));
    }

    public function login() {
        $json = array();
        $this->load->model('account/customer');
        if (!empty($this->request->get['redir'])) {
            $this->session->data['sso_redirect'] = urldecode($this->request->get['redir']);
        }
        if ($this->customer->isLogged()) {
            if (isset($this->session->data['sso_redirect'])) {
                return $this->redirect($this->session->data['sso_redirect']);
            } else {
                echo '<script>window.top.location = "' . $this->url->link('account/account', '', 'SSL') . '";</script>';
                exit;
            }
        }
        $this->load->language('account/login');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateLogin()) {
            unset($this->session->data['guest']);

            // Add to activity log
            $this->load->model('account/activity');

            $activity_data = array(
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
            );

            $this->model_account_activity->addActivity('login', $activity_data);

            if (isset($this->request->post['remember_me'])) {
                setcookie("corecms_customer_remember", $this->encryption->encrypt($activity_data['customer_id']), time() + (10 * 365 * 24 * 60 * 60), "/");
            }

            if (isset($this->session->data['sso_redirect'])) {
                return $this->redirect($this->session->data['sso_redirect']);
                // Added strpos check to pass McAfee PCI compliance test 
            } elseif (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                //  $this->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
                echo '<script>window.top.location = "' . str_replace('&amp;', '&', $this->request->post['redirect']) . '";</script>';
                exit;
            } else {
                echo '<script>window.top.location = "' . $this->url->link('account/account', '', 'SSL') . '";</script>';
                exit;
            }
        }

        $data['text_new_customer'] = $this->language->get('text_new_customer');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_register_account'] = $this->language->get('text_register_account');
        $data['text_returning_customer'] = $this->language->get('text_returning_customer');
        $data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
        $data['text_remember_me'] = $this->language->get('text_remember_me');

        $data['text_forgotten'] = $this->language->get('text_forgotten');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_password'] = $this->language->get('entry_password');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_login'] = $this->language->get('button_login');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['account_register'] = $this->config->get('config_account_register');
        $data['remember_me'] = $this->config->get('config_account_rememberme');


        $data['action'] = $this->url->link('api/sso/login', '', 'SSL');
        $data['register'] = $this->url->link('account/register', '', 'SSL');
        $data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

        // Added strpos check to pass McAfee PCI compliance test 
        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
            $data['redirect'] = $this->request->post['redirect'];
        } elseif (isset($this->session->data['redirect'])) {
            $data['redirect'] = $this->session->data['redirect'];

            unset($this->session->data['redirect']);
        } else {
            $data['redirect'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        $this->template = 'api/sso_login.phtml';

        $data['title'] = $this->config->get('config_meta_title');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $this->data = $data;



        $this->response->setOutput($this->render());
    }

    protected function validateLogin() {
        // Check how many login attempts have been made.
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }

        // Check if customer has been approved.
        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

        if ($customer_info && !$customer_info['approved']) {
            $this->error['warning'] = $this->language->get('error_approved');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            }
        }

        return !$this->error;
    }

    public function logout() {
        $json = array();
        $json['action'] = 'Login User';
        echo '<script>window.top.location = "' . $this->url->link('account/logout', '', 'SSL') . '";</script>';

        /*   $this->response->addHeader('Content-Type: application/json');
          $this->response->setOutput(json_encode($json)); */
    }

    public function register() {
        $json = array();
        $json['action'] = 'Register User';
 echo '<script>window.top.location = "' . $this->url->link('account/register', '', 'SSL') . '";</script>';
    }

}
