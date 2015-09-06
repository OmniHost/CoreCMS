<?php

$cache = true;
$compress = true;
$cachedir = dirname(dirname(__FILE__)) . '/_cache';
$cssdir = dirname(__FILE__) . '/view';
$jsdir = dirname(__FILE__) . '/view';
define("PATHROOT",  ltrim(dirname($_SERVER['PHP_SELF']) . '/',"/"));


header("X-Powered-By: CoreFW");

// Determine the directory and type we should use 
switch ($_GET['type']) {
    case 'css':
        $base = realpath($cssdir);
        break;
    case 'javascript':
        $base = realpath($jsdir);
        break;
    default:
        header("HTTP/1.0 503 Not Implemented");
        exit;
};



$type = $_GET['type'];
$elements = explode(',', $_GET['files']);

// Determine last modification date of the files 
$lastmodified = 0;


while (list(, $element) = each($elements)) {
    $path = realpath($base . '/' . $element);

    // echo '*' . $element  . ':' . $path . '<br />';

    if (($type == 'javascript' && substr($path, -3) != '.js') ||
            ($type == 'css' && substr($path, -4) != '.css')) {


        header("HTTP/1.0 403 Forbidden");
        exit;
    }

    if (substr($path, 0, strlen($base)) != $base || !file_exists($path)) {

        header("HTTP/1.0 404 Not Found");
        exit;
    }


    $lastmodified = max($lastmodified, filemtime($path));
}

// Send Etag hash 
$hash = $lastmodified . '-' . md5($_GET['files']);


/* * *
 * Delete older versions!!!!
 * 
 */

header("Etag: \"" . $hash . "\"");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
        stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"') {
    // Return visit and no modifications, so do not send anything 
    header("HTTP/1.0 304 Not Modified");
    header('Content-Length: 0');
} else {
    // First time visit or files were modified 
    if ($cache) {
        // Determine supported compression method
        $gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
        $deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');

        // Determine used compression method
        $encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');

        // Check for buggy versions of Internet Explorer
        if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
                preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
            $version = floatval($matches[1]);

            if ($version < 6)
                $encoding = 'none';

            if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
                $encoding = 'none';
        }

        // Try the cache first to see if the combined files were already generated 
        $cachefile = 'cache-' . $hash . '.' . $type . ($encoding != 'none' ? '.' . $encoding : '');

        $old = glob($cachedir . "/*" . md5($_GET['files']) . "*");
        foreach ($old as $o) {
            if ($o !=  $cachedir . '/' . $cachefile){
                  @unlink($o);
            }
        }
       





        if (file_exists($cachedir . '/' . $cachefile)) {



            //   echo compress(file_get_contents($cachedir . '/' . $cachefile), 9, $type);
            //  exit;
            if ($fp = fopen($cachedir . '/' . $cachefile, 'rb')) {

                if ($encoding != 'none') {
                    header("Content-Encoding: " . $encoding);
                }

                header("Content-Type: text/" . $type);
                header("Content-Length: " . filesize($cachedir . '/' . $cachefile));
                header('Vary: Accept-Encoding');
                $offset = 60 * 60 * 24 * 30;
                $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
                header($expire);

                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
    }



    // Get contents of the files 
    $contents = '';
    reset($elements);
    if ($type == 'css') {
        $pattern = '(url ?\((.*?)\))';

        $pattern2 = "@import url\((.*?)\);";

        while (list(, $element) = each($elements)) {
            $path = realpath($base . '/' . $element);

            $contents .= preg_replace_callback('/' . $pattern . '/', function($input) use($element) {
                
                        return fix_path($input, $element);
                    }, file_get_contents($path)) . "\n";
            // $contents .= "\n\n" . file_get_contents($path);
        }

      


        if ($compress) {
            $contents = str_replace('; ', ';', str_replace(' }', '}', str_replace('{ ', '{', str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), "", preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents)))));
        }
    } else {
        while (list(, $element) = each($elements)) {
            $path = realpath($base . '/' . $element);
            $contents .= file_get_contents($path) . "\n";
        }

        if ($compress) {
            $contents = str_replace('; ', ';', str_replace(' }', '}', str_replace('{ ', '{', str_replace(array("\t", '  ', '    ', '    '), "", $contents))));
        }
    }


    // Send Content-Type
    header("Content-Type: text/" . $type);
    header('Vary: Accept-Encoding');
    $offset = 60 * 60;
    $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
    header($expire);

    if (isset($encoding) && $encoding != 'none') {
        // Send compressed contents
        $contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
        header("Content-Encoding: " . $encoding);
        header('Content-Length: ' . strlen($contents));
        echo $contents;
    } else {
        // Send regular contents
        header('Content-Length: ' . strlen($contents));
        echo $contents;
    }

    // echo compress($contents, 9, $type);
    // Store cache 
    if ($cache) {



        if ($fp = fopen($cachedir . '/' . $cachefile, 'wb')) {
            fwrite($fp, $contents);
            fclose($fp);
        }
    }
}

function fix_path($input, $file) {

    $path = PATHROOT . 'view';

    $sub_folder = dirname($file);
    if ($sub_folder && $sub_folder != '.') {
        $path .= '/' . $sub_folder;
    }
    $base_path = explode('/', $path);
    $remove = array(' ', '(', ')', 'url', '"', "'");
    $path_2 = str_replace($remove, '', $input[0]);

    

    // if the path starts with !, it needs to remain unchanged
    if (substr($path_2, 0, 1) == '!') {
        return 'url(' . substr($path_2, 1) . ')';
    }
    if (substr($path_2, 0, 2) == '//') {
        return 'url(' . $path_2 . ')';
    }
    if (substr($path_2, 0, 4) == 'http') {
        return 'url(' . $path_2 . ')';
    }
    if (substr($path_2, 0, 5) == 'data:') {
        return 'url(' . $path_2 . ')';
    }
    $rel_path = explode('/', $path_2);
    $count = count($rel_path);

    for ($i = 0; $i < $count; $i++) {
        //echo "Part: ", $rel_path[$i], "\n";
        switch (trim($rel_path[$i])) {
            case '..' :
                // move one up the base path
                array_pop($base_path);
                unset($rel_path[$i]);
                break;

            case '.':
                unset($rel_path[$i]);
                break;

            case '':
                unset($rel_path[$i]);
                break;
        }
    }

    array_values($base_path);
    array_values($rel_path);

    //print_r($base_path);
    //print_r($rel_path);
    // create the path
    if (count($base_path) > 0) {
        $newpath[] = (count($base_path) > 1) ? implode('/', $base_path) : array_shift($base_path);
    }
    if (count($rel_path) > 0) {
        $newpath[] = (count($rel_path) > 1) ? implode('/', $rel_path) : array_shift($rel_path);
    }
    array_values($newpath);

    $newpath = (count($newpath) > 1) ? implode('/', $newpath) : array_shift($newpath);

    $url = ($newpath[0] == '/') ? $newpath : '/' . $newpath;

    return 'url(' . $url . ')';
}

function compress($data, $level = 0, $type) {
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
        // print_r(headers_list() );
        return $data;
    }

    if (connection_status()) {
        return $data;
    }

    header("Content-Type: text/" . $type);
    header('Content-Length: ' . strlen($data));
    header('Content-Encoding: ' . $encoding, true);
    header('Vary: Accept-Encoding');
    header("cache-control: must-revalidate");
    $offset = 60 * 60;
    $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
    header($expire);
    return gzencode($data, (int) $level);
}
