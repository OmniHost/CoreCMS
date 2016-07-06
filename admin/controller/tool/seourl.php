<?php
 /* CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Tools - Custom URLS
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */

class ControllerToolSeourl extends \Core\Controller {

    /**
     * @todo - should be edits / deletes etc.! )
     */
    public function index() {

        $data['heading_title'] = 'Customise SEO URLS';

        $data['filter_keyword'] = (!empty($this->request->get['filter_keyword'])) ? $this->request->get['filter_keyword'] : '';
        $data['filter_query'] = (!empty($this->request->get['filter_query'])) ? $this->request->get['filter_query'] : '';


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $url = '';
        if ($data['filter_keyword']) {
            $url .= '&fiter_keyword=' . urlencode(html_entity_decode($data['filter_keyword'], ENT_QUOTES, 'UTF-8'));
        }
        if ($data['filter_query']) {
            $url .= '&fiter_query=' . urlencode(html_entity_decode($data['filter_query'], ENT_QUOTES, 'UTF-8'));
        }

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Custom SEO Url'),
            'href' => $this->url->link('tool/seourl', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data = $this->gettable($data);

        $data['header'] = $this->getChild('common/header');
        $data['footer'] = $this->getChild('common/footer');
        $this->response->setOutput($this->render('tool/seourl_form.phtml', $data));
    }

    public function gettable($internal = false) {
        $this->load->model('tool/seo');
        $page = isset($this->request->get['page']) ? (int) $this->request->get['page'] : 1;
        $limit = $this->config->get('config_limit_admin');
       
        $start = ($page - 1) * $limit;

        $filter_keyword = (!empty($this->request->get['filter_keyword'])) ? $this->request->get['filter_keyword'] : '';
        $filter_query = (!empty($this->request->get['filter_query'])) ? $this->request->get['filter_query'] : '';

        $filter = array(
            'start' => $start,
            'limit' => $limit,
            'filter_keyword' => $filter_keyword,
            'filter_query' => $filter_query
        );

        $urls = $this->model_tool_seo->getAll($filter);

        $data['urls'] = $urls->rows;

        $url = '';
        if ($filter_keyword) {
            $url .= '&fiter_keyword=' . urlencode(html_entity_decode($filter_keyword, ENT_QUOTES, 'UTF-8'));
        }
        if ($filter_query) {
            $url .= '&fiter_query=' . urlencode(html_entity_decode($filter_query, ENT_QUOTES, 'UTF-8'));
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $urls->total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('tool/seourl/gettable', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
        $pagination->text = $this->language->get('text_pagination');

        $data['pagination'] = $pagination->render();


        if ($internal) {
            return array_merge($internal, $data);
        } else {
            $this->response->setOutput($this->render('tool/seourl_table.phtml', $data));
        }
    }

    public function add() {

        if (isset($this->request->post['keyword']) && isset($this->request->post['query'])) {
            $this->db->query("delete from #__url_alias where query='" . $this->db->escape($this->request->post['query']) . "' or keyword='" . $this->db->escape($this->request->post['keyword']) . "'");
            $this->db->query("insert into #__url_alias set query='" . $this->db->escape($this->request->post['query']) . "',keyword='" . $this->db->escape($this->request->post['keyword']) . "', custom=1");
        }
    }

    public function delete() {
        if (isset($this->request->get['url_alias_id'])) {
            $this->db->query("delete from #__url_alias where url_alias_id='" . (int) $this->request->get['url_alias_id'] . "'");
        }
        $page = isset($this->request->get['page']) ? (int) $this->request->get['page'] : 1;
       
        $filter_keyword = (!empty($this->request->get['filter_keyword'])) ? $this->request->get['filter_keyword'] : '';
        $filter_query = (!empty($this->request->get['filter_query'])) ? $this->request->get['filter_query'] : '';

        $url = '';
        if ($filter_keyword) {
            $url .= '&fiter_keyword=' . urlencode(html_entity_decode($filter_keyword, ENT_QUOTES, 'UTF-8'));
        }
        if ($filter_query) {
            $url .= '&fiter_query=' . urlencode(html_entity_decode($filter_query, ENT_QUOTES, 'UTF-8'));
        }
        $url .= '&page=' . (int)$page;

        $this->redirect($this->url->link('tool/seourl', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

}

?>
