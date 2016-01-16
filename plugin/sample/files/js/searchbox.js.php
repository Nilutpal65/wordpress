<?php
/**	@note	Used to output the JavaScript code that will generate on the page a new searchbox
 *	@date	2013.04.23
 *	@author	Tibi	*/

$wpLoad	= dirname(__FILE__).'/../../../../wp-load.php';
if (!file_exists($wpLoad))
	exit('Directory structure doesn\'t follow WP standards!');
require_once($wpLoad);
?>