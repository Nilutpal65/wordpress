<?php
/*
Plugin Name: WordPress2WORDPRESS
Plugin URI: 
Description: Capitalise all instances of "WordPress" to "WORDPRESS" 
Author: Nilutpal
Author URI:
Version: 0.1
License: LGPLv2
*/
add_filter('the_content' , array('WordPress2WORDPRESS' ,  'the_content'   )) ;
class WordPress2WORDPRESS{

	function the_content($content){
		return preg_replace('/WordPress/' , 'WORDPRESS' , $content  ) ;
	}
}
