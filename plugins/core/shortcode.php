<?php

class Plugin_Core extends \Core\Plugin {
    /* public function progress($attribs, $innerHtml) {
      $this->parseAttributes(array('style' => 'info', 'value' => '100'), $attribs);

      return '<div class="progress">
      <div class="progress-bar ' . $this->attribute('style') . '" role="progressbar" aria-valuenow="' . $this->attribute('value') . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $this->attribute('value') . '%;">
      <span class="sr-only">' . $this->attribute('value') . '%</span>
      </div>
      </div>';
      } */

    public function alert($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'style' => "info",
            'class' => "",
            'id' => "",
                ), $attribs);


        if ($attribs['style']) {
            $attribs['style'] = 'alert-' . $attribs['style'];
        }
        return '<div class="alert ' . $attribs['style'] . ' ' . $attribs['class'] . '" id="' . $attribs['id'] . '"><button type="button" class="close" data-dismiss="alert">×</button>' . $innerHtml . '</div>';
    }

    public function align($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'position' => 'info',
            'class' => "",
            'id' => "",
                ), $attribs);
        $class = $attribs['class'];
        $id = $attribs['id'];
        $position = $attribs['position'];
        if ($position) {
            $position = 'pull-' . $position;
        }
        return '<div class="' . $position . ' ' . $class . '" id="' . $id . '">' . $innerHtml . '</div>';
    }

    // --------------------------------------------------------------------------

    /**
     * Badge
     *
     * Usage: {{ bootstrap:badge style="" }} My Content {{ /bootstrap:badge }}
     * Possible styles: default, success, warning, important, info, inverse
     * 
     * Optional: class and id
     *
     * @return html
     */
    function badge($attribs, $innerHtml) {
        $this->parseAttributes(array('style' => 'default'), $attribs);
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $style = $this->attribute('style');
        if ($style && $style != "default") {
            $style = 'badge-' . $style;
        }
        return '<span class="badge ' . $style . ' ' . $class . '" id="' . $id . '">' . $innerHtml . '</span>';
    }

    /*     * ************* Javacript Carousel Plugin ************** */

    /**
     * Carousel
     *
     * Usage: {{ bootstrap:carousel slug="Gallery Slug" }}
     * This function requires the galleries module to be installed
     * 
     * Optional: class
     *
     * @return html
     */
    function carousel() {
        $class = $this->attribute('class');
        $limit = $this->attribute('limit');
        $slug = $this->attribute('slug');
        $offset = $this->attribute('offset');
        $safe_slug = str_replace(" ", "_", $slug);
        if ($limit) {
            $limit = 'limit="' . $limit . '"';
        }
        if ($offset) {
            $offset = 'offset="' . $offset . '"';
        }

        $html = <<<CAROUSEL
		<div id="$safe_slug" class="carousel slide $class">
		  	<div class="carousel-inner">
		    	{{ galleries:images slug="$slug" $limit $offset}}
					<div class="item 
						{{ if order == "1" }}
							active
						{{ endif }}
						"><img src="{{url:site}}files/large/{{ file_id }}" alt="{{ name }}">
						{{ if description }}
							<div class="carousel-caption"><p>{{ description }}</p></div>
						{{ endif }}
					</div>
				{{ /galleries:images }}
		  	</div>
		  	<a class="carousel-control left" href="#$safe_slug" data-slide="prev">&lsaquo;</a>
		  	<a class="carousel-control right" href="#$safe_slug" data-slide="next">&rsaquo;</a>
		</div>
CAROUSEL;
        return $html;
    }

    /*     * ************* Javacript Carousel Plugin ************** */

    /*     * ************* Javascript Collapse Plugin ************** */

    /**
     * Collapse
     *
     * Usage: {{ bootstrap:collapse title="My Title" }} My Content {{ /bootstrap:collapse }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function collapse() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $title = $this->attribute('title');
        if (!$title) {
            $title = 'Please enter a title';
        }
        $safe_title = str_replace(" ", "_", $title);
        return '<button type="button" id="' . $id . '" class="btn btn-inverse ' . $class . '" data-toggle="collapse" data-target="#' . $safe_title . '">' . $title . '</button>
        		<div id="' . $safe_title . '" class="collapse out">' . $this->content() . '</div>';
    }

    /*     * ************* End Javascript Collapse Plugin ************** */

    // --------------------------------------------------------------------------

    /**
     * Emphasis
     *
     * Usage: {{ bootstrap:emphasis style="" }} My Content {{ /bootstrap:emphasis }}
     * Possible styles: muted, text-warning, text-error, text-info, text-success
     * 
     * Optional: class and id
     *
     * @return html
     */
    function emphasis() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $style = $this->attribute('style');
        if ($style && $style != "muted") {
            $style = "text-" . $style;
        }
        return '<p class="' . $style . ' ' . $class . '" id="' . $id . '">' . $this->content() . '</p>';
    }

    // --------------------------------------------------------------------------

    /**
     * Hero
     *
     * Usage: {{ bootstrap:hero }} My Content {{ /bootstrap:hero }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function hero() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<div class="hero-unit ' . $class . '" id="' . $id . '">' . $this->content() . '</div>';
    }

    // --------------------------------------------------------------------------

    /**
     * Label
     *
     * Usage: {{ bootstrap:label style="" }} My Content {{ /bootstrap:label }}
     * Possible styles: default, success, warning, important, info, inverse
     * 
     * Optional: class and id
     *
     * @return html
     */
    function label() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $style = $this->attribute('style');
        if ($style && $style != "default") {
            $style = 'label-' . $style;
        }
        return '<span class="label ' . $style . ' ' . $class . '" id="' . $id . '">' . $this->content() . '</span>';
    }

    // --------------------------------------------------------------------------

    /**
     * Lead
     *
     * Usage: {{ bootstrap:lead }} My Content {{ /bootstrap:lead }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function lead() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<p class="lead ' . $class . '" id="' . $id . '">' . $this->content() . '</p>';
    }

    // --------------------------------------------------------------------------

   

    // --------------------------------------------------------------------------

    /**
     * Rowfluid
     *
     * Usage: {{ bootstrap:rowfluid }} My Content {{ /bootstrap:rowfluid }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function rowfluid() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<div class="row-fluid ' . $class . '" id="' . $id . '">' . $this->content() . '</div>';
    }

    // --------------------------------------------------------------------------

    /**
     * Span
     *
     * Usage: {{ bootstrap:span size="12" }} My Content {{ /boostrap:span }}
     * 
     * Optional: class, id and offset
     *
     * @return html
     */
    function span() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $size = $this->attribute('size');
        $offset = $this->attribute('offset');
        if (!$size) {
            $size = 12;
        }
        $html = '<div class="span' . $size . ' ' . $class;
        if ($offset) {
            $html .= ' offset' . $offset;
        }
        $html .= '" id="' . $id . '">' . $this->content() . '</div>';
        return $html;
    }

    /*     * ************* Javacript Tabs Plugin ************** */

    /**
     * Tab Header Wrap
     *
     * Usage: {{ bootstrap:tabheaderwrap }}  My Content {{ /bootstrap:tabheaderwrap }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function tabheaderwrap() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<ul class="nav nav-tabs ' . $class . '" id="' . $id . '">' . $this->content() . '</ul>';
    }

    // --------------------------------------------------------------------------

    /**
     * Tab Header
     *
     * Usage: {{ bootstrap:tabheader title="My Title" }}  
     * 
     * Optional: class and id
     *
     * @return html
     */
    function tabheader() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $title = $this->attribute('title');
        $active = $this->attribute('active');
        if ($active == "true") {
            $active = "active";
        } else {
            $active = "";
        }
        if (!$title) {
            $title = 'Please enter a title';
        }
        $safe_title = str_replace(" ", "_", $title);
        return '<li class="' . $active . ' ' . $class . '" id="' . $id . '"><a href="#' . $safe_title . '" data-toggle="tab">' . $title . '</a></li>';
    }

    // --------------------------------------------------------------------------

    /**
     * Tab Content Wrap
     *
     * Usage: {{ bootstrap:tabcontentwrap }} My Content {{ bootstrap:tabcontentwrap }}
     * 
     * Optional: class and id
     *
     * @return html
     */
    function tabcontentwrap() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<div class="tab-content ' . $class . '" id="' . $id . '">' . $this->content() . '</div>';
    }

    // --------------------------------------------------------------------------

    /**
     * Tab Content
     *
     * Usage: {{ bootstrap:tabscontent title="My Title" }} My Content {{ /bootstrap:tabscontent }}  
     * 
     * Optional: class, id and active
     *
     * @return html
     */
    function tabcontent() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $active = $this->attribute('active');
        $title = $this->attribute('title');
        if (!$title) {
            $title = 'Please enter a the same title as a tabheader';
        }
        $safe_title = str_replace(" ", "_", $title);
        if ($active == "true") {
            $active = "active";
        } else {
            $active = "";
        }
        return '<div class="tab-pane ' . $active . ' ' . $class . '" id="' . $safe_title . '">' . $this->content() . '</div>';
    }

    /*     * ************* End Javascript Tabs Plugin ************** */

    /**
     * Well
     *
     * Usage: {{ bootstrap:well }} My Content {{ /bootstrap:well }}
     * 
     * Optional: class and id
     * Add well-large or well-small to class for more or less padding
     *
     * @return html
     */
    function well() {
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        return '<div class="well ' . $class . '" id="' . $id . '">' . $this->content() . '</div>';
    }

    public function module($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'name' => false,
            'module_id' => 0
                ), $attribs);

        if ($attribs['name']) {
            $attribs['name'] = str_replace("module/","",  $attribs['name']);
        }

        if ($attribs['name'] && $this->config->get($attribs['name'] . '_status')) {
            return $this->load->controller('module/' . $attribs['name']);
        }
        
        if ($attribs['module_id']) {
            $this->load->model('extension/module');
            $setting_info = $this->model_extension_module->getModule($attribs['module_id']);

            if ($setting_info && $setting_info['status']) {
               return $this->load->controller('module/' . $attribs['name'], $setting_info);
      
            }
        }
        return '';
    }

    public function gallery($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'name' => '',
            'banner_id' => 0,
            'resize' => false,
                ), $attribs);
        $filter = array();
        if ($attribs['name']) {
            $filter['filter_name'] = $attribs['name'];
        }
        if ($attribs['banner_id']) {
            $filter['filter_banner_id'] = $attribs['banner_id'];
        }
        return $this->load->controller('module/gallery', $filter);
    }

    public function script($attrs) {

        if (!empty($attrs['src'])) {
            $this->document->addScript($attrs['src']);
        }
        return '';
    }

    public function inlinescript($attrs, $script) {
        return '<script>' . html_entity_decode($script, ENT_QUOTES, 'UTF-8') . '</script>';
    }

    public function bootstrap_row($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'id' => '',
            'class' => ''
                ), $attribs);
        $class = $this->attribute('class');
        $id = $this->attribute('id');

        $innerHtml = str_replace(array("&nbsp;[", "&nbsp;"), array("[", "]"), $innerHtml);
        /*     if(\Core\Shortcode::hasShortcode($innerHtml, 'bootstrap_span')){
          var_dump($innerHtml);
          exit;
          } */



        return '<div class="row ' . $class . '" id="' . $id . '">' . $innerHtml . '</div>';
    }

    public function bootstrap_span($attribs, $innerHtml) {
        $attribs = $this->parseAttributes(array(
            'id' => '',
            'class' => '',
            'size' => '',
                ), $attribs);
        $class = $this->attribute('class');
        $id = $this->attribute('id');
        $size = $this->attribute('size');


        if (!$size) {
            $size = "col-xs-12";
        }
        $html = '<div class="' . $size . ' ' . $class;
        $html .= '" id="' . $id . '">' . $innerHtml . '</div>';
        return $html;
    
    }

    public function init() {
        if (!$this->isAdmin()) {
            add_shortcode('bootstrap_alert', array($this, 'alert'));
            add_shortcode('bootstrap_align', array($this, 'align'));
            add_shortcode('gallery', array($this, 'gallery'));
            add_shortcode('module', array($this, 'module'));
            add_shortcode('script', array($this, 'script'));
            add_shortcode('inlinescript', array($this, 'inlinescript'));
            add_shortcode('bootstrap_span', array($this, 'bootstrap_span'));
            add_shortcode('bootstrap_row', array($this, 'bootstrap_row'));
        }
    }

}

$bootstraper = new Plugin_Core();



/*

tags[2] = ["[bootstrap_badge style=%27%27] My Content [/bootstrap_badge]", "Badge", "Badge"];
tags[3] = ["[bootstrap_carousel slug=%27Gallery Slug%27]", "Carousel", "Carousel"];
tags[4] = ["[bootstrap_collapse title=%27My Title%27] My Content  [/bootstrap_collapse]", "Collapse", "Collapse"];
tags[5] = ["[bootstrap_emphasis style=%27%27] My Content  [/bootstrap_emphasis]", "Emphasis", "Emphasis"];
tags[6] = ["[bootstrap_hero] My Content  [/bootstrap_hero]", "Hero", "Hero"];
tags[7] = ["[bootstrap_label style=%27%27] My Content [/bootstrap_label]", "Label", "Label"];
tags[8] = ["[bootstrap_lead] My Content  [/bootstrap_lead]", "Lead", "Lead"];
tags[9] = ["[bootstrap_row] [bootstrap_span size=%27%27] My Content  [/bootstrap_span] [/bootstrap_row]", "Row - Span", "Row - Span"];
tags[10] = ["[bootstrap_tabheaderwrap][bootstrap_tabheader title=%27Tab One%27 active=%27true%27][bootstrap_tabheader title=%27Tab Two%27][/bootstrap_tabheaderwrap][bootstrap_tabcontentwrap][bootstrap_tabcontent title=%27Tab One%27 active=%27true%27]Tab one contents goes here[/bootstrap_tabcontent][bootstrap_tabcontent title=%27Tab Two%27]Tab two contents goes here[/bootstrap_tabcontent][/bootstrap_tabcontentwrap]", "Tabs", "Tabs"];
tags[11] = ["[bootstrap_well]My Content[/bootstrap_well]", "Well", "Well"];

add_shortcode('baztag', 'baztag_func');*/
/**
 * 
 */