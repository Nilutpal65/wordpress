<?php

/*
Plugin Name: Search Travel
Version: 0.1
Description: Demo plugin for search
Author: Nilutpal Borgohain
Author URI:
Plugin URI:
*/

if(!defined('ABSPATH') || !defined('WPINC'))
	exit();

if(!defined('TGSB_PACK'))
    define('TGSB_PACK', '.min');

/*	defining abs path to the given plugin directory, plugin dir name+plugin name, abs path to the plugin PHP file	*/
if(!defined('TG_SEARCHBOXES_ABSPATH'))
	define('TG_SEARCHBOXES_ABSPATH', plugin_dir_path( __FILE__ ));
if(!defined('TG_SEARCHBOXES_BASENAME'))
	define('TG_SEARCHBOXES_BASENAME', plugin_basename(__FILE__));
if(!defined('TG_SEARCHBOXES__FILE__'))
	define('TG_SEARCHBOXES__FILE__', __FILE__);
if(!defined('TGSB_VER')) {
    if (TGSB_PACK == '.dev') {
        define('TGSB_VER', rand(1000000,9999999));
    } else {
        define('TGSB_VER', '1.4.3');
    }
}

/**	Include file with the Widget Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgsbWidget.class.php' );
	
/*	checking if an admin page is being displayed	*/
if(is_admin()) {
	include_once(TG_SEARCHBOXES_ABSPATH.'controllers/controller-admin.php');
	global $Tg_Searchboxes_Admin;
	$Tg_Searchboxes_Admin	= new Tg_Searchboxes_Controller_Admin();
} else {
	include_once(TG_SEARCHBOXES_ABSPATH.'controllers/controller-frontend.php' );
	/*	mapping constructor call to init action	*/
	add_action('init', '_tg_searchboxes_controller_frontend_constructor' );
	
	/**
	* Call constructor on init hook
	*/
	function _tg_searchboxes_controller_frontend_constructor() {
		global $TG_Searchboxes_Frontend;
		$TG_Searchboxes_Frontend	= new Tg_Searchboxes_Controller_Frontend();
	}
	
}
// initializing the widget
add_action('widgets_init', 'tgsb_register_widget');

// registering the widget	
function tgsb_register_widget() {
	register_widget('tgsbWidget');
}
?>