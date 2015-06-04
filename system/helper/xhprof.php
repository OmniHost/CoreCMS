<?php

if (extension_loaded('xhprof')) {

    $xhprof_conf_file = false;
    if (is_file('/media/sf_web/xhprof/xhprof_lib/config.php')) {
        $XHPROF_ROOT = '/media/sf_web/xhprof';
        define('XHPROF_LIB_ROOT', $XHPROF_ROOT . '/xhprof_lib');
        $xhprof_conf_file = XHPROF_LIB_ROOT . '/config.php';
        $isLocalhost = true;
        $x['doprofile'] = true;
    } elseif (is_file('/home/omnihost/xhprof/xhbuild/xhprof_lib/config.php')) {
      $XHPROF_ROOT = '/home/omnihost/xhprof/xhbuild';
      define('XHPROF_LIB_ROOT', $XHPROF_ROOT . '/xhprof_lib');
      $xhprof_conf_file = XHPROF_LIB_ROOT . '/config.php';
      $isLocalhost = false;
      $x['doprofile'] = (rand(1,5) == 1)?true:false;
       $x['doprofile'] = true;
      } 
    if ($xhprof_conf_file) {
        include($xhprof_conf_file);
 $_xhprof['doprofile'] = $x['doprofile'];

        if (in_array(PHP_SAPI, array('cli', 'cgi', 'cgi-fcgi'))) {
            $_SERVER['REMOTE_ADDR'] = null;
            $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
            $_xhprof['doprofile'] = false;
        }

        class visibilitator {

            public static function __callstatic($name, $arguments) {
                $func_name = array_shift($arguments);
                if (is_array($func_name)) {
                    list($a, $b) = $func_name;
                    if (count($arguments) == 0) {
                        $arguments = $arguments[0];
                    }
                    return call_user_func_array(array($a, $b), $arguments);
                } else {
                    call_user_func_array($func_name, $arguments);
                }
            }

        }

    //    if ($controlIPs === false || in_array($_SERVER['REMOTE_ADDR'], $controlIPs)) {
            $_xhprof['display'] = true;
  //          $_xhprof['doprofile'] = true;
            $_xhprof['type'] = 1;
   //     }

        if (in_array(PHP_SAPI, array('cli', 'cgi', 'cgi-fcgi')) || $_SERVER['REQUEST_URI'] == '/misc/uploadify.php') {
            $_xhprof['doprofile'] = false;
        }


//Display warning if extension not available
        if ($_xhprof['doprofile'] === true) {
            include_once(XHPROF_LIB_ROOT . '/utils/xhprof_lib.php');
            include_once(XHPROF_LIB_ROOT . '/utils/xhprof_runs.php');
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }

        function xhprof_shutdown_function() {
            global $_xhprof;


            if ($_xhprof['doprofile'] === true) {
                $profiler_namespace = $_xhprof['namespace'];  // namespace for your application
                $xhprof_data = xhprof_disable();
                $xhprof_runs = new XHProfRuns_Default();
                $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace, null, $_xhprof);
            }
        }

        register_shutdown_function('xhprof_shutdown_function');
    }
}
