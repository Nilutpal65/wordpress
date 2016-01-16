<?php
/**
 * Settings Renderer Class for Travel-Search
 *
 * @package Travel-Search
 * @subpackage SettingsRenderer
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */

class searchboxesSettingsFormRenderer {
	var $options;
	var $controller;
	
		/**	dep/return dates used in admin form -> default date selectors	*/
	/**	NOTE_FOR_CIPRI: MOVE THIS TO THE RIGHT CLASS, WHERE THEY ARE USED!	*/
	private $departure_dates	= array('1 day', '2 days', '3 days', '4 days', '5 days', '6 days',
					'1 week', '2 weeks', '3 weeks', '4 weeks',
					'1 month', '2 months', '3 months',
					'1 year');

	private $return_dates	= array('+1 day', '+2 days', '+3 days', '+4 days', '+5 days', '+6 days',
					'+1 week', '+2 weeks', '+3 weeks', '+4 weeks',
					'+1 month', '+2 months', '+3 months',
					'+1 year');
					
	/**	arrays for travelers, used in renderers	*/
	/**	NOTE_FOR_CIPRI: MOVE THIS TO THE RIGHT CLASS, WHERE THEY ARE USED!	*/
	private $adults		= array(1, 2, 3, 4, 5 );
	private $kids		= array(0, 1, 2, 3, 4);
	private $seniors	= array(0, 1, 2, 3, 4, 5);
	private $rooms		= array(1, 2, 3, 4, 5);

		/**	available/selectable date formats on the admin settings form	*/
	/**	NOTE_FOR_CIPRI: MOVE THIS TO THE RIGHT CLASS, WHERE THEY ARE USED!	*/
	private $date_formats	= array(
		'm/d/Y'	=> 'mm/dd/yyyy (us)',
		'd/m/Y'	=> 'dd/mm/yyyy (gb)'
	);	
	
	function __construct(&$controller = false) {
		$this->controller	= $controller;
		$this->options		= empty($this->controller->options) ? get_option('tg_searchboxes_options') : $this->controller->options;
	}
	
	// WHAT & WHY: method called when the administrative section is initialized
	// WHO & WHEN: Cipri on the 2nd of Februrary 2012	
	function admin_init() {
		// registering the settings
		register_setting('tg_searchboxes_options', 'tg_searchboxes_options', array($this, 'validate_options'));
		add_settings_section('tg_searchboxes_options', 'Default Searchbox Values and Targeting Options', array($this, 'description_text'), 'tg_searchboxes_options');
		// adding the id referral field to the form as a setting field
		add_settings_field('tg_searchboxes_options_id_referral', 'Your unique Travelgrove affiliate ID:', array($this, 'id_referral_input'), 'tg_searchboxes_options', 'tg_searchboxes_options');
		// adding the date format select to the form as a setting field
		add_settings_field('tg_searchboxes_options_date_format', 'Date Format:', array($this, 'date_format_select'), 'tg_searchboxes_options', 'tg_searchboxes_options');
		// adding the departure date select to the form as a setting field
		add_settings_field('tg_searchboxes_options_departure_date', 'Default Departure Date:', array($this, 'departure_date_select'), 'tg_searchboxes_options', 'tg_searchboxes_options');
		// adding the return date select to the form as a setting field
		add_settings_field('tg_searchboxes_options_return_date', 'Default Return Date:', array($this, 'return_date_select'), 'tg_searchboxes_options', 'tg_searchboxes_options');
	}
	
	/*
		WHAT & WHY: function used to validate the value of the inputs sent with the default settings form
		WHO & WHEN: Cipri on Feb, March, April 2012
		@param	array	$input	contains the array with the values of the inputs of the default settings form
		@return array	contains an array with the valid values
	*/
	function validate_options($input) {
		if(empty($this->options))
			return false;
		$valid			= array();
		$valid			= $this->options;
		// flag that will be set to true if the border color value will be valid
		$validBorderColor	= false;
		// flag that will be set to true if the text color value will be valid		
		$validTextColor		= false;
		// flag that will be set to true if the background color value will be valid		
		$validBackgroundColor	= false;
		// flag that will be set to true if the tabs background color value will be valid
		$validTabsColor		= false;
		// flag that will be set to true if the tabs text color value will be valid
		$validTabsTextColor	= false;
		// flag that will be set to true if the tabs borders color value will be valid
		$validTabsBordersColor	= false;
		
		$idReferral = (int)$input['id_referral'];
		// if after appling a cast conversion on the idReferral the idReferral is empty then set it to 999 which is the id for a not set source 
		$idReferral = (empty($idReferral)) ? 999 : $idReferral;
		
		// checking if the idReferral matches the pattern
		if(!preg_match('/^[0-9]+$/', $idReferral)) {
			// if the idReferral doesn't match with the pattern then an error message is set
			add_settings_error( 'tg_searchboxes_options_id_referral', 'tg_searchboxes_options_id_referral_error','The Affiliate Unique ID was not set correctly!','error');
		} else {
			// adding the idReferral to the array containing the valid values
			$valid['id_referral'] = $idReferral;
		}

		// checking if the date format matches the date_formats array set in the caontroller-base.php
		if(!array_key_exists($input['date_format'], $this->date_formats)) {
			// if no match found an error message is set
			add_settings_error( 'tg_searchboxes_options_date_format', 'tg_searchboxes_options_date_format_error','The DATE FORMAT was not set correctly!','error');
		} else {
			// the date_format is added to the array containing the valid values
			$valid['date_format'] = $input['date_format'];
		}
		
		// getting the fromAir value because there are several inputs from which that value can be get
		$fromAir = $this->getFromAir($input);
		//checking if the fromAir value matches the pattern
		if(!empty($fromAir) && !preg_match('/^(.*) \((...)\)$/', $fromAir)) {
			// if the fromAir value does not match the pattern set an error message
			add_settings_error( 'tg_searchboxes_options_from_air', 'tg_searchboxes_options_from_air_error','Please select a location from the drop-down list. &lt;'.$fromAir.'&gt; was not recognized as a valid location!','error');
		} else {
			// adding the fromAir value to the array containing the valid values
			$valid['from_air'] = $fromAir;
		}
		// unseting the $fromAir variable
		unset($fromAir);
		// getting the toAir value because there are several inputs from which that value can be get
		$toAir = $this->getToAir($input);
		//checking if the toAir value matches the pattern
		if(!empty($toAir) && !preg_match('/^(.*) \((...)\)$/', $toAir)) {
			add_settings_error( 'tg_searchboxes_options_to_air', 'tg_searchboxes_options_to_air_error','Please select a location from the drop-down list. &lt;'.$toAir.'&gt; was not recognized as a valid location!','error');
		} else {
			// adding the toAir value to the array containing the valid values
			$valid['to_air'] = $toAir;
		}
		// unseting the $toAir variable
		unset($toAir);


        $hotelCity = $this->getHotelCity($input);
        //checking if the hotelCity value matches the pattern
        if(!empty($hotelCity) && !preg_match('/^(.*) \((.*)\)$/', $hotelCity) && !preg_match('/^(.*), (..)$/', $hotelCity)) {
            add_settings_error( 'tg_searchboxes_options_hotel_city', 'tg_searchboxes_options_hotel_city_error','Please select a location from the drop-down list. &lt;'.$hotelCity.'&gt; was not recognized as a valid location!','error');
        } else {
            // adding the toAir value to the array containing the valid values
            $valid['hotel_city'] = $hotelCity;
        }
        // unseting the $hotelCity variable
        unset($hotelCity);

		
		// setting the departure date
		$departureDate = (empty($input) || empty($input['flights_departure_date'])) ? false : $input['flights_departure_date'];
		// checking the departure date value to be present in the array of departure_dates set on controller-base.php
		if(empty($departureDate) || !in_array($departureDate, $this->departure_dates)) {
			// adding an error message if the departure_date value was not found
			add_settings_error( 'tg_searchboxes_options_departure_date', 'tg_searchboxes_options_departure_date_error','The DEPATURE DATE input was not set correctly!','error');
		} else {
			// adding the departure_date value to the array containing the valid values
			$valid['departure_date'] = $departureDate;
		}
		// unsetting the departureDate variable
		unset($departureDate);
		// setting the returnDate
		$returnDate = (empty($input) || empty($input['flights_return_date'])) ? false : $input['flights_return_date'];
		// checking the departure date value to be present in the array of return_dates set on controller-base.php
		if(empty($returnDate) || !in_array($returnDate, $this->return_dates)) {
			// adding an error message if the return_date value was not found
			add_settings_error( 'tg_searchboxes_options_return_date', 'tg_searchboxes_options_return_date_error','The RETURN DATE input was not set correctly!','error');
		} else {
			// adding the return_date value to the array containing the valid values
			$valid['return_date'] = $returnDate;
		}
		// unsetting the returnDate variable
		unset($returnDate);
		// setting the adults
		$adults = $this->getAdults($input);
		// checking the adults value to be present in the array of adults set on controller-base.php
		if(empty($adults) || !in_array($adults, $this->adults)) {
			// adding an error message if the adults value was not found
			add_settings_error( 'tg_searchboxes_options_adults', 'tg_searchboxes_options_adults_error','The ADULTS input was not set correctly!','error');
		} else {
			// adding the adults value to the array containing the valid values
			$valid['adults'] = $adults;
		}
		// unsetting the adults variable
		unset($adults);
		// setting the kids variable
		$kids = $this->getKids($input);
		// checking the kids value to be present in the array of kids set on controller-base.php
		if(!isset($kids) || !in_array($kids, $this->kids)) {
			// adding an error message if the kids value was not found
			add_settings_error( 'tg_searchboxes_options_kids', 'tg_searchboxes_options_kids_error','The KIDS input was not set correctly!','error');
		} else {
			// adding the kids value to the array containing the valid values
			$valid['kids'] = $kids;
		}
		// unsetting the kids variable
		unset($kids);
		// setting the seniors variable
		$seniors = $this->getSeniors($input);
		// checking the serniors value to be present in the array of seniors set on controller-base.php
		if(!isset($seniors) || !in_array($seniors, $this->seniors)) {
			// adding an error message if the seniors value was not found
			add_settings_error( 'tg_searchboxes_options_seniors', 'tg_searchboxes_options_seniors_error','The SENIORS input was not set correctly!','error');
		} else {
			// adding the seniors value to the array containing the valid values
			$valid['seniors'] = $seniors;
		}
		// unsetting the seniors variable
		unset($seniors);
		// setting the rooms variable
		$rooms = $this->getRooms($input);
		// checking the rooms value to be present in the array of rooms set on controller-base.php
		if(empty($rooms) || !in_array($rooms, $this->rooms)) {
			// adding an error message if the rooms value was not found
			add_settings_error( 'tg_searchboxes_options_rooms', 'tg_searchboxes_options_rooms_error','The ROOMS input was not set correctly!','error');
		} else {
			// adding the rooms value to the array containing the valid values
			$valid['rooms'] = $rooms;
		}
		// unsetting the rooms variable
		unset($rooms);
		// validating the RoundTrip/OneWay variable
		$valid['rtow'] = (empty($input['flights_oneway'])) ? false : true;

        $valid['cruiseline'] = isset($input['cruiseline']) && $input['cruiseline'] ? (string)$input['cruiseline'] : null;
        $valid['length_of_stay'] = isset($input['length_of_stay']) && $input['length_of_stay'] ? (string)$input['length_of_stay'] : null;
        $valid['destination'] = isset($input['destination']) ? (string)$input['destination'] : null;
        $valid['month_year'] = isset($input['month_year']) ? (string)$input['month_year'] : null;


		
		// checking if the value of the border color field matches the pattern
		if(empty($input['brdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['brdcolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_brd_color', 'tg_searchboxes_options_brd_color_error','That was not a valid color code you have set for the border color!','error');
		} else {
			// setting the value of the flag to true
			$validBorderColor	= true;
			// adding the border color value to the array containing the valid values
			$valid['brdcolor']	= $input['brdcolor'];
		}
		
		// checking if the value of the text color field matches the pattern
		if(empty($input['txtcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['txtcolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_txt_color', 'tg_searchboxes_options_txt_color_error','That was not a valid color code you have set for the color of the text!','error');
		} else {
			// setting the value of the flag to true
			$validTextColor		= true;
			// adding the text color value to the array containing the valid values
			$valid['txtcolor']	= $input['txtcolor'];
		}
		
		// checking if the value of the background color field matches the pattern
		if(empty($input['bgdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['bgdcolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_bgd_color', 'tg_searchboxes_options_bgd_color_error','That was not a valid color code you have set for the color of the background!','error');
		} else {
			// setting the value of the flag to true
			$validBackgroundColor		= true;
			// adding the background color value to the array containing the valid values
			$valid['bgdcolor']	= $input['bgdcolor'];
		}
		// checking if the value of the background color of the tabs field matches the pattern
		if(empty($input['tbscolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbscolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_tbs_color', 'tg_searchboxes_options_tbs_color_error','That was not a valid color code you have set for the color of the background of the tabs!','error');
		} else {
			// setting the value of the flag to true
			$validTabsColor		= true;
			// adding the background color value to the array containing the valid values
			$valid['tbscolor']	= $input['tbscolor'];
		}
		
		// checking if the value of the text color of the tabs field matches the pattern
		if(empty($input['tbstxtcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbstxtcolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_tbstxt_color', 'tg_searchboxes_options_tbstxt_color_error','That was not a valid color code you have set for the color of the text of the tabs!','error');
		} else {
			// setting the value of the flag to true
			$validTabsTextColor	= true;
			// adding the background color value to the array containing the valid values
			$valid['tbstxtcolor']	= $input['tbstxtcolor'];
		}
		
		// checking if the value of the borders color of the tabs field matches the pattern
		if(empty($input['tbsbrdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbsbrdcolor'])) {
		// if the value of the border color doesn't match the pattern then set an error message
			add_settings_error( 'tg_searchboxes_options_tbsbrd_color', 'tg_searchboxes_options_tbsbrd_color_error','That was not a valid color code you have set for the color of the borders of the tabs!','error');
		} else {
			// setting the value of the flag to true
			$validTabsBordersColor	= true;
			// adding the background color value to the array containing the valid values
			$valid['tbsbrdcolor']	= $input['tbsbrdcolor'];
		}
		
		// checking the links option value
		$valid['links'] = (empty($input['links'])) ? false : true;

		// checking the noconflict option value
		$valid['noconflict'] = (empty($input['noconflict'])) ? false : true;

		// checking the usejavascript flag value | Tibi | 2013.04.23
		$valid['usejavascript'] = isset($input['usejavascript']) && $input['usejavascript'] ? true : false;
		
		// this value is used to avoid caching for the color file after new values are saved
		$valid['cssfiletimestamp'] = time();

		return $valid;
	}
	
	/**	setting the description of the Settings page	*/
	function description_text() {
		echo '<p>Before actually adding the searchboxes to your pages check your general settings.</p>'.
		'<p>Simply enter your default values into the right fields and leave the tab open that you want to be opened by default.</p>'.
		'<p>Make sure you enter your Travelgrove Affiliate ID as well so Travelgrove can track your commissions.'.
		' <a href="https://www.travelgrove.com/affiliates/login.php?source=wpPlugin" target="_blank" title="Travelgrove\'s Affiliates login page">Click here</a> to get your unique Travelgrove Affiliate ID.</p>';
	}
	
	/**	setting the Affiliate ID input on the Settings Page	*/
	function id_referral_input() {
		echo "<input name='tg_searchboxes_options[id_referral]' id='id_referral' type='text' value='".
			($this->options['id_referral'] == 999 ? 'your ID here' : esc_attr($this->options['id_referral'])).
			"' />";
	}
	
	/**	setting the content of the departure_date select elem on the Settings Page	*/
	function departure_date_select() {
		echo $this->date_input('flights_departure_date', 'tgsb_departure_date');
	}

	/**	setting the content of the return_date select elem on the Settings Page	*/
	function return_date_select() {
		echo $this->date_input('flights_return_date', 'tgsb_return_date', false);
	}
	
	/**	setting the content of the date format select elem on the Settings Page	*/
	function date_format_select() {
        $output = "";
		$output .= "<select name='tg_searchboxes_options[date_format]' id='date_format' style='width:185px'>";
		foreach($this->date_formats as $idx => $date_format) {
			$output	.= "<option value='".esc_attr($idx)."'".
				($this->options['date_format'] == $idx ? " selected='selected'" : '').
				">".esc_attr($date_format)."</option>";
		}
		$output		.= "</select>";
		echo $output;
	}
	
	/*
		WHAT & WHY: function that sets the from_air value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/
	private function getFromAir($input) {
		if(empty($input))
			return '';
		if(!empty($input['flights_from_air']))
			return $input['flights_from_air'];
		if(!empty($input['packages_from_air']))
			return $input['packages_from_air'];
		return '';
	}
	/*
		WHAT & WHY: function that sets the to_air value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/
	private function getToAir($input) {
		if(empty($input))
			return '';
		if(!empty($input['flights_to_air']))
			return $input['flights_to_air'];
		if(!empty($input['packages_to_air']))
			return $input['packages_to_air'];
		if(!empty($input['cars_to_air']))
			return $input['cars_to_air'];
		if(!empty($input['hotels_to_air']))
			return $input['hotels_to_air'];			
		return '';
	}
    private function getHotelCity($input) {
        if(empty($input))
            return '';
        if(!empty($input['hotels_to_air']))
            return $input['hotels_to_air'];
        return $this->getToAir($input);
    }
	
	/*
		WHAT & WHY: function that sets the adults value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/
	private function getAdults($input) {
		if(empty($input))
			return false;
		if(!empty($input['flights_adults']))
			return $input['flights_adults'];
		if(!empty($input['hotels_adults']))
			return $input['hotels_adults'];	
		if(!empty($input['packages_adults']))
			return $input['packages_adults'];
		return false;
	}

	/*
		WHAT & WHY: function that sets the kids value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/	
	private function getKids($input) {
		if(empty($input))
			return false;
		if(isset($input['flights_kids']))
			return $input['flights_kids'];
		if(isset($input['hotels_kids']))
			return $input['hotels_kids'];	
		if(isset($input['packages_kids']))
			return $input['packages_kids'];
		return false;
	}

	/*
		WHAT & WHY: function that sets the seniors value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/	
	private function getSeniors($input) {
		if(empty($input))
			return false;
		if(isset($input['flights_seniors']))
			return $input['flights_seniors'];
		if(isset($input['packages_seniors']))
			return $input['packages_seniors'];
		return false;
	}

	/*
		WHAT & WHY: function that sets the rooms value because there are several inputs containing that value so if one of them is set then that value should be returned
	*/	
	private function getRooms($input) {
		if(empty($input))
			return false;
		if(!empty($input['hotels_rooms']))
			return $input['hotels_rooms'];
		if(!empty($input['packages_rooms']))
			return $input['packages_rooms'];
		return false;
	}
	
	/*
		WHAT & WHY: function used to set the date input for the settings page
	*/
	function date_input($optionName, $tagId, $departFlag = true) {
		if(empty($optionName))
			return false;
		$dr_dates = $departFlag ? $this->departure_dates : $this->return_dates;
        $output = "";
		$output .= "<select ".($departFlag ? "class='depDate'" : "class='retDate'")." name='tg_searchboxes_options[".esc_attr($optionName)."]' ".((!empty($tagId)) ? " id='".esc_attr($tagId)."'" : '')." style='width:185px'>";
		foreach($dr_dates as $dr_date) {
			$output .= "<option value='".esc_attr($dr_date)."'".(($this->controller->options[($departFlag ? 'departure_date' : 'return_date')] == $dr_date) ? " selected='selected'" : '').">".($departFlag ? 'current date +' : 'dep. date ').esc_attr($dr_date)."</option>";
		}
		$output .= "</select>";
		return $output;
	}
	
	/*
		WHAT & WHY: method used to write the changes to the css file
	*/
	function regenerateCSSColorFile($valid) {
		// if the array with valid values is empty return false
		if(empty($valid))
			return array();
		$file	= TG_SEARCHBOXES_ABSPATH.'css/tg_searchboxes_color.css';
		$fp	= fopen($file, 'w+');
		if(!$fp) {
			add_settings_error( 'tg_searchboxes_options_cssfile', 'tg_searchboxes_options_cssfile_error','The file '.$file.' could not be opened for writting!','error');
			// we set the values of the colors variables back to their default values because we couldn't open the file and so their the colors were not changed
			return $this->setColorsValuesBack($valid);
		}
		$fileContent = '.tg_searchbox .tg_container{border-color:'.$valid['brdcolor'].';background-color:'.$valid['bgdcolor'].';color:'.$valid['txtcolor'].'}
.tg_searchbox .tg_tabs li span{color:'.$valid['tbstxtcolor'].';background-color:'.$valid['tbscolor'].' !important;border-color:'.$valid['tbsbrdcolor'].' !important}
.tg_searchbox .tg_tabs li span.sel, .tg_searchbox .tg_tabs li span:hover{background-color:'.$valid['bgdcolor'].' !important;border-color:'.$valid['brdcolor'].' !important;color:'.$valid['txtcolor'].'}';
		$fileContent = preg_replace('/[\r\t\n\s]+/', ' ', $fileContent);
		if(fwrite($fp, $fileContent) === false) {
			add_settings_error( 'tg_searchboxes_options_cssfile', 'tg_searchboxes_options_cssfile_error', "Can't write to file <i><strong>".$file.'</strong></i>!','error');
			// we set the values of the colors variables back to their default values because we couldn't open the file and so their the colors were not changed
			return $this->setColorsValuesBack($valid);
		}
		fclose($fp);
		return $valid;
	}
	
	/*
		WHAT & WHY: method used to set the color variables back to their default values
	*/
	function setColorsValuesBack($valid) {
		if(empty($valid))
			return array();
		$valid['brdcolor']	= $this->controller->options['brdcolor'];
		$valid['bgdcolor']	= $this->controller->options['bgdcolor'];
		$valid['txtcolor']	= $this->controller->options['txtcolor'];
		$valid['tbscolor']	= $this->controller->options['tbscolor'];
		$valid['tbstxtcolor']	= $this->controller->options['tbstxtcolor'];
		$valid['tbsbrdcolor']	= $this->controller->options['tbsbrdcolor'];
		return $valid;
	}
	
	/*
		WHAT & WHY: checking if the file needed for changing the colors of the searchbox is writable
	*/
	function checkWritableCSSFile() {
		$file = TG_SEARCHBOXES_ABSPATH.'css/tg_searchboxes_color.css';
		$fp = @fopen($file,'w');
		if(!$fp)
			return array(false, $file);
		@fclose($fp);
		return array(true, $file);
	}
	
	/*
		WHAT & WHY: method used to verify the ftp credentials and if they are needed then display the form where a user will add them
	*/
	function checkFTPCredentials() {
		// an array with the inputs we are interested into
		$inputArrayKeys = array('brdcolor', 'txtcolor', 'bgdcolor', 'tbscolor', 'tbstxtcolor', 'tbsbrdcolor');
		// setting the $_POST variable from the plugin's options
		// from the $_POST variables are created the hidden inputs that will be set in the form where the user needs to add the ftp credentials
		foreach($this->controller->options as $key => $value) {
			if(!in_array($key, $inputArrayKeys))
				continue;
			$_POST[$key] = $this->controller->options[$key];

		}
		
		$url = wp_nonce_url('admin.php?page=tg_searchboxes&updated=true');
		if (false === ($creds = request_filesystem_credentials($url, '', false, false, $inputArrayKeys) ) ) {
			/*	check if the password was sent but empty => show error msg.	*/
			if(isset($_POST['password']) && empty($_POST['password']))
				print('<div id="message" class="error"><p><strong>Error:</strong> No password given.</p></div>');

			// if we get here, then we don't have credentials yet,
			// but have just produced a form for the user to fill in, 
			// so stop processing for now
			return false; // stop the normal page form from displaying
		}
	
		// now we have some credentials, try to get the wp_filesystem running
		if ( ! WP_Filesystem($creds) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, '', true, false, $inputArrayKeys);
			return false;
		}
		
		return true;
	}
	
	/*
	  WHAT & WHY: writting the contents of the $_POST-ed data to the file
	*/
	function writeToFile($input) {
		if(empty($input))
			return false;
		// verifying the values of the inputs because we don't want to allow posting some malicious data
		if(empty($input['brdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['brdcolor']))
			$input['brdcolor'] = $this->controller->options['brdcolor'];
		if(empty($input['txtcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['txtcolor']))
			$input['txtcolor'] = $this->controller->options['txtcolor'];
		if(empty($input['bgdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['bgdcolor']))
			$input['bgdcolor'] = $this->controller->options['bgdcolor'];
		if(empty($input['tbscolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbscolor']))
			$input['tbscolor'] = $this->controller->options['tbscolor'];
		if(empty($input['tbstxtcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbstxtcolor']))
			$input['tbstxtcolor'] = $this->controller->options['tbstxtcolor'];
		if(empty($input['tbsbrdcolor']) || !preg_match('/^\#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['tbsbrdcolor']))
			$input['tbsbrdcolor'] = $this->controller->options['tbsbrdcolor'];

		global $wp_filesystem;
		
		// setting the absolute path of the folder where the css file is located
		$upload_dir	= TG_SEARCHBOXES_ABSPATH.'css';
		// filename path according to the upload folder:
		// /var/www/vhosts/alpha.travelgrove.com/httpdocs/blog/wp-content/plugins/.../css/tg_searchboxes_color.css
		$filename	= trailingslashit($upload_dir).'tg_searchboxes_color.css';
		// if we are not able to write directly to the file but using the ftp
		if($wp_filesystem->method != 'direct') {
			// setting the relative path and removing the document root from the filename path string
			// /blog/wp-content/plugins/.../css/tg_searchboxes_color.css
			// on ftp the target file should be set begining from the directory that contains the files on the webserver
			// so for example it should look like this /public_html/example.txt or /httpdocs/example.txt
			// that's why we want the basename of the $_SERVER['DOCUMENT_ROOT']
			// /httpdocs/blog/wp-content/plugins/.../css/tg_searchboxes_color.css
			$filename = '/'.preg_replace('@^'.$_SERVER['DOCUMENT_ROOT'].'@i',basename($_SERVER['DOCUMENT_ROOT']),$filename);
		}

		// creating the content that follows to be written in the file
		$fileContent = '.tg_searchbox .tg_container label{color:'.$input['txtcolor'].'}  .tg_searchbox .tg_container{border-color:'.$input['brdcolor'].';background-color:'.$input['bgdcolor'].';color:'.$input['txtcolor'].'}
	.tg_searchbox .tg_tabs li span{color:'.$input['tbstxtcolor'].';background-color:'.$input['tbscolor'].' !important;border-color:'.$input['tbsbrdcolor'].' !important}
	.tg_searchbox .tg_tabs li span.sel, .tg_searchbox .tg_tabs li span:hover{background-color:'.$input['bgdcolor'].' !important;border-color:'.$input['brdcolor'].' !important;color:'.$input['txtcolor'].'}';
		$fileContent = preg_replace('/[\r\t\n\s]+/', ' ', $fileContent);

		// writting the content to the css file
		if ( ! $wp_filesystem->put_contents( $filename, $fileContent, FS_CHMOD_FILE) ) {
			add_settings_error( 'tg_searchboxes_options_writable_css_file', 'tg_searchboxes_options_writable_css_file_error','Error saving the file <strong>'.$filename.'</strong>.','error');
			return false;
		}
		return true;
	}

	
	function __destruct() {
		unset($this->controller, $this->options);
	}
}
?>