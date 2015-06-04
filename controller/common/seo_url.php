<?php

class ControllerCommonSeoUrl extends \Core\Controller {

    public function index() {
        // Add rewrite to url class
        //    if ($this->config->get('config_seo_url')) {
        $this->url->addRewrite($this);
        //     }
        // Decode URL
        if (isset($this->request->get['_route_'])) {
            unset($this->request->get['p']);
            $parts = explode('/', $this->request->get['_route_']);

            foreach ($parts as $part) {
                $query = $this->db->query("SELECT * FROM url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

                if ($query->num_rows) {
                    $url = explode('=', $query->row['query']);

                   
                    if($url[0] == 'ams_page_id') {
                        $this->request->get['ams_page_id'] = $url[1];
                        $page = $this->db->fetchRow("select namespace from #__ams_pages where ams_page_id = '" . (int)$url[1] . "'");
                        $this->request->get['p'] = str_replace(".","/", $page['namespace']);
                    }
                } else {
                    $this->request->get['p'] = 'error/not_found';
                }
            }
            

            if (isset($this->request->get['p'])) {
                return $this->forward($this->request->get['p']);
            }
        }
    }

    public function rewrite($link) {
        $url_info = parse_url(str_replace('&amp;', '&', $link));

        $url = '';

        $data = array();

        parse_str($url_info['query'], $data);

        foreach ($data as $key => $value) {
            if (isset($data['p'])) {
                if($key == 'ams_page_id'){
                     $query = $this->db->query("SELECT * FROM url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];

                        unset($data[$key]);
                    }
                }
                
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
        } elseif ($data['p'] == 'common/home') {

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

            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path'])  . $query;
        } else {

            return str_replace("/index.php", "/", $link);
        }
    }

}
