/**	JS used on settings page where one can set up the default settings for the boxes	*/

// variable containing the departure date used to set the departure dates in the searchbox when a user selects an option from the default departure date select elem
var tgsb_departureDate = false;
// variable containing the return date used to set the return dates in the searchbox when a user selects an option from the default return date select elem
var tgsb_returnDate = false;

var ASoptions = {
    delay: 175,
    timeout: 5000,
    script: TG_Searchboxes_Settings.str_ASAjaxURL,
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
        domainPrefix:	true
    },
    autoSelect:true,
    offsety:0,
    format: function(selLiObj) {
        return selLiObj.innerHTML.replace(/<\/?[a-z]+>/gi,'').replace(/(.*),(.*)\((.*)\)/,'$1 ($3)');
    },
    callback:function(asElem,asObj){
        var inp = jQuery(asObj.fld);
        if(inp.hasClass("asFrom")) {
            inp.parents('.tg_searchbox').find('.tg_container > div:not(.cars)').find('.asFrom').val(inp.val());
        };

        if(inp.hasClass("asTo")) {
            jQuery(".asTo").val(inp.val());
            inp.parents('.tg_searchbox').find('.tg_container > div.cars').find('.asFrom').val(inp.val());
        };
    }

};

var hotelASoptions = {
    delay: 175,
    timeout: 5000,
    script: TG_Searchboxes_Settings.str_ASAjaxURL,
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
        citytype:	'cities',
        domainPrefix:	true,
        nearestAirport:	true
    },
    autoSelect:true,
    offsety:0,
    format: function(selLiObj) {
        return selLiObj.innerHTML.replace(/<\/?[^>]+>/gi,'').replace(/(.*),(.*) \((.*)\)/,'$1,$2');
    },
    callback:function(asElem,asObj){
        var inp = jQuery(asObj.fld);
        if(inp.hasClass("asTo")) {
            var airport = inp.val();
            for(var i=0; i<asElem.attributes.length; i++) {
                if (asElem.attributes[i].nodeName == 'airport') {
                    var val = asElem.attributes[i].value ? asElem.attributes[i].value : asElem.attributes[i].nodeValue;
                    if (val) {
                        try {
                            var arp = eval('(' + val + ')');
                            if (arp && arp.city && arp.iata) {
                                airport = arp.city + ' (' + arp.iata + ')';
                            }
                        } catch(e) {}
                    }
                }
            }
            jQuery(".asTo").not(inp).val(airport);
            inp.parents('#TB_ajaxContent').find('.tg_container > form.cars').find('.asFrom').val(airport);
        }
    }

};

function inputFocus(ev){
	if (this.value==this.defaultValue) this.value=''; // if default value present, removing it
	else this.select(); // if other than def. val. present, selecting it
};
function inputBlur(ev){
	if (this.value=='') this.value=this.defaultValue; // if input is empty, filling it up with default value
};

/* creating the datepicker */
// @param String i1 the content of the departure date input
// @param String i2 the content of the return date input
// @param object rtowInputs the object for the RT/OW radio buttons
// @param function callback used when changing the dates to call the function to generate a shortcode
function createDatepicker(i1,i2,rtowInputs,callback){
	if(typeof(callback) != 'function')
		callback = function(){};
	jQuery('#'+i1+', #'+i2).each(function(){
		var inp = jQuery(this);
		inp.datepicker({
			minDate: 0,
			maxDate: "2y",
			showOn: "both",
			// setting the datepicker image 
			buttonImage: TG_Searchboxes_Settings.str_CalendarURL,
			buttonImageOnly: true,
			dateFormat: TG_Searchboxes_Settings.str_dateFormat,
			// setting the date format 
			onSelect: function(date,dpObj){
				// d1 is the selected date on the datepicker
				d1 = jQuery(this).datepicker("getDate");
				if(this.id == i1) {
					// getting the date from the return date input
					d2 = jQuery.datepicker.parseDate( TG_Searchboxes_Settings.str_dateFormat, jQuery('#'+i2).val());
					// setting the minimum date of the return date to the date of the departure date
					jQuery("#"+i2).datepicker('option','minDate',d1);
					if(d2 && d1>d2) {
						// if the departure date is greater then the return date add 5 day to the departure date and set them to the return date
						d2.setTime(d1.getTime()+60*60*24*5*1000);
						jQuery("#"+i2).val(jQuery.datepicker.formatDate(TG_Searchboxes_Settings.str_dateFormat, d2));
						// the same date value is set to all inputs with the same class
						var cont = jQuery("#"+i2).parents("div.tg_searchbox:eq(0)");
						cont.find('input.retDate').val(jQuery.datepicker.formatDate(TG_Searchboxes_Settings.str_dateFormat, d2));
					};
				};
				var inputName = this.name;
				var cont = jQuery(this).parents("div.tg_searchbox:eq(0)");
				// the same date value is set to all inputs with the same name
				cont.find('input[name="'+inputName+'"]').val(date);
				callback();
				return;
			}
		});
	});
	// we need to check the oneway inputs because regarding this we'll enable or disable the return date input
	if(typeof(rtowInputs) == 'object' && rtowInputs.length > 0) {
		var rt = rtowInputs.get(0).id;
		var ow = rtowInputs.get(1).id;
		// if oneway radio button is checked then the return date input is disabled
		jQuery('#'+ow).change(function(){
			if(this.checked) {
				jQuery('#'+i2).attr('disabled',true);
				callback();
			};
		});
		// if the roundtrip input is checked then the return date input is enabled
		jQuery('#'+rt).change(function(){
			if(this.checked) {
				jQuery('#'+i2).attr('disabled',false);
				callback();
			};
		});
	};
};



/*
	WHAT & WHY: function used to set the date value according to the selected default options
*/
function setTgSBDates(currentDate, days, months, years){
	if(typeof(currentDate) == 'undefined' || !currentDate)
		return false;
	// if days, months or years are undefined then set them to false
	if(typeof(days) == 'undefined')	days			= false;
	if(typeof(months) == 'undefined') months		= false;
	if(typeof(years) == 'undefined') years			= false;
	// if no value was set for days, months or years
	if(!days && !months && !years)
		return false;
	// if a values was set for days	
	if(days) {
		days = parseInt(days).toString();
		// check it to be an int and set a new Date and return the date set
		if(days != 'NaN')
			return new Date(currentDate.getTime() + parseInt(days)*24*60*60*1000);
		return false;
	};
	// if a value was set for months
	if(months) {
		months = parseInt(months).toString();
		// check it to be an int and set a new Date and return the date set
		if(months != 'NaN')
			return new Date(currentDate.setMonth(currentDate.getMonth()+parseInt(months)));
		return false;
	};
	// if a value was set for years
	if(years) {
		years = parseInt(years).toString();
		// check it to be an int and set a new Date and return the date set
		if(years != 'NaN')
			return new Date(currentDate.setFullYear(currentDate.getFullYear()+parseInt(years)));
		return false;
	};
};

/*
	WHAT & WHY: function used to set the departure date
*/
function setDepartureDateToForm(currentSelectedDateValue) {
	// if the currentSelecteDateValue is not set then get that date from the default departure date select element
	if(typeof(currentSelectedDateValue) == 'undefined' || !currentSelectedDateValue)
		currentSelectedDateValue = jQuery('#tgsb_departure_date').val();
	// setting the current date
	var currentDate = new Date();
		
	if(currentSelectedDateValue == '1 day')
		tgsb_departureDate = setTgSBDates(currentDate, 1);
	if(currentSelectedDateValue == '2 days')
		tgsb_departureDate = setTgSBDates(currentDate, 2);
	if(currentSelectedDateValue == '3 days')
		tgsb_departureDate = setTgSBDates(currentDate, 3);
	if(currentSelectedDateValue == '4 days')
		tgsb_departureDate = setTgSBDates(currentDate, 4);
	if(currentSelectedDateValue == '5 days')
		tgsb_departureDate = setTgSBDates(currentDate, 5);
	if(currentSelectedDateValue == '6 days')
		tgsb_departureDate = setTgSBDates(currentDate, 6);
	if(currentSelectedDateValue == '1 week')
		tgsb_departureDate = setTgSBDates(currentDate, 7);
	if(currentSelectedDateValue == '2 weeks')
		tgsb_departureDate = setTgSBDates(currentDate, 14);
	if(currentSelectedDateValue == '3 weeks')
		tgsb_departureDate = setTgSBDates(currentDate, 21);
	if(currentSelectedDateValue == '4 weeks')
		tgsb_departureDate = setTgSBDates(currentDate, 28);
	if(currentSelectedDateValue == '1 month')
		tgsb_departureDate = setTgSBDates(currentDate, false, 1);
	if(currentSelectedDateValue == '2 months')
		tgsb_departureDate = setTgSBDates(currentDate, false, 2);
	if(currentSelectedDateValue == '3 months')
		tgsb_departureDate = setTgSBDates(currentDate, false, 3);
	if(currentSelectedDateValue == '1 year')
		tgsb_departureDate = setTgSBDates(currentDate, false, false, 1);
	if(tgsb_departureDate) {
		var cont = jQuery('.tg_searchbox');
		cont.find('input.depDate').val(jQuery.datepicker.formatDate(TG_Searchboxes_Settings.str_dateFormat, tgsb_departureDate));
	};
};

/*
	WHAT & WHY: function used to set the return date date
*/

function setReturnDateToForm(currentSelectedDateValue) {
	if(typeof(currentSelectedDateValue) == 'undefined' || !currentSelectedDateValue)
		currentSelectedDateValue = jQuery('#tgsb_return_date').val(); 
	if(!tgsb_departureDate)
		return false;
	// sets the return date according to the selected value and the current date	
	if(currentSelectedDateValue == '+1 day')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 1);
	if(currentSelectedDateValue == '+2 days')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 2);
	if(currentSelectedDateValue == '+3 days')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 3);
	if(currentSelectedDateValue == '+4 days')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 4);
	if(currentSelectedDateValue == '+5 days')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 5);
	if(currentSelectedDateValue == '+6 days')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 6);
	if(currentSelectedDateValue == '+1 week')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 7);
	if(currentSelectedDateValue == '+2 weeks')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 14);
	if(currentSelectedDateValue == '+3 weeks')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 21);
	if(currentSelectedDateValue == '+4 weeks')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, 28);
	if(currentSelectedDateValue == '+1 month')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, false, 1);
	if(currentSelectedDateValue == '+2 months')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, false, 2);
	if(currentSelectedDateValue == '+3 months')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, false, 3);
	if(currentSelectedDateValue == '+1 year')
		tgsb_returnDate = setTgSBDates(tgsb_departureDate, false, false, 1);
	if(tgsb_returnDate) {	
		var cont = jQuery('.tg_searchbox');
		cont.find('input.retDate').val(jQuery.datepicker.formatDate(TG_Searchboxes_Settings.str_dateFormat, tgsb_returnDate));
	};
};

jQuery(function(){
	
	jQuery(jQuery('#id_referral').get(0)).focus(inputFocus).blur(inputBlur);
    jQuery(".tgsb_addAS").each(function(){
//		jQuery(this).focus(inputFocus).blur(inputBlur);
        new AS(this.id,ASoptions);
    });
    jQuery(".tgsb_addASH").each(function(){
//		jQuery(this).focus(inputFocus).blur(inputBlur);
        new AS(this.id,hotelASoptions);
    });

	jQuery('div.tg_searchbox ul.tg_tabs li span').click(function(){
		selectedTab = jQuery(this).attr('class').match(/^[a-z]+/);
		var cont = jQuery(this).parents("div.tg_searchbox:eq(0)"); 
		cont.find('ul.tg_tabs li span').removeClass('sel');
		cont.find('ul.tg_tabs li span').attr('style', 'background-color:'+jQuery('#tbscolor').val()+' !important');
		cont.find('ul.tg_tabs li span.'+selectedTab).addClass('sel');
		cont.find('ul.tg_tabs li span.sel').attr('style', 'background-color:'+jQuery('#bgdcolor').val()+' !important');
		cont.find('div.tg_container div').removeClass('sel');
		cont.find('div.tg_container div.'+selectedTab).addClass('sel');
	});
	
	// changing the value of the select elem of the default departure date 
	jQuery('select.depDate').change(function() {
		var slct = jQuery(this);
		// setting the value selected to the "departure date" input field in the search form
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.depDate').val(slct.val());
	});
	// changing the value of the select elem of the default return date 
	jQuery('select.retDate').change(function() {
		var slct = jQuery(this);
		// setting the value selected to the "return date" input field in the search form
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.retDate').val(slct.val());
	});
	// changing the value of the select elem of adults
	jQuery('select.adults').change(function() {
		var slct = jQuery(this);
		// setting the same value to the adults select elements in all search forms where can be found
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.adults').val(slct.val());
	});
	// changing the value of the select elem of kids
	jQuery('select.kids').change(function() {
		var slct = jQuery(this);
		// setting the same value to the kids select elements in all search forms where can be found
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.kids').val(slct.val());
	});
	// changing the value of the select elem of seniors	
	jQuery('select.seniors').change(function() {
		var slct = jQuery(this);
		// setting the same value to the seniors select elements in all search forms where can be found
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.seniors').val(slct.val());
	});
	// changing the value of the select elem of rooms
	jQuery('select.rooms').change(function() {
		var slct = jQuery(this);
		// setting the same value to the rooms select elements in all search forms where can be found
		slct.parents('.tg_searchbox').find('.tg_container > div').find('.rooms').val(slct.val());
	});
	
	jQuery('img.compareImage').click(function(){
		alert('Here you can set up the default values the searchboxes will be filled in with.'+"\n"+'To test how the boxes work add them to your pages or visit the demo page.');
		return false;
	});
	
	// setting the container to the first div
	var formsContainer = jQuery('.tg_searchbox .tg_container > div');
	/* creating the datepicker */
	formsContainer.each(function(){
		var currentForm = jQuery(this);
		//we want only to create the datepicker for the div that has the inputs with the class tgsb_addDP
		if(!currentForm.hasClass('pwr')) {
			var inputs = currentForm.find(".tgsb_addDP");
            if (inputs.length>1) {
                // the ids of the inputs
                var i1 = inputs.get(0).id;
                var i2 = inputs.get(1).id;
                var rtowInputs = false;
                var rtowInputs = currentForm.hasClass('flights') ? jQuery(this).find('input[name="tg_searchboxes_options[flights_oneway]"]') : false;
                createDatepicker(i1, i2, rtowInputs);
            };
		};
	});

	/* farbtastick colorpicker */
	jQuery('.colorSettings span, .colorSettings input').click(function(e){
		// checking if in the class attribute is present at the end one of the strings below
		var currentClass 		= this.className.match(/(i1|i2|i3|i4|i5|i6)$/);
		// if is not found then false is returned
		if(currentClass ==  null)
			return false;
		// setting the colorSettingsContainer
		var colorSettingsContainer	= jQuery(this).parents('.colorSettings');
		// finding the colorPicker container
		var colorPicker			= colorSettingsContainer.find('div.'+currentClass);

		// finding the input where the color string will be added
		// show the color picker in both cases when the user clicks on the icon near the input or when he clicks on the input

		var input			= (this.tagName == 'SPAN') ? 
							colorSettingsContainer.find('input.'+currentClass) 
							: this;
			
		// setting the top value of the color picker container
		colorPicker.css('top', ((this.tagName == 'SPAN') ? this.offsetTop : colorSettingsContainer.find('span.'+currentClass).get(0).offsetTop ));
		// setting the left value of the color picker container		
		colorPicker.css('left', ((this.tagName == 'SPAN') ? this.offsetLeft : colorSettingsContainer.find('span.'+currentClass).get(0).offsetLeft ));

		jQuery(input).blur(function(){
			var inputBackgroundColor = jQuery(input).val();
			jQuery(input).css('background-color', inputBackgroundColor);
			// setting the color on the searchbox so the user will see what happens and how the searchbox will look like
			setSearchboxColor(input, inputBackgroundColor);
			// hide the color picker container 
			colorPicker.addClass('nod');
		});

		jQuery.farbtastic(colorPicker, function(a) {
			// setting the color on the searchbox so the user will see what happens and how the searchbox will look like
			setSearchboxColor(input, a);
			jQuery(input).val(a);
			jQuery(input).css('background-color', a);
		});
		colorPicker.removeClass('nod');
		e.preventDefault();
		// if the user clicks anywhere on the document
		jQuery(document).mousedown( function() {
			// hide the color picker container 
			colorPicker.addClass('nod');
		});
		
	});
	
	/* end farbtastick colorpicker */
	
	// setting the departure dates in the searchboxes according to the default departure date
	setDepartureDateToForm();
	// setting the return dates in the searchboxes according to the default departure date
	setReturnDateToForm();
	
	// setting the deprature dates in the searchboxes according to the date selected by the user in the default departure date elem
	jQuery('#tgsb_departure_date').change(function() {
		setDepartureDateToForm(this.value);
		setReturnDateToForm();
	});
	// setting the return dates in the searchboxes according to the date selected by the user in the default return date elem
	jQuery('#tgsb_return_date').change(function() {
		setDepartureDateToForm();
		setReturnDateToForm(this.value);
	});
	// switching between the 2 tabs (Default settings and Shortcode Generator)
	jQuery('ul.tgsb_settings li a, a.tgsb_shortcodeGenerator, input.tgsb_shortcodeGenerator').click(function(){
		var tgsb_settingsSelectedTab = this.className.match(/^[a-zA-Z_]+/);
		jQuery('ul.tgsb_settings li a, ul.tgsb_settings li').removeClass('current');
		jQuery('ul.tgsb_settings li a.'+tgsb_settingsSelectedTab).addClass('current');
		jQuery('ul.tgsb_settings li a.'+tgsb_settingsSelectedTab).parents('li').addClass('current');
		jQuery('#tgsb_settings').addClass('nod');
		jQuery('#tgsb_shortcodeGenerator').addClass('nod');
		jQuery('#'+tgsb_settingsSelectedTab).removeClass('nod');
		scrollTo(0,0);
		return false;
	});
	// showing the div where the user can change the option regarding the travel search link
	jQuery('a.showTravelSearchLink').click(function () {

		jQuery('div.travelSearchLink').slideToggle('slow');
		return false;
	});
	
});

// function used to set the color on the searchbox so the user can see how the searchbox will look like
function setSearchboxColor(input, colorCode) {
	if(jQuery(input).hasClass('i1')) {
		// setting the color for the borders of the box
		jQuery('.tg_searchbox .tg_container, .tg_searchbox .tg_tabs li span.sel').each(function(){
			if(!jQuery(this).attr('style')) {
				jQuery(this).attr('style', 'border-color: '+colorCode+' !important');
			} else {
				var elemStyle = jQuery(this).attr('style');
				if(elemStyle.match('border-color:'))
					elemStyle = elemStyle.replace(/border-color:[^\;]+/gi, 'border-color: '+colorCode+' !important');
				else
					elemStyle += ';border-color: '+colorCode+' !important';
				elemStyle = elemStyle.replace(/^;/,'').replace(/;;/,';');
				jQuery(this).attr('style', elemStyle);
			};
		});
	};
	if(jQuery(input).hasClass('i2')) {
		// setting the color for the text of the box
		jQuery('.tg_searchbox .tg_container, .tg_searchbox .tg_tabs li span.sel, .tg_searchbox .tg_container label').css('color', colorCode);
	};
	if(jQuery(input).hasClass('i3')) {
		// setting the color for the background of the box
		jQuery('.tg_searchbox .tg_container, .tg_searchbox .tg_tabs li span.sel').each(function() {
			if(!jQuery(this).attr('style')) {
				jQuery(this).attr('style', 'background-color: '+colorCode+' !important');
			} else {
				var elemStyle = jQuery(this).attr('style');
				if(elemStyle.match('background-color:'))
					elemStyle = elemStyle.replace(/background-color:[^\;]+/gi, 'background-color: '+colorCode+' !important');
				else
					elemStyle += ';background-color: '+colorCode+' !important';
				elemStyle = elemStyle.replace(/^;/,'').replace(/;;/,';');
				jQuery(this).attr('style', elemStyle);
			};
		});
	};
	if(jQuery(input).hasClass('i4')) {
		// setting the color for the background of the tabs
		var elem = jQuery('.tg_searchbox .tg_tabs li span').not('.sel');
		if(typeof(elem.attr('style')) == 'undefined') {
			elem.attr('style', 'background-color: '+colorCode+' !important');
		} else {
			var elemStyle = elem.attr('style');
			if(elemStyle.match('background-color:'))
				elemStyle = elemStyle.replace(/background-color:[^\;]+/gi, 'background-color: '+colorCode+' !important');
			else
				elemStyle += ';background-color: '+colorCode+' !important';
			elemStyle = elemStyle.replace(/^;/,'').replace(/;;/,';');
			elem.attr('style', elemStyle);
		};
	};
	if(jQuery(input).hasClass('i5')) {
		// setting the color for the text of the tabs
		jQuery('.tg_searchbox .tg_tabs li span').not('.sel').css('color', colorCode);
	};
	if(jQuery(input).hasClass('i6')) {
		// setting the color for the text of the tabs
		var elem = jQuery('.tg_searchbox .tg_tabs li span').not('.sel');
		if(typeof(elem.attr('style')) == 'undefined') {
			elem.attr('style', 'border-color: '+colorCode+' !important');
		} else {
			var elemStyle = elem.attr('style');
			if(elemStyle.match('border-color:'))
				elemStyle = elemStyle.replace(/border-color:[^\;]+/gi, 'border-color: '+colorCode+' !important');
			else
				elemStyle += ';border-color: '+colorCode+' !important';
			elemStyle = elemStyle.replace(/^;/,'').replace(/;;/,';');
			elem.attr('style', elemStyle);
		};
	};
	return;
};
