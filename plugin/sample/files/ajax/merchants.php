<?php
/**
 * PHP Proxy Script for PROVIDERS requests to Travelgrove from the Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage AJAX Requests
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
/**	defining base path to Travelgrove Server	*/
if(!defined('TG_SERVER_BASEPATH'))
	define('TG_SERVER_BASEPATH', 'http://www.travelgrove.com/');

/*	deserving scripts for different type of providers, hosted on Travelgrove	*/
$serverScripts	= array(
    'flights'	=> 'air.json.php',
	'hotels'	=> 'hotel.json.php',
	'cars'		=> 'car.json.php',
    'packages'	=> 'vacation.json.php',
    'cruises'	=> 'cruise.json.php',
);
/*	handling invalid input; only 'flights', 'hotels', 'cars' or 'packages' are accepted	*/
if(empty($_POST['merchants']) || !isset($serverScripts[$_POST['merchants']]))
	exit();
/*	PHP proxy functions	*/
require_once 'functions.php';
$baseUrl	= TG_SERVER_BASEPATH . TG_PROVIDER_PATH;
$url		= $baseUrl.$serverScripts[$_POST['merchants']];
$params			= $_POST;
$params['searchsystem']	= 'us';
$params['language']	= 'def';
$params['lang']		= 'def';
/**	thus we can identify the source of the traffic	*/
$params['ref']		= 'TG_HTTP_PROXY';
$response		= http_post($url, $params);

setHeaders($response['headers']);
/*	transforming JSON object into a JSON object containing the actual HTML of the providers	*/
$output			= merchantsJSONToHTML($response['content'], $params['sbsize']);
exit(json_encode($output));
