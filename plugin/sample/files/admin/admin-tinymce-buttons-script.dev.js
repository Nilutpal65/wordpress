// variable used in the popup div it's set
var tgsb_selBoxParam = false;
/* javascript file used when clicking the button on the tinyMCE editor */
/* tgsb_images is used fo the images that are replacing the shortcode in the tinymce editor */
var tgsb_images = {
	'160x600':{'url':TG_Searchboxes_Editor_Button.str_tgsb160x600,'width':160,'height':600,'alignment':'alignnone'},
	'300x250':{'url':TG_Searchboxes_Editor_Button.str_tgsb300x250,'width':300,'height':250,'alignment':'alignnone'},
	'300x533':{'url':TG_Searchboxes_Editor_Button.str_tgsb300x533,'width':300,'height':533,'alignment':'alignnone'},
	'728x90':{'url':TG_Searchboxes_Editor_Button.str_tgsb728x90,'width':728,'height':90,'alignment':'alignnone'},
	'dynamic':{'url':TG_Searchboxes_Editor_Button.str_tgsbdynamic,'width':628,'height':250,'alignment':'alignnone'}
};
// getting the "from" field form the serialized array
function tgsbFromAir(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	var returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "from" field and it's value in the serialized array
		if(!returnValue && field.name == 'tgsbFromAir') {
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
    console.log(returnValue);
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

/********************** the object used on tinymce.create **********************************/

var tgsb_popupInitOpt = {
	init:function(ed, url){
		var t = this;

		t.url = url;
		t._createButtons();
		
		ed.addCommand("mceTGSearchboxesInsert",function(){tg_searchboxes_tinymce_button_click(t);});
		ed.addButton("TGSearchboxes",{
			title:TG_Searchboxes_Editor_Button.str_EditorButtonTitle,
			cmd:"mceTGSearchboxesInsert",
			image:url+"/../images/tg20x20.gif"
		});

		ed.onNodeChange.add(function(ed,cm,n){
			cm.setActive("TGSearchboxes",n.nodeName=="IMG")
		});
		
		ed.onMouseDown.add(function(ed, e) {
			if((e.target && e.target.nodeName == 'IMG' && ed.dom.hasClass(e.target, 'TGSearchboxes')) || (e.target && e.target.firstChild && e.target.firstChild.nodeName == 'IMG' && ed.dom.hasClass(e.target.firstChild, 'TGSearchboxes'))) {
				// the value of this variable is used on the popup div when a user wants to edit a shortcode from the tinymce editor
				tgsb_selBoxParam = t.getAttr(e.target.title,'options');
				// catching the error in case the user mistypes the shortcode
				try {
					// the options attribute is present then evaluate it
					tgsb_selBoxParam = eval('('+tgsb_selBoxParam+')');
				}catch(tgsb_err) {
					// if an error is found the set the options variable to the default values
					tgsb_selBoxParam = {"size":"300x250","selectedTab":"flights"};
				};
				ed.plugins.wordpress._showButtons(e.target, 'wp_gallerybtns');
				
			} else {
				tgsb_selBoxParam = false;
			};
		});

		ed.onBeforeSetContent.add(function(ed, o) {
			// called when visual content is displayed in the editor
			o.content = t._do_tg_searchboxes(o.content);
		});
		
		ed.onPostProcess.add(function(ed, o) {
			// called when html editor is displayed in the editor
			if (o.get) {
				o.content = t._get_tg_searchboxes(o.content);
				// unsetting the tgsb_selBoxParam set on tinymce editor
				if(typeof(tgsb_selBoxParam) != 'undefined' && tgsb_selBoxParam)
					tgsb_selBoxParam = false;
			};
		});
	},
	// s = content n = an attribute we want the value to be returned by the function
	getAttr: function(s, n) {
			var res		= new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			if(res)		return tinymce.DOM.decode(res[1]);
			res		= new RegExp(n + "=\'([^\']+)\'", 'g').exec(s);
			if(res)		return tinymce.DOM.decode(res[1]);
			return '';
	},
	
	_get_tg_searchboxes : function(co) {
		return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
			var cls = tgsb_popupInitOpt.getAttr(im, 'class');
			if ( cls.indexOf('TGSearchboxes') != -1 ) {
				//from the title attribute is read the shortcode
				return '<p>['+tinymce.trim(tgsb_popupInitOpt.getAttr(im, 'title'))+']</p>';
			};
			return a;
		});
	},
	
	_do_tg_searchboxes : function(co) {
		return co.replace(/\[tg_searchboxes([^\]]*)\]/g, function(a,b){
			// a it's the whole shortcode i.e. [tg_searchbox options='{"size":"300x533"}' options1='{"size":"160x600"}']
			// b it's a string containing the attributes and values options='{"size":"300x533"}' options1='{"size":"160x600"}'

			var options = tgsb_popupInitOpt.getAttr(a, 'options');
			// default searchbox size
			var tgSearchboxSize = '300x250';
			var tgsbImageUrl = '';
			var tgsbImageWidth = 300;
			var tgsbImageHeight = 250;				
			if(options.length) {
				// catching the error in case the user mistypes the shortcode
				try {
					// the options attribute is present then evaluate it
					options = eval('('+options+')');
				}catch(tgsb_err) {
					// if an error is found the set the options variable to an empty string
					options = '';
				};
				if(typeof(options.size) != 'undefined') {
					tgSearchboxSize = options.size;
				};
			};
			tgsbImageUrl = tgsb_images[tgSearchboxSize].url;
			tgsbImageWidth = tgsb_images[tgSearchboxSize].width;
			tgsbImageHeight = tgsb_images[tgSearchboxSize].height;
			tgsbImageAlign = (typeof(options.alignment) == 'undefined') ? tgsb_images[tgSearchboxSize].alignment : options.alignment;
			// we use the inline style because in this case it's not needed to add some css rules into the file editor-style.css from the current theme folder
			return '<img src="'+tinymce.baseURL+'/plugins/wpgallery/img/t.gif" class="TGSearchboxes mceItem '+tgsbImageAlign+'" style="background-image:url('+tgsbImageUrl+');background-repeat:no-repeat;width:'+tgsbImageWidth+'px;height:'+tgsbImageHeight+'px;padding:0" title="tg_searchboxes'+tinymce.DOM.encode(b)+'"/>';
		});
	},
	_createButtons : function() {
		var t = this, ed = tinyMCE.activeEditor, DOM = tinymce.DOM, editButton, dellButton;

		DOM.remove('wp_gallerybtns');

		DOM.add(document.body, 'div', {
			id : 'wp_gallerybtns',
			style : 'display:none;'
		});

		editButton = DOM.add('wp_gallerybtns', 'img', {
			src : t.url+'/img/edit.png',
			id : 'wp_editgallery',
			width : '24',
			height : '24',
			title : 'Edit Searchbox'
		});

		(tinymce.dom.Event.bind ? tinymce.dom.Event.bind : tinymce.dom.Event.add)(editButton, 'mousedown', function(e) {
			var ed = tinyMCE.activeEditor, el = ed.selection.getNode();
			/*	@note	the condition to run the `mceTGSearchboxesInsert` command was added to resolve conflicts w/ other plugins that use the gallery button
				@date	2013-OCT-17
				@author	Tibi	*/
			if ( el.nodeName == 'IMG' && ed.dom.hasClass(el, 'TGSearchboxes') ) {
				ed.windowManager.bookmark = ed.selection.getBookmark('simple');
				ed.execCommand("mceTGSearchboxesInsert");
			};
		});

		dellButton = DOM.add('wp_gallerybtns', 'img', {
			src : t.url+'/img/delete.png',
			id : 'wp_delgallery',
			width : '24',
			height : '24',
			title : 'Delete Searchbox'
		});

		(tinymce.dom.Event.bind ? tinymce.dom.Event.bind : tinymce.dom.Event.add)(dellButton, 'mousedown', function(e) {
			var ed = tinyMCE.activeEditor, el = ed.selection.getNode();

			if ( el.nodeName == 'IMG' && ed.dom.hasClass(el, 'TGSearchboxes') ) {
				ed.dom.remove(el);

				ed.execCommand('mceRepaint');
				return false;
			};
		});
	},
	createControl:function(n,cm){
		return null
	},
	getInfo:function(){
		return{
			longname:"Travelgrove Searchboxes",
			author:"Travelgrove.com",
			authorurl:"http://www.travelgrove.com/",
			infourl:"http://www.travelgrove.com/",
			version:"1.0"
		};
	}
};
/************************ end of the object used on the tinymce.create function ********************************/

/*
	function used when clicking the editor button to insert / update a searchbox
*/
function tg_searchboxes_tinymce_button_click(tinyMCE_obj){
	var title="Travelgrove Searchboxes";
	
	var url=TG_Searchboxes_Editor_Button.str_EditorButtonAjaxURL.replace(/\&amp\;/ig, '&');
	
	tb_show(title,url,false);
			
	jQuery("#TB_ajaxContent").width("auto").height("94.5%").click(function(event){
		var $target = jQuery(event.target);
		if($target.is('a.send_searchbox_to_editor') || $target.is('input.send_searchbox_to_editor')) {
			// getting the measures of the box
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
			
			optionsString += (selectedTab == null) ? '"selectedTab":"flights",' : 
			// if the selectedTab is set and it's flights then do not set it in the options because the flights selected tab is considered by default
						((selectedTab[1] == 'flights' ) ? '' : 	'"selectedTab":"'+selectedTab[1]+'",'
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
			// transforming the shortcode into an image for the tinymce editor
			tgSearchboxesShortcode	= tinyMCE_obj._do_tg_searchboxes(tgSearchboxesShortcode);
			// adding the shortcode to the editor
			tinyMCE.execCommand("mceInsertContent",0, tgSearchboxesShortcode);
			// unsetting the tgsb_selBoxParam they should be not set after an update is made
			if(typeof(tgsb_selBoxParam) != 'undefined' && tgsb_selBoxParam) {
				tgsb_selBoxParam = false;
			};
			// removing the thickbox popup div
			tb_remove();
			return false;
		};
		return false;
	});
	return false;
};

jQuery(document).ready(function(jQuery){
	tinymce.create("tinymce.plugins.TGSearchboxes", tgsb_popupInitOpt);
	tinymce.PluginManager.add("TGSearchboxes",tinymce.plugins.TGSearchboxes)
});