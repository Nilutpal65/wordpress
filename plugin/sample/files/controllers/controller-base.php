<?php
/**
 * Base Controller for Travel-Search with functions needed both in backend and frontend
 *
 * @package Travel-Search
 * @subpackage Base Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

/**
 * Base Controller class
 */
class Tg_Searchboxes_Controller_Base {
	/**
		* Plugin Options, key is each option's name, value is bool, string or int value;
		* used in derived classes: controller-frontend.php, controller-admin.php
		* @var array
	*/
	/*	public because it's used in renderer	*/
	public $options	= array();

	// for date format conversion between PHP<->JS
	private $date_formats_translation	= array('m/d/Y' => 'mm/dd/yy', 'd/m/Y' => 'dd/mm/yy');
		
	/**	shortcode tag used to add/remove hook for shortcode; thus other actions can be mapped for TG shortcode	*/
	protected $shortcode_tg_searchboxes	= 'tg_searchboxes';

	/**	PHP4 class constructor, calls the PHP5 class constructor __construct()	*/
	function Tg_Searchboxes_Controller_Base() {
		$this->__construct();
	}
/**
	* PHP5 class constructor
*/
	function __construct() {
		/**	getting the TG-searchboxes options from the WP options table, used in both derived classses	*/
		$this->options			= get_option('tg_searchboxes_options');
		/**	making the tracking parameters hookable and applying filters mapped to them	*/
		$this->options['id_referral']	= apply_filters('tg_searchboxes_affiliate_id', isset($this->options['id_referral']) ? $this->options['id_referral'] : 0);
		$this->options['adid']		= apply_filters('tg_searchboxes_adid', isset($this->options['adid']) ? $this->options['adid'] : '');
		/**	making the actual shortcode value hookable	*/
		$this->shortcode_tg_searchboxes	= apply_filters('tg_searchboxes_shortcode', $this->shortcode_tg_searchboxes);

        if (!isset($this->options['cruiseline'])) {
            $this->options['cruiseline'] = null;
        }
        if (!isset($this->options['destination'])) {
            $this->options['destination'] = null;
        }
        if (!isset($this->options['length_of_stay'])) {
            $this->options['length_of_stay'] = null;
        }
        if (!isset($this->options['month_year'])) {
            $this->options['month_year'] = null;
        }

		return;
	}
	
	/**	@note	gets the shortcode that's used in current travel search instance
	 *	@date	2013.04.23
	 * 	@author	Tibi	*/
	public function tg_searchboxes_get_shortcode(){
		return $this->shortcode_tg_searchboxes;
	}
	
	function jquery_ui() {
		wp_deregister_script( 'jquery-ui-core' );
		wp_register_script( 'jquery-ui-core', plugins_url('/js/jquery-ui' . TGSB_PACK . '.js?' . TGSB_VER, TG_SEARCHBOXES__FILE__));
		wp_enqueue_script( 'jquery-ui-core' );
	}

	
	function __destruct() {}
}
?>