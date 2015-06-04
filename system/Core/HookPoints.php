<?php

namespace Core;

Class HookPoints {

    protected static $hooks = array();

    public static function addHook($tag, $callback, $priority = 10, $args = 1) {
        $idx = \uniqid();
       
        self::$hooks[$tag][$priority][$idx] = array('callback' => $callback, 'args' => $args);
    }

    public static function removeHook($tag, $callback) {
        foreach (self::$hooks[$tag] as $priority => $idxa) {
            foreach ($idxa as $idx => $items) {
                if ($item['callback'] === $callback) {
                    unset(self::$hooks[$tag][$priority][$idx]);
                }
            }
        }
    }

    public static function executeHooks($tag, $value = '') {

        if (isset(self::$hooks[$tag])) {
            ksort(self::$hooks[$tag]);
            $args = func_get_args();
            foreach (self::$hooks[$tag] as $pri) {
                foreach ($pri as $idx) {
                    if (!is_null($idx['callback'])) {
                        $value = call_user_func_array($idx['callback'], array_slice($args, 1, (int) $idx['args']));
                    }
                }
            }
        }
        return $value;
    }

    
}
