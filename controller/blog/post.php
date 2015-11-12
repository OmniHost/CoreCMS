<?php

class ControllerBlogPost extends \Core\Controller\Page {

    protected $_namespace = 'blog/post';

    public function index() {

        $this->getPage();

        if ($this->_error) {
            return $this->showErrorPage();
        }

        $this->data['page']['content'] = html_entity_decode($this->data['page']['content'], ENT_QUOTES, 'UTF-8');
        $this->event->trigger('ams.page.content', $this->data['page']['content']);

        $this->data['page']['href'] = $this->url->link('blog/post', 'ams_page_id=' . $this->data['page']['id']);
        $this->data['page']['author'] = $this->db->query("select username, firstname, lastname from #__user where user_id='" . (int) $this->data['page']['user_id'] . "'")->row;


        $this->response->setOutput($this->render());
    }

}
