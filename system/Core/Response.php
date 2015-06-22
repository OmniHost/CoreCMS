<?php

namespace Core;

class Response {

    private $headers = array();
    private $level = 0;
    private $output;

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function header($header, $val) {
        $this->addHeader($header . ':' . $val);
    }

    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    public function setCompression($level) {
        $this->level = $level;
    }

    public function setOutput($output) {
        $this->output = $output;
    }

    private function compress($data, $level = 0) {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
            $encoding = 'gzip';
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        /*
         *  header('Vary: Accept-Encoding');
          header("cache-control: must-revalidate");
          $offset = 60 * 60;
          $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
          header($expire);
         */
        $this->addHeader('Content-Encoding: ' . $encoding);
        $this->addHeader('Vary: Accept-Encoding');

        $output = gzencode($data, (int) $level);

        return $output;
    }

    public function output() {

        if ($this->output) {


            $output = \Core\Shortcode::doShortcode($this->output);

            $doc = \Core\Registry::getInstance()->get('document');
            $scripts = $doc->getScripts();
            if ($scripts) {
                $html = '';
                foreach ($scripts as $script) {
                    $html .= '<script type="text/javascript" src="' . $script . '"></script>';
                }
                $output = str_replace('<!-- Custom JS -->', $html, $output);
            }
            $styles = $doc->getStyles();
            if ($styles) {
                $html = '';
                foreach ($styles as $style) {
                    $html .= '<link rel="' . $style['rel'] . '" type="text/css" href="' . $style['href'] . '" media="' . $style['media'] . '" />';
                }
                $output = str_replace('<!-- Custom CSS -->', $html, $output);
            }
            
            $metas = $doc->getMeta();
            if ($metas) {
                $html = '';
                foreach ($metas as $meta) {
                    $html .= $meta;
                }
                $output = str_replace('<!-- Custom META -->', $html, $output);
            }
            
            $output = \Core\HookPoints::executeHooks('before_render', $output);


            if ($this->level) {

                $output = $this->compress($output, $this->level);
                $this->addHeader('Content-Length: ' . strlen($output));
            }



            if (!headers_sent()) {

                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            echo $output;
        }
    }

}
            $output = \Core\HookPoints::executeHooks('before_render', $output);

