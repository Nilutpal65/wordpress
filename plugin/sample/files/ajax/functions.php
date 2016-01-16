<?php
/**
 * PHP Proxy Script for AJAX requests to Travelgrove from the Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage AJAX Requests
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

/**	defining base path to Travelgrove Server	*/
if(!defined('TG_SERVER_BASEPATH'))
	define('TG_SERVER_BASEPATH', 'http://www.travelgrove.com/');
/**	defining rel path to Provider Logos	*/
if(!defined('TG_PROVIDER_IMAGES'))
	define('TG_PROVIDER_IMAGES', 'images/merchants_small/');
/**	defining rel path to Autosuggestion AJAX handler	*/
if(!defined('TG_ASAJAX_PATH'))
	define('TG_ASAJAX_PATH', 'ajax/');
/**	defining rel path to Provider AJAX handler	*/
if(!defined('TG_PROVIDER_PATH'))
	define('TG_PROVIDER_PATH', 'ajax/merchants/wpplugin/');

/**	sending the headers in the AJAX response	*/
function setHeaders($headers) {
	if(empty($headers))
		return false;
	foreach($headers as $header) {
		// WHAT & WHY: the Content-Length should not be set because it is changed in the file merchants.php the content is manipulated by the function merchantsJSONToHTML() which can generate a bigger Content-Length
		// WHO & WHEN: Cipri on the 24th of April 2012
		if (preg_match('/^Content-Length:/i',$header) || preg_match('/^Transfer-Encoding:/i',$header))
			continue;
		header($header);
	}
	return true;
}
/**	actual HTTP PHP proxy script	*/
function http_post($url, $data) {
	if(!$url)
		return false;
	if(!empty($data) && !empty($data['action'])) {
		// stripslashes should be used if on the action parameter is present an ' then it will be sent like this \' which is wrong so the slash should be striped
		if(get_magic_quotes_gpc())
			$data['action']	= stripslashes($data['action']);
	}
	/*	building up the query string	*/
	$data_url		= http_build_query($data);
	// print('dataURL: '.$data_url."<br>\n");
	$data_len		= strlen($data_url);
	/*	headers that should be pass to Travelgrove	*/
	$headersToBeSent	= array('Accept', 'Accept-Encoding', 'Accept-Language', 'Content-Length', 'Content-Type', 'User-Agent', 'Referer', 'Connection', 'X-Requested-With');
	$headersString		= '';
	/*	getting the headers that were sent for this request	*/
	$headers		= apache_request_headers();
	/*	building up the headers that should be passed to Travelgrove	*/
	foreach($headers as $headerKey => $headerValue) {
		if(!in_array($headerKey, $headersToBeSent))
			continue;
		if($headerKey == 'Connection') {
			$headersString .= "Connection: close\r\n";
			continue;
		}
		if($headerKey == 'Content-Length') {
			$headerValue = $data_len;
		}
		$headersString	.= $headerKey.': '.$headerValue."\r\n";
	}
	$headersString	= (empty($headersString)) ? "Connection: close\r\nContent-Length: $data_len\r\n" : $headersString;
	/**
		@note	use sockets were file_get_contents is not allowed;
		@date	2013-Apr-03;
		@author LZ/Travelgrove Tech;
	*/
	if(!ini_get('allow_url_fopen')) {
		return readRemoteFile($url, $headersString, $data_url);
	}
	/*	otherwise, if file_get_contents is enabled => use it	*/
	return array(	'content'	=> @file_get_contents (
					$url,
					false,
					stream_context_create(
						array(	'http'	=> array('method'	=> 'POST',
									'header'	=> $headersString,
									'content'	=> $data_url)
						)
            				)
				),
			/** $http_response_header reserved variable will contain the headers automatically	*/
            		'headers'	=> $http_response_header);
}

/**
	@note	access remote files via sockets; where file_get_contents is not allowed;
	@date	2013-Apr-03;
	@author	L.Z./Travelgrove Tech;
	@param	String	$url		URL to send the POST request to;
	@param	String	$headers	headers to send; separated by \r\n; already concatenated to string;
	@param	String	$data		POST data to send;
	@param	Int	$connectionTimeOut	connection time out, defaults to 30 sec.
	@param	Int	$readTimeout		read time out, defaults to 30 sec.
	@return	Array	of headers + data read;
*/
function readRemoteFile($url, $headers, $data, $connectionTimeOut = 30, $readTimeout = 30) {
	/*	basic input validation	*/
	if(!$url)
		return false;
	$url	= parse_url($url);
	$errno	= $errstr	= false;
	/*	opening the socket connection	*/
	$fp	= @fsockopen($url['host'], 80, $errno, $errstr, $connectionTimeOut);
	if(!$fp)
		return false;
	/*	add Content-Length if not present	*/
	if(strpos($headers, 'Content-Length') === false) {
		$headers	.= 'Content-Length: '.strlen($data)."\r\n";
	}
				/*	POST request of HTTP 1.1	*/
	$dataToSend	=	"POST ".$url['path']." HTTP/1.0\r\n".
				/*	to the given host	*/
				"Host: ".$url['host']."\r\n".
				/*	sending the headers from the parameter	*/
				$headers.
				/*	Content-Type header for POST parameters	*/
				"Content-Type: application/x-www-form-urlencoded\r\n".
				/*	empy line marking the end of the headers	*/
				"\r\n".
				/*	sending POST data + end of line	*/
				$data."\r\n".
				/*	empy line marking the end of the data/request	*/
				"\r\n";
	/*	sending the whole request	*/
	if(!@fwrite($fp, $dataToSend))
		return false;
	/*	setting stream timeout; no Error/Warning appears, only in meta data timed_out=>true	*/
	if(function_exists('socket_set_timeout')) {
		@socket_set_timeout($fp, $readTimeout, 0);
	} else if(function_exists('stream_set_timeout')) {
		@stream_set_timeout($fp, $readTimeout, 0);
	}
	$contentRead	= '';
	/*	a package read at one iteration, max 40K	*/
	$package	= true;
	/*	if not the end of the file	*/
	// while (!@feof($fp)) {
	while($package) {
		/*	read 40k from the content	*/
		$package	= @fread($fp, 409600);
		if (!$package)
			break;
		$contentRead	.= $package;
	}
	@fclose($fp);
	if(empty($contentRead))
		return false;
	/*	looking for the empty line separating the headers from the content	*/
	$emptyLinePos	= strpos($contentRead, "\r\n\r\n");
	if(!$emptyLinePos)
		return array('content' => $contentRead, 'headers' => null);
	/*	get the headers as a string	*/
	$headers	= substr($contentRead, 0, $emptyLinePos);
	/*	split headers by end-of-line	*/
	$headers	= explode("\r\n", $headers);
	/*	trim each header - just to be sure	*/
	for($i = 0, $n = count($headers); $i<$n; $i++)
		$headers[$i]	= trim($headers[$i]);
	/*	get the content read as a string	*/
	$contentRead	= substr($contentRead, $emptyLinePos+4);
	return array('content' => trim($contentRead), 'headers' => $headers);
}

/**	trasforming the response of JSON into JSON containing the merchants as HTML, to render the merchants	*/
function merchantsJSONToHTML($jsonData, $sbSize) {
	if(empty($jsonData))
		return (string)false;
	$responseArray	= @json_decode($jsonData, true);
	if(empty($responseArray) || empty($responseArray['merchants']))
		return (string)false;
	$out	= array();
	$output	= '';
	$i	= 1;
	foreach($responseArray['merchants'] as $merchant) {
		$output .=	'<span title="'.$merchant['name'].'" rel="'.$merchant['code'].'"'.
					($sbSize != '160x600' && $i%2 == 1 ? ' class="mrr"' : '').'>'.
					'<img width="86" height="21" alt="'.$merchant['url'].'" src="'.TG_SERVER_BASEPATH.TG_PROVIDER_IMAGES.$merchant['logo'].'" />'.
				'</span>';
		$i++;
	}
	$out['merchants']	= $output;
	$out['trackingPixel']	= $responseArray['trackingPixel'];
	return $out;
}


// WHAT & WHY: it seams that the function is not present on all php versions, but on php.net it says (PHP 4 >= 4.3.0, PHP 5) that's why we have to implement it here if it does not exist
// WHO & WHEN: Cipri on the 24th of April 2012
if( !function_exists('apache_request_headers') ) {
	function apache_request_headers() {
		$arh		= array();
		$rx_http	= '/\AHTTP_/';
		foreach($_SERVER as $key => $val) {
			if(preg_match($rx_http, $key)) {
				$arh_key	= preg_replace($rx_http, '', $key);
				$rx_matches	= array();
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches	= explode('_', $arh_key);
				if(count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
					foreach($rx_matches as $ak_key => $ak_val)
						$rx_matches[$ak_key]	= ucfirst(strtolower($ak_val));
						$arh_key	= implode('-', $rx_matches);
				}
				$arh[$arh_key]	= $val;
			}
		}
	return($arh);
	}
}

/**
	@note	basic debugging fn;
	@date	2013-Apr-03;
	@author	L.Z./Travelgrove Tech;
	@param	String|Mixed	$str	message to debug;
	@return	Boolean		true if msg was sent; false otherwise;
*/
function TG_debug($str) {
	if($_SERVER['REMOTE_ADDR']!='85.186.103.20')
		return false;
	print('<pre>');
	var_dump($str);
	print('</pre>');
	return true;
}

?>