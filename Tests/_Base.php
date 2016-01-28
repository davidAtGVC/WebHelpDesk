<?php

defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

define('CONFIG', "HelpDeskConfig.json");

define('CONFIG_name', "name");
define('CONFIG_base', "base");
define('CONFIG_user', "user");
define('CONFIG_password', "password");

function join_path()
{
    $finalPath = '';
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }
    $paths = array_filter( $paths, 'strlen' );

    $path_count = count($paths);
    for ( $idx = 0; $idx < $path_count; $idx++) {
		$item = $paths[$idx];
        if ($idx != 0 && $item[0] == DIRECTORY_SEPARATOR)  {
        	$item = substr($item, 1);
        }
        if ($idx != ($path_count - 1) && substr($item, -1) == DIRECTORY_SEPARATOR) {
        	$item = substr($item, 0, -1);
        }
		$finalPath .= $item;
        if ($idx != ($path_count - 1)) {
        	$finalPath .= DIRECTORY_SEPARATOR;
		}
	}
	$finalPath = str_replace("//", "/", $finalPath);
    return $finalPath;
}

function test_path($name = null)
{
	is_null($name) == false || die( "no test file specified");
	$path = join_path( SYSTEM_PATH, "tests", $name );
	return $path;
}

function test_isFile($name = null)
{
	$path = test_path( $name );
	return is_file($path);
}

function jsonErrorString($code)
{
	$constants = get_defined_constants(true);
	$json_errors = array();
	foreach ($constants["json"] as $name => $value) {
		if (!strncmp($name, "JSON_ERROR_", 11)) {
			$json_errors[$value] = $name;
		}
	}
	return $json_errors[$code];
}

function testConfig( $name = '' )
{
	$filename = test_path( CONFIG );
	if ( test_isFile( CONFIG ) == false ) {
		$config = array(
			"Config 1" => array(
				CONFIG_name => "My Web Helpdesk",
				CONFIG_base => "https://localhost/helpdesk/WebObjects/Helpdesk.woa",
				CONFIG_user => "username",
				CONFIG_password => "password"
			)
		);
		$returnValue = file_put_contents( $filename, json_encode($config, JSON_PRETTY_PRINT));
		if ( json_last_error() != 0 ) {
			throw new \Exception( jsonErrorString(json_last_error()) );
		}
		echo "Created new configuration at $filename, please update with your parameters";
		exit(1);
	}

	$config = json_decode(file_get_contents($filename), true);
	if ( json_last_error() != 0 ) {
		throw new \Exception( jsonErrorString(json_last_error()) );
	}

	if ( is_array($config) && isset($config[$name]) ) {
		return $config[$name];
	}
	return null;
}
