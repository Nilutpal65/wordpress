(function(jQuery){
var popupWPos = 0;
var ajaxLoaders;
var ASoptions;
var hotelASoptions;
// object containing the query string with the searchboxes params used when merchant request should be made, if the query string for a searchbox an a search form is the same then the merchant request is not made and the div having the merchants into it is showed
var tgsb_searchboxesParams = {};


/*
	WHAT & WHY: TGSearchboxes object
	WHO & WHEN: Cipri on the 16th of March 2012
*/
function TGSearchboxes() {
	this.queryCodes = {flights:0, hotels:0, cars:0, packages:0, cruises:0};
	this.set = function(queryCodeMember, queryCodeMemberValue) {
		this.queryCodes[queryCodeMember] = queryCodeMemberValue;
		return true;
	};
	this.replacePlaceholder	= replacePlaceholder;
};
// calling the TGSearchboxes object
TGSB = new TGSearchboxes();
TGSB.$ = jQuery;

/**
	@note	Initializes the window opener object that will be used to open windows
	@date	2014-JAN-07
	@author	Tibi
*/
function initWindowOpener(){
	/*	@note	initializing windowOpener object - this is used to open popup windows when a search is performed
		@date	2013 JUN 03
		@author	Tibi	*/
    TGSB.pph = new PPH({
        def: {
            afterTrigger: function(windows){
                this.windows = {};
                jQuery(".mrcList .mSel").trigger('uncheck');
            }
        },
        /*
        ie: {
            placeholder: function(url){
                var intitem = url.match(/\bintitem=([0-9]+)/);
                intitem = intitem && intitem[1] ? parseInt(intitem[1]) : 0;
                if (!intitem) {
                    return false;
                }
                var box = url.match(/\bsbox=([a-z0-9\_]+)/);
                box = box && box[1] ? box[1] : 0;
                if (!box) {
                    return false;
                }

                var w = Math.round(screen.width * 0.7);
                var h = Math.round(screen.height * 0.7);
                var l = screen.width - w - 40;

                var url = TG_Searchboxes_Variables.plugin_url + '/placeholder.dev.html#'+ intitem + "|" + box;

                return {
                    url: url,
                    params: 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,top=40,left='+l+',width='+w+',height='+h
                };
            }
        }
        */
        ie: {
            type: 'popunderwindow',
            when: 'trigger',
            onTrigger: function(){}
        }
    });
}

// WHAT & WHY: function used to remove the error class from an input
function remErr(){
	jQuery(this).removeClass("err").unbind('focus',remErr);
};

function inputFocus(ev){
	if (this.value==this.defaultValue) this.value=''; // if default value present, removing it
	else this.select(); // if other than def. val. present, selecting it
};
function inputBlur(ev){
	if (this.value=='') this.value=this.defaultValue; // if input is empty, filling it up with default value
};

/**	@note	replaces a placeholder of a searchbox w/ the searchbox (when the searchbox is loaded via JS)
	@date	2013.04.22
	@author	Tibi	*/
function replacePlaceholder(shortcode, html){
	//replacing on document ready to make sure teh element is already defined
	jQuery(function(){
		var htmlObj	= jQuery(html);
		var shortcodeObj= jQuery('span#'+shortcode);
		if (shortcodeObj.length==0)
			return false;
		shortcodeObj.replaceWith(htmlObj);
		tgsb_initSingleSearchbox(htmlObj);
	});
};

/*
	WHAT & WHY: function used to validate the searchbox inputs
	WHO & WHEN: Cipri on March, April 2012
	@param	object	obj	object of an input from the search form
	@param	bool	onsbmt	if is set to true then a message regarding the merchants selection will showed in an alert
*/
function validateSearchbox(obj, onsbmt, addErrorClass) {
	// the obj is the submit input
	// flights searchbox 
	onsbmt = (typeof(onsbmt) == 'undefined' || !onsbmt) ? false : true;
	addErrorClass = (typeof(addErrorClass) == 'undefined' || !addErrorClass) ? false : true;
	var errMsg = '';
	var errClass = '';
	// locations pattern
	var patt1 = /^.*\s\(...\)$/i;
	// dates pattern
	var patt2 = /[0-9]{2}\/[0-9]{2}\/[0-9]{4}/;
	// setting the current form jquery object
	var currentForm = jQuery(obj).parents('form');
    var departure;
	if(!currentForm.hasClass('hotels')) {
		// the departure location is not set on the hotels searchbox
		departure = currentForm.find('input.asFrom');
	};
	// setting the arrival input jquery object	
	var arrival = currentForm.find('input.asTo');
	// setting the departure date input jquery object
	var departureDate = currentForm.find('input.depDate');
	// setting the return date input jquery object
	var returnDate = currentForm.find('input.retDate');
	// initializing the date object
	var date = new Date();
	// setting the departure date using the datepicker getDate method
	var d1 = departureDate.datepicker('getDate');
	// setting the return date using the datepicker getDate method
	var d2 = returnDate.datepicker('getDate');
	// if on the flights search form

    if (!currentForm.hasClass('cruises')) {

        if(currentForm.hasClass('flights')) {
            // on the 728x90 box the radio buttons for RT/OW are not present
            // finding the roundtrip/oneway inputs
            var rtowInputs = currentForm.find('input[name=oneway]');
            // oneway checked flag set to false
            var owChk = false;
            // if exist the roundtrip/oneway inputs ( because on the box 728x90 they are not present)
            if(typeof(rtowInputs.get(0)) != 'undefined' && typeof(rtowInputs.get(1)) != 'undefined') {
                var rt = rtowInputs.get(0).id; // get the id of the roundtrip input
                var ow = rtowInputs.get(1).id; // get the id of the oneway input
                owChk = jQuery('#'+ow).attr('checked'); // setting the oneway flag
            }

        }
        // if not on the hotels search form
        if(!currentForm.hasClass('hotels')) {
            // the departure location is not set on the hotels searchbox
            if(!patt1.test(departure.val())) {
                // comparing the departure location with the pattern
                errMsg += "\t- a departure location\n";
                errClass += '#'+departure.get(0).id+',';
            }
        }
        // if on the hotels search form
        if(currentForm.hasClass('hotels')) {
            if(arrival.val().length<3) {
                // comparing the arrival location with the pattern
                errMsg += "\t- an arrival location\n";
                errClass += '#'+arrival.get(0).id+',';
            }
        } else {
            if(!patt1.test(arrival.val())) {
                // comparing the arrival location with the pattern
                errMsg += "\t- an arrival location\n";
                errClass += '#'+arrival.get(0).id+',';
            }
        }

        if(!patt2.test(departureDate.val()) || (d1>0 && d1<date) ) {
            // comparing the departure date with the pattern
            errMsg += "\t- a valid departure date\n";
            errClass += '#'+departureDate.get(0).id+',';
        }
        if(currentForm.hasClass('flights')) {
            // if the oneway radio button is not checked and the return date is not set
            if(!owChk && !patt2.test(returnDate.val())) {
                // comparing the return date with the pattern
                errMsg += "\t- a valid returning date\n";
                errClass += '#'+returnDate.get(0).id+',';
            }
            if (!owChk && d1>0 && d2>0 && d1>d2) {
            // if the oneway radio button is not checked and the departure date is bigger then zero and the return date is bigger then zero and the departure date is bigger then the return date
                errMsg += "\t- a greater returning date than the departure date\n";
                errClass+='#'+returnDate.get(0).id+',';
            }
        } else {
            if(!patt2.test(returnDate.val())) {
                // comparing the return date with the pattern
                errMsg += "\t- a valid returning date\n";
                errClass += '#'+returnDate.get(0).id+',';
            }
            if (d1>0 && d2>0 && d1>d2) {
                errMsg += "\t- a greater returning date than the departure date\n";
                errClass+='#'+returnDate.get(0).id+',';
            }
        }
    }
	// the comma is present at the end of the errClass variable then replace it
	if(errClass.match(/\,$/)) {
		errClass = errClass.substr(0, (errClass.length-1));
	};

	if(addErrorClass) {
		// add the error class to the fields with error values and on focus remove the error class
		jQuery(errClass).addClass("err").focus(remErr);
	};
	// if this variable is set to true
	if(onsbmt) {
		// get the selected merchants from the current form merchants container
		var selectedMerchants = currentForm.find('div.mrcList span.mSel');
		// if there is no merchant selected
		if(selectedMerchants.length < 1) {
			// if the error message is empty
			if(errMsg.length == 0) {
				alert('Please select at least two providers to compare their prices.');
				return false;
			} else {
				// if the error message is not empty add the error message referring to the selected merchants
				errMsg += "Please select at least two providers to compare their prices.\n";
			};
		};
	};

	// if the error message is not empty add a string at the begining of the error message else return false
	return errMsg.length ? "Please enter:\n"+errMsg : false;
};

/*
	WHAT & WHY: function used to make a classic search request
	WHO & WHEN: Cipri on March, April 2012
	@param	object	object of an input of the search form
*/
function makeClassicSearchRequest(obj) {
	// validating the Searchbox
	var errMsg = validateSearchbox(obj, true, true);
	// if error message the alert it
	if(errMsg.length) {
		alert(errMsg);
		return false;
	};
	// opening the popups with the search request
	ppups(obj);
	// remove the submit class from the submit button
	jQuery(obj).removeClass('submited');
	return true;
};

function getJumpLink(obj, mName, mId){
	if (!obj || !mName || !mId)
		return false;
	// building the params from the form parent of the source object
	var fields =  (typeof(obj) != 'undefined') ? jQuery(obj).parents('form').serializeArray() : '';
	// creating the querystring that will be submited
	var queryString = createQueryString(obj, fields);
	// if no query string returned the return false
	if(!queryString)
		return false;
	// setting the searchbox jquery object
	var searchbox = jQuery(obj).parents('form');
	// default query code flag set to 1
	var tgsb_querycode = 1;
	// default url set to empty
	var url = '';
	// if on the flights search form
	if(searchbox.hasClass('flights')) {
		// set the url of the search with the channel 3
		url = 'http://www.travelgrove.com/cgi-bin/link_counter_new.cgi?channel=3';
		// set the query code for the flights search
		tgsb_querycode = TGSB.queryCodes['flights'];
	};
	// if on the hotels search form
	if(searchbox.hasClass('hotels')) {
		// set the url of the search with the channel 6
		url = 'http://www.travelgrove.com/cgi-bin/hotels/link_counter_new.cgi?channel=6';
		// set the query code for the hotels search
		tgsb_querycode = TGSB.queryCodes['hotels'];
	};
	// if on the packages search form
	if(searchbox.hasClass('packages')) {
		// set the url of the search with the channel 7
		url = 'http://www.travelgrove.com/cgi-bin/vacation/link_counter_new.cgi?channel=7';
		// set the query code for the packages search
		tgsb_querycode = TGSB.queryCodes['packages'];
	};
	// if on the cars search form
	if(searchbox.hasClass('cars')) {
		// set the url of the search with the channel 4
		url = 'http://www.travelgrove.com/cgi-bin/cars/link_counter_new.cgi?channel=4';
		// set the query code for the cars search
		tgsb_querycode = TGSB.queryCodes['cars'];
	};
    // if on the cars search form
    if(searchbox.hasClass('cruises')) {
        // set the url of the search with the channel 4
        url = 'http://www.travelgrove.com/cgi-bin/cruises/link_counter_new.cgi?channel=5';
        // set the query code for the cars search
        tgsb_querycode = TGSB.queryCodes['cruises'];
    };
	// if the url was not set the return false
	if(url.length==0)
		return false;
	// setting the separator by searching the first occurence of the ? character	
	var separator	= url.indexOf('?')>=0 ? '&' : '?';
	url	+= separator + queryString +
		'&lang=def' +
		'&dateFormat=mm/dd/yyyy' +
		'&trafficSource=wpplugin' + 
		'&searchsystem=us' +
		'&querycode=' + tgsb_querycode + '&merchant=' + mName + '&intitem=' + mId +
        '&sbox=' + jQuery(obj).parents('.tg_searchbox:eq(0)').attr('id');
	return url;
};
TGSB.getJumpLink = getJumpLink;

/*
	WHAT & WHY: function used to create the url needed for the merchants popups on classic search
	WHO & WHEN: Cipri on the 14th of March 2012 
	@param	object	obj	containing an object of an input of the search form
*/
function ppups(obj) {
	if (!obj)
		return false;

    var merchantSet	 	= jQuery(obj).parents('form:eq(0)').find(".mrcList .mSel");
    merchantSet.each(function(){
        var t = jQuery(this);
        var mName = t.attr('title');
        var mId = t.attr('rel');
        var url = getJumpLink(obj, mName, mId);
        var woid = t.data('woid');
        TGSB.pph.update(woid, url);
    });

    TGSB.pph.trigger();
	 
	return true;
};

/*
	WHAT & WHY: function used to open new windows for the merchants for classic search
	WHO & WHEN: Cipri on the 14th of March 2012
*/
function jump(url) {
	// setting the coords of the window
	var myWinCoord =
		"left="+(popupWPos+=20)+
		",top="+popupWPos+
		",width="+(screen.width*0.8)+
		",height="+(screen.height*0.8)+
		",resizable = yes,scrollbars=yes, toolbar=yes, location=yes";
	// opening a new window
	var nw = window.open(url,"_blank",myWinCoord);
	// if the window was not opened the return false
	if(!nw) return false;
	// returns the new window object
	return nw;
};
/*
	WHAT & WHY: function used to make a request for the merchants
	WHO & WHEN: Cipri on the 12th of March 2012
	@param	object	contains an object of an input of the forum from where is made the merchants request
	@param	bool	used to show the error messages from the validation of the searchbox
*/
function makeMerchantsRequest(obj, showErrorMessages, addErrorClass) {
	showErrorMessages = (typeof(showErrorMessages) == 'undefined' || !showErrorMessages) ? false : true;
	addErrorClass = (typeof(addErrorClass) == 'undefined' || !addErrorClass) ? false : true;	
	var tgsb_querycode = 0;
	// getting the size of the searchox | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
	var searchboxsize = jQuery(obj).parents('.tg_searchbox').attr('class').match(/m(160x600|300x250|300x533|728x90|dynamic)/);
	var searchboxId = jQuery(obj).parents('.tg_searchbox').get(0).id;
	// if no one of the sizes is set then return false
	if(searchboxsize == null || (searchboxsize[1] != '160x600' && searchboxsize[1] != '300x250' && searchboxsize[1] != '300x533' && searchboxsize[1] != '728x90' && searchboxsize[1] != 'dynamic'))
		return false;
	// setting the jquery object of the parent form
	var searchbox = jQuery(obj).parents('form');
	// setting the submit button of the current form
	var submitButton = searchbox.find('input.tgsb_submit_button');
	// validate the searchbox inputs values	
	if((errMsg = validateSearchbox(obj, false, addErrorClass))) {
		// if the showErrorMessages flag is set the show an alert with the errors
		if(showErrorMessages)
			alert(errMsg);
		// removing the submited class from the submit button
		jQuery(submitButton).removeClass('submited');
		// returning false if errors are found			
		return false;
	};

	// serializing the form inputs
	var fields =  (typeof(obj) != 'undefined') ? jQuery(obj).parents('form').serializeArray() : '';
	// creating the query string	
	var queryString = createQueryString(obj, fields);

	if(!queryString)
		return false;
	// setting the searchbox type
	var searchboxType = searchbox.attr('class').match(/(flights|hotels|packages|cars|cruises)/);
	
	if(searchboxType == null)
		return false;
	// setting the merchants container
	var merchantsContainer = searchbox.find('div.mrcList'); 
	if(typeof(tgsb_searchboxesParams[searchboxId]) == 'undefined'){
		tgsb_searchboxesParams[searchboxId] = {};
	};
	if(typeof(tgsb_searchboxesParams[searchboxId][searchboxType]) == 'undefined' || tgsb_searchboxesParams[searchboxId][searchboxType] != queryString) {
		// hide the search form
		if(searchboxsize[1] != 'dynamic')
			searchbox.find('div.formContent').addClass('nod');
		// add the nod class to the div containing the help class
		searchbox.find('div.help').addClass('nod');
		merchantsContainer.html(ajaxLoaders[searchboxsize[1]]);	
		// show the merchants container div and align it's content ( for ajax loader image) to center 
		merchantsContainer.addClass('alCnt').removeClass('nod');

		// merchants limit is by default set to 7
		var merchantsLimit = 7;
		// if searchbox size is 728x90 set the merchants limit to 6
		if(searchboxsize[1] == '728x90')
			merchantsLimit = 6;
		// if searchbox size is 300x250 set the merchants limit to 8
		if(searchboxsize[1] == '300x250')
			merchantsLimit = 8;
		// if the searchbox size is 300x533 set the merchants limit 12
		if(searchboxsize[1] == '300x533' || searchboxsize[1] == 'dynamic')
			merchantsLimit = 12;
		// if searchbox type is hotels and searchbox size is 160x600 the set the merchants limit to 10
		if(searchboxType[1] == 'hotels' && searchboxsize[1] == '160x600')
			merchantsLimit = 10;

		tgsb_searchboxesParams[searchboxId][searchboxType] = queryString;
		// making the ajax request

		// if on the flights search form
		if(searchbox.hasClass('flights')) {
			// set the query code for the flights search
			tgsb_querycode = TGSB.queryCodes['flights'];
		};
		// if on the hotels search form
		if(searchbox.hasClass('hotels')) {
			// set the query code for the hotels search
			tgsb_querycode = TGSB.queryCodes['hotels'];
		};
		// if on the packages search form
		if(searchbox.hasClass('packages')) {
			// set the query code for the packages search
			tgsb_querycode = TGSB.queryCodes['packages'];
		};
        // if on the cars search form
        if(searchbox.hasClass('cars')) {
            // set the query code for the cars search
            tgsb_querycode = TGSB.queryCodes['cars'];
        };
        // if on the cruises search form
        if(searchbox.hasClass('cruises')) {
            // set the query code for the cars search
            tgsb_querycode = TGSB.queryCodes['cruises'];
        };
		var dta = queryString+'&impId='+tgsb_querycode+'&merchants='+searchboxType[1]+'&sbsize='+searchboxsize[1]+'&limit='+merchantsLimit;
		jQuery.ajax({
			// the url is from the TG_Searchboxes_Variables object set in the DOM
			url: TG_Searchboxes_Variables.str_merchantsAjaxURL,
			type: 'post',
			data: dta,
			// dataType was set to text to be compatible with older and newer versions of jQuery
			dataType: 'text',
			success: function(rsp){
				// evaluating the response
				var jsonObj = eval('('+rsp+')');
				// removing the align center class used for ajax loader image
				merchantsContainer.removeClass('alCnt');
				// adding in the merchants container the html containing the merchants
				// for the searchboxes with the size of 300x250 or 728x90 add the "back" link so the user will be able to go back to the searchbox from the div containing the merchants
				merchantsContainer.html(jsonObj.merchants + ( (searchboxsize[1] == '300x250' || searchboxsize[1] == '728x90') ? '<a href="#" class="tgBackToSearchbox">change this search</a>' : '') );
				// finding the tracking pixel
				var trackingPixel = searchbox.find('img.trackingPixel');
				// if the tracking pixel is not present
				if(!trackingPixel.length) {
					// add the tracking pixel after the merchants container
					merchantsContainer.after('<img class="trackingPixel" src="'+jsonObj.trackingPixel+'" width="1" height="1" />');
				};
				// if the tracking pixel is already present then only change the src attribute of it
				if(trackingPixel.length) {
					trackingPixel.get(0).src = jsonObj.trackingPixel;
				};
				// set the merchants from the spans contained in the merchants container
				var merchants = searchbox.find('div.mrcList').find('span');

//				if(searchboxsize[1] == '300x250' || searchboxsize[1] == '728x90' || searchboxsize[1] == 'dynamic')
					submitButton.addClass('tgsb_submit_button_cmp');
					submitButton.val('compare prices');
			// clicking on the submit button make a classic Search request
			// here an unbind for the click event is needed otherwise if no merchant is selected the alert that asks the user to select at least one merchant will appear so many times the submit button is clicked
				submitButton.unbind('click').click(function(){
					makeClassicSearchRequest(this);
					return false;
				});
				// on the searchboxes with the sizes of 300x250 or 728x90
				if(searchboxsize[1] == '300x250' || searchboxsize[1] == '728x90' || searchboxsize[1] == 'dynamic') {
					// finding the "back" link to the search form
					searchbox.find('div.mrcList a.tgBackToSearchbox').click(function(){
						// if the "back" link is clicked then hide the merchants container
						merchantsContainer.addClass('nod');
						// find the search form and show it
						searchbox.find('div.formContent').removeClass('nod');
						// click on the submit button
						submitButton.removeClass('tgsb_submit_button_cmp');
						submitButton.val('search');
						submitButton.unbind('click').click(function(){
							// making the merchants request	
							makeMerchantsRequest(this);
							return false;
						});
						return false;
					});
				};
				// iterating the merchants
				merchants.bind('check', function(){
                    var t	= jQuery(this);
                    if (t.hasClass('mSel')){
                        return true;
                    }
                    t.addClass('mSel');
                    var url		= getJumpLink(t, t.attr('title'), t.attr('rel'));
                    var woid = TGSB.pph.add(url);
                    t.data('woid', woid);
                    if (!woid)
                        t.removeClass('mSel');
                }).bind('uncheck', function(){
                    var t	= jQuery(this);
                    if (!t.hasClass('mSel')){
                        return true;
                    }
                    t.removeClass('mSel');
                    var woid = t.data('woid');
                    TGSB.pph.remove(woid);
                    t.data('woid', '');
                }).click(function(){
					var t	= jQuery(this);
					if (t.hasClass('mSel')){
                        t.trigger('uncheck');
                    } else {
                        t.trigger('check');
                    }
				});
                if (TGSB.pph.browser.mozilla()) {
                    merchants.filter('[rel="5760"]').not('.mSel').click();
                }
				// removing the submited class from the submit button
				jQuery(submitButton).removeClass('submited');
			}			
		});
		return true;
	};
	if(typeof(tgsb_searchboxesParams[searchboxId][searchboxType]) != 'undefined' || tgsb_searchboxesParams[searchboxId][searchboxType] == queryString) {
		if(searchboxsize[1] != 'dynamic')
			searchbox.find('div.formContent').addClass('nod');
		submitButton.addClass('tgsb_submit_button_cmp');
		submitButton.val('compare prices');
		merchantsContainer.removeClass('nod');
		// clicking on the submit button make a classic Search request
		// here an unbind for the click event is needed otherwise if no merchant is selected the alert that asks the user to select at least one merchant will appear so many times the submit button is clicked
		submitButton.unbind('click').click(function(){
			makeClassicSearchRequest(this);
			return false;
		});
	};
};

/*
	WHAT & WHY: function used to create the query string
	WHO & WHEN: Cipri on March, April 2012
	@param	object	obj	contains the object of an input from the form
	@param	array	fields	contains the serialized array of the form fields
	return	string	contains the query string set from the serialized array fields
*/
function createQueryString(obj, fields) {
	if(typeof(fields) == 'undefined' || fields.length == 0)
		return false;
	var queryString = '';
	// iterating the fields array
	jQuery.each(fields, function(i, field){
		/* if the date format is not mm/dd/yyyy then we should process the dates and set them in that format because on the landing pages on travelgrove.com that's the correct format of the date */
		if(TG_Searchboxes_Variables.str_dateFormat != 'mm/dd/yy' && (field.name == 'dep_date' || field.name == 'arr_date' || field.name == 'start_date' || field.name == 'end_date') ) {
			// setting the content form jquery object
			var contForm = jQuery(obj).parents('form');
			// setting the date from the input field
			var tgsb_date = contForm.find('input[name='+field.name+']:eq(0)').datepicker('getDate');
			// adding the date to the query string
			queryString += field.name+'='+setDateByDateFormat(tgsb_date)+((i<(queryString.length-1)) ? '&' : '');
		};
		// check if the value the field is present in the queryString if it's present add an empty string else add the field.name and it's value to the queryString
		queryString += (queryString.match(field.name+'=') && (field.name =='dep_date' || field.name == 'arr_date' || field.name == 'start_date' || field.name == 'end_date')) ? '' : field.name+'='+field.value+((i<(fields.length-1)) ? '&' : '');
	});
	
	return queryString;
};

/*
	WHAT & WHY: function used to make an impression tracking
	WHO & WHEN: Cipri on the 16th of March 2012
	@params	string		selectedTab	contains the class of the selected tab
	@params jQuery object	frmObj		contains the jquery form object of the selected form
	@params bool	if all went well true is returned otherwise false
*/
function makeImpressionTrackingRequest(selectedTab, frmObj, callback) {
	if(typeof(selectedTab) == 'undefined')
		return false;
	if(typeof(callback) != 'function')
		callback = function(){};
	
	// if the queryCode for that selectedTab is not set then do an impressionTracking
	if(TGSB.queryCodes[selectedTab] == 0 || !TGSB.queryCodes[selectedTab]) {
		// making an impression tracking request using the script tag that will be inserted in the DOM
		var impressionTrackingQueryString = createImpressionTrackingQueryString(frmObj);
		// if the impression tracking query string is empty the return false;
		if(impressionTrackingQueryString.length == 0)
			return false;
		impressionTrackingQueryString += '&searchbox='+selectedTab;
		// getting the script used for impression tracking using the impression query string
		jQuery.getScript('http://www.travelgrove.com/js/affiliates/wpPluginImpTrack.php?'+impressionTrackingQueryString, callback);
	}
	// if all went well true is returned
	return true;
}

/*
	WHAT & WHY: function used to create the Impression Tracking Query String
	WHO & WHEN: Cipri on the 16th of March 2012
	@params jQuery object	frmObj	contains the jquery form object
*/
function createImpressionTrackingQueryString(frmObj) {
	if(typeof(frmObj) == 'undefined')
		return '';
	// setting the value of the "from" field to the impression query string
	var tgsbFrom = (typeof(frmObj.find('.asFrom').val()) != 'undefined') ? 'from='+frmObj.find('.asFrom').val()+'&' : '';
	// setting the value of the "to" field to the impression query string
	var tgsbTo = (typeof(frmObj.find('.asTo').val()) != 'undefined') ? 'to='+frmObj.find('.asTo').val()+'&' : '';
	// setting the default value for the departure date
	var tgsbDepDate = '';
	// setting the default value for the return date
	var tgsbRetDate = '';
	if((typeof(frmObj.find('.depDate').val()) != 'undefined')) {
		// if a value is found for the departure date and the date format is different then "mm/dd/yy"
		if(TG_Searchboxes_Variables.str_dateFormat != 'mm/dd/yy') {
			tgsbDepDate = frmObj.find('.depDate').val();
			newtgsbDepDate = tgsbDepDate.split('/');
			// create the right value of the departure date
			tgsbDepDate = newtgsbDepDate[1]+'/'+newtgsbDepDate[0]+'/'+newtgsbDepDate[2];
		};
		// setting the departure date value to the impression query string 
		tgsbDepDate = 'dep_date='+tgsbDepDate+'&';
	};
	if((typeof(frmObj.find('.retDate').val()) != 'undefined')) {
		// if a value is found for the return date and the date format is different then "mm/dd/yy"
		if(TG_Searchboxes_Variables.str_dateFormat != 'mm/dd/yy') {
			tgsbRetDate = frmObj.find('.retDate').val();
			newtgsbRetDate = tgsbRetDate.split('/');
			// create the right value of the return date
			tgsbRetDate = newtgsbRetDate[1]+'/'+newtgsbRetDate[0]+'/'+newtgsbRetDate[2];
		};
		// setting the return date value to the impression query string
		tgsbRetDate = 'ret_date='+tgsbRetDate+'&';
	};
	// setting the idReferral to the impression query string, if it's not defined then set the idReferral value to 999
	var tgsbIdReferral = (typeof(frmObj.find('input[name=idReferral]').val()) != 'undefined') ? 'idReferral='+frmObj.find('input[name=idReferral]').val() : 999;
	// setting the subID to the impression query string, if it's not defined then set the subID to 106
	var tgsbSubID = (typeof(frmObj.find('input[name=subID]').val()) != 'undefined') ? 'subID='+frmObj.find('input[name=subID]').val() : 106;
			
	return tgsbFrom+tgsbTo+tgsbDepDate+tgsbRetDate+tgsbIdReferral+'&'+tgsbSubID+'&trafficSource=wpplugin';
};



/*
	WHAT & WHY: setting the date to the format mm/dd/yyyy
	WHO & WHEN: Cipri on the 23rd of February 2012
*/
function setDateByDateFormat(dateObj) {
	tgsb_day = (dateObj.getDate() < 10) ? '0'+dateObj.getDate() : dateObj.getDate();
	tgsb_month = dateObj.getMonth();
	tgsb_month += 1;
	tgsb_month = (tgsb_month < 10) ? '0'+tgsb_month : tgsb_month;
	tgsb_year = dateObj.getFullYear();
	return tgsb_month+'/'+tgsb_day+'/'+tgsb_year;
};

/* creating the datepicker */
// @param String i1 the content of the departure date input
// @param String i2 the content of the return date input
// @param object rtowInputs the object for the RT/OW radio buttons
function createDatepicker(i1,i2,rtowInputs){
	jQuery('#'+i1+', #'+i2).each(function(){
		var inp = jQuery(this);
		inp.datepicker({
			minDate: 0,
			maxDate: "2y",
			showOn: "both",
			// setting the datepicker image 
			buttonImage: TG_Searchboxes_Variables.str_CalendarURL,
			buttonImageOnly: true,
			dateFormat: TG_Searchboxes_Variables.str_dateFormat,
			// setting the date format 
			onSelect: function(date,dpObj){
				// d1 is the selected date on the datepicker
				d1 = jQuery(this).datepicker("getDate");
				if(this.id == i1) {
					// getting the date from the return date input
					d2 = jQuery.datepicker.parseDate( TG_Searchboxes_Variables.str_dateFormat, jQuery('#'+i2).val());
					// setting the minimum date of the return date to the date of the departure date
					jQuery("#"+i2).datepicker('option','minDate',d1);
					if(d2 && d1>d2) {
						// if the departure date is greater then the return date add 5 day to the departure date and set them to the return date
						d2.setTime(d1.getTime()+60*60*24*5*1000);
						jQuery("#"+i2).val(jQuery.datepicker.formatDate(TG_Searchboxes_Variables.str_dateFormat, d2));
					};
				};
			}
		});
	});
	// we need to check the oneway inputs because regarding this we'll enable or disable the return date input
	if(typeof(rtowInputs) == 'object' && rtowInputs.length > 0) {
		// round trip /oneway select element is present on the 728x90 sized box
		if(rtowInputs.length == 1) {
			// select element id
			var selectRTOW = rtowInputs.get(0).id;
			// when changing the select element
			jQuery('#'+selectRTOW).change(function(){
				// if oneway selected then the return date input should be disabled else should be enabled
				jQuery('#'+i2).attr('disabled',((this.value.length) ? true : false));

			}); 
		};
		// radio buttons for roundtrip oneway
		if(rtowInputs.length == 2) {
			// roundtrip radio input id
			var rt = rtowInputs.get(0).id;
			// oneway radio input id 
			var ow = rtowInputs.get(1).id;
			// getting the searchbox size | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
			var searchboxsize = jQuery('#'+i2).parents('.tg_searchbox').attr('class').match(/m(160x600|300x250|300x533|728x90|dynamic)/);
			// if oneway radio button is checked then the return date input is disabled
			jQuery('#'+ow).change(function(){
				if(this.checked){
					jQuery('#'+i2).attr('disabled',true);
					// make merachants request only for the boxes 160x600, 300x533, dynamic 
					if(searchboxsize != null && (searchboxsize[1] == '160x600' || searchboxsize[1] == '300x533' || searchboxsize[1] == 'dynamic'))
						makeMerchantsRequest(jQuery('#'+i2).get(0));
				};
			});
			// if the roundtrip input is checked then the return date input is enabled
			jQuery('#'+rt).change(function(){
				if(this.checked) {
					jQuery('#'+i2).attr('disabled',false);
					// make merachants request only for the boxes 160x600, 300x533, dynamic
					if(searchboxsize != null && (searchboxsize[1] == '160x600' || searchboxsize[1] == '300x533' || searchboxsize[1] == 'dynamic'))
						makeMerchantsRequest(jQuery('#'+i2).get(0));
				};
			});
		};
	};
};


/*
	WHAT & WHY: function that will be used on a click event to set the search details in the searchform
	WHO & WHEN: Cipri on the 4th of April 2012
	@param	string	selectedTab	contains the name of the class of the tab that should be selected
	@param	bool	roundTripOneWay	if set to true then it's roundtrip otherwise is oneway
	@param	string	fromDepart	contains the location of the departure location
	@param	string	toArrival	contains the location of the arrival location
	@param	string	departDate	contains the departure date
	@param	string	returnDate	contains the return date
	
	ATTENTION! This function was not tested yet
*/

function tgsb_setSearchboxDetails(selectedTab, roundTripOneWay, fromDepart, toArrival, departDate, returnDate) {
	// checking if the params given are set
	if(typeof(selectedTab) == 'undefined' || typeof(roundTripOneWay) == 'undefined' || typeof(fromDepart) == 'undefined' || typeof(toArrival) == 'undefined' || typeof(departDate) == 'undefined' || typeof(returnDate) == 'undefined')
		return false;
	// finding the searchbox on the page
	var cont = jQuery('#content').find('div.tg_searchbox');
	// if the selected tab is not the one is wanted the return false
	if(!cont.find('ul.tg_tabs li span.sel').hasClass(selectedTab))
		return false;
	// finding the selected form
	var searchform			= cont.find('form.sel');
	// finding the submit button on the search form
	var submitButton		= searchform.find('input.tgsb_submit_button');
	// finding the "from" input on the search form
	var departLocationInput		= searchform.find('input.asFrom');
	// finding the "to" input on the search form
	var arrivalLocationInput	= searchform.find('input.asTo');
	// finding the "depart date" input on the search form
	var departDateInput		= searchform.find('input.depDate');
	// finding the "return date" input on the search form
	var returnDateInput		= searchform.find('input.depDate');
	//checking if the "from" input is present on the form
	if(typeof(departLocationInput) == 'object' && departLocationInput.length > 0) {
		// if the "from" input is present on the form the set the value got from the params
		departLocationInput.val(fromDepart);
	};
	//checking if the "to" input is present on the form
	if(typeof(arrivalLocationInput) == 'object' && arrivalLocationInput.length > 0) {
		// if the "to" input is present on the form the set the value got from the params
		arrivalLocationInput.val(toArrival);
	};
	//checking if the "depart date" input is present on the form
	if(typeof(departDateInput) == 'object' && departDateInput.length > 0) {
		// if the "depart date" input is present on the form the set the value got from the params
		departDateInput.val(departDate);
	};
	//checking if the "return date" input is present on the form
	if(typeof(returnDateInput) == 'object' && returnDateInput.length > 0) {
		// if the "return date" input is present on the form the set the value got from the params
		returnDateInput.val(returnDate);
	};
	
	if(selectedTab == 'flights') {
		// on the flights form the oneway inputs are checked if they exist
		var rtowInputs = false;
		var rtowInputs = currentForm.hasClass('flights') ? jQuery(this).find('input[name=oneway]') : false;
		if(typeof(rtowInputs) == 'object' && rtowInputs.length > 0) {
			var rt = rtowInputs.get(0).id; // roundtrip radio input id
			var ow = rtowInputs.get(1).id; // oneway radio input id
			// roundTripOneWay is set to false then we bind the click event to the oneway radio input
			if(!roundTripOneWay) { 
				jQuery('#'+ow).click();
			};
		};
	};
	//make merchants request
	makeMerchantsRequest(submitButton.get(0));
	return false;
};

/**	@note	initializes a single searchbox set (bins AS objects, merchants, etc.)
 *		functionality was not modified, only jQuery refences were changed to point to elements inside `tgsb` got as param
 * 	@date	2013.04.22
 * 	@author	Tibi	*/
function tgsb_initSingleSearchbox(tgsb){
	tgsb	= jQuery(tgsb).filter(':not(.tg_searchbox_initialized)');
	tgsb.addClass('tg_searchbox_initialized');
	if (tgsb.length==0)
		return false;
	// on focusing on an input it's default value will disappear
	// bluring the default value will appear back 
	tgsb.find(".tgsb_addAS").each(function(){
		jQuery(this).focus(inputFocus).blur(inputBlur);
		new AS(this.id,ASoptions);
	});
	tgsb.find(".tgsb_addASH").each(function(){
		jQuery(this).focus(inputFocus).blur(inputBlur);
		new AS(this.id,hotelASoptions);
	});
	//clicking on the searchoxes tabs
	tgsb.find('ul.tg_tabs li span').click(function(){
		// getting the selected tab
		selectedTab = jQuery(this).attr('class').match(/^[a-z]+/);
		// getting the parrent container with the css class tg_searchbox
		var cont = jQuery(this).parents("div.tg_searchbox:eq(0)"); 
		// selecting a tab
		cont.find('ul.tg_tabs li span').removeClass('sel');
		cont.find('ul.tg_tabs li span.'+selectedTab).addClass('sel');
		// selecting a form
		cont.find('div.tg_container form').removeClass('sel');
		cont.find('div.tg_container form.'+selectedTab).addClass('sel');
		var selectedForm = cont.find('div.tg_container form.sel');
		var submitButton = selectedForm.find('input.tgsb_submit_button');
		// make an impression tracking request for the selected tab, selected form
		makeImpressionTrackingRequest(selectedTab, selectedForm, function(){
			// setting the searchboxsize | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
			var searchboxsize = selectedForm.parents('.tg_searchbox').attr('class').match(/m(160x600|300x250|300x533|728x90|dynamic)/);
			// for the boxes sized 160x600, 300x533 and dynamic make merchants request
			if(searchboxsize[1] == '160x600' || searchboxsize[1] == '300x533' || searchboxsize[1] == 'dynamic')
				makeMerchantsRequest(submitButton.get(0), false, false);
		});
	});

	tgsb.find('form').each(function() {
		var currentForm = jQuery(this);
		// setting the searchboxsize | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
		var searchboxsize = currentForm.parents('.tg_searchbox').attr('class').match(/m(160x600|300x250|300x533|728x90|dynamic)/);
		// setting the submit button of the current form
		var submitButton = currentForm.find('input.tgsb_submit_button');
		if(currentForm.hasClass('sel')) {
			selectedTab = currentForm.attr('class').match(/^[a-z]+/);
			//making an impression tracking when selecting a tab
			makeImpressionTrackingRequest(selectedTab, currentForm, function(){
				// setting the searchboxsize | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
				var searchboxsize = currentForm.parents('.tg_searchbox').attr('class').match(/m(160x600|300x250|300x533|728x90|dynamic)/);
				// for the boxes sized 160x600, 300x533 and dynamic make merchants request
				if(searchboxsize[1] == '160x600' || searchboxsize[1] == '300x533' || searchboxsize[1] == 'dynamic')
					makeMerchantsRequest(submitButton.get(0), false, false);
			});
		};
		var airClass = currentForm.find('select[name=class]');
		// if an element which has the name class is present on the form
		if(airClass.length) {
			// then when it is changed
			airClass.change(function(){
				// make a merchants request
				makeMerchantsRequest(this);
			});
		};

		// datepicker inputs
		var inputs = currentForm.find(".tgsb_addDP");
        if (inputs.length>1) {
            var i1 = inputs.get(0).id; // departure date input
            var i2 = inputs.get(1).id; // return date input
            // setting the default value of the oneway input
            var rtowInputs = false;
            var rtowInputs = currentForm.hasClass('flights') ? currentForm.find('input[name=oneway], select[name=oneway]') : false;
            /* creating the datepicker */
            createDatepicker(i1, i2, rtowInputs);
        }
		/*	@note	the submit button should be wrapped behind an iframe in chrome to make popups work
			@date	2013 JUN 06
			@author	Tibi	*/
		currentForm.submit(function() {
			if(submitButton.hasClass('submited'))
				return false;
			submitButton.addClass('submited');

			makeMerchantsRequest(submitButton.get(0), true, true);
			return false;
		});
	});
}


jQuery(function(){

	ajaxLoaders = {
		'160x600':'<img src="'+TG_Searchboxes_Variables.str_ajaxLoaderCircle+'" width="100" height="100" alt="loading..." />',
		'300x250':'<img src="'+TG_Searchboxes_Variables.str_ajaxLoaderCircle+'" width="100" height="100" alt="loading..." />',
		'300x533':'<img src="'+TG_Searchboxes_Variables.str_ajaxLoaderCircle+'" width="100" height="100" alt="loading..." />',
		'728x90':'<img src="'+TG_Searchboxes_Variables.str_ajaxLoaderBert+'" width="128" height="15" alt="loading..." />',
		'dynamic':'<img src="'+TG_Searchboxes_Variables.str_ajaxLoaderBert+'" width="128" height="15" alt="loading..." />'
	};

	ASoptions = {
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
			domainPrefix:	true
		},
		autoSelect:true,
		offsety:0,
		format: function(selLiObj) {
			return selLiObj.innerHTML.replace(/<\/?[a-z]+[^>]*>/gi,'').replace(/(.*),(.*)\((.*)\)/,'$1 ($3)');
		},
		callback:function(selLiObj, asObj) {
			// checking the size of the searchbox | regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
			var searchboxsize = jQuery(asObj.fld).parents('.tg_searchbox').attr('class').match(/(300x250|728x90)/);
			// the merchants refresh is not made when location is selected from the autosuggestion on the boxes with the measures of 300x250 and 728x90 because on those searchboxes the process includes 2 screens
			if(searchboxsize == null)
				makeMerchantsRequest(asObj.fld);
		},
		errorHandler:function(asObj) {
			jQuery(asObj.fld).addClass("err").focus(remErr);
		}
	};
	
	hotelASoptions = {
		delay: 175,
		timeout:5000,
		script: TG_Searchboxes_Variables.str_ASAjaxURL,
		loadingClass: 'tgsb_as_load',
		className: 'tgsb_as tgsb_asMargin',
		json: true,
		frameForIE: true,
		ajaxParams: {
			action: '',
			json: true,
			lng:'def',
			dsgn:'flg',
			addtag:'em',
			citytype:'cities',
			domainPrefix:true
		},
		autoSelect:true,
		offsety:0,
		format: function(selLiObj) {
			//jQuery(".tgsb_addDest").val(selLiObj.id);
			return selLiObj.innerHTML.replace(/<\/?[^>]+>/gi,'').replace(/(.*),(.*) \((.*)\)/,'$1,$2');
		},
		callback:function(selLiObj,asObj) {
			var inp = jQuery(asObj.fld);
			inp.parents('div.tg_searchbox').find(".tgsb_addDest").val(selLiObj.id);
			// regexp changed by Tibi to match in whole class instead of the end of the string | 2013.04.23
			var searchboxsize = inp.parents('.tg_searchbox').attr('class').match(/(300x250|728x90)/);
	// the merchants refresh is not made when location is selected from the autosuggestion on the boxes with the measures of 300x250 and 728x90 because on those searchboxes the process includes 2 screens
			if(searchboxsize == null)
				makeMerchantsRequest(asObj.fld);
		}
	};
	
	/*	searchbox initialization moved to a separate function to make possible the initialization of the searchboxes if they are loaded via JS as well (after doc. ready) */
	tgsb_initSingleSearchbox('.tg_searchbox');
	/*	if this JS file is not yet loaded when the searchbox builder JS is loaded, instead of adding the searchbox to the DOM,
	 *	the JS files add the searchbox to a JS array and here we walk through this array and add each searchbox to the DOM
	 *	IMPORTANT: if this file is already loaded when the searchbox JS is loaded, the placeholder is replaced w/ the searchbox	*/
	if (typeof(TGSB_placeholders)!='undefined' && TGSB_placeholders.length>0){
		for(var i=0;i<TGSB_placeholders.length;i++)
			replacePlaceholder(TGSB_placeholders[i].placeholder, TGSB_placeholders[i].html);
	}
	
	initWindowOpener();
});
})(tgsb_myjquery);