<?php

class ControllerModuleTestimonial extends \Core\Controller {

    private $error = array();

    protected function index($setting) {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');



        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_stars'] = $this->language->get('text_stars');
        $this->data['text_more'] = $this->language->get('text_more');
        $this->data['text_testify'] = $this->language->get('text_testify');
        $this->data['text_more_testimonials'] = $this->language->get('text_more_testimonials');
        $this->data['text_no_testimonials'] = $this->language->get('text_no_testimonials');

        $this->data['display_photo'] = $this->config->get('testimonial_module_photo');

        $this->data['display_rating'] = $this->config->get('testimonial_display_rating_module');

        $this->data['photo_size'] = $setting['image_height'];

        $this->load->model('tool/image');

        $this->data['testimonials'] = array();

        $results = $this->model_extension_testimonial->getTestimonials(0, $setting['limit'], $this->session->data['language']);

        foreach ($results as $result) {
            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $photo = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
            } else {
                $photo = $this->model_tool_image->resize('no_photo.jpg', $setting['image_width'], $setting['image_height']);
            }

            $testimony = nl2br(html_entity_decode($result['testimony'], ENT_QUOTES, 'UTF-8'));

            $word_string = '';

            $words = explode(' ', strip_tags($testimony));

            if ($this->config->get('testimonial_excerpt_module')) {
                if ($setting['word_limit']) {
                    $excerpt_words = $setting['word_limit'];
                } else {
                    $excerpt_words = 5;
                }

                if ($excerpt_words > count($words)) {
                    $excerpt_words = count($words);
                }

                for ($i = 0; $i < $excerpt_words; $i++) {
                    $word_string .= $words[$i] . ' ';
                }
            }

            $this->data['testimonials'][] = array(
                'testimony' => $word_string,
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'website' => $result['website'],
                'company' => $result['company'],
                'title' => $result['title'],
                'photo' => $photo,
                'rating' => $result['rating'],
                'stars' => sprintf($this->language->get('text_stars'), $result['rating']),
                'href' => $this->url->link('module/testimonial/display', 'testimonial_id=' . $result['testimonial_id'])
            );
        }

        $this->data['testify'] = $this->url->link('module/testimonial/testify');
        $this->data['more_testimonials'] = $this->url->link('module/testimonial/all');

        $this->data['enable_guest'] = $this->config->get('testimonial_guest_status') && !$this->customer->isLogged();

        if ($this->customer->isLogged()) {
            $this->data['is_logged'] = TRUE;
        } else {
            $this->data['is_logged'] = FALSE;
        }


        $this->template = 'module/testimonial.phtml';


        $this->render();
    }

    public function all() {
        $this->language->load('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('heading_title'));


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial/all'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_testimonials'] = $this->language->get('text_testimonials');
        $this->data['text_stars'] = $this->language->get('text_stars');
        $this->data['text_more'] = $this->language->get('text_more');
        $this->data['text_no_testimonials'] = $this->language->get('text_no_testimonials');

        $this->data['button_testify'] = $this->language->get('button_testify');
        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['testify'] = $this->url->link('module/testimonial/testify');
        $this->data['continue'] = $this->url->link('common/home');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $page_limit = $this->config->get('testimonial_pagination_limit');


        $testimonial_total = $this->model_extension_testimonial->getTotalTestimonials();

        $this->data['testimonials'] = array();

        if ($testimonial_total) {
            $results = $this->model_extension_testimonial->getTestimonials(($page - 1) * $page_limit, $page_limit);

            if ($this->config->get('testimonial_photo_size')) {
                $photo_size = $this->config->get('testimonial_photo_size');
            } else {
                $photo_size = 64;
            }

            $this->data['display_photo'] = $this->config->get('testimonial_display_photo');

            $this->data['display_rating'] = $this->config->get('testimonial_display_rating');

            $this->data['photo_size'] = $photo_size;

            $this->load->model('tool/image');

            foreach ($results as $result) {
                if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                    $photo = $this->model_tool_image->resize($result['image'], $photo_size, $photo_size);
                } else {
                    $photo = $this->model_tool_image->resize('no_photo.jpg', $photo_size, $photo_size);
                }

                $testimony = nl2br(html_entity_decode($result['testimony'], ENT_QUOTES, 'UTF-8'));

                $word_string = '';

                $words = explode(' ', strip_tags($testimony));

                if ($this->config->get('testimonial_excerpt_page')) {
                    if ($this->config->get('testimonial_words')) {
                        $excerpt_words = $this->config->get('testimonial_words');
                    } else {
                        $excerpt_words = 10;
                    }

                    if ($excerpt_words > count($words)) {
                        $excerpt_words = count($words);
                    }

                    for ($i = 0; $i < $excerpt_words; $i++) {
                        $word_string .= $words[$i] . ' ';
                    }
                }

                $this->data['testimonials'][] = array(
                    'testimony' => $word_string,
                    'name' => $result['firstname'] . ' ' . $result['lastname'],
                    'website' => $result['website'],
                    'company' => $result['company'],
                    'title' => $result['title'],
                    'photo' => $photo,
                    'rating' => $result['rating'],
                    'stars' => sprintf($this->language->get('text_stars'), $result['rating']),
                    'href' => $this->url->link('module/testimonial/display', 'testimonial_id=' . $result['testimonial_id'])
                );
            }
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new \Core\Pagination();
        $pagination->total = $testimonial_total;
        $pagination->page = $page;
        $pagination->limit = $page_limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('module/testimonial/all', 'page={page}');

        $this->data['pagination'] = $pagination->render();


        $this->template = 'module/testimonial/list.phtml';


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

        $this->response->setOutput($this->render());
    }

    public function display() {
        $this->language->load('module/testimonial');
        $this->load->model('extension/testimonial');

        if (isset($this->request->get['testimonial_id'])) {
            $testimonial_id = $this->request->get['testimonial_id'];
        } else {
            $testimonial_id = 0;
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('module/testimonial/all');

        if ($this->config->get('testimonial_photo_size')) {
            $photo_size = $this->config->get('testimonial_photo_size');
        } else {
            $photo_size = 64;
        }

        $this->data['display_photo'] = $this->config->get('testimonial_display_photo');

        $this->data['display_rating'] = $this->config->get('testimonial_display_rating');

        $this->data['photo_size'] = $photo_size;

        $this->load->model('tool/image');

        $testimonial_info = $this->model_extension_testimonial->getTestimonial($testimonial_id);

        if ($testimonial_info) {
            $testimonial_from = sprintf($this->language->get('text_testimonial_from'), $testimonial_info['firstname'] . ' ' . $testimonial_info['lastname']);

            $this->document->setTitle($testimonial_from);

            $this->data['breadcrumbs'][] = array(
                'href' => $this->url->link('module/testimonial/display', 'testimonial_id=' . $testimonial_info['testimonial_id']),
                'text' => $testimonial_from,
                'separator' => $this->language->get('text_separator')
            );

            $this->data['heading_title'] = $testimonial_from;

            if ($testimonial_info['image'] && file_exists(DIR_IMAGE . $testimonial_info['image'])) {
                $photo = $this->model_tool_image->resize($testimonial_info['image'], $photo_size, $photo_size);
            } else {
                $photo = $this->model_tool_image->resize('no_photo.jpg', $photo_size, $photo_size);
            }

            $this->data['testimonial'] = array(
                'testimony' => nl2br(html_entity_decode($testimonial_info['testimony'], ENT_QUOTES, 'UTF-8')),
                'name' => $testimonial_info['firstname'] . ' ' . $testimonial_info['lastname'],
                'website' => $testimonial_info['website'],
                'company' => $testimonial_info['company'],
                'title' => $testimonial_info['title'],
                'photo' => $photo,
                'rating' => $testimonial_info['rating'],
                'stars' => sprintf($this->language->get('text_stars'), $testimonial_info['rating'])
            );


            $this->template = 'module/testimonial/display.phtml';


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

            $this->response->setOutput($this->render());
        } else {
            return $this->not_found();
        }
    }

    public function testify() {
        $this->load->language('module/testimonial');
        $this->load->model('extension/testimonial');

        $this->document->setTitle($this->language->get('text_testify'));

        if (!$this->config->get('testimonial_guest_status') && !$this->customer->isLogged()) {
            $this->redirect($this->url->link('common/home'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->addTestimonial();

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('module/testimonial/all'));
        }

        $this->data['template'] = $this->config->get('config_template');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('module/testimonial/testify'),
            'text' => $this->language->get('text_testify'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('text_testify');

        $this->data['text_good'] = $this->language->get('text_good');
        $this->data['text_bad'] = $this->language->get('text_bad');
        $this->data['text_note'] = $this->language->get('text_note');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_website'] = $this->language->get('entry_website');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_testimony'] = $this->language->get('entry_testimony');
        $this->data['entry_rating'] = $this->language->get('entry_rating');
        $this->data['entry_captcha'] = $this->language->get('entry_captcha');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['firstname'])) {
            $this->data['error_firstname'] = $this->error['firstname'];
        } else {
            $this->data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $this->data['error_lastname'] = $this->error['lastname'];
        } else {
            $this->data['error_lastname'] = '';
        }

        if (isset($this->error['testify_email'])) {
            $this->data['error_email'] = $this->error['testify_email'];
        } else {
            $this->data['error_email'] = '';
        }

        if (isset($this->error['rating'])) {
            $this->data['error_rating'] = $this->error['rating'];
        } else {
            $this->data['error_rating'] = '';
        }

        if (isset($this->error['testimony'])) {
            $this->data['error_testimony'] = $this->error['testimony'];
        } else {
            $this->data['error_testimony'] = '';
        }

        if (isset($this->error['captcha'])) {
            $this->data['error_captcha'] = $this->error['captcha'];
        } else {
            $this->data['error_captcha'] = '';
        }

        $this->data['action'] = $this->url->link('module/testimonial/testify');
        $this->data['cancel'] = $this->url->link('module/testimonial/all');

        if (isset($this->request->post['firstname'])) {
            $this->data['firstname'] = $this->request->post['firstname'];
        } elseif ($this->customer->isLogged()) {
            $this->data['firstname'] = $this->customer->getFirstName();
        } else {
            $this->data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $this->data['lastname'] = $this->request->post['lastname'];
        } elseif ($this->customer->isLogged()) {
            $this->data['lastname'] = $this->customer->getLastName();
        } else {
            $this->data['lastname'] = '';
        }

        if (isset($this->request->post['testify_email'])) {
            $this->data['testify_email'] = $this->request->post['testify_email'];
        } elseif ($this->customer->isLogged()) {
            $this->data['testify_email'] = $this->customer->getEmail();
        } else {
            $this->data['testify_email'] = '';
        }

        if (isset($this->request->post['website'])) {
            $this->data['website'] = $this->request->post['website'];
        } else {
            $this->data['website'] = '';
        }

        if (isset($this->request->post['company'])) {
            $this->data['company'] = $this->request->post['company'];
        } else {
            $this->data['company'] = '';
        }

        if (isset($this->request->post['title'])) {
            $this->data['title'] = $this->request->post['title'];
        } else {
            $this->data['title'] = '';
        }

        if (isset($this->request->post['rating'])) {
            $this->data['rating'] = $this->request->post['rating'];
        } else {
            $this->data['rating'] = '';
        }

        if (isset($this->request->post['testimony'])) {
            $this->data['testimony'] = $this->request->post['testimony'];
        } else {
            $this->data['testimony'] = '';
        }

        if (isset($this->request->post['captcha'])) {
            $this->data['captcha'] = $this->request->post['captcha'];
        } else {
            $this->data['captcha'] = '';
        }

        $this->template = 'module/testimonial/new.phtml';


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

        $this->response->setOutput($this->render());
    }

    public function write() {
        $this->language->load('module/testimonial');

        $json = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->addTestimonial();

            if ($this->config->get('testimonial_auto_approve')) {
                $json['success'] = $this->language->get('text_success');
            } else {
                $json['success'] = $this->language->get('text_review');
            }
        } else {
            $json['error'] = $this->error['message'];
        }

        $this->response->setOutput(json_encode($json));
    }

    public function addTestimonial() {
        $this->load->model('extension/testimonial');

        $this->model_extinsion_testimonial->addTestimonial($this->request->post);
    }

    private function validate() {
        $this->load->model('extension/testimonial');

        if ((strlen(utf8_decode($this->request->post['firstname'])) < 3) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
            $this->error['message'] = $this->language->get('error_firstname');
        }

        if ((strlen(utf8_decode($this->request->post['lastname'])) < 3) || (strlen(utf8_decode($this->request->post['lastname'])) > 32)) {
            $this->error['message'] = $this->language->get('error_lastname');
        }

        if ((strlen(utf8_decode($this->request->post['testify_email'])) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['testify_email'])) {
            $this->error['message'] = $this->language->get('error_email');
        }

        if (!$this->request->post['rating']) {
            $this->error['message'] = $this->language->get('error_rating');
        }

        if (strlen(utf8_decode($this->request->post['testimony'])) < 3) {
            $this->error['message'] = $this->language->get('error_testimony');
        }

        if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
            $this->error['message'] = $this->language->get('error_captcha');
        }

        if ($this->model_fido_testimonial->emailExists($this->request->post['testify_email'])) {
            $this->error['message'] = $this->language->get('error_email_exists');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
