<?php

class ControllerUserLogin extends \Core\Controller {

    private $error = array();

    public function index() {
        $this->language->load('user/login');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
            $this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->session->data['token'] = md5(mt_rand());

            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0 )) {
                $this->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
            } else {
                $this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
            }
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_login'] = $this->language->get('text_login');
        $this->data['text_forgotten'] = $this->language->get('text_forgotten');

        $this->data['entry_username'] = $this->language->get('entry_username');
        $this->data['entry_password'] = $this->language->get('entry_password');

        $this->data['button_login'] = $this->language->get('button_login');

        if ((isset($this->session->data['token']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token']))))) {
            $this->error['warning'] = $this->language->get('error_token');
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['action'] = $this->url->link('user/login', '', 'SSL');

        if (isset($this->request->post['username'])) {
            $this->data['username'] = $this->request->post['username'];
        } else {
            $this->data['username'] = '';
        }

        if (isset($this->request->post['password'])) {
            $this->data['password'] = $this->request->post['password'];
        } else {
            $this->data['password'] = '';
        }

        if (isset($this->request->get['p'])) {
            $route = $this->request->get['p'];

            unset($this->request->get['p']);

            if (isset($this->request->get['p'])) {
                unset($this->request->get['p']);
            }

            $url = '';
            unset($this->request->get['token']);
            if ($this->request->get) {
                $url .= http_build_query($this->request->get);
            }

            $this->data['redirect'] = $this->url->link($route, $url, 'SSL');
        } elseif (isset($this->request->post['redirect'])) {
            $this->data['redirect'] = $this->request->post['redirect'];
        } else {
            $this->data['redirect'] = '';
        }


        $this->data['forgotten'] = $this->url->link('user/forgotten', '', 'SSL');



        $this->data['title'] = $this->language->get('heading_title');
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        $this->template = 'user/login.phtml';


        $this->response->setOutput($this->render());
    }

    public function status() {
        $route = '';

        if (isset($this->request->get['p'])) {
            $part = explode('/', $this->request->get['p']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }
        }

        $ignore = array(
            'user/login',
            'user/forgotten',
            'user/reset'
        );

        if (!$this->user->isLogged() && !in_array($route, $ignore)) {
            return $this->forward('user/login');
        }

        if (isset($this->request->get['p'])) {

            $ignore = array(
                'user/login',
                'user/logout',
                'user/forgotten',
                'user/reset',
                'error/not_found',
                'error/permission'
            );

            $config_ignore = array();

            if ($this->config->get('config_token_ignore')) {
                $config_ignore = $this->config->get('config_token_ignore');
            }

            $ignore = array_merge($ignore, $config_ignore);


            if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {

                return $this->forward('user/login');
            }
        } else {
            if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
                return $this->forward('user/login');
            }
        }
    }

    public function permission() {
        if (isset($this->request->get['p'])) {
            $route = '';

            $part = explode('/', $this->request->get['p']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }

            $ignore = array(
                'common/home',
                'user/login',
                'user/logout',
                'user/forgotten',
                'user/reset',
                'error/not_found',
                'error/permission'
            );

            if (!in_array($route, $ignore) && !$this->user->hasPermission('access', $route)) {

                return $this->forward('error/permission');
            }
        }
    }

    protected function validate() {
        if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
            $this->error['warning'] = $this->language->get('error_login');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>