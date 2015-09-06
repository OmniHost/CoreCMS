<?php 

/** @should migrate to a setting in the system settings --- does not need its own module/controller!!!! (only frontend)

define("DIR_BACKUP", DIR_SYSTEM."backup/");

class ControllerToolScheduledBackup extends Controller { 
	private $error = array();
	
	public function index() {		
		$this->load->language('tool/scheduled_backup');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('tool/scheduled_backup');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			$backup_tables = $this->request->post['backup_tables'];
			
			switch($this->request->post['backup_schedule'])
			{
				case "once_hourly":	// Every hour
					$backup_next_schedule = date("Y/m/d H:00:00", strtotime("+1 hour"));			
					break;
				case "twice_daily":	// 00:00, 12:00
					if(date("H") <= 12)
					{
						$backup_next_schedule = date("Y/m/d 11:59:59");
					}
					elseif(date("H") <= 24)
					{
						$backup_next_schedule = date("Y/m/d 00:00:00", strtotime("+1 day"));
					}
					break;
				case "once_daily":	// 00:00
					$backup_next_schedule = date("Y/m/d 00:00:00", strtotime("+1 day"));
					break;
        case "once_weekly":	// 00:00
					$backup_next_schedule = date("Y/m/d 00:00:00", strtotime("+1 week"));
					break;
				case "twice_a_month":	// 15 day, last day
						$backup_next_schedule = date("Y/m/d 00:00:00", strtotime("+15 day"));
					 break;
				case "test_now":
					$backup_next_schedule = date("Y/m/d H:i:s", strtotime("+1 minutes"));
					break;
				case "never":
				default:
					$backup_next_schedule = "2000-01-01 00:00:00";
					break;			
			} 
			
			
			$backup_configuration = array(
				  'backup_tables'		=>	$backup_tables
				, 'backup_options'		=>	$this->request->post['backup_options']
				, 'backup_destination'	=>	$this->request->post['backup_destination']
				, 'backup_next_schedule'=>  $backup_next_schedule
				, 'backup_schedule'		=>	$this->request->post['backup_schedule']
				, 'backup_enable'		=>	$this->request->post['backup_enable']
			);
			
			$this->model_tool_scheduled_backup->updateSettingValue("scheduler", "backup_configuration", serialize($backup_configuration));
      
      $this->session->data['success'] = $this->language->get('text_success');
      
			$this->response->redirect($this->url->link('tool/scheduled_backup', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
				
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['entry_backup'] = $this->language->get('entry_backup');
    $data['entry_status'] = $this->language->get('entry_status');
    $data['entry_backup_options'] = $this->language->get('entry_backup_options');
    $data['entry_scheduled_backup'] = $this->language->get('entry_scheduled_backup');
    
    $data['text_enabled'] = $this->language->get('text_enabled');
    $data['text_disabled'] = $this->language->get('text_disabled');
    
    
    $data['text_email_backup_to'] = $this->language->get('text_email_backup_to');
    $data['text_save_to'] = $this->language->get('text_save_to');
    $data['text_never'] = $this->language->get('text_never');
    $data['text_once_hourly'] = $this->language->get('text_once_hourly');
    $data['text_twice_daily'] = $this->language->get('text_twice_daily');
    $data['text_once_daily'] = $this->language->get('text_once_daily');
    $data['text_once_weekly'] = $this->language->get('text_once_weekly');
    $data['text_twice_a_month'] = $this->language->get('text_twice_a_month');
    $data['text_test'] = $this->language->get('text_test');
    $data['text_next_backup_schedule'] = $this->language->get('text_next_backup_schedule');
    
		
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$data['tab_general'] = $this->language->get('tab_general');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['cancel'] = $this->url->link('tool/scheduled_backup', 'token=' . $this->session->data['token'], 'SSL');
		$data['action'] = $this->url->link('tool/scheduled_backup', 'token=' . $this->session->data['token'], 'SSL');
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['dir_backup'] = DIR_BACKUP;
		
		
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('tool/scheduled_backup', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['backup_setting'] = $this->url->link('tool/scheduled_backup/backup_setting', 'token=' . $this->session->data['token'], 'SSL');

		$data['scheduler_log'] = "";
		$data['tables'] = $this->model_tool_scheduled_backup->getTables();
		
		$backup_configuration = $this->model_tool_scheduled_backup->getSettingValue("scheduler", "backup_configuration");
    
    $data['backup_configuration'] = unserialize($backup_configuration);
    
		if(!empty($data['backup_configuration']))
		{
			//$data['backup_configuration']['backup_tables'] = unserialize($this->data['backup_configuration']['backup_tables']);
				
		}
		else
		{
			$data['backup_configuration'] = array(
                  'backup_tables'       =>  array()
                , 'backup_options'      =>  "email"
                , 'backup_destination'  =>  ""
                , 'backup_next_schedule'=>  ""
                , 'backup_schedule'     =>  "never"
                , 'backup_enable'       =>  false
            );
		}
			
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tool/scheduled_backup.tpl', $data));
	}
	
	public function backup_setting() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			print_r($_POST);
		} 
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/scheduled_backup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}		
	}
}
?>