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

$ curl "https://localhost/helpdesk/WebObjects/Helpdesk.woa\
> /ra/clientInterface/Tickets?page=1&limit=3\
> &apiKey=v32lXMFAi7dl3zGrtETArXqKVF8svfAfXZpIwC0P"

*/

// $options = getopt( "f:c:e:");
// echo "Options " . var_export($options, true) . PHP_EOL. PHP_EOL;

$qualifier = WebHelpDesk_Qualifier( Ticket_Fields::subject, Qualifier_Operation::Like, "Internal name*");
list($json, $headers) = WebHelpDesk_Command( 'clientInterface/Tickets', null, $qualifier, CONFIG_Client_Key );

echo json_encode($json, JSON_PRETTY_PRINT) .PHP_EOL;

