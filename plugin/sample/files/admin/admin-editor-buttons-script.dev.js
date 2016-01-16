/* javascript file used when clicking the button on the HTML editor */
// getting the "from" field form the serialized array
function tgsbFromAir(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "from" field and it's value in the serialized array
		if(field.name == 'tgsbFromAir') {
			// if the "from" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.from_air) {
				// if it's value it's not equalto the default value set it as a return value
				returnValue = '"from_air":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "to" field form the serialized array
function tgsbToAir(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "to" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbToAir' && jQuery(field.obj).parents('form.hotels').length==0) {
			// if the "to" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.to_air) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"to_air":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
}
function tgsbHotelCity(serializedFieldsArray) {
    // if the serialized array is not defined or it's length is 0 the return an empty string
    if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
        return '';
    var returnValue = '';
    jQuery.each(serializedFieldsArray, function(i, field){
        // checking for the "to" field and it's value in the serialized array
        if(!returnValue && field.name == 'tgsbToAir' && jQuery(field.obj).parents('form.hotels').length>0 ) {
            // if the "to" field was found
            if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.hotel_city) {
                // if it's value it's not equal to the default value set it as a return value
                returnValue = '"hotel_city":"'+field.value+'"';
                return;
            }
        }
    });
    return returnValue;
}


// getting the "departure date" field form the serialized array
function tgsbDepartureDate(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "departure date" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbDepartureDate') {
			// if the "departure date" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.departure_date) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"departure_date":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "return date" field form the serialized array
function tgsbReturnDate(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "return date" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbReturnDate') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.return_date) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"return_date":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "adults" field form the serialized array
function tgsbAdults(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "adults" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbAdults') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.adults) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"adults":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "kids" field form the serialized array
function tgsbKids(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "kids" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbKids') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.kids) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"kids":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "seniors" field form the serialized array
function tgsbSeniors(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "seniors" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbSeniors') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.seniors) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"seniors":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "rooms" field form the serialized array
function tgsbRooms(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "rooms" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbRooms') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.rooms) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"rooms":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "roundtrip/oneway" field form the serialized array
function tgsbRTOW(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "rooms" field and it's value in the serialized array
		if(!returnValue && field.name == 'oneway') {
			field.value = (field.value == 'on') ? true : false;
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.rtow) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"rtow":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

function tgsbCruises(fields){
    var ret = "";
    jQuery.each(fields, function(idx, val){
        if (
            val.value &&
            val.value != TG_Searchboxes_Variables.tgsbDefaultSettings[val.name] &&
            (
            val.name == 'cruiseline'
            || val.name == 'length_of_stay'
            || val.name == 'destination'
            || val.name == 'month_year'
            )
        ) {
            ret += '"' + val.name + '":"' + val.value + '",';
        }
    });
    return ret;
}

// getting the value from the radio buttons used for the alignment of the box
function setAlignment() {
	// default value is alignnone
	var algn = 'alignnone';
	// finding the alignment radio buttons on the popup div
	var alignment = jQuery("#TB_ajaxContent").find('input[name=img_align]');
	// iterating the radio buttons
	for(i in alignment){
		// if the radio button is checked
		if(alignment[i].checked == true) {
			// then set it's value to the variable that holds the default value
			algn = alignment[i].value;
			break;
		};
	};
	// return the value
	return algn;
};


jQuery(document).ready(function(jQuery){
	var editor_toolbar=jQuery("#ed_toolbar");
	if(editor_toolbar){
		var theButton = document.createElement("input");
		theButton.type="button";
		theButton.value=TG_Searchboxes_Editor_Button.str_EditorButtonCaption;
		theButton.className="ed_button";
		theButton.title=TG_Searchboxes_Editor_Button.str_EditorButtonCaption;
		theButton.id="ed_button_tg_searchboxes";
		editor_toolbar.append(theButton);
		jQuery("#ed_button_tg_searchboxes").click(tg_searchboxes_button_click)
	};
	function tg_searchboxes_button_click(){
		var title = "Travelgrove Searchboxes";
		var url = TG_Searchboxes_Editor_Button.str_EditorButtonAjaxURL.replace(/\&amp\;/ig, '&');
		tb_show(title,url,false);
		jQuery("#TB_ajaxContent").width("auto").height("94.5%").click(function(event){
			var $target=jQuery(event.target);
			if($target.is("a.send_searchbox_to_editor") || $target.is('input.send_searchbox_to_editor')){
				var tgSearchboxMeasures = jQuery('ul.measuresChooser li a.current').text();
				tgSearchboxMeasures = (tgSearchboxMeasures.length == 0) ? '300x250' : tgSearchboxMeasures;
				var selectedTab = jQuery('.sb'+tgSearchboxMeasures+' .tg_searchbox .tg_container').find('form.sel').attr('class').match(/^(flights|hotels|cars|packages|cruises)/);
				var fields = [];
                jQuery('.sb'+tgSearchboxMeasures+' .tg_searchbox .tg_container form').find("input, select, textarea").each(function(){
                    if ((this.type == 'radio' || this.type == 'checkbox') && !this.checked) {
                        return;
                    }
                    fields.push({
                        name: this.name,
                        value: this.value,
                        obj: this
                    });
                });
				// marks if searchbox should be loaded with JS file or not | Tibi | 2013.04.23
				var loadFromJS	= jQuery('#travelSearchUseJavaScript').attr('checked');
				var optionsString = '';
				// getting the "from" value
				var tgsb_fromAir = tgsbFromAir(fields);
				// adding the "from" value to the options string
				optionsString += (tgsb_fromAir.length) ? tgsb_fromAir+',' : '';
                // getting the "to" value
                var tgsb_toAir = tgsbToAir(fields);
                // adding the "to" value to the options string
                optionsString += (tgsb_toAir.length) ? tgsb_toAir+',' : '';
                // getting the "to" value
                var tgsb_hotelCity = tgsbHotelCity(fields);
                // adding the "to" value to the options string
                optionsString += (tgsb_hotelCity.length) ? tgsb_hotelCity+',' : '';
				// getting the "departure date" value
                var tgsb_departureDate = tgsbDepartureDate(fields);
				// adding the "departure date" value to the options string
				optionsString += (tgsb_departureDate.length) ? tgsb_departureDate+',' : '';
				// getting the "return date" value
                var tgsb_returnDate = tgsbReturnDate(fields);
				// adding the "return date" value to the options string
				optionsString += (tgsb_returnDate.length) ? tgsb_returnDate+',' : '';
				// getting the "adults" value
                var tgsb_adults = tgsbAdults(fields);
				// adding the "adults" value to the options string
				optionsString += (tgsb_adults.length) ? tgsb_adults+',' : '';
				// getting the "kids" value
                var tgsb_kids = tgsbKids(fields);
				// adding the "kids" value to the options string
				optionsString += (tgsb_kids.length) ? tgsb_kids+',' : '';
				// getting the "seniors" value
                var tgsb_seniors = tgsbSeniors(fields);
				// adding the "seniors" value to the options string
				optionsString += (tgsb_seniors.length) ? tgsb_seniors+',' : '';
				// getting the "rooms" value
                var tgsb_rooms = tgsbRooms(fields);
				// adding the "seniors" value to the options string
				optionsString += (tgsb_rooms.length) ? tgsb_rooms+',' : '';
				// getting the "roundtrip/oneway" value
                var tgsb_rtow = tgsbRTOW(fields);
				// adding the "roundtrip/oneway" value to the options string
				optionsString += (tgsb_rtow.length) ? tgsb_rtow+',' : '';
                // adding the cruises parameters to the options string
                optionsString += tgsbCruises(fields);

        			// adding the flag that matks if SB should be loaded w/ JS or not value to the options string | Tibi | 2013.04.23
        			optionsString += loadFromJS ? '"usejavascript":"on",' : '';
				
				// if the box measures is "300x250" then it is not needed to add the size as option because that is considered the default value
				optionsString += (tgSearchboxMeasures == '300x250') ? '' : '"size":"'+tgSearchboxMeasures+'",';
				// if the selected tab is null the set the selected tab to flights
				
				optionsString += (selectedTab == null) ? '"selectedTab":"flights"' : 
				// if the selectedTab is set and it's flights then do not set it in the options because the flights selected tab is considered by default
							((selectedTab[1] == 'flights' ) ? '' : 	'"selectedTab":"'+selectedTab[1]+'"'
							);
				// setting the alignment
				alignment = setAlignment();
				// if the alignment is set to the default value the set an empty string
				optionsString += (alignment == 'alignnone') ? '' : '"alignment":"'+alignment+'"';
				
				if(optionsString.match(/\,$/)) {
					// if a comma is found on the end of the string then remove it
					optionsString = optionsString.replace(/\,$/, '');
				};
				// creating the shortcode string that will be added into the editor
				var tgSearchboxesShortcode = '[tg_searchboxes'+(optionsString.length ? ' options=\'{'+optionsString+'}\']' :']') ;
				send_to_editor(tgSearchboxesShortcode);
			};
			return false;
		});
		return false;
	};
});