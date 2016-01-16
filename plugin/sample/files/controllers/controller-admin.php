<?php
/**
 * Admin Controller for Travel Search with functions for the backend
 *
 * @package Travel-Search
 * @subpackage Admin Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

/**	Include file with the Base Controller Class	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'controllers/controller-base.php' );
/**	Class for generating the admin settings form	*/
require_once ( TG_SEARCHBOXES_ABSPATH.'classes/searchboxesSettingsFormRenderer.class.php' );

/**
 * Admin Controller class, extends Base Controller Class
 */
class Tg_Searchboxes_Controller_Admin extends Tg_Searchboxes_Controller_Base {
	/**
		* Slug that will be appended to the URL of the plugin by WordPress, e.g. http://example.com/wp-admin/tools.php?page=tg_searchboxes
		* @var string
	*/
	private $page_slug	= 'tg_searchboxes';
	/**
	 *	* this variable will contain the searchboxesSettingsFormRenderer object.
		* the class earchboxesSettingsFormRenderer is initialized in the constructor and it's also used in the tg_searchboxes_load_view method
	*/
	private $searchboxesSettingsFormRenderer;

	/**	Searchboxs default values, needed at installation only; later on defaults will be read from DB options	*/
 	private $default_options	= array(
		'from_air'		=> '',
		'to_air'		=> '',
		'departure_date'	=> '5 days',
		'return_date'		=> '+5 days',
		'adults'		=> 1,
		'kids'			=> 0,
		'seniors'		=> 0,
		'rooms'			=> 1,
		'rtow'			=> false,
		'date_format'		=> 'm/d/Y',
		'id_referral'		=> 999,
		'brdcolor'		=> '#b7c791',
		'txtcolor'		=> '#333333',
		'bgdcolor'		=> '#f2fed4',
		'tbscolor'		=> '#f9ffed',
		'tbstxtcolor'		=> '#53667b',
		'tbsbrdcolor'		=> '#d9d9d9',
		'links'			=> true,
		'cssfiletimestamp'	=> '0',
		'noconflict'		=> false,
		// marks if the loading method of the searchbox should be PHP or JavaScript | Tibi | 2013.04.22
		'usejavascript'		=> false
		);

	/**
	* Nonce for security of links/forms, to prevent "CSRF" (Cross-site request forgery); used for AJAX requests
	* @var string
	*/
	var $nonce_base		= 'tg_searchboxes-nonce';

	/**	PHP4 class constructor, calls the PHP5 class constructor __construct()	*/
	function Tg_Searchboxes_Controller_Admin() {
		$this->__construct();
	}
	
	/**	PHP5 class constructor	 */
	function __construct() {
	   	//	activation hook used to install the plugin - saving the default options into WP options table
		register_activation_hook( TG_SEARCHBOXES__FILE__, array( $this, 'tg_searchboxes_install' ) );
		//	deac.hook - deleting the options for TG-searchboxes from WP options table
		register_deactivation_hook(TG_SEARCHBOXES__FILE__, array($this, 'tg_searchboxes_deactivate'));
		//	call parent's constructor for reading the options from WP options table
		parent::__construct();
		/**	date format used in JS, needed for Calendar scripts, based on User Settings	*/
		$this->options['date_format_js']	= $this->options['date_format'] == 'd/m/Y' ? 'dd/mm/yy' : 'mm/dd/yy';

		// adding a menu to the menues on the left of the dashboard
		add_action( 'admin_menu', array($this, 'tg_searchboxes_create_menu'));

		// initializing the class needed to display the html for the searchboxes settings page
		$this->searchboxesSettingsFormRenderer = new searchboxesSettingsFormRenderer($this);
		add_action('admin_init', array($this->searchboxesSettingsFormRenderer, 'admin_init'));
//		add_action('admin_init', array($this, 'jquery_ui'), 0);
		//	is an AJAX request or not; DOING_AJAX is set by WP automatically; AJAX req. is triggered on Add-TG-Box button click
		$doing_ajax	= defined('DOING_AJAX') ? DOING_AJAX : false;
		//	accepting AJAX req.s as valid only from the post editing interface; page-slug is assigned to the button
		$valid_ajax_call= isset($_GET['page']) && $this->page_slug == $_GET['page'] ? true : false;
		// have to check for possible call by editor button to show the searchboxes in a popup div done by thickbox
		if($doing_ajax && $valid_ajax_call && isset($_GET['action']) && 'tg_searchboxes' == $_GET['action'] ) {
			// valid ajax req. => Editor Button clicked => then on the thickbox popup div show the content of the specified views
			// currently there's only 1 action+fn. for that:do_action_tg_searchboxes
			add_action( 'init', array( $this, 'do_action_' . $_GET['action'] ) );
		}
		// the editor button will be present only on the pages in the array below
		$pages_with_editor_button	= array( 'post.php', 'post-new.php', 'page.php', 'page-new.php' );
		foreach ( $pages_with_editor_button as $page ) {
			// if any of the given pages are loaded	=> edd the editor button
			add_action( 'load-' . $page, array( $this, 'add_editor_button' ) ); 
		}
		return;
	}

	/**	adding an install hook	*/
	function tg_searchboxes_install() {
		/*	init the plugin w/ default options, defined above	*/
		$this->options	= $this->default_options;
		// setting the default options into the database
		add_option(	// the options variable name into the DB
				'tg_searchboxes_options',
				// value
				$this->options,
				// flag for deprecated options
				'',
				// puts options into object cache on each page load
				'yes');
		return true;
	}
	
	/**	adding a deactivation hook	*/
	function tg_searchboxes_deactivate() {
		// deleting the options from the database
		delete_option('tg_searchboxes_options');
		// removing hook for shortcode [tg_searchbox]
		remove_shortcode($this->shortcode_tg_searchboxes);
		// deactivating the plugin
		deactivate_plugins(basename(TG_SEARCHBOXES__FILE__));
		return true;
	}

	/**	adding the admin menu + submenu(s)	*/
	function tg_searchboxes_create_menu() {
		// creating a menu in the Dasboard->Settings with the name Travelgrove Searchboxes;
		// $searchboxesSettingsPage will contain the WP-internal slug for the TG-S.boxes admin page
		$searchboxesSettingsPage	= add_menu_page(
			// page title
			'Travel Search &rsaquo; Default Settings',
			// menu title
			'Travel Search',
			// this menu will be displayed for users w/ rights to manage options
			'manage_options',
			// The slug name to refer to this menu by (should be unique for this menu)
			$this->page_slug,
			// The function that displays the page content for the menu page
			array($this, 'tg_searchboxes_load_view'),
//			array($this, 'tg_searchboxes_settings'),
			// The url to the icon to be used for this menu
			plugins_url( '/images/tg20x20.gif', TG_SEARCHBOXES__FILE__ )
		);
		// loading the JS+CSS needed for the searchbox settings page when the page is displayed
		add_action(	'admin_print_styles-'.$searchboxesSettingsPage,
				// enqueuing the needed JS+CSS files via WP
				array($this, 'tg_searchboxes_admin_head'));

		$demoPage	= add_submenu_page(
			// parent page slug
			$this->page_slug,
			// page title
			'Demo Page - see the Searchboxes in action',
			// menu title
			'Demo Page',
			// this sub-menu will be displayed for users w/ rights to manage options
			'manage_options',
			// the slug name to refer to this menu by (should be unique for this menu)
			'tg_searchboxes_demo',
			// the function to be called to output the content for this page.
			array($this, 'tg_searchboxes_load_view')
		);
		// loading the JS+CSS needed when the demo page is displayed; the same as on the post page
		add_action('admin_print_styles-'.$demoPage, array($this, 'tg_searchboxes_frontend_head'));
		return true;
	}

	/**	loading the requested views; used only on admin pages	*/
	function tg_searchboxes_load_view($page_type) {
		if(empty($page_type)) {
			/**	page-type empty == callback for (sub)menu pages => use current_filter to detect which page is this	*/
			$page_type	= current_filter() == 'toplevel_page_tg_searchboxes' ? 'settings' : 'demo';
		}
		/**	settings page, tg_searchboxes( == s.box loaded via ajax), demo page	*/
		$views	= array('settings', 'tg_searchboxes', 'demo');
		/**	unknown/unspecified view	*/
		if(empty($page_type) || !in_array($page_type, $views))
			return false;
		
		// the title of the pages
		/**	headlines/titles	*/
		$headlines	= array(
			/*	settings page	*/
			'settings'	=> 'Travel Search &rsaquo; Default Settings',
			/*	box loaded via AJAX into thickbox	*/
			'tg_searchboxes'=> 'Travelgrove Searchboxes',
			/*	demo page	*/
			'demo'		=> 'Travelgrove Searchboxes &rsaquo; Demo Page');
		/*	title used in the view /views/common/header.php	*/
		$tg_sb_headline	= isset($headlines[$page_type]) ? $headlines[ $page_type ] : null;

		/**	top part of admin pages, currently containing only an h1 title;	*/
		$tg_sb_header	= TG_SEARCHBOXES_ABSPATH. 'views/admin/header.php';
		/** after updating the options we check if the css file containg the rules for the colors of the searchboxes can be written */
		if(
			isset($_GET['page'])
			&& $_GET['page'] == 'tg_searchboxes' &&
			(	(isset($_GET['updated']) && $_GET['updated'] == 'true')
				|| (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true')
			)
		) {
			/** if we don't have the rights to write directly to the css file then ask the user to add the ftp credentials in a form */
			if(!$this->searchboxesSettingsFormRenderer->checkFTPCredentials())
				return false;
			/** if we have the rights to write directly to the css file or if we have the right ftp credentials then we try to write in the css file */
			if(!$this->searchboxesSettingsFormRenderer->writeToFile($_POST))
				/** if we couldn't write to the css file then display the error message */
				settings_errors('tg_searchboxes_options_writable_css_file_error');
		}
		/**	content of the pages	*/
		$tg_sb_content	= TG_SEARCHBOXES_ABSPATH. 'views/admin/'.$page_type.'/'.'content.php';
		/**	main view that includes the other views	*/
		include (TG_SEARCHBOXES_ABSPATH.'views/admin/main.php');
		return true;
	}

	/**	method used to add the button needed in the HTML editor and TinyMCE Editor	*/
	function add_editor_button() {
		// adding the CSS+JS files needed for thickbox to WP query; 
		add_thickbox();

		// parameters needed on the ajax request made for the popup div
		$params		= array('page'		=> $this->page_slug,
					'action'	=> 'tg_searchboxes',
					'width'		=> '800',
					'height'	=> '800');
        	// adding the params to the url which will be used to make the ajax request
		$ajax_url	= add_query_arg( $params, admin_url( 'admin-ajax.php' ) );
		// retrieve URL with nonce added to URL query
		$ajax_url	= wp_nonce_url( $ajax_url, $this->get_nonce( $params['action'], false ) );
		// sanitizing the URL using esc_url
		$ajax_url	= esc_url( $ajax_url);
		
		// setting the jsfile that needs to be included
		$jsfile		= "admin/admin-editor-buttons-script" . TGSB_PACK . ".js?" . TGSB_VER;
        	// printing the scripts needed
		add_action('wp_print_scripts', array($this, 'tg_searchboxes_admin_scripts'));

		// HTML editor integration
		wp_register_script( 'tg_searchboxes-editor-button-js', plugins_url( $jsfile, TG_SEARCHBOXES__FILE__ ), array( 'jquery', 'jquery-core', 'jquery-migrate', 'thickbox'/*, 'media-upload', 'quicktags'*/), '1.0', true );
		wp_localize_script( 'tg_searchboxes-editor-button-js', 'TG_Searchboxes_Editor_Button', 
			array(
				'str_EditorButtonCaption'	=> __( 'Travelgrove Searchboxes'),
				'str_EditorButtonTitle'		=> __( "Insert Travelgrove's Searchboxes"),
				'str_EditorButtonAjaxURL'	=> $ajax_url,
				// the searchoxes image url are needed in the tinyMCE editor
				'str_tgsb160x600'		=> plugins_url('images/tgsb160x600.png', TG_SEARCHBOXES__FILE__),
				'str_tgsb300x250'		=> plugins_url('images/tgsb300x250.png', TG_SEARCHBOXES__FILE__),
				'str_tgsb300x533'		=> plugins_url('images/tgsb300x533.png', TG_SEARCHBOXES__FILE__),
				'str_tgsb728x90'		=> plugins_url('images/tgsb728x90.png', TG_SEARCHBOXES__FILE__),
				'str_tgsbdynamic'		=> plugins_url('images/tgsbdynamic.png', TG_SEARCHBOXES__FILE__)
        		) 
        	);

		// TinyMCE integration
		if ( user_can_richedit() ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ));
			add_filter( 'mce_buttons', array( $this, 'add_tinymce_button' ), 0);
		}
		
		add_action( 'admin_print_footer_scripts', array( $this, '_print_editor_button' ), 51);
	}
	
	function _print_editor_button() {
		wp_print_scripts( 'tg_searchboxes-editor-button-js' );
	}

    /**
     * Add "Table" button and separator to the TinyMCE toolbar
     *
     * @param array $buttons Current set of buttons in the TinyMCE toolbar
     * @return array Current set of buttons in the TinyMCE toolbar, including "TGSearchboxes" button
     */
    function add_tinymce_button( $buttons ) {
    	$buttons[] = '|';
        $buttons[] = 'TGSearchboxes';
    	return $buttons;
    }

    /**
     * Register "Table" button plugin to TinyMCE
     *
     * @param array $plugins Current set of registered TinyMCE plugins
     * @return array Current set of registered TinyMCE plugins, including "TGSearchboxes" button plugin
     */
	function add_tinymce_plugin( $plugins ) {
//        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.dev' : '';
		$jsfile = "admin/admin-tinymce-buttons-script" . TGSB_PACK . ".js?" . TGSB_VER;
		$plugins['TGSearchboxes']	= plugins_url( $jsfile, TG_SEARCHBOXES__FILE__ );
			return $plugins;
	}
	
	 /**	"AJAX List of Searchboxes" action handler	*/
	function do_action_tg_searchboxes() {
		//testing if the current request was referred from an admin page
     		check_admin_referer( $this->get_nonce( 'tg_searchboxes' ) );
     		// loading the corespondent view
		$this->tg_searchboxes_load_view( 'tg_searchboxes' );
		// necessary to stop page building here!
		exit();
 	}
	
	/**
	* Generate the complete nonce string, from the nonce base, the action and an item,
	* e.g. tg_searchboxes-nonce_tg_searchboxes.
	* The get_nonce function is used to prevent CSRF (Cross-site request forgery)
	* used for AJAX requests only!
	*
	* @param string	$action	- Action for which the nonce is needed
	* @param string	$item	- (optional) Item for which the action will be performed, like "tg_searchboxes" or "custom_field"
	* @return string The complete nonce string
	*/
	private function get_nonce( $action, $item = false ) {
		return $this->nonce_base . '_' . $action.( false !== $item  ? '_' . $item : '');
	}

	/**	enqueuing the autosuggestion and datepicker scripts with their dependencies, they are needed on the editor page	*/
	function tg_searchboxes_admin_scripts() {
		/**	auto-suggestion drop-down for city/airport inputs	*/
		wp_enqueue_script(	'tgsb_autosuggestion',
					plugins_url('/js/autosuggestion' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate'));
		/**	jQuery DatePicker calendar for date inputs	*/
		wp_enqueue_script(	'tgsb_datepicker_script',
					plugins_url('/js/jquery-ui-datepicker' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core'));
		return true;
	}

	/**	adding the JS+CSS to the page when settings page is displayed	*/
	function tg_searchboxes_admin_head() {
		// autosuggestion for airports / cities inputs
		wp_enqueue_script(	'tgsb_autosuggestion',
					plugins_url('/js/autosuggestion' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate'));

		// adding CSS for the datepicker (for depart, return, check-in, check-out, pick-up, drop-off dates)
		wp_enqueue_style('tgsb_datepicker_style',	plugins_url('/css/ui-lightness/datepicker' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__));
		// adding JS for the datepicker (for depart, return, check-in, check-out, pick-up, drop-off dates)
		wp_enqueue_script(	'tgsb_datepicker_script',
					plugins_url('/js/jquery-ui-datepicker' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core'));
		// enqueuing the farbtastic color-picker css file
		wp_enqueue_style( 'farbtastic' );
		// enqueuing the farbtastic color-picker js file
		wp_enqueue_script( 'farbtastic' );
		// main CSS rules used on the TG searchbox Settings Page of the plugin
		wp_enqueue_style('tgsb_searchboxes_settings_style', plugins_url('/css/tg_searchboxes_settings' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array());
		// including customized settings, set by the user
		// added the filemtime because the values of the color are changing in the css file when a user decide to change them and so we want to do a cache refresh for that file
		wp_enqueue_style('tgsb_searchboxes_color_style', plugins_url('/css/tg_searchboxes_color.css', TG_SEARCHBOXES__FILE__), array(), $this->options['cssfiletimestamp']);
		// adding the js file used for the google plus one button
		// it was added like that because we want to be print between the head tags and because it has also inline js "{parsetags:'explicit'}" that has to be present
		?>
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{parsetags:'explicit'}</script>
		<?php
		// adding the JS file used for settings /js/tg_searchboxes_settings.js to the DOM
		// the JS file used on the Setting Page of the plugin
		wp_enqueue_script(
				'tgsb_searchboxes_settings_script',
				// path
				plugins_url('/js/tg_searchboxes_settings' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
				// dependencies
				array('tgsb_datepicker_script', 'farbtastic', 'jquery', 'jquery-core', 'jquery-migrate'),
				'',
				// add script to footer because on it are attached some js variables
				true);

		// adding to the DOM the js variables needed in the settings JS file
		wp_localize_script(
			// the name of the script where are this variables attached to
      			'tgsb_searchboxes_settings_script',
			// js object name that will contain the variables
      			'TG_Searchboxes_Settings',
			array(	'str_CalendarURL'	=> plugins_url('/images/tg_searchboxes/calendarnew.png', TG_SEARCHBOXES__FILE__),
				'str_ASAjaxURL'		=> plugins_url('/ajax/autosuggestion.php', TG_SEARCHBOXES__FILE__),
				'str_dateFormat'	=> $this->options['date_format_js'],
				'tgsbDefaultSettings'	=> json_encode($this->options)
			)
      		);
		/**	main CSS rules for the searchboxes	*/
		wp_enqueue_style('tg_searchboxes_css', plugins_url('/css/tg_searchboxes' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array());
		// adding the JS file used for the shortcodes generation on the settings page /js/tg_searchboxes_shortcodes.js
		wp_enqueue_script(
				'tgsb_searchboxes_shortcodes_script',
				// path
				plugins_url('/js/tg_searchboxes_shortcodes' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
				// dependencies
				array('tgsb_datepicker_script', 'jquery', 'jquery-core', 'jquery-migrate'),
				'1.2',
				// add script to footer because on it are attached some js variables
				true);
		// options needed for the default settings variables
		$options			= $this->options;
		// the dates should be present in the date format set
		$depDateTimestamp		= strtotime('+'.$options['departure_date']);
		$retDateTimestamp		= strtotime($options['return_date'], $depDateTimestamp);
		$options['departure_date']	= date($options['date_format'], $depDateTimestamp);
		$options['return_date']		= date($options['date_format'], $retDateTimestamp);
		unset($depDateTimestamp, $retDateTimestamp);
		// adding to the DOM the js variables needed in the shortcodes JS file
		wp_localize_script(
			'tgsb_searchboxes_shortcodes_script',
			'TG_Searchboxes_Variables',
			array(	'str_CalendarURL'	=> plugins_url('/images/tg_searchboxes/calendarnew.png', TG_SEARCHBOXES__FILE__),
				'str_ASAjaxURL'		=> plugins_url('/ajax/autosuggestion.php', TG_SEARCHBOXES__FILE__),
				'str_dateFormat'	=> $this->options['date_format_js'],
				'tgsbDefaultSettings'	=> json_encode($options),
				'demoPage'		=> admin_url().'admin.php?page=tg_searchboxes_demo'
			)
		);
		unset($options);
		return true;
	}

	/**	enque JS/CSS used on Demo page, inside admin interface	*/
	function tg_searchboxes_frontend_head() {
		/**	customized CSS rules of the searchbox	*/
				// added the filemtime because the values of the color are changing in the css file when a user decide to change them and so we want to do a cache refresh for that file
		wp_enqueue_style('tg_searchboxes_color_css', plugins_url('/css/tg_searchboxes_color.css', TG_SEARCHBOXES__FILE__), array(), $this->options['cssfiletimestamp']);
		/**	main CSS rules for the searchboxes	*/
		wp_enqueue_style('tg_searchboxes_css', plugins_url('/css/tg_searchboxes' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__), array());
		/**	jQuery datepicker CSS rules for Calendars	*/
		wp_enqueue_style('datepicker_stylesheet', plugins_url('/css/ui-lightness/datepicker' . TGSB_PACK . '.css?' . TGSB_VER, TG_SEARCHBOXES__FILE__));
		
		/**	autosuggestion JS for city/airport inputs	*/
		wp_enqueue_script(	'tgsb_autosuggestion',
					plugins_url('/js/autosuggestion' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate'));
		/**	jQuery DatePicker calendar for date inputs	*/
		wp_enqueue_script(	'tgsb_datepicker_script',
					plugins_url('/js/jquery-ui-datepicker' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__),
					array('jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core'));
		/**	main JS for dynamic functionalities of the searchboxes	*/
		wp_enqueue_script(	'tgsb_main_script',
					plugins_url( '/js/tg_searchboxes' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__ ),
					array('tgsb_datepicker_script', 'tgsb_autosuggestion'),
					// version number
					'',
					//add this script to the footer because on it are attached some js variables
					true);
		// adding to the DOM the js variables needed in the main JS file
		/**	local JS variables needed for the searchboxes	*/
		wp_localize_script(
			// the name of the script where are this variables attached to
			'tgsb_main_script',
			// js object name that will contain the variables
			'TG_Searchboxes_Variables',
			array(	'str_CalendarURL'	=> plugins_url('/images/tg_searchboxes/calendarnew.png', TG_SEARCHBOXES__FILE__),
				'str_ASAjaxURL'		=> plugins_url('/ajax/autosuggestion.php', TG_SEARCHBOXES__FILE__),
				'str_dateFormat'	=> $this->options['date_format_js'],
				'str_merchantsAjaxURL'	=> plugins_url('/ajax/merchants.php', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderCircle'	=> plugins_url('/images/tg_searchboxes/ajax-loader-circle.gif', TG_SEARCHBOXES__FILE__),
				'str_ajaxLoaderBert'	=> plugins_url('/images/tg_searchboxes/ajax-loader-bert.gif', TG_SEARCHBOXES__FILE__))
		);
		return true;
	}
}
?>