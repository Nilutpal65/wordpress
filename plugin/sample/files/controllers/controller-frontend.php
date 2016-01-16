<?php
/**
 * Front-end Controller for Travel-Search (Travelgrove Searchboxes)
 *
  * @package Travel-Search
 * @subpackage Frontend Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
/**	Include file with the Base Controller Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'controllers/controller-base.php' );
/**	Include SearchBox Renderer Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' );

/**	Frontend Controller class, extends Base Controller Class	*/
class Tg_Searchboxes_Controller_Frontend extends Tg_Searchboxes_Controller_Base {
/**	PHP4 class constructor, calls the PHP5 class constructor __construct()	*/
function Tg_Searchboxes_Controller_Frontend() {
	$this->__construct();
}

/**	PHP5 class constructor	*/
function __construct() {
	/**	call parent's constructor for reading the options from WP options table	*/
	parent::__construct();
//	add_action('wp_enqueue_scripts', array($this, 'jquery_ui'), 0);
	/**	adding to the dom the css files needed by the plugin in the frontend	*/
	$this->enqueue_tg_searchboxes_css();
	/**	adding to the dom the js files and variables needed by the plugin in the frontend	*/
	$this->enqueue_tg_searchboxes_js();
	/**	adding a hook for the shortcode "tg_searchboxes";
	* WP will automatically pass the found arguments: [shortcode argument1=value1 argument2=value2]	*/
	add_shortcode($this->shortcode_tg_searchboxes, array($this, 'tg_searchboxes_handle_tg_shortcode'));
	
	// checking if current request is to load the searchbox via external JS file
	$this->check_load_with_javascript();
	return;
}


/**	@note	function that checks if teh current request is not a request for loading a searchbox via JS | if it's one of these, it will output the searchbox in JS format and it breaks the script executions
		NOTE: all non-wordpress hooks that are registered to the shutdown hook won't be called to prevent any output error since this file serves javaScript requests
	@date	2013.04.22
	@author	Tibi
*/
function check_load_with_javascript(){
	//if request is not for loading searchbox via JS, returning
	if (!isset($_GET['tgsb_command']) || $_GET['tgsb_command']!='js_searchbox')
		return false;

	// doing an action to let other developers hook into our plugin
	do_action('tgsb_before_js_load');

	$req	= $_GET;

	$req['usejavascript']	= false;
	$placeholder	= $req['tgsbPlaceholder'];
	unset($req['tgsbPlaceholder']);
	// the tgsb_command command parameter should not be sent to the shortcode parser - any other parameter can be sent
	unset($req['tgsb_command']);
	// removing slashes from the parameters if needed
	if (get_magic_quotes_gpc())
		foreach($req as $k=>$v) {
			$req[$k]	= stripslashes($v);
		};
		
	/* the searchboxindex should be adjusted to the correct value*/
	$neededIndex	= preg_match('/[0-9]+/',$placeholder,$match) ? $match[0] : 1;
	$currentIndex	= 1;
	while($neededIndex>$currentIndex){
		$tgSbRenderer	= new tgSearchboxesRenderer(NULL, NULL);
		unset($tgSbRenderer);
		$currentIndex++;
	}
	
	/**	@note	If we get a subID via GET parameter, we have to use that subID | for the filter using priority of 20 - this should be enough to make sure no other filters were registered after this filter
		@date	2013-JUL-10
		@author	Tibi	*/
	if ($req['subID']) {
		$this->javascriptSubID	= $req['subID'];
		add_filter('tg_searchboxes_subID', array($this, 'set_javascript_subid'), 20);
	};
		
	// building up shortcode
	$params	= "";
	foreach($req as $k=>$v)
		$params	.= " ". $k ."='". preg_replace("/'/","\\'",$v) ."'";
	$shortcode	= '['. $this->tg_searchboxes_get_shortcode() .''. $params .']';
	$html	= do_shortcode($shortcode);
	$html	= preg_replace('/[\s\n\t\r]+/',' ',$html);
	$html	= str_replace("'","\\'",$html);
	//sending output as compressed w/ the correct headers
	@header('Content-Type: text/javascript',true);
	ob_start('ob_gzhandler');
	print "
	(function(){
		var html = '".$html."';
		if (typeof(TGSB)!='undefined')
			TGSB.replacePlaceholder('".$placeholder."', html);
		else {
			TGSB_placeholders	= typeof(TGSB_placeholders)=='array' ?
				TGSB_placeholders : new Array();
			TGSB_placeholders.push({'placeholder':'".$placeholder."', 'html':html});
		};
	})();";

	// unregistering any non-wordpress hook binded to `shutdown` event to prevent any output after we are done
	$this->unregister_shutdown_hooks();

	// doing an action to let other developers hook into our plugin
	do_action('tgsb_after_js_load');

	// exiting the script to prevent any other output
	exit();
}

/**	@note	used to set the subID got via GET parameter if searchbox is loaded via JS | binded to `tg_searchboxes_subID` filter
	@date	2013-JUL-10
	@author	Tibi	*/
public function set_javascript_subid($defSubId){
	return $this->javascriptSubID ? $this->javascriptSubID : $defSubId;
}

/**	@note	unregisters non-wordpress shutdown hooks to prevent outpt after the script is terminated
	@date	2013.04.22
	@author	Tibi
	@return Int the number of unregistered hooks	*/
function unregister_shutdown_hooks(){
	// used to detect any filters registered to the shutdown action
	global $wp_filter;
	// unregistering non-wordpress `shutdown` action hooks
	$unregistered	= 0;
	foreach($wp_filter['shutdown'] as $priority => $filters){
		foreach($filters as $filterName =>$filter){
			if (preg_match('/^wp\_/',$filterName))
				continue;
			remove_action('shutdown', $filterName, $priority);
			$unregistered++;
		}
	}
	return $unregistered;
}


/**	handle shortcodes via renderer class	*/
function tg_searchboxes_handle_tg_shortcode($attr) {
	/**	init the renderer w/ the params. found in the source, by WP fn.	*/
	$tgSearchboxesRenderer	= new tgSearchboxesRenderer($this, $attr);
	// return the generated searchbox
	return $tgSearchboxesRenderer->renderSearchboxes();
}


/**	enque the required CSS files	*/
function enqueue_tg_searchboxes_css() {
	/**	CSS of customized options	*/
	// added the filemtime because the values of the color are changing in the css file when a user decide to change them and so we want to do a cache refresh for that file
	// adding the timestamp that was saved when new colors where saved for the css color file
	wp_enqueue_style('tg_searchboxes_color_style', plugins_url('/css/tg_searchboxes_color.css', TG_SEARCHBOXES__FILE__), array(), $this->options['cssfiletimestamp']);
	/**	basic/main CSS rules for the boxes	*/
	wp_enqueue_style('tg_searchboxes_style', plugins_url('/css/tg_searchboxes' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array());
	/**	CSS rules for the datepicker calendars	*/
	wp_enqueue_style('tgsb_datepicker_style', plugins_url('/css/ui-lightness/datepicker' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__));
	return true;
}

/**	enque the required JS files	*/
function enqueue_tg_searchboxes_js() {

	/**	AutoSuggestion drop-down for aiports+cities; (name, path, dependencies) - dependent on jQuery	*/
	wp_enqueue_script('tgsb_autosuggestion', plugins_url('/js/autosuggestion' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array('jquery', 'jquery-core', 'jquery-migrate'));
	/**	jQuery DatePicker; dependent on jQuery and jQuery UI	*/
	wp_enqueue_script('tgsb_datepicker_script', plugins_url('/js/jquery-ui-datepicker' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array('jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core'));

	/**	@note	JS file holding the class that handles popups
		@date	2013-JUN-4
		@author	Tibi	*/
	wp_enqueue_script('tgsb_popup_handler_script',
			plugins_url( '/js/popupHandler' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__ ),
			false,
			// version number
			'',
			// adding it to footer to make sure it will appear AFTER inline variables are set
			empty($this->options['noconflict']) ? true : false );

	/**	dynamic functionalities of the searchboxes; main JS file	*/
	wp_enqueue_script('tgsb_main_script',
			plugins_url( '/js/tg_searchboxes' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__ ),
			array('tgsb_datepicker_script', 'tgsb_autosuggestion', 'jquery', 'jquery-core', 'jquery-migrate', 'tgsb_popup_handler_script'),
			// version number
			'',
			// adding it to footer to make sure it will appear AFTER inline variables are set
			(empty($this->options['noconflict']) ? true : false));
			
	/**	context-dependent variables needed for dynamic functionality, in the main JS	*/
	wp_localize_script(
			// the name of the script where are this variables attached to
			'tgsb_main_script',
			// js object name that will contain the variables
			'TG_Searchboxes_Variables',
			array(	'plugin_url'		=> plugins_url('', TG_SEARCHBOXES__FILE__),
				'str_CalendarURL'	=> plugins_url('/images/tg_searchboxes/calendarnew.png', TG_SEARCHBOXES__FILE__),
				'str_ASAjaxURL'		=> plugins_url('/ajax/autosuggestion.php', TG_SEARCHBOXES__FILE__),
				/*	converting PHP-format date to JS-format	*/
				'str_dateFormat'	=> $this->options['date_format'] == 'd/m/Y' ? 'dd/mm/yy' : 'mm/dd/yy',
				'str_merchantsAjaxURL'	=> plugins_url('/ajax/merchants.php', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderCircle'	=> plugins_url('/images/tg_searchboxes/ajax-loader-circle.gif', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderBert'	=> plugins_url('/images/tg_searchboxes/ajax-loader-bert.gif', TG_SEARCHBOXES__FILE__))
	);
	return true;
}

}
?>