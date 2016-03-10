<?php

class ControllerCommonSeoUrl extends \Core\Controller {

    protected $_internals = array(
        'account' => 'account/account',
        'blog' => 'blog/blog',
        'contact-us' => 'common/contact',
        'maintenance' => 'common/maintenance',
        
    );

    public function index() {
        // Add rewrite to url class
        //    if ($this->config->get('config_seo_url')) {
        $this->url->addRewrite($this);
        //     }
        // Decode URL

        if (isset($this->request->get['_route_'])) {
            unset($this->request->get['p']);
            
            if($this->request->get['_route_'] == 'robots.txt'){
                 header('X-Powered-By: CoreCMS - http://www.omnihost.co.nz');
                 header('Content-Type: text/plain; charset=utf-8;');
                 echo $this->config->get('config_robots');
                 exit;
            }

            if (isset($this->_internals[$this->request->get['_route_']])) {
                $this->request->get['p'] = $this->_internals[$this->request->get['_route_']];
            } else {

                $parts = explode('/', $this->request->get['_route_']);

                foreach ($parts as $part) {
                    $query = $this->db->query("SELECT * FROM #__url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

                    if ($query->num_rows) {
                        $url = explode('=', $query->row['query']);


                        if ($url[0] == 'ams_page_id') {
                            $this->request->get['ams_page_id'] = $url[1];
                            $page = $this->db->fetchRow("select namespace from #__ams_pages where ams_page_id = '" . (int) $url[1] . "'");
                            $this->request->get['p'] = str_replace(".", "/", $page['namespace']);
                        }
                    } else {
                        $this->request->get['p'] = $this->request->get['_route_'];
                    }
                }
            }

            if (isset($this->request->get['p'])) {
                $this->template($this->request->get['p']);
                unset($this->request->get['_route_']);
                return $this->forward($this->request->get['p']);
            }
            $this->template();
        }
    }

    public function rewrite($link) {
        $url_info = parse_url(str_replace('&amp;', '&', $link));

        $url = '';

        $data = array();

        parse_str($url_info['query'], $data);


        
        foreach ($data as $key => $value) {
            if (isset($data['p'])) {
                if ($key == 'ams_page_id') {
                    $query = $this->db->query("SELECT * FROM #__url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];

                        unset($data[$key]);
                    }
                }
            }
            if (array_search($data['p'], $this->_internals)) {
                $url .= '/' . array_search($data['p'], $this->_internals);
            }
            if ($data['p'] == 'common/home') {
                $url .= '/';
            }
        }

        if ($url) {
            unset($data['p']);

            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . $key . '=' . $value;
                }

                if ($query) {
                    $query = '?' . trim($query, '&');
                }
            }

            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
        } else {
            $url .= '/' . $data['p'];
            unset($data['p']);
            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . $key . '=' . $value;
                }

                if ($query) {
                    $query = '?' . trim($query, '&');
                }
            }

            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
        }
    }
    
    
    public function template($route = 'common/home') {

        //Layout Override::
        $this->load->model('design/layout');
        $layout_id = 0;

        if (isset($this->request->get['ams_page_id'])) {
            $layout_id = $this->model_design_layout->getAmsLayout($this->request->get['ams_page_id']);
        } else {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }
     
        

        $template = $this->model_design_layout->getTemplate($layout_id);
        if ($template) {
            $this->config->set('config_template', $template);
      
        }
    }

}
