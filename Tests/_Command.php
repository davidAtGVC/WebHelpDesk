<?php

defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

function WebHelpDesk_Qualifier( $attribute, $operation, $value )
{
	return "(" . $attribute . " " . $operation . " '".$value ."')";
}

function WebHelpDesk_Command( $resource = '', $params = array(), $qualifier = null, $configName = CONFIG_Tech_Key )
{
	if ( is_null($resource) || strlen($resource) == 0) {
		throw new \Exception('No request resource specified');
	}

	$config = testConfig( $configName );
	if ( is_array($config) && isset($config[CONFIG_base]) ) {
		$reqParams = array(
			"login" => $config[CONFIG_user],
			"password" => $config[CONFIG_password]
		);
		if ( is_array($params) ) {
			$reqParams = array_merge($params, $reqParams);
		}

		$q = '';
		if (is_string($qualifier)) {
			$q = "&" . Qualifier_Operation::OP . "=" . $qualifier;
		}

		$url = $config[CONFIG_base] . "/ra/" . $resource . "?" . http_build_query($reqParams) . $q;
		echo $url .PHP_EOL;

		list($data, $headers) = performGET( $url );
		if ( $data != false ) {
			$json = json_decode($data, true);
			if ( json_last_error() != 0 ) {
				throw new \Exception(jsonErrorString(json_last_error()));
			}

			return array($json, $headers);
		}
	}
	throw new \Exception('Please update the configuration for '.$configName);
}

function http_stringForCode($code = NULL) {
	if ($code !== NULL) {
		switch ($code) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default:
				$text = 'Unknown http status code "' . htmlentities($code) . '"';
				break;
		}
		return $text;
	}

	return 'Unknown http status code "' . htmlentities($code) . '"';
}

if (!function_exists('http_parse_headers'))
{
    function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }
}

function performGET($url)
{
	if (empty($url) == false) {
		if ( function_exists('curl_version') == true) {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_AUTOREFERER, true );
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );	# required for https urls
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30 );		# seconds to wait for connection
			curl_setopt($ch, CURLOPT_TIMEOUT, 180 );			# seconds to wait for completion
			curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

			try {
				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				// extract the headers
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$headers = http_parse_headers(substr($response, 0, $header_size));
				$data = substr($response, $header_size);

				if ( $http_code < 200 || $http_code >= 300 ) {
					throw new \Exception('Return code (' . $http_code . '): '
						. http_stringForCode($http_code) . " "
						. curl_error($ch)
					);
				}
			}
			finally {
				curl_close($ch);
			}
			return array( $data, $headers );
		}
	}
	return false;
}
