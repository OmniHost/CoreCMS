<?php

/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 *  @name Dashboard Widget - Calendar
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 */
class ControllerDashboardCalendar extends \Core\Controller {

    protected function install() {
        $this->db->query('CREATE TABLE IF NOT EXISTS `#__mod_calendar` (
                          `AgeId` bigint(20) NOT NULL AUTO_INCREMENT,
                          `AgeUserId` int(11) DEFAULT NULL,
                          `AgeNomeEvento` varchar(150) DEFAULT NULL,
                          `AgeDescricao` text,
                          `AgeCor` varchar(8) DEFAULT NULL,
                          `AgeDataInicial` date DEFAULT NULL,
                          `AgeDataFinal` date DEFAULT NULL,
                          `AgeHoraInicial` time DEFAULT NULL,
                          `AgeHoraFinal` time DEFAULT NULL,
                          `AgeRepetir` int(11) DEFAULT \'0\',
                          `AgeStatus` int(11) DEFAULT \'1\',
                          `AgeInsertDate` datetime DEFAULT NULL,
                          `AgeUpdateDate` datetime DEFAULT NULL,
                          PRIMARY KEY (`AgeId`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
    }

    public function index() {
        $this->load->language('dashboard/calendar');
        $this->install();

        $data['token'] = $this->session->data['token'];
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_title'] = $this->language->get('text_title');
        $data['text_read_event'] = $this->language->get('text_read_event');
        $data['text_new_event'] = $this->language->get('text_new_event');
        $data['text_tab_basic'] = $this->language->get('text_tab_basic');
        $data['text_tab_advanced'] = $this->language->get('text_tab_advanced');

        $data['button_cancel'] = $this->language->get('button_go_back');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_dismiss'] = $this->language->get('button_dismiss');
        $data['button_save'] = $this->language->get('button_save');

        $data['label_event_name'] = $this->language->get('label_event_name');
        $data['label_initial_date'] = $this->language->get('label_initial_date');
        $data['label_final_date'] = $this->language->get('label_final_date');
        $data['label_initial_time'] = $this->language->get('label_initial_time');
        $data['label_final_time'] = $this->language->get('label_final_time');
        $data['label_duration'] = $this->language->get('label_duration');
        $data['label_repeat'] = $this->language->get('label_repeat');
        $data['label_color'] = $this->language->get('label_color');
        $data['label_description'] = $this->language->get('label_description');
        $data['label_optional'] = $this->language->get('label_optional');

        $data['option_duration_allday'] = $this->language->get('option_duration_allday');
        $data['option_duration_scheduled_time'] = $this->language->get('option_duration_scheduled_time');
        $data['option_repeat_once'] = $this->language->get('option_repeat_once');
        $data['option_repeat_every_day'] = $this->language->get('option_repeat_every_day');
        $data['option_repeat_weekly'] = $this->language->get('option_repeat_weekly');
        $data['option_repeat_two_weeks'] = $this->language->get('option_repeat_two_weeks');
        $data['option_repeat_monthly'] = $this->language->get('option_repeat_monthly');
        $data['option_repeat_semester'] = $this->language->get('option_repeat_semester');
        $data['option_repeat_yearly'] = $this->language->get('option_repeat_yearly');
        $data['option_color_blue'] = $this->language->get('option_color_blue');
        $data['option_color_red'] = $this->language->get('option_color_red');
        $data['option_color_yellow'] = $this->language->get('option_color_yellow');
        $data['option_color_green'] = $this->language->get('option_color_green');
        $data['option_color_orange'] = $this->language->get('option_color_orange');
        $data['option_color_black'] = $this->language->get('option_color_black');
        $data['option_color_purple'] = $this->language->get('option_color_purple');
        $data['option_color_aero'] = $this->language->get('option_color_aero');

        $data['format_calendar_lang'] = $this->language->get('format_calendar_lang');

        /* Javascript Lang Texts */
        $data['javascript_lang'] = array();
        $data['javascript_lang']['text_read_event_title_name'] = $this->language->get('text_read_event_title_name');
        $data['javascript_lang']['text_read_event_nothing_written'] = $this->language->get('text_read_event_nothing_written');
        $data['javascript_lang']['text_edit_event'] = $this->language->get('text_edit_event');
        $data['javascript_lang']['text_new_event'] = $this->language->get('text_new_event');
        $data['javascript_lang']['button_save'] = $this->language->get('button_save');
        $data['javascript_lang']['text_wait'] = $this->language->get('text_wait');
        $data['javascript_lang']['text_delete_title'] = $this->language->get('text_delete_title');
        $data['javascript_lang']['text_delete_desc'] = $this->language->get('text_delete_desc');
        $data['javascript_lang']['text_delete_yes'] = $this->language->get('text_delete_yes');
        $data['javascript_lang']['text_delete_no'] = $this->language->get('text_delete_no');
        $data['javascript_lang']['text_delete_success_title'] = $this->language->get('text_delete_success_title');
        $data['javascript_lang']['text_delete_success_desc'] = $this->language->get('text_delete_success_desc');
        $data['javascript_lang']['text_delete_error_title'] = $this->language->get('text_delete_error_title');
        $data['javascript_lang']['text_delete_error_desc'] = $this->language->get('text_delete_error_desc');
        $data['javascript_lang']['text_save_error_desc'] = $this->language->get('text_save_error_desc');
        $data['javascript_lang']['format_print_event_date'] = $this->language->get('format_print_event_date');
        $data['javascript_lang']['format_print_event_time'] = $this->language->get('format_print_event_time');
        $data['javascript_lang']['label_repeat'] = $this->language->get('label_repeat');
        $data['javascript_lang'] = json_encode($data['javascript_lang']);

        /* CSSs */
        $this->document->addStyle('view/plugins/fullcalendar/fullcalendar.min.css');
        $this->document->addStyle('view/plugins/iCheck/all.css');
        $this->document->addStyle('view/plugins/sweetalert/sweetalert.css');

        /* JavaScripts */
        $this->document->addScript('view/plugins/iCheck/icheck.min.js');
        $this->document->addScript('view/plugins/fullcalendar/moment.min.js');
        $this->document->addScript('view/plugins/fullcalendar/fullcalendar.min.js');
        $this->document->addScript('view/plugins/fullcalendar/' . $data['format_calendar_lang'] . '.js');
        $this->document->addScript('view/plugins/sweetalert/sweetalert.min.js');
        $this->document->addScript('view/js/calendar.js');




        $this->template = 'dashboard/calendar.phtml';
        $this->data = $data;
        return $this->render();
    }

    public function getdata() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['start']) && isset($this->request->post['end'])) {
            $start = $this->request->post['start'];
            $end = $this->request->post['end'];
            $this->load->model('dashboard/calendar');
            echo json_encode($this->model_dashboard_calendar->getData($start, $end));
        } else {
            echo '[]';
        }
    }

    public function adddata() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('dashboard/calendar');
            echo $this->model_dashboard_calendar->addData($this->request->post);
        } else {
            echo 0;
        }
    }

    public function editdata() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('dashboard/calendar');
            echo $this->model_dashboard_calendar->editData($this->request->post);
        } else {
            echo 0;
        }
    }

    public function deldata() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['id']) && (int) $this->request->post['id'] > 0) {
            $this->load->model('dashboard/calendar');
            echo $this->model_dashboard_calendar->delData((int) $this->request->post['id']);
        } else {
            echo 0;
        }
    }

}
