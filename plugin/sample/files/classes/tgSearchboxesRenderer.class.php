<?php
/**
 * Renderer Class for the HTML searchboxes for Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage Frontend Renderer Class
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
class tgSearchboxesRenderer {
	/*		*/
	private $atts	= array();
	private $controller;
	private $defaultSize		= '300x250';
	private $defaultSelectedTab	= 'flights';
	private $subID;
	/*	added to be able to compare if default subID is in use or not | Tibi | 2013-Jul-10	*/
	private $defaultSubID		= '106';
	private $defaultAlignment	= 'alignnone';

	private static $nrOfBoxes;
	private $valuesToBeSet;
	
	/**	arrays for travelers, used in renderers	*/

	private $adults		= array(1, 2, 3, 4, 5);
	private $kids		= array(0, 1, 2, 3, 4);
	private $seniors	= array(0, 1, 2, 3, 4, 5);
	private $rooms		= array(1, 2, 3, 4, 5);
	private $flightsClass	= array('Economy', 'Business', 'First');

    private $cruiseDest = array(
        "" => "Any Destination",
        "1201" => "Africa",
        "184" => "Alaska",
        "858" => "Asia",
        "1295" => "Bahamas",
        "1035" => "Bermuda",
        "1294" => "Canada/ New England",
        "24" => "Caribbean",
        "186" => "Eastern Caribbean",
        "188" => "Exotic Caribbean",
        "189" => "Southern Caribbean",
        "187" => "Western Caribbean",
        "992" => "Costa Rica",
        "23" => "Europe",
        "407" => "Mediterranean",
        "795" => "Northern Europe",
        "793" => "Transatlantic",
        "1465" => "Western Europe",
        "26" => "Hawaii",
        "22" => "Latin America",
        "7" => "Mexico",
        "56" => "Baja California",
        "1977" => "Gulf of Mexico",
        "411" => "Mexican Riviera",
        "240" => "Panama Canal",
        "1296" => "South America",
        "25" => "South Pacific",
        "89" => "Australia",
        "848" => "New Zealand",
        "579" => "Tahiti",
        "6" => "Western U.S.",
        "1896" => "Pacific Coast",
    );
    private $cruiseLine = array(
        "12" => "Any cruise line",
        "2" => "Carnival Cruise Line",
        "3" => "Celebrity Cruises",
        "13" => "Costa Cruises",
        "4" => "Crystal Cruises",
        "19" => "Cunard Line Ltd.",
        "5" => "Disney Cruise Line",
        "6" => "Holland America Cruise Line",
        "7" => "Norwegian Cruise Line",
        "8" => "Princess Cruises",
        "15" => "Radisson Seven Seas",
        "11" => "Royal Caribbean",
        "16" => "Seabourn Cruise Line",
        "17" => "Silversea Cruises",
    );
    private $cruiseLength = array(
        "1" => "Any Cruise Length",
        "2" => "1-2 Nights",
        "3" => "3-6 Nights",
        "4" => "7-9 Nights",
        "5" => "10-15 Nights",
        "6" => "15 and more Nights",
    );
    private $cruiseMonth;
		
	
	function __construct($controller, $atts) {
		$this->controller	= $controller;
		/**	@note	tg_searchboxes_attributes filter was added to make available custom searchbox filling
				note that for admin interface no hook is yet added to filter search details
			@date	2013-SEP-04
			@author	Tibi	*/
		if (is_admin())
			$this->atts	= $atts;
		else
			$this->atts	= apply_filters('tg_searchboxes_attributes', $atts);
		/**	make the subID hookable - only for internal uses	*/
		$this->subID		= apply_filters('tg_searchboxes_subID', $this->defaultSubID);
		/**	current number of boxes needed for incremental IDs for inputs / labels	*/
		self::$nrOfBoxes++;

        $this->cruiseMonth = array(
            '' => 'Any Month'
        );
        for($i = 0; $i<12; $i++) {
            $time = strtotime("+{$i} months");
            $value = date("Ym", $time);
            $output = date("M-Y", $time);
            $this->cruiseMonth[$value] = $output;
        }
        if (!isset($this->atts['ajaxSettings'])) {
            $this->atts['ajaxSettings'] = null;
        }
        if (!isset($this->atts['defaultSettings'])) {
            $this->atts['defaultSettings'] = null;
        }
		return;
	}
	
	/**	@note	method that's used to generate the <script> tag for the JS file that creates the searchbox
			used if `usejavascript` option is true
		@date	2013.04.23
		@author	Tibi	*/	
	function renderJavaScript(){
		$queryString	= 'tgsb_command=js_searchbox';
		$atts	= $this->atts;
		// `usejavascript` option shouldn't be sent to JS file
		unset($atts['usejavascript']);
		// eliminating some default values that shouldn't be sent to JS file
		if ($atts['alignment']==$this->defaultAlignment)
			unset($atts['alignment']);
		if ($atts['size']==$this->defaultSize)
			unset($atts['size']);
		foreach($atts as $name => $value){
			// default values shouldn't be sent to JS file
			if ($value == $this->controller->options[$name])
				continue;
			$queryString.= '&'. urlencode($name) .'='. urlencode($value);
		}
		
		/*	@note	subID comparison to default subID added because subID was not transfered to the JS file and custom subID was lost when SB loaded from JS
			@date	2013-JUL-10
			@author	Tibi	*/
		if ($this->defaultSubID!=$this->subID)
			$queryString	.= '&subID='. (int)$this->subID;
		
		$queryString	.= '&tgsbPlaceholder=tgsb_'.self::$nrOfBoxes;
		//the link to the javascript file (php that generates JS code)
		$jsLink		= plugins_url('/js/searchbox.js.php?'.$queryString, TG_SEARCHBOXES__FILE__);
		$script		= '<script type="text/javascript" src="'.$jsLink.'"></script>';
		$script		= '<script type="text/javascript">'.
					'var s= document.createElement("script");'.
					's.type= "text/javascript";'.
					's.src= "'.$jsLink.'";'.
					's.async=true;'.
					'var h=document.head?document.head:document.getElementsByTagName("head")[0];'.
					'h.appendChild(s);'.
				'</script>';
		// will be REPLACED with the searchbox
		$placeholder	= '<span class="tgsbPlaceholder" id="tgsb_'.self::$nrOfBoxes.'"></span>';
		return $placeholder.$script;
	}
	
	function renderSearchboxes() {
		if(empty($this->atts)) {
			foreach($this->controller->options as $option => $optionValue) {
				$this->atts[$option] = $optionValue;
			}
			$this->atts['size']		= $this->defaultSize;
			$this->atts['alignment']	= $this->defaultAlignment;
		}
		if(!empty($this->atts['options'])) {
			$optionsAtts			= json_decode($this->atts['options'], true);
			/**	@note	Options was copied entirely over atts, but we should copy it only by value to prevent the removal of variables that are not present in `options`
				@date	2013-SEP-04
				@author	Tibi	*/
			//$this->atts = $optionsAtts;
			foreach($optionsAtts as $k => $v)
				$this->atts[$k]		= $v;
			unset($this->atts['options']);
		}
		/* on admin section we shouldn't use JS since the JS hadnling is not included on these pages */
		if (is_admin())
			$this->atts['usejavascript']	= false;
		/* if the usejavascript flag is not set at all, we use the default settings */
		if (!isset($this->atts['usejavascript']))
			$this->atts['usejavascript']	= $this->controller->options['usejavascript'];
		/* if the usejavascript flag is active, instead of returning the searchbox HTML, we return the placeholder & script tag that will load the searchbox via JS */
		if ($this->atts['usejavascript']){
			return $this->renderJavaScript();
		}
		if(empty($this->atts['size'])) {
			$this->atts['size']		= $this->defaultSize;
		}
		
		if(empty($this->atts['selectedTab'])) {
			$this->atts['selectedTab']	= $this->defaultSelectedTab;
		}
		
		if(empty($this->atts['alignment'])) {
			$this->atts['alignment']	= $this->defaultAlignment;
		}

		$departureDateTimestamp = strtotime('+'.$this->controller->options['departure_date']);
		$departureDate = date($this->controller->options['date_format'], $departureDateTimestamp);
		$returnDate = date($this->controller->options['date_format'], strtotime($this->controller->options['return_date'], $departureDateTimestamp));
		
		if(!empty($this->atts['departure_date']) || !empty($this->atts['return_date'])) {

			// default departure timestamp and return timestamp were set if one of those attributes are present in the shortcode
			$setDepartureDateTimestamp = 0;
			$setReturnDateTimestamp = 0;
		}
		if(!empty($this->atts['departure_date'])) {
			$date = $this->getDateDetails($departureDate);
			$nowDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// returning the departure date shortcode attribute in an array(year, month, day, y/m/d)
			$date = $this->getDateDetails($this->atts['departure_date']);
			// setting the departure date timestamp
			$setDepartureDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// if the departure date timestamp is bigger then the current timestamp then set the departure date from the shortcode's attribute
			if($setDepartureDateTimestamp > $nowDateTimestamp) {
				$departureDate	= $this->atts['departure_date'];
			/**	@note	if we don't use the dep. date specified as param, we shouldn't use it later to
					determine if the return date is correct or not - later we will use the default
					departure date to validate the return date
				@date	2013.03.06
				@author	Tibi	*/
			} else {
				$setDepartureDateTimestamp	= 0;
			}
		}
		if(!empty($this->atts['return_date'])) {
			// if the departure date timestamp is empty
			if(empty($setDepartureDateTimestamp)) {
				// returning the departure date set value in an array(year, month, day, y/m/d)
				$date = $this->getDateDetails($departureDate);
				// setting the departure date timestamp
				$setDepartureDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			}
			// returning the return date shortcode attribute in an array(year, month, day, y/m/d)
			$date = $this->getDateDetails($this->atts['return_date']);
			// setting the return date timestamp
			$setReturnDateTimestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			// if the return date timestamp is bigger then the departure date timestamp set the return date from the shortcode's attribute
			if($setReturnDateTimestamp > $setDepartureDateTimestamp) {
				$returnDate	= $this->atts['return_date'];
			}
		}
        $this->valuesToBeSet = array();
        $this->valuesToBeSet['from_air'] = empty($this->atts['from_air'])
            ? $this->controller->options['from_air']
			: $this->atts['from_air'];
        $this->valuesToBeSet['to_air'] = empty($this->atts['to_air'])
            ? $this->controller->options['to_air']
			: $this->atts['to_air'];
        $this->valuesToBeSet['hotel_city'] = empty($this->atts['hotel_city'])
            ? (empty($this->controller->options['hotel_city']) ? $this->valuesToBeSet['to_air'] : $this->controller->options['hotel_city'])
            : $this->atts['hotel_city'];
        $this->valuesToBeSet['departure_date'] = $departureDate;
        $this->valuesToBeSet['return_date'] = $returnDate;
        $this->valuesToBeSet['adults'] = empty($this->atts['adults'])
			? $this->controller->options['adults']
			: $this->atts['adults'];
        $this->valuesToBeSet['kids'] = !isset($this->atts['kids'])
			? $this->controller->options['kids']
            : $this->atts['kids'];
        $this->valuesToBeSet['seniors'] = !isset($this->atts['seniors'])
            ? $this->controller->options['seniors']
            : $this->atts['seniors'];
        $this->valuesToBeSet['rooms'] = empty($this->atts['rooms'])
            ? $this->controller->options['rooms']
            : $this->atts['rooms'];
        $this->valuesToBeSet['rtow'] = !isset($this->atts['rtow'])
            ? $this->controller->options['rtow']
            : $this->atts['rtow'];
        $this->valuesToBeSet['cruiseline'] = !isset($this->atts['cruiseline'])
            ? $this->controller->options['cruiseline']
            : $this->atts['cruiseline'];
        $this->valuesToBeSet['destination'] = !isset($this->atts['destination'])
            ? $this->controller->options['destination']
            : $this->atts['destination'];
        $this->valuesToBeSet['length_of_stay'] = !isset($this->atts['length_of_stay'])
            ? $this->controller->options['length_of_stay']
            : $this->atts['length_of_stay'];
		$this->valuesToBeSet['month_year'] = !isset($this->atts['month_year'])
            ? $this->controller->options['month_year']
            : $this->atts['month_year'];
		
		if (!isset($output)) {
			$output = '';
		};
		
		if (!isset($this->atts['defaultSettings'])) {
			$this->atts['defaultSettings'] = false;
		}
		
		$output .=	'<div class="tg_searchbox '.$this->atts['alignment'].' m'.$this->atts['size'].'" id="tgsb_'.self::$nrOfBoxes.'">';
		$output .=		'<ul class="tg_tabs">';
		$output .=			'<li><span class="flights'.(($this->atts['selectedTab'] == 'flights') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Air' : 'Flights').'</span></li>';
		$output .=			'<li><span class="hotels'.(($this->atts['selectedTab'] == 'hotels') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Hotel' : 'Hotels').'</span></li>';
		$output .=			(($this->atts['size']=='160x600') ? '' : '<li><span class="packages'.(($this->atts['selectedTab'] == 'packages') ? ' sel' : '').'">Packages</span></li>');
		$output .=			'<li><span class="cars'.(($this->atts['selectedTab'] == 'cars') ? ' sel' : '').'">'.(($this->atts['size']=='160x600') ? 'Car' : 'Cars').'</span></li>';
        $output .=			(($this->atts['size']=='160x600' || $this->atts['size'] == '728x90') ? '' : '<li><span class="cruises'.(($this->atts['selectedTab'] == 'cruises') ? ' sel' : '').'">Cruises</span></li>');
		$output .=		'</ul>';
		$output .=		'<div class="tg_container">';
		$output .=			($this->atts['defaultSettings']) ? '<div class="flights sel">' : '<form method="post" action="" class="flights'.(($this->atts['selectedTab'] == 'flights') ? ' sel' : '').'">';
		$output .=			$this->renderFlightsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		$output .=			($this->atts['defaultSettings']) ? '<div class="hotels">' : '<form class="hotels'.(($this->atts['selectedTab'] == 'hotels') ? ' sel' : '').'" method="post" action="">';
		$output .=			$this->renderHotelsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		
		if($this->atts['size'] != '160x600') {
			// we don't have the packages searchbox on the 160x600  box
			$output .=			($this->atts['defaultSettings']) ? '<div class="packages">' : '<form class="packages'.(($this->atts['selectedTab'] == 'packages') ? ' sel' : '').'" method="post" action="">';
			$output .=			$this->renderPackagesSearchbox();
			$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
		}
		
		$output .=			($this->atts['defaultSettings']) ? '<div class="cars">' : '<form class="cars'.(($this->atts['selectedTab'] == 'cars') ? ' sel' : '').'" method="post" action="">';
		$output .=			$this->renderCarsSearchbox();
		$output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';

        if($this->atts['size'] != '160x600' && $this->atts['size'] != '728x90') {
            // we don't have the cruises searchbox on the 160x600  box
            $output .=			($this->atts['defaultSettings']) ? '<div class="cruises">' : '<form class="cruises'.(($this->atts['selectedTab'] == 'cruises') ? ' sel' : '').'" method="post" action="">';
            $output .=			$this->renderCruisesSearchbox();
            $output .=			($this->atts['defaultSettings']) ? '</div>' : '</form>';
        }

		// the "get this widget" link will be present only on the Dynamic Sized Box 
		$output .=			'<div class="pwr">travel search by '.
						($this->controller->options['links']
                            ? '<a href="http://www.travelgrove.com/">Travelgrove</a>'
                            : 'Travelgrove'
                        );
		if($this->atts['size'] == 'dynamic' && $this->controller->options['links'])
			$output .=		' (<a class="wdg" href="http://labs.travelgrove.com/wordpress-plugins/travel-search/">get this widget</a>)';
		$output .=			'</div>';
		$output .=		'</div>';
		$output .=	'</div>';
		return $output;
	}
	
	function renderFlightsSearchbox() {
		$output = array(
					'160x600'	=> 'renderFlightsSearchbox160x600', 
					'300x250'	=> 'renderFlightsSearchbox300x250', 
					'300x533'	=> 'renderFlightsSearchbox300x533', 
					'728x90'	=> 'renderFlightsSearchbox728x90',
					'dynamic'	=> 'renderFlightsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		return $this->$method();	
	}
	
	function renderHotelsSearchbox() {
		$output = array(
					'160x600'	=> 'renderHotelsSearchbox160x600', 
					'300x250'	=> 'renderHotelsSearchbox300x250', 
					'300x533'	=> 'renderHotelsSearchbox300x533', 
					'728x90'	=> 'renderHotelsSearchbox728x90',
					'dynamic'	=> 'renderHotelsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		return $this->$method();
	}
	
	function renderPackagesSearchbox() {
		$output = array( 
					'300x250'	=> 'renderPackagesSearchbox300x250', 
					'300x533'	=> 'renderPackagesSearchbox300x533', 
					'728x90'	=> 'renderPackagesSearchbox728x90',
					'dynamic'	=> 'renderPackagesSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output))
			return '';
		return $this->$method();
	}

    function renderCruisesSearchbox() {
        $output = array(
            '300x250'	=> 'renderCruisesSearchbox300x250',
            '300x533'	=> 'renderCruisesSearchbox300x533',
            '728x90'	=> 'renderCruisesSearchbox728x90',
            'dynamic'	=> 'renderCruisesSearchboxDynamic'
        );
        $method = $output[$this->atts['size']];
        if(!array_key_exists($this->atts['size'], $output))
            return '';
        return $this->$method();
    }
	
	function renderCarsSearchbox() {
		$output = array(
					'160x600'	=> 'renderCarsSearchbox160x600', 
					'300x250'	=> 'renderCarsSearchbox300x250', 
					'300x533'	=> 'renderCarsSearchbox300x533', 
					'728x90'	=> 'renderCarsSearchbox728x90',
					'dynamic'	=> 'renderCarsSearchboxDynamic'
			);
		$method = $output[$this->atts['size']];
		if(!array_key_exists($this->atts['size'], $output) || !method_exists($this, $method))
			return '';
		$carsSearchbox = $this->$method();
		return $carsSearchbox;
	}
	



/* Flights Renderisation Functions */

private function renderFlightsSearchbox160x600() {
			$output = 
			'<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input class="oneway" type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
			$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
			'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
			$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
			'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />');
			$output .= '<br />
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}

	private function renderFlightsSearchbox300x250() {
			$output = '<div class="formContent">';
			$output .= '<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				
			$output .= $this->renderSubmitButton();
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}
	
	private function renderFlightsSearchbox300x533() {
			$output = 
			'<input type="radio" id="tgsb_'.self::$nrOfBoxes.'_rt" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" id="tgsb_'.self::$nrOfBoxes.'_ow" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}

	private function renderFlightsSearchbox728x90() {
        $output = "";
		$output .= '<div class="formContent">';
		$output .= '<span>
			<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
			<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
		</span>
		<span>
			<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_f') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('flights_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_f', false) : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '</span>
			<span class="akstt">	
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>
			<span class="s">	
				<label for="tgsb_'.self::$nrOfBoxes.'_rtow">Trip type:</label>';	
				$output .= '<select class="tt" name="oneway" id="tgsb_'.self::$nrOfBoxes.'_rtow">';
					$output .= '<option value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' selected="selected"' : '').'>round-trip</option>';
					$output .= '<option value="on" '.((!empty($this->valuesToBeSet['rtow'])) ? ' selected="selected"' : '').'>oneway</option>';			
				$output .= '</select>
			</span>
			</span>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= '<div class="mrcList nod"></div>';
			$output .= $this->renderSubmitButton();
			// removing the spaces between the tags
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}
	
	private function renderFlightsSearchboxDynamic() {
		if (!isset($this->atts['ajaxSettings'])) {
			$this->atts['ajaxSettings'] = false;
		}
			$output = '<div class="formContent">';
			$output .= '<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_rt" value=""'.((empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_rt">Roundtrip</label>
			<input type="radio" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_oneway]' : 'oneway').'" id="tgsb_'.self::$nrOfBoxes.'_ow" value="on"'.((!empty($this->valuesToBeSet['rtow'])) ? ' checked="checked"' : '').' /><label class="radio" for="tgsb_'.self::$nrOfBoxes.'_ow">One Way</label>
			<div class="hr"></div>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_f">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'inp_dep_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_f" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_f">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'inp_arr_arp_cd_1')).'" id="tgsb_'.self::$nrOfBoxes.'_to_f" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			</span><br />
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_f">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_f" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_f">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_f" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" '.((!empty($this->valuesToBeSet['rtow'])) ? ' disabled="disabled"' : '').' />';
				$output .= '
			</span><br />
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_f">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_f', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_f">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'inp_child_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_children_f', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_f">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[flights_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'inp_senior_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_seniors_f', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_class_f">Class:</label>';
				$output .= $this->createSelectTag('class', 'tgsb_'.self::$nrOfBoxes.'_class_f', 'tt', $this->flightsClass, 'Economy', true);
				$output .='</span>';
				
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';				
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
			$output .= $this->renderSubmitButton();
			$output .= '<div class="spcr"></div>';
			$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
			return $output;
	}	
	
/* Hotels Renderisation Functions */	

private function renderHotelsSearchbox160x600() {
		$output =
		'<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
		<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['hotel_city']).'" class="tgsb_addASH asTo" />
		<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
		'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
		$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
		'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
		$output .= '<span class="s">
			<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
		<span class="s">
			<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= '<div class="help">&nbsp;</div>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}


	private function renderHotelsSearchbox300x250() {
		$output = '<div class="formContent">';
		$output .='<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['hotel_city']).'" class="tgsb_addASH asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
		$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
		$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
		$output .= '</span>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderHotelsSearchbox300x533() {
		$output =
		'<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['hotel_city']).'" class="tgsb_addASH asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
				$output .= '</span>';

		$output .= '<div class="mrcList nod"></div>';
		$output .= '<div class="help">&nbsp;</div>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}
	
	private function renderHotelsSearchbox728x90() {
        $output = "";
		$output .= '<div class="formContent">';
		$output .= '<div>
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['hotel_city']).'" class="tgsb_addASH asTo" /><br />
				<span>
					<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_h') : 
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
				<span class="r">
					<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('hotels_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_h', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '</span>
			</div>
			<span class="akstt">
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span></span>';

		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '<div class="spcr">&nbsp;</div>';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		$output .= '<div class="spcr">&nbsp;</div>';
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}

	private function renderHotelsSearchboxDynamic() {
		$output = '<div class="formContent">';
		$output .='
				<label for="tgsb_'.self::$nrOfBoxes.'_city_h">City:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'airport')).'" id="tgsb_'.self::$nrOfBoxes.'_city_h" value="'.esc_attr($this->valuesToBeSet['hotel_city']).'" class="tgsb_addASH asTo" />
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_h">Check-In:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'start_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_h" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
		$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_h">Check-Out:</label>';
		$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'end_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_h" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
		$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_h">Adults:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'inp_adult_pax_cnt')), 'tgsb_'.self::$nrOfBoxes.'_adults_h', 'adults', $this->adults, $this->valuesToBeSet['adults']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_h">Rooms:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_room')), 'tgsb_'.self::$nrOfBoxes.'_rooms_h', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
		$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_h">Kids:</label>';
		$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[hotels_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'no_child')), 'tgsb_'.self::$nrOfBoxes.'_children_h', 'kids', $this->kids, $this->valuesToBeSet['kids']);
		$output .= '</span>';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
		$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
		$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	
/* Packages Renderisation Functions */

	/* we don't have a 160x600 packages searchbox as I saw the screenshot got from Peter */
	
	private function renderPackagesSearchbox300x250() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderPackagesSearchbox300x533() {
		$output = 
		'<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1'/*'inp_dep_arp_cd_1'*/)).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr'/*'inp_arr_arp_cd_1'*/)).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_depeparture_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_p') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_p', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults'/*'inp_adult_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'no_rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens'/*'inp_child_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors'/*'inp_senior_pax_cnt'*/)), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= '<div class="mrcList nod"></div>';
				$output .= '<div class="help">&nbsp;</div>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	
	private function renderPackagesSearchbox728x90() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_depeparture_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_p') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('packages_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_p', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			<span class="akstt">
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span></span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderPackagesSearchboxDynamic() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_p">From:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbFromAir' : 'city1')).'" id="tgsb_'.self::$nrOfBoxes.'_from_p" value="'.esc_attr($this->valuesToBeSet['from_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_p">To:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'arr')).'" id="tgsb_'.self::$nrOfBoxes.'_to_p" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_p">Depart:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_p" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_p">Return:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_p" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_adults_p">Adults:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_adults]' : (($this->atts['ajaxSettings']) ? 'tgsbAdults' : 'adults')), 'tgsb_'.self::$nrOfBoxes.'_adults_p', 'adults', $this->adults, $this->valuesToBeSet['adults']);
				$output .='</span>
			<span class="s rm">
				<label for="tgsb_'.self::$nrOfBoxes.'_rooms_p">Rooms:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_rooms]' : (($this->atts['ajaxSettings']) ? 'tgsbRooms' : 'rooms')), 'tgsb_'.self::$nrOfBoxes.'_rooms_p', 'rooms', $this->rooms, $this->valuesToBeSet['rooms']);
				$output .= '
			</span>
			<span class="s kd">
				<label for="tgsb_'.self::$nrOfBoxes.'_children_p">Kids:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_kids]' : (($this->atts['ajaxSettings']) ? 'tgsbKids' : 'childrens')), 'tgsb_'.self::$nrOfBoxes.'_children_p', 'kids', $this->kids, $this->valuesToBeSet['kids']);
			$output .= '</span>
			<span class="s">
				<label for="tgsb_'.self::$nrOfBoxes.'_seniors_p">Seniors:</label>';
				$output .= $this->createSelectTag((($this->atts['defaultSettings']) ? 'tg_searchboxes_options[packages_seniors]' : (($this->atts['ajaxSettings']) ? 'tgsbSeniors' : 'seniors')), 'tgsb_'.self::$nrOfBoxes.'_seniors_p', 'seniors', $this->seniors, $this->valuesToBeSet['seniors']);
				$output .= '</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
				$output .= '</div>';
				$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
				$output .= $this->renderSubmitButton();	
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}

/* Cars Renderisation Functions */

	private function renderCarsSearchbox160x600(){
		$output = '
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" />
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
			$output .= '<div class="mrcList nod"></div>';
			$output .= '<div class="help">&nbsp;</div>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}


	private function renderCarsSearchbox300x250(){

		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderCarsSearchbox300x533(){
		$output = '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>
			';
			$output .= '<div class="mrcList nod"></div>';
			$output .= '<div class="help">&nbsp;</div>';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);		
		return $output;
	}
	
	private function renderCarsSearchbox728x90() {
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" /><br />
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>			
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_departure_date', 'tgsb_'.self::$nrOfBoxes.'_dep_cal_c') :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />');
			$output .= '<br />
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= (($this->atts['defaultSettings']) ? $this->date_input('cars_return_date', 'tgsb_'.self::$nrOfBoxes.'_arr_cal_c', false) :
				'<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />');
				$output .= '<br />
			</span>';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
				$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
				$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
		$output .= '</div>';
		$output .= '<div class="mrcList nod"></div>';		
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}
	
	private function renderCarsSearchboxDynamic(){
		$output = '<div class="formContent">';
		$output .= '<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_from_c">Pick-Up:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_from_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'where')).'" id="tgsb_'.self::$nrOfBoxes.'_from_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asFrom" />
			</span>
			<span class="r">			
				<label for="tgsb_'.self::$nrOfBoxes.'_to_c">Drop-Off:</label>
				<input type="text" name="'.(($this->atts['defaultSettings']) ? 'tg_searchboxes_options[cars_to_air]' : (($this->atts['ajaxSettings']) ? 'tgsbToAir' : 'cr_drp_off_cty_name')).'" id="tgsb_'.self::$nrOfBoxes.'_to_c" value="'.esc_attr($this->valuesToBeSet['to_air']).'" class="tgsb_addAS asTo" /><br />
			</span>
			<span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dep_cal_c">Pick-Up Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbDepartureDate' : 'dep_date').'" id="tgsb_'.self::$nrOfBoxes.'_dep_cal_c" value="'.esc_attr($this->valuesToBeSet['departure_date']).'" class="tgsb_addDP depDate" />';
			$output .= '</span>
			<span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_arr_cal_c">Drop-Off Date:</label>';
				$output .= '<input type="text" readonly="readonly" name="'.(($this->atts['ajaxSettings'] || $this->atts['defaultSettings']) ? 'tgsbReturnDate' : 'arr_date').'" id="tgsb_'.self::$nrOfBoxes.'_arr_cal_c" value="'.esc_attr($this->valuesToBeSet['return_date']).'" class="tgsb_addDP retDate" />';
				$output .= '<br />
			</span>
			';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
			$output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
			$output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
			$output .= '</div>';
			$output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
		$output .= $this->renderSubmitButton();
		// removing the spaces between the tags
		$output = preg_replace('/>[\s\t\r\n]+</','><',$output);
		return $output;
	}


    private function renderCruisesSearchbox300x250() {
        $output = '<div class="formContent">';

        $output .= $this->getCruisesSelectsHtml();

        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
        $output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
        $output .= '</div>';
        $output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
        $output .= $this->renderSubmitButton();
        // removing the spaces between the tags
        $output = preg_replace('/>[\s\t\r\n]+</','><',$output);
        return $output;
    }

    private function renderCruisesSearchbox300x533() {
        $output = "";

        $output .= $this->getCruisesSelectsHtml();

        $output .= '<div class="mrcList nod"></div>';
        $output .= '<div class="help">&nbsp;</div>';
        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
        $output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
        $output .= $this->renderSubmitButton();
        // removing the spaces between the tags
        $output = preg_replace('/>[\s\t\r\n]+</','><',$output);
        return $output;
    }


    private function renderCruisesSearchbox728x90() {
        $output = '<div class="formContent">';

        $output .= $this->getCruisesSelectsHtml();

        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
        $output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
        $output .= '</div>';
        $output .= '<div class="mrcList nod"></div>';
        $output .= $this->renderSubmitButton();
        // removing the spaces between the tags
        $output = preg_replace('/>[\s\t\r\n]+</','><',$output);
        return $output;
    }

    private function renderCruisesSearchboxDynamic() {
        $output = '<div class="formContent">';

        $output .= $this->getCruisesSelectsHtml();

        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="idReferral" value="'.esc_attr($this->controller->options['id_referral']).'" />';
        $output .= ($this->atts['defaultSettings'] || $this->atts['ajaxSettings']) ? '' : '<input type="hidden" name="subID" value="'.esc_attr($this->subID).'" />';
        $output .= ($this->controller->options['adid']) ? '<input type="hidden" name="adid" value="'.esc_attr($this->controller->options['adid']).'" />' : '';
        $output .= '</div>';
        $output .= $this->atts['defaultSettings'] ? '' : '<div class="mrcList nod"></div>';
        $output .= $this->renderSubmitButton();
        // removing the spaces between the tags
        $output = preg_replace('/>[\s\t\r\n]+</','><',$output);
        return $output;
    }

    private function getCruisesSelectsHtml()
    {
        $s = $this->getCruiseSelects();
        $output = '
            <span>
				<label for="tgsb_'.self::$nrOfBoxes.'_line_c">Cruiseline:</label>
				' . $s->lineSelect . '
			</span>
            <span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_length_c">Length of stay:</label>
				' . $s->lengthSelect . '
			</span>
            <span>
				<label for="tgsb_'.self::$nrOfBoxes.'_dest_c">Destination:</label>
				' . $s->destSelect . '
			</span>
            <span class="r">
				<label for="tgsb_'.self::$nrOfBoxes.'_month_c">Month:</label>
				' . $s->monthSelect . '
			</span>
        ';
        return $output;
    }

    private function getCruiseSelects()
    {
        $ret = new StdClass();
        $ret->lineSelect = $this->createSelectTag(
            $this->atts['defaultSettings'] ? 'tg_searchboxes_options[cruiseline]' : 'cruiseline',
            'tgsb_'.self::$nrOfBoxes.'_line_c',
            'cruises',
            $this->cruiseLine,
            isset($this->valuesToBeSet['cruiseline']) ? $this->valuesToBeSet['cruiseline'] : '',
            /*useOptKeyAsValue:*/true
        );
        $ret->lengthSelect = $this->createSelectTag(
            $this->atts['defaultSettings'] ? 'tg_searchboxes_options[length_of_stay]' : 'length_of_stay',
            'tgsb_'.self::$nrOfBoxes.'_length_c',
            'cruises',
            $this->cruiseLength,
            isset($this->valuesToBeSet['length_of_stay']) ? $this->valuesToBeSet['length_of_stay'] : '',
            /*useOptKeyAsValue:*/true
        );
        $ret->destSelect = $this->createSelectTag(
            $this->atts['defaultSettings'] ? 'tg_searchboxes_options[destination]' : 'destination',
            'tgsb_'.self::$nrOfBoxes.'_dest_c',
            'cruises',
            $this->cruiseDest,
            isset($this->valuesToBeSet['destination']) ? $this->valuesToBeSet['destination'] : '',
            /*useOptKeyAsValue:*/true
        );
        $ret->monthSelect = $this->createSelectTag(
            $this->atts['defaultSettings'] ? 'tg_searchboxes_options[month_year]' : 'month_year',
            'tgsb_'.self::$nrOfBoxes.'_month_c',
            'cruises',
            $this->cruiseMonth,
            isset($this->valuesToBeSet['month_year']) ? $this->valuesToBeSet['month_year'] : '',
            /*useOptKeyAsValue:*/true
        );

        return $ret;
    }

	/**	HTML for the submit button	*/
	private function renderSubmitButton() {
		
		$button = array(
			'160x600'	=> '<input class="tgsb_submit_button" type="submit" value="compare prices" />', 
			'300x250'	=> '<input class="tgsb_submit_button" type="submit" value="search" />', 
			'300x533'	=> '<input class="tgsb_submit_button" type="submit" value="compare prices" />', 
			'728x90'	=> '<input class="tgsb_submit_button" type="submit" value="search" />',
			'dynamic'	=> '<input class="tgsb_submit_button" type="submit" value="search" />',
			);
			
		if(!empty($this->atts['defaultSettings']))
			return '<img src="'.plugins_url('/images/tg_searchboxes/search_button1.png', TG_SEARCHBOXES__FILE__).'" width="79" height="30" alt="compare" class="compareImage" />';
			
		if(!array_key_exists($this->atts['size'], $button) || empty($button[$this->atts['size']]))
			return '<input class="tgsb_submit_button" type="image" src="'.plugins_url('/images/tg_searchboxes/search_button1.png', TG_SEARCHBOXES__FILE__).'" />';
		return $button[$this->atts['size']];
	}

	/**	rendering the html code needed for a select tag	*/
	private function createSelectTag($selectTagName, $selectTagId, $selectTagClass, $options = array(), $selectedOption, $useOptKeyAsValue = false) {
		if(empty($options))
			return '';
		$output = '';
		foreach($options as $idx => $optionContent) {
			$output .= '<option value="'.esc_attr($useOptKeyAsValue ? $idx : $optionContent).'"'.(($selectedOption == ($useOptKeyAsValue ? $idx : $optionContent) ) ? ' selected="selected"' : '').'>'.esc_attr($optionContent).'</option>';
		}
		
		$output = '<select'.((!empty($selectTagName)) ? ' name="'.$selectTagName.'"' : '').((!empty($selectTagId)) ? ' id="'.$selectTagId.'"' : '').((!empty($selectTagClass)) ? ' class="'.$selectTagClass.'"' : '').' >'.$output.'</select>';
		return $output;
	}
	
	/**	setting the date input for the settings page	*/
	function date_input($optionName, $tagId, $departFlag = true) {
		if(empty($optionName))
			return false;
		$dr_dates = $departFlag ? $this->controller->departure_dates : $this->controller->return_dates;
        $output = "";
		$output .= "<select ".($departFlag ? "class='depDate'" : "class='retDate'")." name='tg_searchboxes_options[".$optionName."]' ".((!empty($tagId)) ? " id='".$tagId."'" : '').">";
		foreach($dr_dates as $dr_date) {
			$output .= "<option value='".esc_attr($dr_date)."'".(($this->controller->options[($departFlag ? 'departure_date' : 'return_date')] == $dr_date) ? " selected='selected'" : '').">".esc_attr($dr_date)."</option>";
		}
		$output .= "</select>";
		return $output;
	}
	
		/**
	 * gets an array with the details substracted from a date string
	 * @param String the date string MM/DD/YYYY if search_opt is us, DD/MM/YYYY otherwise
	 * @return Array with 3 entry, as follows: array(year, month, day, y/m/d)
	*/
	function getDateDetails($date){
		if($this->controller->options['date_format'] == 'm/d/Y') {
			if(!preg_match('/^([01]?[0-9])\/([0-3]?[0-9])\/(20[0-9]{2})$/',$date,$match))
				return false;
			return array((int)$match[3],(int)$match[1],(int)$match[2],"$match[1]/$match[2]/$match[3]");
		};
		if(!preg_match('/^([0-3]?[0-9])\/([01]?[0-9])\/(20[0-9]{2})$/',$date,$match))
			return false;
		return array((int)$match[3],(int)$match[2],(int)$match[1],"$match[1]/$match[2]/$match[3]");
	} 

	function __destruct() {
		unset($this->atts);
	}
}
