<?php

/**
 * openDeal - Opensource Deals Platform
 *
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2014 Craig Smith
 * @link        https://github.com/openDeal/openDeal
 * @license     https://raw.githubusercontent.com/openDeal/openDeal/master/LICENSE
 * @since       1.0.0
 * @package     Core
 * GPLV3 Licence
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Core;

abstract class Controller {

    protected $id;
    protected $layout;
    private $error;

    /**
     * Template to be rendered
     * @var string
     */
    protected $template;

    /**
     * Child Controller/Actions to call
     * @var array
     */
    protected $children = array();

    /**
     * Data for injection into the Template
     * @var array 
     */
    protected $data = array();

    /**
     * The parsed output of the tempate and data
     * @var string
     */
    protected $output;

    public function __construct() {
        
    }

    /**
     * Magic method to get a key from the registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    /**
     * Sets a registry key
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    /**
     * Forward current call to a differend action without reload
     * @param string $route
     * @param array $args
     * @return \Core\Action
     */
    protected function forward($route, $args = array()) {
        return new \Core\Action($route, $args);
    }

    /**
     * Redirect the browser to a new URL
     * @param string $url
     * @param int $status
     */
    protected function redirect($url, $status = 302) {
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    /**
     * Returns child output
     * @param string $child
     * @param array $args
     * @return string
     */
    protected function getChild($child, &$args = array()) {
        $action = new \Core\Action($child, $args);

        if (file_exists($action->getFile())) {
            require_once(__modification($action->getFile()));

            $class = $action->getClass();

            $controller = new $class();

            $controller->{$action->getMethod()}($action->getArgs());

            return $controller->output;
        } else {
            throw new \Core\Exception('Error: Could not load controller ' . $child . '!');
            exit();
        }
    }

    /**
     * Test whether or not the Action has a specific action
     * @param string $child
     * @param array $args
     * @return boolean
     */
    protected function hasAction($child, $args = array()) {
        $action = new \Core\Action($child, $args);

        if (file_exists($action->getFile())) {
            require_once(__modification($action->getFile()));

            $class = $action->getClass();

            $controller = new $class();

            if (method_exists($controller, $action->getMethod())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setOverride($key) {
        if (!$this->template) {
            throw new \Core\Exception("Template needs to be set first");
        }
        $key = slug($key);
        $path = substr($this->template, 0, -5) . $key . '.phtml';

        $theme = $this->config->get('config_template');
        if (is_file(DIR_TEMPLATE . $theme . '/' . $path)) {
            $this->template = $path;
        }
    }

    /**
     * Returns the output
     * @return string
     */
    protected function render($tpl = false, $data = array()) {

        if ($tpl) {
            $this->template = $tpl;
        }

        if ($data) {
            $this->data = array_deap_merge($data, $this->data);
        }

        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        $this->output = $this->load->view($this->template, $this->data);
        return $this->output;
        /*
        $template = DIR_TEMPLATE;

        if (NS != 'admin' && NS != 'installer') {
            $theme = $this->config->get('config_template');
            if ($theme) {
                if (is_file($template . $theme . '/' . $this->template)) {
                    $template .= $theme . '/';
                } else {
                    $template .= 'default/';
                }
            } elseif (!is_file($template . $this->template)) {
                $template .= 'default/';
            }
        }

        //is there an override ???

        if (file_exists($template . $this->template)) {
            $this->fillTranslations();
            extract($this->data);

            ob_start();

            require(__modification($template . $this->template));

            $this->output = ob_get_contents();

            ob_end_clean();

            return $this->output;
        } else {
            throw new \Core\Exception('Error: Could not load template ' . $template . $this->template . '!');
            exit();
        }*/
    }

    public function not_allowed() {
        $this->language->load('error/not_allowed');

        $this->document->settitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['p'])) {
            $data = $this->request->get;

            unset($data['_route_']);

            $route = $data['p'];

            unset($data['p']);

            $url = '';

            if ($data) {
                $url = '&' . urldecode(http_build_query($data, '', '&'));
            }

            if (isset($this->request->server['https']) && (($this->request->server['https'] == 'on') || ($this->request->server['https'] == '1'))) {
                $connection = 'ssl';
            } else {
                $connection = 'nonssl';
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link($route, $url, $connection),
                'separator' => $this->language->get('text_separator')
            );
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_error'] = $this->language->get('text_error');

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->response->addheader($this->request->server['SERVER_PROTOCOL'] . '/1.1 401 Not Authorised');

        $this->data['continue'] = $this->url->link('common/home');


        $this->template = 'error/not_allowed.phtml';

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

        $this->response->setoutput($this->render());
    }

    public function not_found($heading = false, $msg = false) {
        $this->language->load('error/not_found');





        if ($heading) {
            $this->data['heading_not_found'] = $heading;
            $this->document->setTitle($heading);
            $this->data['heading_title'] = $heading;
        } else {
            $this->data['heading_not_found'] = $this->language->get('heading_not_found');
            $this->document->setTitle($this->language->get('heading_title'));
            $this->data['heading_title'] = $this->language->get('heading_title');
        }

        if ($msg) {
            $this->data['text_not_found'] = $msg;
        } else {
            $this->data['text_not_found'] = $this->language->get('text_not_found');
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', null, 'SSL'),
            'active' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->data['heading_title'],
            'href' => $this->url->link('error/not_found', null, 'SSL'),
            'active' => true
        );

        $this->template = 'error/not_found.phtml';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->addHeader("Status Code: " . $_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        $this->response->setOutput($this->render());
    }

    protected function fillTranslations() {
        $translations = $this->language->all();
        foreach ($translations as $k => $v) {
            if (!isset($this->data[$k]) && substr($k, 0, 6) != 'error_') {
                $this->data[$k] = $v;
            }
        }
    }

    public function getOutput() {
        return $this->output;
    }

    public function renderJSON($data = array()) {

        if ($data) {
            $this->data = array_deap_merge($data, $this->data);
        }

        $json = \json_encode($this->data);

# JSON if no callback
        if (!isset($this->request->get['callback'])) {
            $this->output = $json;
            return $this->output;
        }

        if ($this->is_valid_callback($this->request->get['callback'])) {
            return("{$this->request->get['callback']}($json)");
        } else {
            return \json_encode("Invalid Callback provided");
        }
    }

    protected function is_valid_callback($subject) {
        $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

        $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
            'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
            'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
            'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
            'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
            'private', 'public', 'yield', 'interface', 'package', 'protected',
            'static', 'null', 'true', 'false');



        return preg_match($identifier_syntax, $subject) && !in_array(strtolower($subject), $reserved_words);
    }

}
