/**	JS used inside the thickbox on the editor interface, while adding/editing a searchbox	*/
function setParamsToBoxes(obj) {
	// if the size of the box is not defined in the object
	if(typeof(obj.size) == 'undefined' || !obj.size) {
		// the set the size to the default value of '300x250'
		obj.size = '300x250';
	};
	// if the selectedTab of the box is not defined in the object
	if(typeof(obj.selectedTab) == 'undefined' || !obj.selectedTab) {
		// the set the selectedTab to the default value of 'flights'
		obj.selectedTab = 'flights';
	};
	
	// if the selectedTab of the box is not defined in the object
	if(typeof(obj.alignment) == 'undefined' || !obj.alignment) {
		// the set the selectedTab to the default value of 'flights'
		obj.alignment = 'alignnone';
	};
	// getting the radio buttons used for the alignment of the box
	var alignRadioButtons = jQuery('#TB_ajaxContent').find('input[name=img_align]');
	// iterating the radion buttons

	for(var i=0; i<alignRadioButtons.length; i++) {
		// if the value from the radio button is equal to the value of the given object
		if(alignRadioButtons[i].value == obj.alignment) {
			// set that radio button as checked
			alignRadioButtons[i].checked = true;
			break;
		};
	};

	// finding the menu with the searchboxes measures in the popup div
	var tgsb_popupMenu			= jQuery('#TB_ajaxContent').find('ul.measuresChooser');
	var tgsb_popupCurrentSelectedBoxSize	= '300x250';
	// getting the size of a box from the current selected option in the menu
	tgsb_popupCurrentSelectedBoxSize	= tgsb_popupMenu.find('a.current').html();
	// removing the current class from the current selected option in the menu
	tgsb_popupMenu.find('a.current').removeClass('current');
	// adding the current class to the option in the menu specified from the obj got from argument
	tgsb_popupMenu.find('a.'+obj.size).addClass('current');
	// removing the crnt class from the searchbox container which displays the current box
	jQuery('#TB_ajaxContent').find('div.tgsb.sb'+tgsb_popupCurrentSelectedBoxSize).removeClass('crnt');
	// finding the new selected box container
	var tgsb_currentSelectedBoxContainer = jQuery('#TB_ajaxContent').find('div.tgsb.sb'+obj.size);
	// adding the crnt class to the searchbox container which is set on the obj got from argument
	tgsb_currentSelectedBoxContainer.addClass('crnt');
	// finding the menu of the searchbox
	var tgsb_searchboxMenu = tgsb_currentSelectedBoxContainer.find('ul.tg_tabs');
	// removing the sel class from the selected tab of the menu
	tgsb_searchboxMenu.find('span.sel').removeClass('sel');
	// adding the sel class to the tab which is set on the obj.selectedTab
	tgsb_searchboxMenu.find('span.'+obj.selectedTab).addClass('sel');
	// finding the searchbox forms container element
	var tgsb_searchboxFormContainer = tgsb_currentSelectedBoxContainer.find('div.tg_container');
	// finding the selected form and removing the sel class from it
	tgsb_searchboxFormContainer.find('form.sel').removeClass('sel');
	// adding the sel class to the form set on the obj.selectedTab
	tgsb_searchboxFormContainer.find('form.'+obj.selectedTab).addClass('sel');
	// finding the forms from the popup div
	var tgsb_searchboxesForms = jQuery('#TB_ajaxContent').find('form');
	var tgsb_insertLink = jQuery('#TB_ajaxContent').find('a.send_searchbox_to_editor');
	tgsb_insertLink.html('Update');
	jQuery('#TB_ajaxContent').find('input.send_searchbox_to_editor').val('Update Box');
	if (obj.usejavascript)
		jQuery('#TB_ajaxContent').find("#travelSearchUseJavaScript").attr('checked',true);
	// iterating each form
	tgsb_searchboxesForms.each(function(){
		var currentForm = jQuery(this);
		// if on the object we got the from_air variable is set and the current form hasn't the class cars
		if(typeof(obj.from_air) != 'undefined' && obj.from_air && !currentForm.hasClass('cars'))
			// find the input with the class asFrom and set it's value to obj.from_air
			currentForm.find('input.asFrom').val(obj.from_air);
		// if on the object we got the to_air variable is set
		if(typeof(obj.to_air) != 'undefined' && obj.to_air) {
			// find the input with the class asFrom and set it's value to obj.to_air
			currentForm.find('input.asTo').val(obj.to_air);
			// if the current form has the class cars
			if(currentForm.hasClass('cars'))
				// find the input with the class asFrom and set it's value to obj.to_air
				currentForm.find('input.asFrom').val(obj.to_air);
		}
        if(typeof(obj.hotel_city) != 'undefined' && obj.hotel_city && currentForm.hasClass('hotels')) {
            // find the input with the class asFrom and set it's value to obj.to_air
            currentForm.find('input.asTo').val(obj.hotel_city);
        }
		// if on the object we got the departure_date variable is set
		if(typeof(obj.departure_date) != 'undefined' && obj.departure_date)
			// find the input with the class depDate and set it's value to obj.departure_date
			currentForm.find('input.depDate').val(obj.departure_date);
		// if on the object we got the return_date variable is set
		if(typeof(obj.return_date) != 'undefined' && obj.return_date)
			// find the input with the class retDate and set it's value to obj.ret_date
			currentForm.find('input.retDate').val(obj.return_date);
		// if on the object we got the adults variable is set
		if(typeof(obj.adults) != 'undefined' && obj.adults)
			// find the input with the class adults and set it's value to obj.adults
			currentForm.find('select.adults').val(obj.adults);
		// if on the object we got the kids variable is set
		if(typeof(obj.kids) != 'undefined' && obj.kids)
			// find the input with the class kids and set it's value to obj.kids
			currentForm.find('select.kids').val(obj.kids);
		// if on the object we got the seniors variable is set
		if(typeof(obj.seniors) != 'undefined' && obj.seniors)
			// find the input with the class seniors and set it's value to obj.seniors
			currentForm.find('select.seniors').val(obj.seniors);
		// if on the object we got the rooms variable is set
		if(typeof(obj.rooms) != 'undefined' && obj.rooms)
			// find the input with the class rooms and set it's value to obj.rooms		
			currentForm.find('select.rooms').val(obj.rooms);
		// if on the object we got the rooms variable is set
		if(typeof(obj.rtow) != 'undefined') {
			var rtowInputs = currentForm.hasClass('flights') ? currentForm.find('input[name=oneway], select[name=oneway], label.radio') : false;
			var rtowValue = (obj.rtow == false) ? '' : 'on';
			// select element
			if(rtowInputs.length == 1) {
				var selectRTOW = rtowInputs.get(0).id;
				jQuery('#'+selectRTOW).val(rtowValue);
			};
			// radio buttons
			if(rtowInputs.length == 2) {
				// roundtrip radio input id
				var rt = rtowInputs.get(0).id;
				// oneway radio input id 
				var ow = rtowInputs.get(1).id;
				if(rtowValue == 'on')
					jQuery('#'+ow).get(0).checked = true;
				else
					jQuery('#'+rt).get(0).checked = true;				
			};
		};
        if(typeof(obj.cruiseline) != 'undefined' && obj.cruiseline)
            currentForm.find('select[name="cruiseline"]').val(obj.cruiseline);
        if(typeof(obj.destination) != 'undefined' && obj.destination)
            currentForm.find('select[name="destination"]').val(obj.destination);
        if(typeof(obj.length_of_stay) != 'undefined' && obj.length_of_stay)
            currentForm.find('select[name="length_of_stay"]').val(obj.length_of_stay);
        if(typeof(obj.month_year) != 'undefined' && obj.month_year)
            currentForm.find('select[name="month_year"]').val(obj.month_year);
	});
};

if(typeof(tgsb_selBoxParam) != 'undefined' && tgsb_selBoxParam) {
	setParamsToBoxes(tgsb_selBoxParam);
};

// travelSearchUseJavaScript added by Tibi on 2013.04.23 | click event should work on this input and label as well
jQuery('input[name=img_align], #basic .align .field label, #travelSearchUseJavaScript, label[for=travelSearchUseJavaScript]').click(function(event){
	event.stopPropagation();
	return true;
});

jQuery('ul.measuresChooser li a').click(function(){
	// setting the measure from the anchor text
	measureChoosed = jQuery(this).text();
	// remove the class (current), from the a tag
	jQuery('ul.measuresChooser li a').removeClass('current');
	// adding the class (current), to the a tag, which shows which element is choose
	jQuery(this).addClass('current');
	// removing the class (crnt) from the div containing the searchbox
	jQuery('div.tgsb').removeClass('crnt');
	// adding the class (crnt) to the div containing the searchbox with the choose measurement
	jQuery('div.sb'+measureChoosed).addClass('crnt');
});


var ASoptions = {
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
			jQuery(".asFrom").val(inp.val());
			inp.parents('#TB_ajaxContent').find('.tg_container > form.cars').find('.asFrom').val('');
		}
		
		if(inp.hasClass("asTo")) {
			jQuery(".asTo").val(inp.val());
			inp.parents('#TB_ajaxContent').find('.tg_container > form.cars').find('.asFrom').val(inp.val());
		}
	}
};
var hotelASoptions = {
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
        citytype:	'cities',
        maxResults:	10,
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
function createDatepicker(i1,i2,rtowInputs){
jQuery('#'+i1+', #'+i2).each(function(){
	var inp = jQuery(this);
    var tsbvars = typeof(TG_Searchboxes_Variables)!='undefined' ? TG_Searchboxes_Variables : {};
	inp.datepicker({
		minDate: 0,
		maxDate: "2y",
		showOn: "both",
		// setting the datepicker image 
		buttonImage: tsbvars.str_CalendarURL,
		buttonImageOnly: true,
		dateFormat: tsbvars.str_dateFormat,
		// setting the date format 
		onSelect: function(date,dpObj){
			// d1 is the selected date on the datepicker
			d1 = jQuery(this).datepicker("getDate");
			if(this.id == i1 && tsbvars.str_dateFormat) {
				// getting the date from the return date input
				d2 = jQuery.datepicker.parseDate(tsbvars.str_dateFormat, jQuery('#'+i2).val());
				// setting the minimum date of the return date to the date of the departure date
				jQuery("#"+i2).datepicker('option','minDate',d1);
				if(d2 && d1>d2) {
					// if the departure date is greater then the return date add 5 day to the departure date and set them to the return date
					d2.setTime(d1.getTime()+60*60*24*5*1000);
                    var dateFormatted = jQuery.datepicker.formatDate(tsbvars.str_dateFormat, d2);
					jQuery("#"+i2).val(dateFormatted);
					// the same date value is set to all inputs with the same class
					var cont = jQuery("#"+i2).parents("div.tg_searchbox:eq(0)");
					cont.find('input.retDate').val(dateFormatted);
				};
			};
			inputName = this.name;
			var cont = jQuery(this).parents("div.tg_searchbox:eq(0)");
			// the same date value is set to all inputs with the same name
			cont.find('input[name="'+inputName+'"]').val(date);
			return;
		}
	});
});
// we need to check the oneway inputs because regarding this we'll enable or disable the return date input
if(typeof(rtowInputs) == 'object' && rtowInputs.length > 0) {
	var rt = rtowInputs.get(0).id;
	var ow = rtowInputs.get(1).id;
	rtowInputs.click(function(event){
		event.stopPropagation();
		if(this.id == ow && this.checked){
			jQuery('#'+i2).attr('disabled',true);
		};
		if(this.id == rt && this.checked){
			jQuery('#'+i2).attr('disabled',false);
		};
	});
};
};

jQuery('select.adults').change(function() {
	var slct = jQuery(this);
	slct.parents('.tg_searchbox').find('.tg_container > form').find('.adults').val(slct.val());
});
jQuery('select.kids').change(function() {
	var slct = jQuery(this);
	slct.parents('.tg_searchbox').find('.tg_container > form').find('.kids').val(slct.val());
});
jQuery('select.seniors').change(function() {
	var slct = jQuery(this);
	slct.parents('.tg_searchbox').find('.tg_container > form').find('.seniors').val(slct.val());
});
jQuery('select.rooms').change(function() {
	var slct = jQuery(this);
	slct.parents('.tg_searchbox').find('.tg_container > form').find('.rooms').val(slct.val());
});

try {

    jQuery(".tgsb_addAS").each(function(){
        jQuery(this).focus(inputFocus).blur(inputBlur);
        new AS(this.id,ASoptions);
    });
    jQuery(".tgsb_addASH").each(function(){
        jQuery(this).focus(inputFocus).blur(inputBlur);
        new AS(this.id,hotelASoptions);
    });

    jQuery('div.tg_searchbox ul.tg_tabs li span').click(function(){
        selectedTab = jQuery(this).attr('class').match(/^[a-z]+/);
        var cont = jQuery(this).parents("div.tg_searchbox:eq(0)");
        cont.find('ul.tg_tabs li span').removeClass('sel');
        cont.find('ul.tg_tabs li span.'+selectedTab).addClass('sel');
        cont.find('div.tg_container form').removeClass('sel');
        cont.find('div.tg_container form.'+selectedTab).addClass('sel');
    });

    /*
    jQuery('.tg_searchbox form').submit(function(){
        alert('Here you can set up the default values the searchboxes will be filled in with.'+"\n"+'To test how the boxes work add them to your pages or visit the demo page.');
        return false;
    });
    */
    jQuery('input.tgsb_submit_button').click(function() {
        alert('Here you can set up how the box should appear inside your post.'+"\n"+
            'To see this box in action hit Insert and update your post,'+"\n"+
            'or visit the demo page of this plugin at:'+"\n"+
            TG_Searchboxes_Variables.demoPage);
        return false;
    });



    jQuery('.tg_searchbox form').each(function(){
        try {
            var inputs = jQuery(this).find(".tgsb_addDP");
            if (inputs.length > 1) {

            var i1 = inputs.get(0).id;
            var i2 = inputs.get(1).id;
            var currentForm = jQuery(this);
            var rtowInputs = false;
            var rtowInputs = currentForm.hasClass('flights') ? jQuery(this).find('input[name=oneway], label.radio') : false;
            createDatepicker(i1, i2, rtowInputs);
            }
        } catch(e) {
            if (typeof(console)!='undefined' && console.log) {
                console.log("ERROR IN DATEPICKER: " + e.toString());
            }
        }
    });

} catch(e) {
    if (typeof(console)!='undefined' && console.log) {
        console.log("ERROR IN DATEPICKER: " + e.toString());
    }
}