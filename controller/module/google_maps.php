<?php
class ControllerModuleGoogleMaps extends \Core\Controller
{
	public function index($setting)
	{
		static $module_map = 0;

		$this->document->addScript('//maps.google.com/maps/api/js?sensor=true');

		//--Load Helper
		$this->load->helper('google_maps');
                


		$maps = array();
		if (isset($this->request->post['google_maps_module_map']))
		{
			$maps = $this->request->post['google_maps_module_map'];
		}
		else if ($this->config->has('google_maps_module_map'))
		{
			$maps = $this->config->get('google_maps_module_map');
		}

		$data['gmaps'] = array();
		$fistmaplatlong = false;
		foreach ($maps as $map)
		{
			foreach ( $setting['ids'] as $smts )
			{
				if ( $smts == $map['id'] )
				{
					if ($fistmaplatlong == false)
					{
						$data['gmap_flatlong'] = $map['latitude'] . ',' . $map['longitude'];
						$fistmaplatlong = true;
					}
					$tmpmaptext = $map['maptext'];

					//@vkronlein bugfix 20/11/2013
					$tmpmaptext = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', '', $tmpmaptext);

					
					$data['gmaps'][] = array(
						'balloon_width'	=> gmaps_width_height($map['balloon_width'], '200px'),
						'onelinetext'	=> false,
						'latlong'		=> $map['latitude'] . ',' . $map['longitude'],
						'maptext'		=> html_entity_decode($tmpmaptext, ENT_QUOTES, 'UTF-8')
					);
					break;
				}
			}
		}

		$data['gmap_maptype'] 	= $setting['maptype'];
		$data['gmap_width'] 	= gmaps_width_height($setting['width'], '100%');
		$data['gmap_height'] 	= gmaps_width_height($setting['height'], '350px');
		$data['gmap_zoom'] 		= $setting['zoom'];

		// Check language marker
		if ( file_exists(DIR_IMAGE . 'google_maps/marker.png') )
		{
			$data['gmap_marker'] = 'img/google_maps/marker.png';
			$data['gmap_marker_image_size'] = '129, 42';
			$data['gmap_marker_point'] = '18, 42';
		}
		
		else
		{
			// Default marker from google
			$data['gmap_marker'] = '/maps.google.com/intl/en_us/mapfiles/ms/micons/red.png';
			$data['gmap_marker_image_size'] = '32, 32';
			$data['gmap_marker_point'] = '15, 32';
		}
		$data['module_map'] = $module_map++;
                
                $this->data = $data;
        $this->template = 'module/google_maps.phtml';
        $this->setOverride($setting['name']);
        return $this->render();

		
	}

}