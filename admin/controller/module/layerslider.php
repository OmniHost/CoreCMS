<?php
class ControllerModuleLayerslider extends \Core\Controller {
	private $error = array();
	public function index() {
		$this->load->language('module/layerslider');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/plugins/layerslider/layerslider.css');
		$this->document->addStyle('view/plugins/layerslider/css/bootstrap-colorpicker.min.css');
		$this->document->addStyle('view/plugins/layerslider/css/jquery-ui.css');
		$this->document->addScript('view/plugins/layerslider/jquery-ui.js');
		$this->document->addScript('view/plugins/layerslider/js/bootstrap-colorpicker.min.js');
		$this->document->addScript('view/plugins/layerslider/js/removeclasses.js');
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		if (isset($this->request->server['HTTPS'])) {
			$server = $this->config->get('config_ssl');
			$data['base_url'] = HTTPS_CATALOG;
                    
		} else {
			$server = $this->config->get('config_url');
			$data['base_url'] = HTTP_CATALOG;
		}
			
		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('layerslider', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			if (isset($this->request->post['save']) && $this->request->post['save'] == 'stay' && $this->request->get['module_id']) {
				$this->redirect($this->url->link('module/layerslider', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')); 
			} else {
                $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            }
		}
		
		if (isset($this->request->get['module_id'])) {
			$data['has_module_id'] = true; 
		} else {
			$data['has_module_id'] = false;
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['tab_section'] = $this->language->get('tab_section');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_only_hover'] = $this->language->get('text_only_hover');
		$data['text_add_section'] = $this->language->get('text_add_section');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_nav_buttons'] = $this->language->get('text_nav_buttons');
		$data['text_nav_bullets'] = $this->language->get('text_nav_bullets');
		$data['text_nav_play_pause'] = $this->language->get('text_nav_play_pause');
		$data['text_nav_thumbs'] = $this->language->get('text_nav_thumbs');
		$data['text_nav_circle'] = $this->language->get('text_nav_circle');
		$data['text_nav_timer_bar'] = $this->language->get('text_nav_timer_bar');
		$data['text_nav_pause_hover'] = $this->language->get('text_nav_pause_hover');
		$data['h3_module_settings'] = $this->language->get('h3_module_settings');
		$data['h3_slideshow_sizing'] = $this->language->get('h3_slideshow_sizing');
		$data['h3_slide_navigation'] = $this->language->get('h3_slide_navigation');
		$data['h3_slideshow_skin'] = $this->language->get('h3_slideshow_skin');
		$data['button_save'] = $this->language->get('button_save');
        $data['button_save_stay'] = $this->language->get('button_save_stay');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_section'] = $this->language->get('button_add_section');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['text_general_settings'] = $this->language->get('text_general_settings');
		$data['text_google_fonts'] = $this->language->get('text_google_fonts');
		$data['text_slides'] = $this->language->get('text_slides');
		$data['text_preview_language'] = $this->language->get('text_preview_language');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_import'] = $this->language->get('text_import');
		$data['fonts_help_block'] = $this->language->get('fonts_help_block');
		$data['text_slide_settings'] = $this->language->get('text_slide_settings');
		$data['text_sort_order'] = $this->language->get('text_sort_order');
		$data['text_duration'] = $this->language->get('text_duration');
		$data['text_slide_transition'] = $this->language->get('text_slide_transition');
		$data['text_link_target'] = $this->language->get('text_link_target');
		$data['text_slide_preview'] = $this->language->get('text_slide_preview');
		$data['text_change_slide_bg'] = $this->language->get('text_change_slide_bg');
		$data['text_add_text_layer'] = $this->language->get('text_add_text_layer');
		$data['text_add_button_layer'] = $this->language->get('text_add_button_layer');
		$data['text_add_image_layer'] = $this->language->get('text_add_image_layer');
		$data['text_layer_list'] = $this->language->get('text_layer_list');
		$data['text_layer_in'] = $this->language->get('text_layer_in');
		$data['text_layer_out'] = $this->language->get('text_layer_out');
		$data['text_system_fonts'] = $this->language->get('text_system_fonts');
		$data['text_google_fonts'] = $this->language->get('text_google_fonts');	
		$data['text_slide_duration'] = $this->language->get('text_slide_duration');
		$data['text_layer_settings'] = $this->language->get('text_layer_settings');
		$data['text_layer_content'] = $this->language->get('text_layer_content');
		$data['text_layer_position'] = $this->language->get('text_layer_position');
		$data['text_offset_left'] = $this->language->get('text_offset_left');
		$data['text_offset_top'] = $this->language->get('text_offset_top');
		$data['text_layer_style'] = $this->language->get('text_layer_style');
		$data['text_font_family'] = $this->language->get('text_font_family');
		$data['text_font_weight'] = $this->language->get('text_font_weight');
		$data['text_font_size'] = $this->language->get('text_font_size');
		$data['text_color'] = $this->language->get('text_color');
		$data['text_background'] = $this->language->get('text_background');
		$data['text_padding'] = $this->language->get('text_padding');
		$data['text_border_radius'] = $this->language->get('text_border_radius');
		$data['text_custom_css'] = $this->language->get('text_custom_css');
		$data['text_animation_in'] = $this->language->get('text_animation_in');
		$data['text_animation_out'] = $this->language->get('text_animation_out');
		$data['text_effect'] = $this->language->get('text_effect');
		$data['text_easing'] = $this->language->get('text_easing');
		$data['text_duration'] = $this->language->get('text_duration');
		$data['text_button_text'] = $this->language->get('text_button_text');
		$data['text_button_href'] = $this->language->get('text_button_href');
		$data['text_button_target'] = $this->language->get('text_button_target');
		$data['text_button_class'] = $this->language->get('text_button_class');
		$data['text_layer_sort_order'] = $this->language->get('text_layer_sort_order');

		// Slide Transitoins 
		$data['slide_transitions'][] = array();
		$data['slide_transitions'] = array(
		"transition2d: 1" => "2D #1", "transition2d: 2" => "2D #2", "transition2d: 3" => "2D #3", "transition2d: 4" => "2D #4", "transition2d: 5" => "2D #5", "transition2d: 6" => "2D #6", "transition2d: 7" => "2D #7", "transition2d: 8" => "2D #8", "transition2d: 9" => "2D #9", "transition2d: 10" => "2D #10",  "transition2d: 11" => "2D #11", "transition2d: 12" => "2D #12", "transition2d: 13" => "2D #13", "transition2d: 14" => "2D #14", "transition2d: 15" => "2D #15", "transition2d: 16" => "2D #16", "transition2d: 17" => "2D #17", "transition2d: 18" => "2D #18", "transition2d: 19" => "2D #19", "transition2d: 20" => "2D #20",  "transition2d: 21" => "2D #21", "transition2d: 22" => "2D #22", "transition2d: 23" => "2D #23", "transition2d: 24" => "2D #24", "transition2d: 25" => "2D #25", "transition2d: 26" => "2D #26", "transition2d: 27" => "2D #27", "transition2d: 28" => "2D #28", "transition2d: 29" => "2D #29", "transition2d: 30" => "2D #30",  "transition2d: 31" => "2D #31", "transition2d: 32" => "2D #32", "transition2d: 33" => "2D #33", "transition2d: 34" => "2D #34", "transition2d: 35" => "2D #35", "transition2d: 36" => "2D #36", "transition2d: 37" => "2D #37", "transition2d: 38" => "2D #38", "transition2d: 39" => "2D #39", "transition2d: 40" => "2D #40",  "transition2d: 41" => "2D #41", "transition2d: 42" => "2D #42", "transition2d: 43" => "2D #43", "transition2d: 44" => "2D #44", "transition2d: 45" => "2D #45", "transition2d: 46" => "2D #46", "transition2d: 47" => "2D #47", "transition2d: 48" => "2D #48", "transition2d: 49" => "2D #49", "transition2d: 50" => "2D #50",  "transition2d: 51" => "2D #51", "transition2d: 52" => "2D #52", "transition2d: 53" => "2D #53", "transition2d: 54" => "2D #54", "transition2d: 55" => "2D #55", "transition2d: 56" => "2D #56", "transition2d: 57" => "2D #57", "transition2d: 58" => "2D #58", "transition2d: 59" => "2D #59", "transition2d: 60" => "2D #60",  "transition2d: 61" => "2D #61", "transition2d: 62" => "2D #62", "transition2d: 63" => "2D #63", "transition2d: 64" => "2D #64", "transition2d: 65" => "2D #65", "transition2d: 66" => "2D #66", "transition2d: 67" => "2D #67", "transition2d: 68" => "2D #68", "transition2d: 69" => "2D #69", "transition2d: 70" => "2D #70",  "transition2d: 71" => "2D #71", "transition2d: 72" => "2D #72", "transition2d: 73" => "2D #73", "transition2d: 74" => "2D #74", "transition2d: 75" => "2D #75", "transition2d: 76" => "2D #76", "transition2d: 77" => "2D #77", "transition2d: 78" => "2D #78", "transition2d: 79" => "2D #79", "transition2d: 80" => "2D #80",  "transition2d: 81" => "2D #81", "transition2d: 82" => "2D #82", "transition2d: 83" => "2D #83", "transition2d: 84" => "2D #84", "transition2d: 85" => "2D #85", "transition2d: 86" => "2D #86", "transition2d: 87" => "2D #87", "transition2d: 88" => "2D #88", "transition2d: 89" => "2D #89", "transition2d: 90" => "2D #90",  "transition2d: 91" => "2D #91", "transition2d: 92" => "2D #92", "transition2d: 93" => "2D #93", "transition2d: 94" => "2D #94", "transition2d: 95" => "2D #95", "transition2d: 96" => "2D #96", "transition2d: 97" => "2D #97", "transition2d: 98" => "2D #98", "transition2d: 99" => "2D #99", "transition2d: 100" 	=> "2D #100",  "transition2d: 101" 	=> "2D #101", "transition2d: 102" 	=> "2D #102", "transition2d: 103" 	=> "2D #103", "transition2d: 104" 	=> "2D #104", "transition2d: 105" 	=> "2D #105", "transition2d: 106" 	=> "2D #106", "transition2d: 107" 	=> "2D #107", "transition2d: 108" 	=> "2D #108", "transition2d: 109" 	=> "2D #109", "transition2d: 110" 	=> "2D #110", "transition2d: 111" 	=> "2D #111", "transition2d: 112" 	=> "2D #112", "transition2d: 113" 	=> "2D #113",   "transition3d: 1" => "3D #1", "transition3d: 2" => "3D #2", "transition3d: 3" => "3D #3", "transition3d: 4" => "3D #4", "transition3d: 5" => "3D #5", "transition3d: 6" => "3D #6", "transition3d: 7" => "3D #7", "transition3d: 8" => "3D #8", "transition3d: 9" => "3D #9", "transition3d: 10" => "3D #10",  "transition3d: 11" => "3D #11", "transition3d: 12" => "3D #12", "transition3d: 13" => "3D #13", "transition3d: 14" => "3D #14", "transition3d: 15" => "3D #15", "transition3d: 16" => "3D #16", "transition3d: 17" => "3D #17", "transition3d: 18" => "3D #18", "transition3d: 19" => "3D #19", "transition3d: 20" => "3D #20",  "transition3d: 21" => "3D #31", "transition3d: 22" => "3D #32", "transition3d: 23" => "3D #33", "transition3d: 24" => "3D #34", "transition3d: 25" => "3D #35", "transition3d: 26" => "3D #36", "transition3d: 27" => "3D #37", "transition3d: 28" => "3D #38", "transition3d: 29" => "3D #39", "transition3d: 30" => "3D #30",  "transition3d: 31" => "3D #31", "transition3d: 32" => "3D #32", "transition3d: 33" => "3D #33", "transition3d: 34" => "3D #34", "transition3d: 35" => "3D #35", "transition3d: 36" => "3D #36", "transition3d: 37" => "3D #37", "transition3d: 38" => "3D #38", "transition3d: 39" => "3D #39", "transition3d: 40" => "3D #40",  "transition3d: 41" => "3D #41", "transition3d: 42" => "3D #42", "transition3d: 43" => "3D #43", "transition3d: 44" => "3D #44", "transition3d: 45" => "3D #45", "transition3d: 46" => "3D #46", "transition3d: 47" => "3D #47", "transition3d: 48" => "3D #48", "transition3d: 49" => "3D #49", "transition3d: 50" => "3D #50",  "transition3d: 51" => "3D #51", "transition3d: 52" => "3D #52", "transition3d: 53" => "3D #53", "transition3d: 54" => "3D #54", "transition3d: 55" => "3D #55", "transition3d: 56" => "3D #56", "transition3d: 57" => "3D #57", "transition3d: 58" => "3D #58", "transition3d: 59" => "3D #59", "transition3d: 60" => "3D #60",  "transition3d: 61" => "3D #61", "transition3d: 62" => "3D #62",  "transition3d: 63" => "3D #63", "transition3d: 64" => "3D #64", "transition3d: 65" => "3D #65", "transition3d: 66" => "3D #66", "transition3d: 67" => "3D #67", "transition3d: 68" => "3D #68", "transition3d: 69" => "3D #69", "transition3d: 70" => "3D #70",  "transition3d: 71" => "3D #71", "transition3d: 72" => "3D #72", "transition3d: 73" => "3D #73", "transition3d: 74" => "3D #74", "transition3d: 75" => "3D #75", "transition3d: 76" => "3D #76", "transition3d: 77" => "3D #77", "transition3d: 78" => "3D #78", "transition3d: 79" => "3D #79", "transition3d: 80" => "3D #80",  "transition3d: 81" => "3D #81", "transition3d: 82" => "3D #82", "transition3d: 83" => "3D #83", "transition3d: 84" => "3D #84", "transition3d: 85" => "3D #85", "transition3d: 86" => "3D #86", "transition3d: 87" => "3D #87", "transition3d: 88" => "3D #88", "transition3d: 89" => "3D #89", "transition3d: 90" => "3D #90",  "transition3d: 91" => "3D #91", "transition3d: 92" => "3D #92", "transition3d: 93" => "3D #93", "transition3d: 94" => "3D #94", "transition3d: 95" => "3D #95", "transition3d: 96" => "3D #96", "transition3d: 97" => "3D #97", "transition3d: 98" => "3D #98", "transition3d: 99" => "3D #99", "transition3d: 100" => "3D #100"
		);
		
		// System Fonts //
		$data['system_fonts'][] = array();
		$data['system_fonts'] = array(
			"Arial, Helvetica Neue, Helvetica, sans-serif" => "Arial",
			"Comic Sans MS, Comic Sans MS, cursive" => "Comic sans",
			"Courier New, Courier New, monospace" => "Courier New",
			"Georgia, Times, Times New Roman, serif" => "Georgia",
			"Impact, Charcoal, sans-serif" => "Impact",
			"Lucida Sans Typewriter, Lucida Console, Monaco, Bitstream Vera Sans Mono, monospace" => "Lucida Sans Typewriter",
			"Palatino, Palatino Linotype, Palatino LT STD, Book Antiqua, Georgia, serif" => "Palatino Linotype",
			"Tahoma, Verdana, Segoe, sans-serif" => "Tahoma",
			"Times New Roman, Times, Baskerville, Georgia, serif" => "Times New Roman",
			"Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif" => "Trebuchet",
			"Verdana, Geneva, sans-serif" => "Verdana"
		);
		
		// Transitoins In 
		$data['transitions'][] = array();
		$data['transitions'] = array(
			"offsetxin:0;" 							=> "Fade In",
			"offsetxin:-50;" 						=> "Short from left",
			"offsetxin:0;offsetyin:-40;" 			=> "Short from top",
			"offsetxin:50;" 						=> "Short from right",
			"offsetxin:0;offsetyin:40;" 			=> "Short from bottom",
			"offsetxin:left;" 						=> "Long from left",
			"offsetyin:top;offsetxin:0;" 			=> "Long from top",
			"offsetxin:right;" 						=> "Long from right",
			"offsetyin:bottom;offsetxin:0;" 		=> "Long from bottom",
			"offsetxin:0;scalexin:0;scaleyin:0;" 	=> "Scale up",
			"offsetxin:0;scalexin:3;scaleyin:4;" 	=> "Scale down",
			"offsetxin:0;rotatein:-20;" 			=> "Rotate (20 deg)",
			"offsetxin:0;rotatein:-20;scalexin:0;scaleyin:0;" 			=> "Rotate (20 deg) + Scale up",
			"offsetxin:0;rotatein:-20;scalexin:4;scaleyin:4;" 			=> "Rotate (20 deg) + Scale down",
			"offsetxin:0;rotatein:-180;" 			=> "Rotate (180 deg)",
			"offsetxin:0;rotatein:-180;scalexin:0;scaleyin:0;" 			=> "Rotate (180 deg) + Scale up",
			"offsetxin:0;rotatein:-180;scalexin:4;scaleyin:4;" 			=> "Rotate (180 deg) + Scale down",
			"offsetxin:0;rotatein:-360;" 			=> "Rotate (360 deg)",
			"offsetxin:0;rotatein:-360;scalexin:0;scaleyin:0;" 			=> "Rotate (360 deg) + Scale up",
			"offsetxin:0;rotatein:-360;scalexin:4;scaleyin:4;" 			=> "Rotate (360 deg) + Scale down",
			"offsetxin:0;rotateyin:90;transformoriginin:left 50% 0;" => "Fold in right",
			"offsetxin:0;rotatexin:90;transformoriginin:50% bottom 0;" => "Fold in top",
			"offsetxin:0;rotatexin:-90;transformoriginin:50% top 0;" => "Fold in bottom",
			"offsetxin:0;rotateyin:-90;transformoriginin:right 50% 0;" => "Fold in left",
			"offsetxin:0;rotateyin:90;skewxin:60;transformoriginin:40% 40% 0;" 	=> "Lively in",
			"offsetxin:0;rotateyin:90;skewxin:60;transformoriginin:40% 40% 0;scalexin:0;scaleyin:0;" 	=> "Lively in + Scale up",
			"offsetxin:0;rotateyin:90;skewxin:60;transformoriginin:40% 40% 0;scalexin:4;scaleyin:4;" 	=> "Lively in + Scale down",
			"offsetxin:-10;offsetyin:-10;rotatein:-1;scalexin:0.98;scaleyin:0.98;" 			=> "Discrete from left",
			"offsetxin:10;offsetyin:10;rotatein:1;scalexin:0.98;scaleyin:0.98;" 			=> "Discrete from right"
		);
		
		// Transitoins Out 
		$data['transitionsouts'][] = array();
		$data['transitionsouts'] = array(
			"offsetxout:0;" 							=> "Fade Out",
			"offsetxout:-50;" 						=> "Short to left",
			"offsetxout:0;offsetyout:-40;" 			=> "Short to top",
			"offsetxout:50;" 						=> "Short to right",
			"offsetxout:0;offsetyout:40;" 			=> "Short to bottom",
			"offsetxout:left;" 						=> "Long to left",
			"offsetyout:top;offsetxout:0;" 			=> "Long to top",
			"offsetxout:right;" 						=> "Long to right",
			"offsetyout:bottom;offsetxout:0;" 		=> "Long to bottom",
			"offsetxout:0;scalexout:3;scaleyout:4;" 	=> "Scale up",
			"offsetxout:0;scalexout:0;scaleyout:0;" 	=> "Scale down",
			"offsetxout:0;rotateout:-20;" 			=> "Rotate (20 deg)",
			"offsetxout:0;rotateout:-20;scalexout:4;scaleyout:4;" 			=> "Rotate (20 deg) + Scale up",
			"offsetxout:0;rotateout:-20;scalexout:0;scaleyout:0;" 			=> "Rotate (20 deg) + Scale down",
			"offsetxout:0;rotateout:-180;" 			=> "Rotate (180 deg)",
			"offsetxout:0;rotateout:-180;scalexout:4;scaleyout:4;" 			=> "Rotate (180 deg) + Scale up",
			"offsetxout:0;rotateout:-180;scalexout:0;scaleyout:0;" 			=> "Rotate (180 deg) + Scale down",
			"offsetxout:0;rotateout:-360;" 			=> "Rotate (360 deg)",
			"offsetxout:0;rotateout:-360;scalexout:4;scaleyout:4;" 			=> "Rotate (360 deg) + Scale up",
			"offsetxout:0;rotateout:-360;scalexout:0;scaleyout:0;" 			=> "Rotate (360 deg) + Scale down",
			"offsetxout:0;rotateyout:90;transformoriginout:left 50% 0;" => "Fold out right",
			"offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;" => "Fold out top",
			"offsetxout:0;rotatexout:-90;transformoriginout:50% top 0;" => "Fold out bottom",
			"offsetxout:0;rotateyout:-90;transformoriginout:right 50% 0;" => "Fold out left",
			"offsetxout:0;rotateyout:90;skewxout:60;transformoriginout:40% 40% 0;" 	=> "Lively out",
			"offsetxout:0;rotateyout:90;skewxout:60;transformoriginout:40% 40% 0;scalexout:4;scaleyout:4;" 	=> "Lively out + Scale up",
			"offsetxout:0;rotateyout:90;skewxout:60;transformoriginout:40% 40% 0;scalexout:0;scaleyout:0;" 	=> "Lively out + Scale down",
			"offsetxout:-10;offsetyout:-10;rotateout:-1;scalexout:0.98;scaleyout:0.98;" 			=> "Discrete to left",
			"offsetxout:10;offsetyout:10;rotateout:1;scalexout:0.98;scaleyout:0.98;" 			=> "Discrete to right"
		);
		
		// Easings 
		$data['easings'][] = array();
		$data['easings'] = array(
			"linear"  => "linear",
			"swing"  => "swing",
			"jswing"  => "jswing",
			"easeInQuad"  => "easeInQuad",
			"easeInCubic"  => "easeInCubic",
			"easeInQuart"  => "easeInQuart",
			"easeInQuint"  => "easeInQuint",
			"easeInSine"  => "easeInSine",
			"easeInExpo"  => "easeInExpo",
			"easeInCirc"  => "easeInCirc",
			"easeInElastic"  => "easeInElastic",
			"easeInBack"  => "easeInBack",
			"easeInBounce"  => "easeInBounce",
			"easeOutQuad"  => "easeOutQuad",
			"easeOutCubic"  => "easeOutCubic",
			"easeOutQuart"  => "easeOutQuart",
			"easeOutQuint"  => "easeOutQuint",
			"easeOutSine"  => "easeOutSine",
			"easeOutExpo"  => "easeOutExpo",
			"easeOutCirc"  => "easeOutCirc",
			"easeOutElastic"  => "easeOutElastic",
			"easeOutBack"  => "easeOutBack",
			"easeOutBounce"  => "easeOutBounce",
			"easeInOutQuad"  => "easeInOutQuad",
			"easeInOutCubic"  => "easeInOutCubic",
			"easeInOutQuart"  => "easeInOutQuart",
			"easeInOutQuint"  => "easeInOutQuint",
			"easeInOutSine"  => "easeInOutSine",
			"easeInOutExpo"  => "easeInOutExpo",
			"easeInOutCirc"  => "easeInOutCirc",
			"easeInOutElastic"  => "easeInOutElastic",
			"easeInOutBack"  => "easeInOutBack",
			"easeInOutBounce"  => "easeInOutBounce"	
		);
		
		// Font Weights 
		$data['fontweights'][] = array();
		$data['fontweights'] = array(
			"400" 		=> "400 (Normal)",
			"100" 		=> "100",
			"200" 		=> "200",
			"300" 		=> "300",
			"500" 		=> "500",
			"600" 		=> "600",
			"700" 		=> "700",
			"800" 		=> "800",
			"900" 		=> "900"
		);
		
		// Button Classes
		$data['button_classes'][] = array();
		$data['button_classes'] = array(
			"ls_btn ls_btn_dark" 	=> "Flat - Dark",
			"ls_btn ls_btn_white" 		=> "Flat - White",
			"ls_btn ls_btn_lightgrey" 	=> "Flat - Light Grey",
			"ls_btn ls_btn_darkgrey" 		=> "Flat - Dark Grey",
			"ls_btn ls_btn_green" 		=> "Flat - Green",
			"ls_btn ls_btn_red" 		=> "Flat - Red",
			"ls_btn ls_btn_blue" 		=> "Flat - Blue",
			"ls_btn ls_btn_navy" 		=> "Flat - Navy",
			"ls_btn ls_btn_orange" 		=> "Flat - Orange",
			"ls_btn ls_btn_yellow" 		=> "Flat - Yellow",
			"ls_btn ls_btn_white_outline" 	=> "Outline - Light",
			"ls_btn ls_btn_dark_outline" 	=> "Outline - Dark",
			"ls_btn ls_btn_dark ls_btn_3d" 	=> "3D - Dark",
			"ls_btn ls_btn_white ls_btn_3d" 		=> "3D - White",
			"ls_btn ls_btn_lightgrey ls_btn_3d" 	=> "3D - Light Grey",
			"ls_btn ls_btn_darkgrey ls_btn_3d" 		=> "3D - Dark Grey",
			"ls_btn ls_btn_green ls_btn_3d" 		=> "3D - Green",
			"ls_btn ls_btn_red ls_btn_3d" 		=> "3D - Red",
			"ls_btn ls_btn_blue ls_btn_3d" 		=> "3D - Blue",
			"ls_btn ls_btn_navy ls_btn_3d" 		=> "3D - Navy",
			"ls_btn ls_btn_orange ls_btn_3d" 		=> "3D - Orange",
			"ls_btn ls_btn_yellow ls_btn_3d" 		=> "3D - Yellow",
			
			"ls_btn ls_btn_lg ls_btn_dark" 	=> "Flat - Dark (Large)",
			"ls_btn ls_btn_lg ls_btn_white" 		=> "Flat - White (Large)",
			"ls_btn ls_btn_lg ls_btn_lightgrey" 	=> "Flat - Light Grey (Large)",
			"ls_btn ls_btn_lg ls_btn_darkgrey" 		=> "Flat - Dark Grey (Large)",
			"ls_btn ls_btn_lg ls_btn_green" 		=> "Flat - Green (Large)",
			"ls_btn ls_btn_lg ls_btn_red" 		=> "Flat - Red (Large)",
			"ls_btn ls_btn_lg ls_btn_blue" 		=> "Flat - Blue (Large)",
			"ls_btn ls_btn_lg ls_btn_navy" 		=> "Flat - Navy (Large)",
			"ls_btn ls_btn_lg ls_btn_orange" 		=> "Flat - Orange (Large)",
			"ls_btn ls_btn_lg ls_btn_yellow" 		=> "Flat - Yellow (Large)",
			"ls_btn ls_btn_lg ls_btn_white_outline" 	=> "Outline - Light (Large)",
			"ls_btn ls_btn_lg ls_btn_dark_outline" 	=> "Outline - Dark (Large)",
			"ls_btn ls_btn_lg ls_btn_dark ls_btn_3d" 	=> "3D - Dark (Large)",
			"ls_btn ls_btn_lg ls_btn_white ls_btn_3d" 		=> "3D - White (Large)",
			"ls_btn ls_btn_lg ls_btn_lightgrey ls_btn_3d" 	=> "3D - Light Grey (Large)",
			"ls_btn ls_btn_lg ls_btn_darkgrey ls_btn_3d" 		=> "3D - Dark Grey (Large)",
			"ls_btn ls_btn_lg ls_btn_green ls_btn_3d" 		=> "3D - Green (Large)",
			"ls_btn ls_btn_lg ls_btn_red ls_btn_3d" 		=> "3D - Red (Large)",
			"ls_btn ls_btn_lg ls_btn_blue ls_btn_3d" 		=> "3D - Blue (Large)",
			"ls_btn ls_btn_lg ls_btn_navy ls_btn_3d" 		=> "3D - Navy (Large)",
			"ls_btn ls_btn_lg ls_btn_orange ls_btn_3d" 		=> "3D - Orange (Large)",
			"ls_btn ls_btn_lg ls_btn_yellow ls_btn_3d" 		=> "3D - Yellow (Large)"
			
		);
		
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
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/layerslider', 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/layerslider', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
			);			
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('module/layerslider', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('module/layerslider', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
	
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '1140';
		}
		
		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '500';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		/*if (isset($this->request->post['lang'])) {
			$data['lang'] = $this->request->post['lang'];
		} elseif (!empty($module_info)) {
			$data['lang'] = $module_info['lang'];
		} else {
			$data['lang'] = $this->config->get('config_language_id');
		}*/
                $data['lang'] =  $this->config->get('config_language_id');
		
		if (isset($this->request->post['nav_buttons'])) {
			$data['nav_buttons'] = $this->request->post['nav_buttons'];
		} elseif (!empty($module_info)) {
			$data['nav_buttons'] = $module_info['nav_buttons'];
		} else {
			$data['nav_buttons'] = 'hover';
		}
		
		if (isset($this->request->post['nav_bullets'])) {
			$data['nav_bullets'] = $this->request->post['nav_bullets'];
		} elseif (!empty($module_info)) {
			$data['nav_bullets'] = $module_info['nav_bullets'];
		} else {
			$data['nav_bullets'] = 'hover';
		}
		
		if (isset($this->request->post['nav_play_pause'])) {
			$data['nav_play_pause'] = $this->request->post['nav_play_pause'];
		} elseif (!empty($module_info)) {
			$data['nav_play_pause'] = $module_info['nav_play_pause'];
		} else {
			$data['nav_play_pause'] = 'true';
		}
		
		if (isset($this->request->post['nav_thumbs'])) {
			$data['nav_thumbs'] = $this->request->post['nav_thumbs'];
		} elseif (!empty($module_info)) {
			$data['nav_thumbs'] = $module_info['nav_thumbs'];
		} else {
			$data['nav_thumbs'] = 'true';
		}
		
		if (isset($this->request->post['nav_circle'])) {
			$data['nav_circle'] = $this->request->post['nav_circle'];
		} elseif (!empty($module_info)) {
			$data['nav_circle'] = $module_info['nav_circle'];
		} else {
			$data['nav_circle'] = 'true';
		}
		
		if (isset($this->request->post['nav_timer_bar'])) {
			$data['nav_timer_bar'] = $this->request->post['nav_timer_bar'];
		} elseif (!empty($module_info)) {
			$data['nav_timer_bar'] = $module_info['nav_timer_bar'];
		} else {
			$data['nav_timer_bar'] = 'true';
		}
		
		if (isset($this->request->post['nav_pause_hover'])) {
			$data['nav_pause_hover'] = $this->request->post['nav_pause_hover'];
		} elseif (!empty($module_info)) {
			$data['nav_pause_hover'] = $module_info['nav_pause_hover'];
		} else {
			$data['nav_pause_hover'] = 'true';
		}
		
		if (isset($this->request->post['slideshow_skin'])) {
			$data['slideshow_skin'] = $this->request->post['slideshow_skin'];
		} elseif (!empty($module_info)) {
			$data['slideshow_skin'] = $module_info['slideshow_skin'];
		} else {
			$data['slideshow_skin'] = 'v5';
		}
		
		// Google Web Fonts
		if (isset($this->request->post['g_fonts'])) {
			$data['g_fonts'] = $this->request->post['g_fonts'];
		} elseif (!empty($module_info)) {
			if (isset($module_info['g_fonts'])) {
			$g_fonts = $module_info['g_fonts'];
			}
		} else {
			$g_fonts = array();
		}
		
		$data['g_fonts'] = array();
		
		if (isset($g_fonts)) {
		
		foreach ($g_fonts as $g_font) {
			
		$this->document->addStyle('http://fonts.googleapis.com/css?family=' . $g_font['import']);
				
			$data['g_fonts'][] = array(
				'name'   => $g_font['name'],
				'import'   => $g_font['import']
			);
		}
		}
		
		// Slides & Layers
        if (isset($this->request->post['sections'])) {
			$data['sections'] = $this->request->post['sections'];
		} elseif (!empty($module_info)) {
			if (isset($module_info['sections'])) {
			$sections = $module_info['sections'];
			}
		} else {
			$sections = array();
		}
		
		$data['sections'] = array();
		
		if (isset($sections)) {
			
		foreach ($sections as $section) {
			
			$groups = array();
			
			$i = 0;
			
            if (isset($section['groups'])) {
				foreach($section['groups'] as $group) {
					$groups[$i] = $group;
					$i++;
				}
			}
			
			$data['sections'][] = array(
				'sort_order'   => $section['sort_order'],
				'link'   => $section['link'],
				'link_new_window'   => $section['link_new_window'],
				'duration'   => $section['duration'],
				'slide_transition'   => $section['slide_transition'],
				'thumb_image'   => $section['thumb_image'],
				'groups'  => $groups
			);
		}
		
		}
		$data['languages'] = array(
                    array(
                        'language_id' => '1',
                        'name' => 'English',
                        'code' => 'en',
                        'image' => 'view/plugins/layerslider/img/transparent.png',
                        'directory' => 'english'
                    )
                );
                
                
		$data['header'] = $this->getChild('common/header');
		//$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->getChild('common/footer');
                
		$this->response->setOutput($this->render('module/layerslider.phtml', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/layerslider')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
          		
		return !$this->error;
	}
}
