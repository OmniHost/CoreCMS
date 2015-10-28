<?php

namespace Core;

class Remote {

    /**
     * Request a resource
     *
     * @param string $url 
     * @param array $params associative array of parameters to send
     * @param string $method GET/POST
     * @param array $http_auth assoc array with 'user' and 'password' values set if needed
     * @param array $options misc assoc array of options
     * @return array of headers, body as url resource data
     */
    static public function request($url, $params = array(), $method = 'GET', $http_auth = array(), $options = array()) {
        // create cURL instance
        $ch = curl_init();

        // if empty query string, don't bother
        $query_string = '';
        if (!empty($params)) {
            if (isset($options['raw']) && $options['raw'] == true) {
                $query_string = $params;
            } else {
                if (!is_array($params) && !empty($params)) {
                    $params = array($params);
                }
                $query_string = http_build_query($params);
                if (isset($options['decode']) && $options['decode'] == 'false') {
                    $query_string = rawurldecode($query_string);
                }
            }
        }

        //    echo $query_string;

        /* parse options */

        // timeout
        $timeout = 20; // default timeout
        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        // http header
        if (isset($options['header']) && !empty($options['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
        }

        /* end options parse */

        curl_setopt($ch, CURLOPT_USERAGENT, "Request/CoreCMS - (omnihost.co.nz)");
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // this is an insecure setting. =(
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // this one too
        // http auth stuff; assume user/password are keys
        if (is_array($http_auth) && isset($http_auth['user'])) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $http_auth['user'] . ':' . $http_auth['password']);
        }

        $method = strtoupper($method); // for ease of use

        switch ($method) {
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                break;
            case 'POST':

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                break;
            case 'GET':
            default:
                if (strlen($query_string) > 0) {
                    $query_string = '?' . $query_string;
                }
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_URL, $url . $query_string);
                break;
        }

        $result = curl_exec($ch);
        $data = curl_getinfo($ch);

        //error_log( print_r( $data, 1 ));
        //error_log( print_r( $result, 1));
        if (curl_errno($ch)) {
            error_log(curl_error($ch) . ": " . curl_errno($ch));
            error_log(print_r($data, 1));
            return false;
        } else {
            curl_close($ch);
        }

        // because CURLOPT_HEADER is true, we have to split up the headers and response body 
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        // process headers into a nice little array:
        $raw_headers = explode("\r\n", $header);
        $headers = self::parse_headers($header);

        // send beautiful response back
        $result = array(
            'info' => $data, // from cURL response, includes http codes
            'raw_headers' => $raw_headers,
            'headers' => $headers,
            'body' => $body,
        );



        return $result;
    }

    /**
     * Similar to pecl http's http_parse_headers
     *  but will NOT favor that if it pecl_htp extension is loaded
     *
     * @param string $headers 
     * @return array 
     */
    static public function parse_headers($headers) {
        /* extensions' handling of this is slightly different (eg: Set-Cookie) (it's actually more desirable behavior)
          if( function_exists( 'http_parse_headers' ) ) {
          return http_parse_headers( $headers );
          }
         */
        $retval = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {

                $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
                    return strtoupper($m[0]);
                }, strtolower(trim($match[1])));

                //       $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                //    \Vxd\Debug::dump($match[1]);
                //      exit;
                if (isset($retval[$match[1]])) {
                    $retval[$match[1]] = array($retval[$match[1]], $match[2]);
                } else {
                    $retval[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retval;
    }

    static public function ticketApi($post) {
        $config = \Vxd\Vxd::$config['osTicket'];



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Request/RankedByREview - (rankedbyreview.com)');
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'X-API-Key: ' . $config['key']));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // this is an insecure setting. =(
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // this one too

        $result = curl_exec($ch);
        $data = curl_getinfo($ch);
        curl_close($ch);

        // because CURLOPT_HEADER is true, we have to split up the headers and response body 
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        // process headers into a nice little array:
        $raw_headers = explode("\r\n", $header);
        $headers = self::parse_headers($header);

        // send beautiful response back
        $result = array(
            'info' => $data, // from cURL response, includes http codes
            'raw_headers' => $raw_headers,
            'headers' => $headers,
            'body' => $body,
        );




        return $result;
    }

}
