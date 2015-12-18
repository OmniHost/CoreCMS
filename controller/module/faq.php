<?php

class ControllerModuleFaq extends \Core\Controller {

    public function index() {
        $this->language->load('module/faq');

        $this->load->model('module/faq');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/faq')
        );

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $filter_data = array(
            'page' => $page,
            'limit' => 20,
            'start' => 20 * ($page - 1),
        );

        $total = $this->model_module_faq->getTotalFaq();

        $pagination = new \Core\Pagination();
        $pagination->text = $this->language->get('text_pagination');
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = 20;
        $pagination->url = $this->url->link('information/faq', 'page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($total - 10)) ? $total : ((($page - 1) * 10) + 10), $total, ceil($total / 10));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_question'] = $this->language->get('text_question');
        $data['text_answer'] = $this->language->get('text_answer');
        $data['text_date'] = $this->language->get('text_date');
        $data['text_view'] = $this->language->get('text_view');

        $all_faq = $this->model_module_faq->getAllFaq($filter_data);

        $data['all_faq'] = array();



        foreach ($all_faq as $faq) {
            $data['all_faq'][] = array(
                'question' => html_entity_decode($faq['question'], ENT_QUOTES, "UTF-8"),
                'answer' => html_entity_decode($faq['answer'], ENT_QUOTES, "UTF-8"),
                'view' => $this->url->link('extension/faq/faq', 'faq_id=' . $faq['faq_id']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($faq['date_added']))
            );
        }


        $this->children = array(
            'common/column_top',
            'common/column_bottom',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setoutput($this->render('module/faq_list.phtml', $data));
    }

}
