<?php

class ModelCmsPage extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'cms.page';
    public $content;

    public function getFormFields($tabs) {

        if (isset($this->request->post['content'])) {
            $data['content'] = $this->request->post['content'];
        } elseif (!empty($this->content)) {
            $data['content'] = $this->content;
        } else {
            $data['content'] = '';
        }

        $tabs['general']['content'] = array(
            'key' => 'content',
            'type' => 'html',
            'value' => $data['content'],
            'label' => $this->_language->get('entry_content'),
            'required' => false
        );




        return $tabs;
    }

    public function populate($array) {
        parent::populate($array);
    }

}
