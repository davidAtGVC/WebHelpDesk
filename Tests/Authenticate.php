<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));

require SYSTEM_PATH .'Tests/_Base.php';
require SYSTEM_PATH .'Tests/_Enums.php';
require SYSTEM_PATH .'Tests/_Command.php';

//curl "https://localhost/helpdesk/WebObjects/Helpdesk.woa/ra/Tickets/1?username=admin&password=admin"

$config = testConfig( "Gevity" );
if ( is_array($config) && isset($config[CONFIG_base]) ) {
	$params = array(
		"list" => "mine",
		"username" => $config[CONFIG_user],
		"password" => $config[CONFIG_password]
	);
	$resource = "Tickets";
	$url = $config["base"] . "/ra/" . $resource . "/?" . http_build_query($params);
	echo $url .PHP_EOL;

	$result = performGET( $url );
	echo json_encode($result, JSON_PRETTY_PRINT) .PHP_EOL;
}
exit;

