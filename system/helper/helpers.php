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
/**
 * @todo - repackage to better places
 */
/**
 * Autoloads class
 * @param string $className
 */

/**
 * Splits a string by capitals (usefull for camelcase seperation)
 * @param string $string
 * @param boolean $ucfirst - return first letters upper (true) or all lower (false)
 * @param string|boolean $glue use this character to group the items together
 * @return string
 */
function splitByCaps($string, $ucfirst = true, $glue = false) {

    $pattern = "/(.)([A-Z])/";
    $replacement = "\\1 \\2";
    $return = ($ucfirst) ?
            ucfirst(preg_replace($pattern, $replacement, $string)) :
            strtolower(preg_replace($pattern, $replacement, $string));

    return ($glue) ? str_replace(' ', $glue, $return) : $return;
}

function unslug($text) {
    return ucfirst(str_replace('_', ' ', $text));
}

function textAutoLink($str, $attributes = array(), $protocols = array('http', 'https', 'mail')) {
    /* $attrs = '';
      foreach ($attributes as $attribute => $value) {
      $attrs .= " {$attribute}=\"{$value}\"";
      }

      $str = ' ' . $str;
      $str = preg_replace(
      '`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a href="$2"' . $attrs . '>$2</a>', $str
      );
      $str = substr($str, 1);

      return $str; */
    return \Vxd\String::linkify($str, $protocols, $attributes);
}

/**
 * print_r wrapper with pre tags pre added
 * @param mixed $val
 */
function debugPre($val, $return = false) {
    if ($return) {
        return '<pre>' . print_r($val, 1) . '</pre>';
    }
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

/**
 * var_dump wrapper with pre tags pre added
 * @param mixed $val
 */
function debugDump($val) {
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
}

function returnBetween($start, $end, $string, $cut = false, $case_insensitive = true) {
    $start = ($case_insensitive) ? strtolower($start) : $start;
    $end = ($case_insensitive) ? strtolower($end) : $end;
    $len = strlen($start);
    $scheck = ($case_insensitive) ? strtolower($string) : $string;

    if ($len > 0) {
        $pos1 = strpos($scheck, $start);
    } else {
        $pos1 = 0;
    }

    if ($pos1 !== false) {
        if ($end == '') {
            return substr($string, $pos1 + $len);
        }
        $pos2 = strpos(substr($scheck, $pos1 + $len), $end);
        if ($pos2 !== false) {
            return substr($string, $pos1 + $len, $pos2);
        }
    }
    return '';
}

/**
 * Get the current users ipaddress
 * @return string ipaddress
 */
function get_client_ip() {
    $r = Core\Registry::getInstance()->request;

    $ipaddress = '';
    if (isset($r->server['HTTP_CLIENT_IP']))
        $ipaddress = $r->server['HTTP_CLIENT_IP'];
    else if (isset($r->server['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $r->server['HTTP_X_FORWARDED_FOR'];
    else if (isset($r->server['HTTP_X_FORWARDED']))
        $ipaddress = $r->server['HTTP_X_FORWARDED'];
    else if (isset($r->server['HTTP_FORWARDED_FOR']))
        $ipaddress = $r->server['HTTP_FORWARDED_FOR'];
    else if (isset($r->server['HTTP_FORWARDED']))
        $ipaddress = $r->server['HTTP_FORWARDED'];
    else if (isset($r->server['REMOTE_ADDR']))
        $ipaddress = $r->server['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

function generateRandomString($length = 1, $extrachars = '') {
    $characters = 'abcdefghijklmnopqrstuvwxyz' . $extrachars;
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function array_deap_merge($arr1, $arr2) {
    foreach ($arr2 as $k => $v) {
        if (array_key_exists($k, $arr1) && is_array($v)) {
            $arr1[$k] = array_deap_merge($arr1[$k], $arr2[$k]);
        } else {
            $arr1[$k] = $v;
        }
    }
    return $arr1;
}

function genRbrPassword($clearText) {
    $encrypted = hash('sha256', $clearText);
    $hasher = \Core\Registry::getInstance()->get('config')->get('config_encryption');
    $hash = hash('CRC32', sha1($encrypted . $hasher));
    return $encrypted . ':' . $hash;
}

function guessTextColor($hex) {
// returns brightness value from 0 to 255
// strip off any leading #
    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

function fixajaxurl($url) {
    return str_replace("&amp;", '&', $url);
}

function shrinkUrl($url) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://to.ly/api.php?longurl=" . urlencode($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $shorturl = curl_exec($ch);
    curl_close($ch);
    if (strpos($shorturl, "Invalid URL") !== false || strpos(stripslashes($shorturl), 'http://to.ly') === false) {
        $return = $url;
    } else {
        $return = $shorturl;
    }

    return $return;
}

function nicetime($unix_date, $format = "Y-m-d h:i a", $bad_date = 'Bad Date', $past = false) {

    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

    $now = time();
    //$unix_date = strtotime($date);
    // check validity of date
    if (empty($unix_date)) {
        return $bad_date;
    }

    if ($unix_date < ($now - (60 * 60 * 24))) {
        return Date($format, $unix_date);
    }

    // is it future date or past date
    if ($now > $unix_date) {
        $difference = $now - $unix_date;
        $tense = "ago";
    } else {
        $difference = $unix_date - $now;
        $tense = "from now";
    }

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if ($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";
}

function filenameslug($input, $whitespace = '-') {
    $input = str_replace(array("&amp;", ' '), "", $input);
    $string = strtolower(strip_tags($input));
    $string = preg_replace("/[^a-z0-9\/\s-.]/", " ", $string);
    $slug = preg_replace("/[\s-]+/", " ", $string);
    $slug = strtolower(str_replace(' ', $whitespace, $slug));
    return $slug;
}

function slug($input, $whitespace = '-') {
    $input = str_replace(array("&amp;", '&'), "", $input);
    $string = strtolower(strip_tags($input));
    $string = preg_replace("/[^a-z0-9\s-]/", "", $string);
    $slug = preg_replace("/[\s-]+/", " ", $string);
    $slug = strtolower(str_replace(' ', $whitespace, $slug));
    return $slug;
}

/**
 * 
 * @param type $id
 * @param type $name
 * @param type $value
 * @param type $class
 * @param type $rows
 */
function wysiwyg($id, $name, $value = '', $class = "form-control", $rows = 20) {
    init_wysiwyg();
    $ed = '<textarea id="' . $id . '" name="' . $name . '" rows="' . $rows . '" class="' . $class . '">';
    $ed .= $value;
    $ed .= '</textarea>';
    $ed .= '<script>docReady(function() { CKEDITOR.replace(\'' . $id . '\', { '
            . 'baseHref: \'' . registry('config')->get('config_catalog') . "'\n"
            . '});});</script>';
    return $ed;
}

function init_wysiwyg() {
    if (isset(\Core\Registry::getInstance()->get('request')->server['HTTPS']) && ((\Core\Registry::getInstance()->get('request')->server['HTTPS'] == 'on') || (\Core\Registry::getInstance()->get('request')->server['HTTPS'] == '1'))) {
        $uri = HTTPS_CATALOG;
    } else {
        $uri = HTTP_CATALOG;
    }

    \Core\Registry::getInstance()->get('document')->addScript('view/plugins/ckeditor/ckeditor.js?');
    \Core\Registry::getInstance()->get('document')->addScript($uri . '?p=cms/page/ajaxlist');
    \Core\Registry::getInstance()->get('document')->addScript($uri . '?p=cms/page/downloadlist');
}

function __($str) {
    echo \Core\Registry::getInstance()->get('language')->get($str);
}

function __modification($filename) {

    $file = DIR_MODIFICATION . ltrim(substr($filename, strlen(DIR_ROOT)), "/");


    if (file_exists($file)) {
        return $file;
    } else {
        return $filename;
    }
}

function placeFormField($formfield) {
    registry('formbuilder')->placeFormField($formfield);
}

function formfield($field) {

    return registry('formbuilder')->render($field);
}

function render_select($arr, $selected = 0, $level = 0) {
    $html = '';
    foreach ($arr as $opt) {
        // $html .= print_r($opt, 1);
        $iselected = ($opt['id'] == $selected) ? 'selected' : '';
        $html .= '<option value="' . $opt['id'] . '" ' . $iselected . '>' . str_repeat('-', $level) . ' ' . $opt['name'] . '</option>';
        if (isset($opt['children']) && count($opt['children'])) {
            $html .= render_select($opt['children'], $selected, $level + 1);
        }
    }
    return $html;
}

function registry($key = false) {
    if ($key) {
        return \Core\Registry::getInstance()->get($key);
    }
    return \Core\Registry::getInstance();
}

function request() {
    return registry('request');
}

function formatDate($date, $inputFormat, $outputFormat = "Y-m-d") {
    if (empty($date)) {
        return FALSE;
    }
    $limiters = array('.', '-', '/');
    foreach ($limiters as $limiter) {
        if (strpos($inputFormat, $limiter) !== false) {
            $_date = explode($limiter, $date);
            $_iFormat = explode($limiter, $inputFormat);
            $_iFormat = array_flip($_iFormat);
            break;
        }
    }
    if (!isset($_iFormat) || !isset($_date) || count($_date) !== 3) {
        return FALSE;
    }
    return date($outputFormat, mktime(0, 0, 0, $_date[isset($_iFormat['m']) ? $_iFormat['m'] : $_iFormat['n']], $_date[isset($_iFormat['d']) ? $_iFormat['d'] : $_iFormat['j']], $_date[$_iFormat['Y']]));
}

function dateformat_PHP_to_MomentJs($php_format) {
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => '', //No Translation
        'w' => 'e',
        'z' => 'DDDD',
        // Week
        'W' => 'w',
        // Month
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', //No Translation
        // Year
        'L' => '', //No Translation
        'o' => 'gggg',
        'Y' => 'YYYY',
        'y' => 'YY',
        // Time
        'a' => 'a',
        'A' => 'A',
        'B' => '', //No Translation
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => '', //No Translation
        //Timezone
        'e' => '',
        'I' => '',
        'O' => 'ZZ',
        'P' => 'Z',
        'T' => '',
        'Z' => '',
        'c' => 'YYYY-MM-DD\THH:i:sZ', //2004-02-12T15:19:21+00:00
        'r' => 'ddd, D MMM HH:i:s Z', //Thu, 21 Dec 2000 16:01:07 +0200
        'U' => 'X'
    );
    $jqueryui_format = "";
    $escaping = false;
    for ($i = 0; $i < strlen($php_format); $i++) {
        $char = $php_format[$i];
        if ($char === '\\') { // PHP date format escaping character
            $i++;
            if ($escaping)
                $jqueryui_format .= $php_format[$i];
            else
                $jqueryui_format .= '\'' . $php_format[$i];
            $escaping = true;
        }
        else {
            if ($escaping) {
                $jqueryui_format .= "'";
                $escaping = false;
            }
            if (isset($SYMBOLS_MATCHING[$char]))
                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
            else
                $jqueryui_format .= $char;
        }
    }
    return $jqueryui_format;
}

function sort_this_array(&$base_array, $column, $sort_order = SORT_DESC) {

    $sort_column = array();
    foreach ($base_array as $key => $row) {
        $sort_column[$key] = $row[$column];
    }

    array_multisort($sort_column, $sort_order, $base_array);
}

function sort_enabled_installed(&$base_array, $statuscol = 'status', $installed = 'installed', $sort_order_status = SORT_DESC, $sort_order_installed = SORT_DESC) {

    $sort_column = array();
    $status = $install = array();
    foreach ($base_array as $key => $row) {
        $status[$key] = $row[$statuscol];
        $install[$key] = $row[$installed];
    }

    array_multisort($status, $sort_order_status, $install, $sort_order_installed, $base_array);
}
