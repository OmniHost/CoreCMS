<?php

if (!function_exists('gmaps_width_height')) {

    function gmaps_width_height($value, $ret = 'auto') {
        if (is_numeric($value))
            $ret = $value . 'px';
        else if (strpos(strtolower($value), 'px') !== false) {
            $tmp = explode('px', $value);
            if (is_numeric($tmp[0]))
                $ret = $tmp[0] . 'px';
        }
        else if (strpos($value, '%') !== false) {
            $tmp = explode('%', $value);
            if (is_numeric($tmp[0]))
                $ret = $tmp[0] . '%';
        }
        else if (strtolower($value) == 'auto' or strlen(trim($value)) == 0)
            $ret = 'auto';

        return $ret;
    }

}







