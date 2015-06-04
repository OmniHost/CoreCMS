<?php

add_shortcode('span_color', 'span_color_func');

function span_color_func($attribs, $innercontent = '') {

    $attribs = \Core\Shortcode::shortcodeAtts(array(
                'textcolor' => "#cc0000"
                    ), $attribs);

    $html = '<span style="color:' . $attribs['textcolor'] . '">' . $innercontent . '</span>';
    return $html;
}

$html = 'runa n alert [bootstrap_alert style=\'warning\']Alert[/bootstrap_alert][div_color]content 2[/div_color]';

class Plugin_demo extends \Core\Plugin {

    public function div_color($attribs, $innercontent = '') {

        $attribs = \Core\Shortcode::shortcodeAtts(array(
                    'textcolor' => "#cc0000"
                        ), $attribs);

        $html = '<div style="color:' . $attribs['textcolor'] . '">' . $innercontent . '</div>';
        return $html;
    }

    public function init() {
        add_shortcode('div_color', array($this, 'div_color'));
    }

}

$psd = new Plugin_demo;

class DemoHook extends \Core\Hook {

    public function init() {
        $this->addHook('editor_links', array($this, 'demo_editor_pages'));
    }

    function demo_editor_pages($pages = array()) {
      
        $pages[] = array('xxx','http://hahahah');
        return $pages;
    }

}
$class = new DemoHook();