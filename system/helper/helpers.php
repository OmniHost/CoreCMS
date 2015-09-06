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
    $ed .= '<script>docReady(function() { CKEDITOR.replace(\'' . $id . '\', { ' . "\n"
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
}

function add_shortcode($tag, $func) {
    \Core\Shortcode::addShortcode($tag, $func);
}

function __modification($filename) {

    $file = DIR_MODIFICATION . ltrim(substr($filename, strlen(DIR_ROOT)), "/");


    if (file_exists($file)) {
        return $file;
    } else {
        return $filename;
    }
}

function formfield($field) {
    $class = 'form-control';
    if ($field['required']) {
        $class .= ' required';
    }

    if (!empty($field['class'])) {
        $class .= ' ' . $field['class'];
    }

    if (empty($field['id'])) {
        $field['id'] = slug($field['key']) . 'Input';
    }

    switch ($field['type']):

        case "html":
            return wysiwyg($field['id'], $field['key'], $field['value']);
            break;
        case 'textarea':
            $rows = (!empty($field['rows'])) ? $field['rows'] : 5;
            return '<textarea id="' . $field['id'] . '"  name="' . $field['key'] . '" class="' . $class . '" rows="' . $rows . '">' . $field['value'] . '</textarea>';
            break;

        case 'autocomplete_list':
            $html = '<input type="autocomplete" data-key="' . $field['key'] . '" data-target="' . $field['id'] . '" data-url="' . $field['url'] . '" value="" placeholder="' . $field['label'] . '" id="input-' . $field['id'] . '" class="form-control" />';
            $html .= '<div id="' . $field['id'] . '" class="well well-sm autocomplete-list" style="height: 150px; overflow: auto;">';
            //$html .= print_r($field['value'], 1);
            foreach($field['value'] as $download){
                $html .= '<div class="list-group-item" id="' . $field['id'] . '-' . $download['download_id'] . '"><i class="fa fa-minus-circle text-danger"></i> ' . $download['name'] . '<input type="hidden" name="' . $field['key'].'[]" value="' . $download['id'] . '"></div>';
            }

            /*
             * <div id="downloadsInput1"><i class="fa fa-minus-circle text-danger"></i> TenXIcon<input type="hidden" name="downloads[]" value="1"></div>
             */
            
            $html .= '</div>';

            return $html;
            break;

        case 'scrollbox':

            $return = '<div class="scrollbox">
                <ul class="list-group">';
            foreach ($field['options'] as $option) {
                $return .= '<li class="list-group-item">';
                if (in_array($option['ams_page_id'], $field['value'])) {
                    $return .= '<input type="checkbox" name="' . $field['key'] . '[]" value="' . $option['ams_page_id'] . '" checked="checked">' . $option['name'];
                } else {
                    $return .= '<input type="checkbox" name="' . $field['key'] . '[]" value="' . $option['ams_page_id'] . '">' . $option['name'];
                }
                $return .= '</li>';
            }
            $return .= '</ul></div>';
            return $return;
            /* <li class="list-group-item even">
              <input type="checkbox" name="allowed_groups[]" value="1">

              Default                                                </li>
              <li class="list-group-item odd">


              Everybody                                                </li>
              <li class="list-group-item even">
              <input type="checkbox" name="allowed_groups[]" value="-2">

              Guest                                                </li>
              <li class="list-group-item odd">
              <input type="checkbox" name="allowed_groups[]" value="-3">

              Users                                                </li>
              </ul>
              </div> */
            break;

        case "image":
            return '<a href="" id="thumb-' . slug($field['key']) . '" data-toggle="image" class="img-thumbnail">'
                    . '<img src="' . $field['thumb'] . '" alt="" title="" data-placeholder="' . $field['placeholder'] . '" /></a>'
                    . '<input type="hidden" name="' . $field['key'] . '" value="' . $field['value'] . '" id="input-' . slug($field['key']) . '" />';

            break;
        case "datetime":
            registry('document')->addScript('view/plugins/datetimepicker/moment.min.js');
            \Core\Registry::getInstance()->get('document')->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
            \Core\Registry::getInstance()->get('document')->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');
            $dateformat = dateformat_PHP_to_MomentJs(\Core\Registry::getInstance()->get('language')->get('date_time_format_short'));
            $id = !empty($field['id']) ? $field['id'] : slug('input-' . $field['key']);

            return '<input id="' . $id . '" data-date-format="' . $dateformat . '" type="text" name="' . $field['key'] . '" class="' . $class . ' datetimeinput" value="' . $field['value'] . '" />'
                    . ''
                    . '<script>docReady(function () {'
                    . '$(\'#' . $id . '\').datetimepicker({format: "' . $dateformat . '"});});'
                    . '</script>';
            break;
        case "text":
        default:
            return '<input type="' . $field['type'] . '" name="' . $field['key'] . '" class="' . $class . '" value="' . $field['value'] . '" />';
    endswitch;
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
