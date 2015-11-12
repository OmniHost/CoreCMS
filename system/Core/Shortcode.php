<?php

namespace Core;

class Shortcode {

    public static $shortcode_tags = array();

    public static function init($ns) {
        self::_registerShortcodes(DIR_PLUGINS);
    }

   

    protected static function _registerShortcodes($path) {
        $files = GLOB($path . '*/shortcode.php');
        foreach($files as $file){
            include_once(__modification($file));
        }
    }

    public static function addShortcode($tag, $func) {
        if (is_callable($func))
            self::$shortcode_tags[$tag] = $func;
    }

    public static function removeShortcode($tag) {
        unset(self::$shortcode_tags[$tag]);
    }

    public static function removeAllShortcodes() {
        self::$shortcode_tags = array();
    }

    public static function shortcodeExists($tag) {
        return array_key_exists($tag, self::$shortcode_tags);
    }

    public static function hasShortcode($content, $tag) {
        if (false === strpos($content, '[')) {
            return false;
        }

        if (self::shortcodeExists($tag)) {
            preg_match_all('/' . self::getShortcodeRegex() . '/s', $content, $matches, PREG_SET_ORDER);
            if (empty($matches))
                return false;

        
            
            foreach ($matches as $shortcode) {
                if ($tag === $shortcode[2]) {
                    return true;
                } elseif (!empty($shortcode[5]) && self::hasShortcode($shortcode[5], $tag)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function doShortcode($content) {
        if (false === strpos($content, '[')) {
            return $content;
        }
        

        if (empty(self::$shortcode_tags) || !is_array(self::$shortcode_tags))
            return $content;

        $pattern = self::getShortcodeRegex();
        while(preg_match("/$pattern/s", $content)){
            $content = preg_replace_callback("/$pattern/sS", 'self::doShortcodeTag', $content);
        }
       // exit;
        
        
   /*    while(false !== strpos($content, '[')) {
           $content = preg_replace_callback("/$pattern/s", 'self::doShortcodeTag', $content);
           
        }*/
        return ($content);
    }

    public static function getShortcodeRegex() {
        $tagnames = array_keys(self::$shortcode_tags);
        $tagregexp = join('|', array_map('preg_quote', $tagnames));

        // WARNING! Do not change this regex without changing self::doShortcodeTag() and self::stripShortcodeTag()
        // Also, see shortcode_unautop() and shortcode.js.
        return
                '\\['                              // Opening bracket
                . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                . "($tagregexp)"                     // 2: Shortcode name
                . '(?![\\w-])'                       // Not followed by word character or hyphen
                . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                . '(?:'
                . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                . '[^\\]\\/]*'               // Not a closing bracket or forward slash
                . ')*?'
                . ')'
                . '(?:'
                . '(\\/)'                        // 4: Self closing tag ...
                . '\\]'                          // ... and closing bracket
                . '|'
                . '\\]'                          // Closing bracket
                . '(?:'
                . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                . '[^\\[]*+'             // Not an opening bracket
                . '(?:'
                . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                . '[^\\[]*+'         // Not an opening bracket
                . ')*+'
                . ')'
                . '\\[\\/\\2\\]'             // Closing shortcode tag
                . ')?'
                . ')'
                . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    public static function doShortcodeTag($m) {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = self::shortcodeParseAtts(html_entity_decode($m[3], ENT_QUOTES, 'UTF-8'));
 
        
        
        if (isset($m[5])) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func(self::$shortcode_tags[$tag], $attr, $m[5], $tag) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func(self::$shortcode_tags[$tag], $attr, null, $tag) . $m[6];
        }
    }

    public static function shortcodeParseAtts($text) {
        $atts = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) and strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }
        } else {
            $atts = ltrim($text);
        }
        
        return $atts;
    }

    public static function shortcodeAtts($pairs, $atts) {
        $atts = (array) $atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts))
                $out[$name] = ltrim(rtrim($atts[$name],"'"),"'");
            else
                $out[$name] = ltrim(rtrim($default,"'"),"'");
        }
        return $out;
    }

    public static function stripShortcodes($content) {
        if (false === strpos($content, '[')) {
            return $content;
        }

        if (empty(self::$shortcode_tags) || !is_array(self::$shortcode_tags))
            return $content;

        $pattern = self::getShortcodeRegex();

        return preg_replace_callback("/$pattern/s", 'self::stripShortcodeTag', $content);
    }

    public static function stripShortcodeTag($m) {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }

}
