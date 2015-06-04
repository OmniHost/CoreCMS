<?php

namespace Core;

final class Front {

    /**
     * Any actions that should run prior to the requested route
     * @var array 
     */
    protected $pre_action = array();

    /**
     * Holds the error handling data
     * @var null|\Core\Action 
     */
    protected $error;

    public function __construct() {
        
    }

    /**
     * Add any pre-actions to the current request
     * @param \Core\Action $pre_action
     */
    public function addPreAction($pre_action) {
        $this->pre_action[] = $pre_action;
    }

    /**
     * Dispatches the current action
     * @param \Core\Action $action
     * @param \Core\Action $error - what should happen if the route does not exist
     */
    public function dispatch($action, $error) {
        $this->error = $error;
        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);
         

            if ($result) {
                $action = $result;

                break;
            }
        }

        while ($action) {
            $action = $this->execute($action);
        }
    }

    /**
     * Executes the current action
     * @param \Core\Action $action
     * @return string
     */
    private function execute($action) {
        if (file_exists($action->getFile())) {
            require_once(__modification($action->getFile()));

            $class = $action->getClass();

            $controller = new $class();

            if (is_callable(array($controller, $action->getMethod()))) {
                $action = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
            } else {
                $action = $this->error;

                $this->error = '';
            }
        } else {
            $action = $this->error;

            $this->error = '';
        }

        return $action;
    }

}
