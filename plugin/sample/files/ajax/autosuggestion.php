<?php
/**
 * PHP Proxy Script for AJAX requests to Travelgrove from the Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage AJAX Requests
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
/*	handling invalid input	*/
if(empty($_POST['citytype']) || !in_array($_POST['citytype'], array('airports', 'cities')))
	exit();
/*	PHP proxy functions	*/
require_once 'functions.php';
$baseUrl	= TG_SERVER_BASEPATH.TG_ASAJAX_PATH;
$currentUrl	= $_POST['citytype'] == 'airports' ? 'airport_new.php' : 'hotel_suggest.php';
$_POST['ref']	= 'TG_HTTP_PROXY';
$response	= http_post($baseUrl.$currentUrl, $_POST);
setHeaders($response['headers']);
exit($response['content']);
?>