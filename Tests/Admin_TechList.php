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

/*

$ curl "https://localhost/helpdesk/WebObjects/Helpdesk.woa/ra/Techs?\
limit=3\
&apiKey=v32lXMFAi7dl3zGrtETArXqKVF8svfAfXZpIwC0P"

*/

$techs = WebHelpDesk_Command( 'Techs' );
echo json_encode($techs, JSON_PRETTY_PRINT) .PHP_EOL;

