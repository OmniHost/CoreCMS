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
    protected function getChild($child, $args = array()) {
        $action = new \Core\Action($child, $args);

        if (file_exists($action->getFile())) {
            require_once(__modification($action->getFile()));

            $class = $action->getClass();

            $controller = new $class();

            $controller->{$action->getMethod()}($action->getArgs());

            return $controller->output;
        } else {
            trigger_error('Error: Could not load controller ' . $child . '!');
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

    /**
     * Returns the output
     * @return string
     */
    protected function render($tpl = false, $data = array()) {
        
        if($tpl){
            $this->template = $tpl;
        }
        
        if($data){
            $this->data = array_deap_merge($data, $this->data);
        }
        
        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

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
            trigger_error('Error: Could not load template ' . $template . $this->template . '!');
            exit();
        }
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
            if (!isset($this->data[$k]) && substr($k,0,6) != 'error_') {
                $this->data[$k] = $v;
            }
        }
    }

}
