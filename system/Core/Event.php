<?php

namespace Core;

class Event {

    private $data = array();

    public function register($key, $action, $priority = 0) {
        $this->data[$key][] = array(
            'action' => $action,
            'priority' => (int) $priority,
        );
    }

    public function unregister($key, $action) {
        if (isset($this->data[$key])) {
            foreach ($this->data[$key] as $index => $event) {
                if ($event['action'] == $action) {
                    unset($this->data[$key][$index]);
                }
            }
        }
    }

    public function mergeTrigger($key, &$arg = array()) {
        if (isset($this->data[$key])) {
            usort($this->data[$key], array("\Core\Event", "cmpByPriority"));
            foreach ($this->data[$key] as $event) {
                $action = $this->createAction($event['action'], $arg);
                $arg = $action->execute();
            }
        }
        return $arg;
    }

    public function trigger($key, &$arg = array()) {
        if (isset($this->data[$key])) {
            usort($this->data[$key], array("\Core\Event", "cmpByPriority"));
            foreach ($this->data[$key] as $event) {
                $action = $this->createAction($event['action'], $arg);
                if ($action->getFile()) {
                    require_once(__modification($action->getFile()));
                    $class = $action->getClass();
                    $controller = new $class();
                    if (method_exists($controller, $action->getMethod())) {
                        $controller->{$action->getMethod()}($arg);
                    }
                }
            }
        }
    }

    protected static function cmpByPriority($a, $b) {
        if ($a['priority'] == $b['priority']) {
            return 0;
        }

        return ($a['priority'] > $b['priority']) ? -1 : 1;
    }

    protected function createAction($action, &$arg) {
        return new \Core\Action($action, $arg);
    }

}
