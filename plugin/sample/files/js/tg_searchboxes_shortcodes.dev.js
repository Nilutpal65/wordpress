/*
 * WHAT & WHY: js file used to generate the shortcodes on Shortcode Generator section on the Settings page
*/
if(typeof(TG_Searchboxes_Variables) != 'undefined' && typeof(TG_Searchboxes_Variables.tgsbDefaultSettings) != 'undefined') {
        if(TG_Searchboxes_Variables.tgsbDefaultSettings.match(/^\{\&quot\;/))
                TG_Searchboxes_Variables.tgsbDefaultSettings = TG_Searchboxes_Variables.tgsbDefaultSettings.replace(/\&quot\;/ig, '"');
        TG_Searchboxes_Variables.tgsbDefaultSettings = eval("("+TG_Searchboxes_Variables.tgsbDefaultSettings+")");
};
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
		if(field.name == 'tgsbToAir') {
			// if the "to" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.to_air) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"to_air":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

function tgsbHotelCity(serializedFieldsArray) {
    // if the serialized array is not defined or it's length is 0 the return an empty string
    if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
        return '';
    var returnValue = '';
    jQuery.each(serializedFieldsArray, function(i, field){
        // checking for the "to" field and it's value in the serialized array
        if(field.name == 'tgsbToAir' && jQuery(field).parents('form.hotels').length>0) {
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
		if(field.name == 'tgsbDepartureDate') {
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
		if(field.name == 'tgsbReturnDate') {
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
		if(field.name == 'tgsbAdults') {
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
		if(field.name == 'tgsbKids') {
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
		if(field.name == 'tgsbSeniors') {
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
		if(field.name == 'tgsbRooms') {
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
		if(field.name == 'oneway') {
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


/* WHAT & WHY: function used to generate the shortcode */
function generateShortcode() {
        var tgSearchboxMeasures = jQuery('#tgsb_shortcodeGenerator ul.measuresChooser li a.current').text();
        tgSearchboxMeasures = (tgSearchboxMeasures.length == 0) ? '300x250' : tgSearchboxMeasures;
        var selectedTab = jQuery('#tgsb_shortcodeGenerator .sb'+tgSearchboxMeasures+' .tg_searchbox .tg_container').find('form.sel').attr('class').match(/^(flights|hotels|cars|packages|cruises)/);
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
	// Tibi | 2013.04.23 | checking if we have to set the flag that marks that we have to use JS load for the JS
	var loadFromJS	= jQuery('#tgsb_shortcodeGenerator #travelSearchShortcodeUseJavaScript').attr('checked');
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
                                ((selectedTab[1] == 'flights' ) ? '' : 									'"selectedTab":"'+selectedTab[1]+'"'
                                );
        if(optionsString.match(/\,$/)) {
                // if a comma is found on the end of the string then remove it
                optionsString = optionsString.replace(/\,$/, '');
        };
        // creating the shortcode string that will be added into the editor
        var tgSearchboxesShortcode = '[tg_searchboxes'+(optionsString.length ? ' options=\'{'+optionsString+'}\']' :']') ;
        if(typeof(tgSearchboxesShortcode) != 'undefined' && tgSearchboxesShortcode.length) {
                jQuery('#tgsb_shortcodeGenerator #tgsb_shortcode').val(tgSearchboxesShortcode);
                jQuery('#tgsb_shortcodeGenerator #tgsb_shortcode_php').val('<?php echo do_shortcode('+"'"+tgSearchboxesShortcode.replace(/\'/g, "\\\'")+"'"+'); ?>');
        };
        return true;
};

/* WHAT & WHY: autosuggestion options used for the searchboxes on the shortcode generator section */
var ASoptionsShortcodes = {
	delay: 175,
	timeout: 5000,
	script: TG_Searchboxes_Variables.str_ASAjaxURL,
	loadingClass: 'tgsb_as_load',
	className: 'tgsb_as tgsb_asMargin',
	json: true,
	frameForIE: true,
	ajaxParams: {
		action:		'',
		json:		true,
		lng:		'def',
		dsgn:		'flg',
		addtag:		'em',
		citytype:	'airports',
		maxResults:	10,
		domainPrefix:	true

	},
	autoSelect:true,
	offsety:0,
	format: function(selLiObj) {
		return selLiObj.innerHTML.replace(/<\/?[a-z]+>/gi,'').replace(/(.*),(.*)\((.*)\)/,'$1 ($3)');
	},
	callback:function(asElem,asObj) {
		var inp = jQuery(asObj.fld);
		if(inp.hasClass("asFrom")) {
			jQuery("#tgsb_shortcodeGenerator .asFrom").val(inp.val());
			inp.parents('#tgsb_shortcodeGenerator').find('.tg_container > form.cars').find('.asFrom').val('');
		};
		
		if(inp.hasClass("asTo")) {
			jQuery("#tgsb_shortcodeGenerator .asTo").val(inp.val());
			inp.parents('#tgsb_shortcodeGenerator').find('.tg_container > form.cars').find('.asFrom').val(inp.val());
		};
                generateShortcode();
	}
};

jQuery(function(){
        jQuery('#tgsb_shortcodeGenerator ul.measuresChooser li a').click(function(){
                // setting the measure from the anchor text
                measureChoosed = jQuery(this).text();
                // remove the class (current), from the a tag
                jQuery('#tgsb_shortcodeGenerator ul.measuresChooser li a').removeClass('current');
                // adding the class (current), to the a tag, which shows which element is choose
                jQuery(this).addClass('current');
                // removing the class (crnt) from the div containing the searchbox
                jQuery('div.tgsb').removeClass('crnt');
                // adding the class (crnt) to the div containing the searchbox with the choose measurement
                jQuery('div.sb'+measureChoosed).addClass('crnt');
                // generating the shortcode
                generateShortcode();
                return false;
        });
        
        jQuery('#tgsb_shortcodeGenerator div.tg_searchbox ul.tg_tabs li span').click(function(){
                // switching between the tabs of the searchbox
                selectedTab = jQuery(this).attr('class').match(/^[a-z]+/);
                var cont = jQuery(this).parents("div.tg_searchbox:eq(0)"); 
                cont.find('ul.tg_tabs li span').removeClass('sel');
                cont.find('ul.tg_tabs li span.'+selectedTab).addClass('sel');
                cont.find('div.tg_container form').removeClass('sel');
                cont.find('div.tg_container form.'+selectedTab).addClass('sel');
                generateShortcode();
                return false;
        });
        jQuery('#tgsb_shortcodeGenerator input.tgsb_submit_button').click(function() {
                // if the submit button of a searchbox on the shortcode generator section is clicked then alert with this message
                alert('Here you can set up how the box should appear inside your post.'+"\n"+
                        'To see this box in action hit Insert and update your post,'+"\n"+
                        'or visit the demo page of this plugin at:'+"\n"+
                        TG_Searchboxes_Variables.demoPage);
                return false;
        });
        jQuery('#tgsb_shortcodeGenerator select.adults').change(function() {
                // changing the value of the adult select on selected the searchboxe from the shortcode generator section
                var slct = jQuery(this);
                slct.parents('.tg_searchbox').find('.tg_container > form').find('.adults').val(slct.val());
                generateShortcode();
        });
        jQuery('#tgsb_shortcodeGenerator select.kids').change(function() {
                // changing the value of the kids select on selected the searchboxe from the shortcode generator section
                var slct = jQuery(this);
                slct.parents('.tg_searchbox').find('.tg_container > form').find('.kids').val(slct.val());
                generateShortcode();
        });
        jQuery('#tgsb_shortcodeGenerator select.seniors').change(function() {
                // changing the value of the seniors select on selected the searchboxe from the shortcode generator section
                var slct = jQuery(this);
                slct.parents('.tg_searchbox').find('.tg_container > form').find('.seniors').val(slct.val());
                generateShortcode();
        });
        jQuery('#tgsb_shortcodeGenerator select.rooms').change(function() {
                // changing the value of the rooms select on selected the searchboxe from the shortcode generator section
                var slct = jQuery(this);
                slct.parents('.tg_searchbox').find('.tg_container > form').find('.rooms').val(slct.val());
                generateShortcode();
        });
        jQuery("#tgsb_shortcodeGenerator .tgsb_addAS, #tgsb_shortcodeGenerator .tgsb_addASH").each(function(){
                jQuery(this).focus(inputFocus).blur(inputBlur);
                // initializing the autosuggestion for the (from, to) inputs on the shortcode generator section
                new AS(this.id,ASoptionsShortcodes);
        });
        jQuery('#tgsb_shortcodeGenerator .tg_searchbox form').each(function(){
                 // initializing the datepicker for the (departure date, return date) inputs on the shortcode generator section
                var inputs = jQuery(this).find(".tgsb_addDP");
                if (inputs.length>1) {
                    var i1 = inputs.get(0).id;
                    var i2 = inputs.get(1).id;
                    var currentForm = jQuery(this);
                    var rtowInputs = false;
                    var rtowInputs = currentForm.hasClass('flights') ? jQuery(this).find('input[name=oneway]') : false;
                    createDatepicker(i1, i2, rtowInputs, generateShortcode);
                };
        });
    jQuery('#tgsb_shortcodeGenerator select.cruises').change(function() {
        // changing the value of the kids select on selected the searchboxe from the shortcode generator section
        /*
        var slct = jQuery(this);
         */
        /*
        slct.parents('.tg_searchbox').find('.tg_container > form').find('.kids').val(slct.val());
         */
        generateShortcode();
    });
	jQuery("#travelSearchShortcodeUseJavaScript").click(generateShortcode);
});