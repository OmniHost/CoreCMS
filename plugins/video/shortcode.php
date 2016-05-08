<?php

class Plugin_video extends \Core\Plugin {

    public function video_shortcode($atts, $content = '') {
        $this->parseAttributes(array(
            'align' => 'center',
            'aspect_ratio' => '16:9',
            'width' => '100',
            'params' => '',
            'autoplay' => 0), $atts);

        $aspect_ratio = str_replace(':', '-', $this->attribute('aspect_ratio'));
        


        return $this->get_embed_video($content, $this->attribute('align'), $aspect_ratio, $this->attribute('width'), $this->attribute('autoplay'), $this->attribute('params'));
    }

    public function get_embed_video($url, $align, $aspect, $width = null, $autoplay = 0, $params = '') {
        $code = $this->before_video($align, $aspect, $width);
        $code .= $this->embed_video($url, $autoplay,$params);
        $code .= $this->after_video();
        return $code;
    }

    private function before_video($align, $aspect, $width = null) {
        $code = '<div class="vid-container resp-video-' . $align . '"';
        if (isset($width)) {
            $code .= ' style="width: ' . $width . '%;"';
        }
        $code .= '>';
        $code .= '<div class="resp-video-wrapper size-' . $aspect . '">';
        return $code;
    }

    private function embed_video($url, $autoplay = 0, $params = '') {
        $regex = "/ (width|height)=\"[0-9\%]*\"/";
        require_once('AutoEmbed.php');
        $autoEmbed = new AutoEmbed();
        $embed_code = $autoEmbed->get_html($url, array('width' => '100%', 'height' => '100%', 'autoplay' => $autoplay, 'params' => $params));
        if (!$embed_code) {
            return '<strong>Error: Invalid URL!</strong>';
        }
        
        $this->document->addScript('view/plugins/fitvids/jquery.fitvids.js');
        
        return preg_replace($regex, '', $embed_code);
    }

    private function after_video() {
        $code = '</div>';
        $code .= '</div>';
        return $code;
    }

    public function init() {
        if (!$this->isAdmin()) {
            add_shortcode('video', array($this, 'video_shortcode'));
        }
    }

}

new Plugin_video();
