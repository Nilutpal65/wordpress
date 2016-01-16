window.undefined = window.undefined;
function loading() {};
var parentObject = this;

function cloneObject(src) {
	var dest = new Object;
	for (i in src) {
		if (typeof src[i] == 'object') {
			dest[i] = cloneObject(src[i]);
		}
		else dest[i] = src[i];
	}
	return dest;
};

parentObject.getPos = function(e){
        var o = jQuery(e).offset();
        return {
                'x' : o.left,
                'y' : o.top
        }
};

var defaultParams = {
	script:"ajax/suggestions/airports.php",
	minchars:3,
	className:"as",
	delay:500,
	// the ajax request delay
	timeout:2500,
	// the dissappear delay
	cache:true,
	loadingClass:"aLoad",
	offsety:2,
	showNoResults:false,
	maxheight:250,
	noResults:"No results!",
	meth:"POST",
	json:false,
	frameForIE:true,
	autoSelect:true,
	ajaxParams:{
		action : "test",
		json : this.json
	},

	setAjaxParams:function(asObj) {
		var idx=0, aPrms='';
		for(key in this.ajaxParams) {
			if(idx==0)	aPrms += key+'='+asObj.fld.value;
			else		aPrms += '&'+key+'='+this.ajaxParams[key];
			/*	it it's the first element, passing the input
				otherwise passing the ajaxParams' value	*/
			idx++;
		}
		asObj.ajaxObject.data = aPrms;
		return true;
		// setting up the first parameter to the value of the field
	},

	setAjaxParams:function(asObj) {
		var idx=0, aPrms='';
		for(key in this.ajaxParams) {
			if(idx==0)	aPrms += key+'='+asObj.fld.value;
			else		aPrms += '&'+key+'='+this.ajaxParams[key];
			/*	it it's the first element, passing the input
				otherwise passing the ajaxParams' value	*/
			idx++;
		}
		asObj.ajaxObject.data = aPrms;
		return true;
		// setting up the first parameter to the value of the field
	},
	format:function(liObj) {
		return liObj.innerHTML.replace(/<\/?[a-z]+>/gi,'');
	},
	errorHandler:function(asObj) {
		return;
	}
};
if(parentObject.AutoSuggest == undefined) {
	parentObject.AutoSuggest = {};
};
parentObject.AutoSuggest = function(fldID, pObj) {
	//The autosuggestion contructor function -- init variables
	if(!(this.fld=jQuery('#'+fldID)[0])) return false;
	// if the field element wasn't found ==> return
	this.fld.setAttribute("autocomplete","off");
	//prevent autocomplete from client
	this.sInput		= "";
	// The previously entered text to compare with the current -- the 'search for ...' buffer
	this.nInputChars 	= 0;
	//the length of the previously entered string
	this.aSuggestions 	= {};
	// The previously obtained results -- the 'results' buffer
	this.defText = this.fld.value;
	// the default text that should appear in the input field (if focus lost and input is empty)
	this.highlighted 	= null;
	// the selected item
	this.opt=pObj?cloneObject(pObj):{};
	// the parameters object
	for(k in defaultParams) {
		if(typeof(this.opt[k])!=typeof(defaultParams[k])) {
			this.opt[k]=defaultParams[k];
		};
	};
	var pointer = this;
	// points to the current object
	this.ajaxObject = {
		url:		this.opt.script,
		global:		false,
		type:		this.opt.meth,
		/** taken out since it did not work on admin interface like this
		dataType:	this.opt.json ? 'json' : 'xml',*/
		dataType:	'text',
		data:		this.opt.ajaxParams,
		error:		function(req, errmsg, exc) {
					// console.log("AJAX error: "+errmsg+", an exception occured of the following type:"+exc);
		},
		success:	function(rsp) {pointer.generateList(rsp);},
		errorFlag:	0,
		/*	using it for determinating if normal results was returned or not	*/
		complete:	function(reqObj, tos) {
			// console.log("Object: "+reqObj+", The type of success:"+tos);
		}
		//type of success
	};
	this.coords = getPos(this.fld);
	this.coords.y += this.fld.offsetHeight + this.opt.offsety;
	// coords.w = pointer.fld.offsetWidth;
	//calculating the position of the input field: x,y + the height of the input field  + the offset y setted in the params
	if(this.opt.frameForIE && jQuery.browser && jQuery.browser.msie && parseInt(jQuery.browser.version)<7) {
		this.iframe = jQuery('<iframe src="about:blank" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" style="position:absolute;background:#fff;z-index:399;">');
	} else {
		this.opt.frameForIE = false;
	};
	// if frameForIE is turned on, the Browser is IE and the version is lower than 7 ==> 6, 5.5, 5
	// otherwise even if frameForIE was turned on will be turned off
	this.blurTimer = 0;
	// if the field loses the focus but the request doesn't finished --> preventing the list to appear	
	// set keyup handler for field
	// NOTE: not using addEventListener because UpArrow fired twice in Safari
	var pointer=this;
	this.isFocused=true;
	/*	used to detect if the field has the focus right now	*/
	this.fld.onkeypress	= function(ev) {return pointer.onKeyPress(ev);};
	this.fld.onkeyup	= function(ev) {return pointer.onKeyUp(ev);};
	this.fld.onblur		= function(ev) {
		pointer.isFocused=false;
		if(!pointer.fld.value) return;
		pointer.blurTimer = setTimeout(function() {
			if(pointer.ajaxObject.errorFlag)
				pointer.opt.errorHandler(this);
			/*	calling the error-handling function if previously no results were returned	*/
			if(pointer.opt.autoSelect)
				pointer.setHighlightedValue();
			else pointer.clearSuggestions(ev);
			pointer.highlighted=null;
		}, pointer.opt.delay/2);
	};
	//this.fld.onclick = function(ev) {this.select();}
	this.fld.onfocus = function(ev) {
		/*this.select();*/
		pointer.highlighted=null;
		pointer.isFocused=true;
	};
	this.req={};
	// the XMLHTTP request object
	this.fld.autoSuggest = this;
};

parentObject.AutoSuggest.prototype.onKeyUp = function(ev) {
	//var key = ev.keyCode || window.event.keyCode;
	// for some strange reason not working -- maybe only from jQuery
	var key = (window.event) ? window.event.keyCode : ev.keyCode;
	// set responses to keypress events in the field
	// this allows the user to use the arrow keys to scroll through the results
	// Array up and down catched here
	var bubble = true;
	switch(key) {
		case 38: //Array UP
			this.changeHighlight(key);
			bubble = false;
			break;
		case 40: //Array DOWN
			this.changeHighlight(key);
			bubble = false;
			break;
		case 13:break;
		default:this.getSuggestions();
	}
	return bubble;
};

parentObject.AutoSuggest.prototype.onKeyPress = function(ev) {
	//var key = ev.keyCode || window.event.keyCode;
	var key = (window.event) ? window.event.keyCode : ev.keyCode;
	// set responses to keydown events in the field
	// this allows the user to use the arrow keys to scroll through the results
	// ESCAPE clears the list
	// TAB sets the current highlighted value
	var bubble = true;
	this.isFocused=true;
	//alert(key);
	switch(key) {
		case 13: //RETURN
			this.setHighlightedValue();
			bubble = false;
			break;
		case 27: //ESC
			this.setHighlightedValue();
			break;
		case 9: break; //TAB
	}
	return bubble;
};

parentObject.AutoSuggest.prototype.getSuggestions = function() {
	var val = this.fld.value.toLowerCase();
	// if input stays the same, do nothing
	if (val == this.sInput) return false;
	// input length is less than the min required to trigger a request
	// reset input string
	// do nothing
	if (val.length < this.opt.minchars) {
		this.clearSuggestions();
		this.sInput = "";
		return false;
	}
	// if caching enabled, and user is typing (ie. length of input is increasing)
	// filter results out of aSuggestions from last request
	this.makeReq=true;
	if (val.length>this.nInputChars && this.aSuggestions.length && this.opt.cache && this.aSuggestions[0] && this.aSuggestions[0].childNodes) {
	//if the entered string is longer than the stored one,
	//the buffer is not empty
	//and caching is enabled
		//val = val.replace(/(\(|\))/g,'');
		val = val.replace(/[^a-zA-Z????]/g,' ');
		// escaping all the special caracters and numbers
		with(this.aSuggestions[0]) {
			for (var i=0;i<childNodes.length;i++) {
				if(childNodes[i].nodeType!=1) {
					continue;
				};
				/*
				var txt = childNodes[i].textContent?
					childNodes[i].textContent:
					childNodes[i].innerHTML.replace(/<\/?b>/gi,'');
				*/
				var txt	= childNodes[i].innerHTML.replace(/<\/?[^>]+>/gi, '');
				if(!txt.toLowerCase().match(val)) {
					removeChild(childNodes[i--]);
					//if a childNode was removed setting decrementing the counter to don't jump over an item
				} else {
					childNodes[i].innerHTML = this.highlightText(childNodes[i].innerHTML, val);
					// if found remove the highlighting and making the new one
				};
			};
		};
		if(!this.aSuggestions[0].childNodes.length) {
			this.clearSuggestions();
			this.doAjaxRequest(this.ajaxObject);
			return false;
		};
		this.makeReq=false;
		// it there is no results left in the cache deleting the suggestion box and making a new request
		if(this.opt.frameForIE)
			this.updateIframe(this.coords);
		// updating the iframe's position
		this.sInput = val;
		this.nInputChars = val.length;
		// actualizing the word-buffer / the nr. of car.
		if(!this.aSuggestions[0].parentNode) {
			document.body.appendChild(this.aSuggestions[0]);
			if(this.opt.frameForIE) {
				document.body.appendChild(this.iframe[0]);
			};
		};
		//if the timeout occured and the box is no longer visible
		if(this.highlighted) {
			this.clearHighlight();
		};
		//resetting the highlighted element if exists
		//this.killTimeout();
		// deleting the hideing timeout
		return false;
	} else {
	// do new request
		this.sInput = val;
		this.nInputChars = val.length;
		var pointer = this;
		clearTimeout(this.ajID);
		this.ajID = setTimeout( function() { pointer.doAjaxRequest(pointer.ajaxObject); }, this.opt.delay );
		// the ID for the timeout of the AJAX request
		return false;
	}
};

parentObject.AutoSuggest.prototype.doAjaxRequest = function () {
	if(this.fld.value.length<this.opt.minchars) {
		return;
	};
	jQuery(this.fld).addClass(this.opt.loadingClass);
	// have to check here too due the execution delay ==> if the user enters LAS and at once hit the backspace ==> LA
	// the first condition: length('LAS')>=minchars is true so a request will be sent, but when sends it,
	// the value of the field is already 'LA'
	this.opt.setAjaxParams(this);
	//eval("this.ajaxObject.data."+this.opt.varname+"=this.fld.value;");
	// varname = the current value of the input field
	this.req = jQuery.ajax(this.ajaxObject);
	this.ajaxObject.errorFlag = 0;
	// sending the request via jquery ajax abstraction
};
parentObject.AutoSuggest.prototype.generateList = function (req) {
	this.coords = getPos(this.fld);
	this.coords.y += this.fld.offsetHeight + this.opt.offsety;
	this.makeReq=false;
	// the function processes the given response
	if(this.aSuggestions[0] && this.aSuggestions[0].parentNode) {
		this.aSuggestions.remove();
	};
	//if the element is not on-the-fly, but have a valid parent node -- body -- ==>
	// there is a previous result ==> deleting it
	var jsondata,rsp;
	if (this.opt.json) {
		try { jsondata = eval('('+req+')'); } catch(e) { return e; }
		rsp='<ul>';
		n = jsondata.argumentList ? jsondata.argumentList.length : 0;
		for (i=0;i<jsondata.results.length;i++) {
			rsp += '<li';
			for(j=0;j<n;j++) {
				var argName = jsondata.argumentList[j];
				var argValue = jsondata.results[i][argName];
				rsp += ' '+argName+'="' + this.escapeHtml(argValue) + '"';
			}
			rsp += '>'+jsondata.results[i].txt+'</li>';
		}
		rsp+='</ul>';
	} else { //XML
		rsp = req.substr(40);
	}
	jQuery(this.fld).removeClass(this.opt.loadingClass);
	if (this.isFocused) {
		this.createList(rsp, jsondata);
	}
	else {
		if(this.opt.autoSelect) {
			this.autoSelect(rsp, jsondata);
		}
	}
};

parentObject.AutoSuggest.prototype.escapeHtml = function(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

parentObject.AutoSuggest.prototype.autoSelect = function(rsp, jsonObj) {
	if(!jsonObj.results || !jsonObj.results.length) {
		this.opt.errorHandler(this);
		this.ajaxObject.errorFlag = 1;
		clearTimeout(this.blurTimer);
		return false;
	}
	/*	if we don't get any results:
			calling the right error-handling function,
			setting the error flag and
			deleting the blur timer since the field does not have the focus yet
	*/
	/* if(true || jsonObj.results[0].matchType=='airport') { */
	/*	let's just use it for all cases	*/
	firstElement = '<li';
	n = jsonObj.argumentList ? jsonObj.argumentList.length : 0;
	for(j=0;j<n;j++) {
		var argName = jsonObj.argumentList[j];
		var argValue = jsonObj.results[0][argName];
		firstElement += ' '+argName+'="'+argValue+'"';
	}
	firstElement += '>'+jsonObj.results[0].txt+'</li>';;
	this.highlighted = jQuery(firstElement).get(0);
	this.setHighlightedValue();
	return true;
};

parentObject.AutoSuggest.prototype.createList = function(rsp, jsonObj) {
	if(!jsonObj.results || !jsonObj.results.length) {
		this.ajaxObject.errorFlag = 1;
		/*
			setting up the error flag, but the error-handling function will be called only when the field loses the focus,
			avoiding in this way to run the error-handling process when the user misspells the city then he corrects:
			mexd --> mex
		*/
		return false;
	}
	// the autosuggestion object
	var pointer = this;
	// variables for hightlighting
	pointer.aSuggestions = jQuery(rsp).attr({"class":pointer.opt.className, "id":"as"+pointer.fld.id}).css({left:pointer.coords.x, top:pointer.coords.y}).appendTo('body').css({'display':'block'});
	pointer.aSuggestions.hide();
	pointer.aSuggestions.show();
	internal_cnt = 0;
	pointer.aSuggestions.find('li').each(function(){
		internal_cnt++;
		jQuery(this).addClass(internal_cnt%2==0 ? 'even' : 'odd');
	});
	if(this.opt.frameForIE)
		this.updateIframe(this.coords).appendTo('body');
	// width:coords.w,
	pointer.aSuggestions.children("li").each(function(idx) {
		this.innerHTML	= pointer.highlightText(this.innerHTML, pointer.fld.value);
	/*
		strippedTxt: Mexico City, Mexico - History
		highlighedTxt: <b>Mex</b>ico City, Mexico - History
		this.innerHTML: <span class="user_page_icons upi_history">Mexico City, Mexico - <i>History</i></span>
		highlighting
	*/
		jQuery(this).mousedown(function() {pointer.setHighlightedValue();pointer.fld.focus();});
		jQuery(this).mouseover(function() {pointer.setHighlight(this);});
	});
	if (!this.isFocused) {
		this.setHighlightedValue();
		return;
		/*	if the focus was already moved from the selected element, select the first match	*/
	}
	// pointer.aSuggestions.mouseover(function() {pointer.killTimeout();});
	// pointer.aSuggestions.mouseout(function() {pointer.resetTimeout();});
	// set mouseover functions for the list object
	// when mouse pointer leaves list, set a timeout to remove the list after an interval
	// when mouse enters list, kill the timeout so the list won't be removed
	pointer.highlighted = null;
	// currently no item is highlighted
	// pointer.resetTimeout();
	// remove list after an interval
	// no results
	if(pointer.aSuggestions[0].childNodes.length == 0) {
		if (pointer.opt.showNoResults) {
			pointer.aSuggestions.append('<li>'+pointer.opt.noResults+'</li>');
		} else {
			pointer.clearSuggestions();
		};
	};
	return;
};

parentObject.AutoSuggest.prototype.changeHighlight = function(key) {
	// n = this.aSuggestions[0].childNodes.length;
	// var act = (this.highlighted+(key-39)) % n || n;
	// the index to be activated
	// Down Key == 40 ==> 40 - 39 == 1
	// Up Key == 38 ==> 38 - 39 == -1
	// % n ==> if it is greater than the nr. of the fields ==> act = 1, the first will be highlighted
	// || n ==> if it is 0 ==> act=n, the last will be highlighted
	// this.setHighlight(act);

	// the previos, numeric method --> doesn't work if caching is enabled, bacause some elements can be removed, 
	// so the indexes don't remain valid
	// changed with OO modell, this.highlighted stores always the selected LI object
	if(!this.aSuggestions[0]) {
		return false;
	};
	/*	preventing a bug: when the list is already dissapeared but the user hits up and down arrows	*/
	if(this.highlighted==null) {
		return this.setHighlight(this.highlighted = this.aSuggestions[0].firstChild);
	};
	if(key==38) {//UP
		if(this.highlighted == this.aSuggestions[0].firstChild) {
			return this.setHighlight(this.aSuggestions[0].lastChild);
		};
		// if the first elemenent is selected ==> select the last one
		return (this.highlighted.previousSibling.nodeType==1)?
				this.setHighlight(this.highlighted.previousSibling):
				this.setHighlight(this.highlighted.previousSibling.previousSibling);
		// else select the previous one if it's not a blank text node
	} else { //DOWN
		if(this.highlighted == this.aSuggestions[0].lastChild) {
			return this.setHighlight(this.aSuggestions[0].firstChild);
		};
		// if the last elemenent is selected ==> select the first one
		return ((this.highlighted.nextSibling.nodeType==1)?
				this.setHighlight(this.highlighted.nextSibling):
				this.setHighlight(this.highlighted.nextSibling.nextSibling));
		// else select the next one, if it's an element node and not a blank text one
		//if it is a blank text node ==> next->next
	};
};

parentObject.AutoSuggest.prototype.setHighlight = function(liObj) {
	if(this.highlighted!=null) {
		this.clearHighlight();
	};
	this.highlighted = liObj;
	jQuery(this.highlighted).addClass('as_hl');
	return true;
	// for bubbleing the 'onmouseover' event
};

parentObject.AutoSuggest.prototype.clearHighlight = function() {
	// numeric method changed to the OO
	jQuery(this.highlighted).removeClass('as_hl');
	this.highlighted = null;
};
parentObject.AutoSuggest.prototype.setHighlightedValue = function () {
	if(this.ajaxObject.errorFlag) {
		return;
	};
	clearTimeout(this.blurTimer);
	if(!this.fld.value) {
		return;
	};
	/*	if there is nothing in the input just return	*/
	if(!this.highlighted) {
	/*	if there is no highlighted element	*/
		try{
		var t=	this.aSuggestions &&
			this.aSuggestions.children && 
			this.aSuggestions.children('li:eq(0)').get(0);
		} catch(err) {};
		/*	checking if:
				the aSuggestions object exists,
				if it has the children function so it's a jquery object,
				and if it's populated
		*/
		if(!t) {
		/*	if there are NO elements	*/
			this.clearSuggestions();
			return false;
		}
		this.setHighlight(t);
		/*	highlight the first menu item if there are any	*/
	}
	if (this.highlighted && !this.makeReq) {
		this.sInput = this.fld.value = this.opt.format(this.highlighted);
	};
	//if (this.isfocused) this.fld.focus();

	/*safari bugfix removed by Tibi on 2012.01.09 - not needed any more and it produced an error in chrome and safari*/
	/*if (this.fld.selectionStart) {
		this.fld.setSelectionRange(this.sInput.length, this.sInput.length);
	};*/
	// move cursor to end of input (safari)

	if(typeof(this.opt.callback) == "function") {
		this.opt.callback(this.highlighted,this);
	};
	this.clearSuggestions();
	return true;
};

/*parentObject.AutoSuggest.prototype.killTimeout = function() {
	clearTimeout(this.toID);
	// delete the temporized event, which clears the list, so it will never occur
};

parentObject.AutoSuggest.prototype.resetTimeout = function() {
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function() { pointer.clearSuggestions(); }, pointer.opt.timeout);
	// timing the hide event
};*/
parentObject.AutoSuggest.prototype.clearSuggestions = function () {
	// this.killTimeout();
	// stopping the timeout
// 	if(this.aSuggestions[0] && this.aSuggestions[0].parentNode) {
// 		this.aSuggestions.remove();
// 		if(this.opt.frameForIE)
// 			this.iframe.remove();
// 	}

	if (this.aSuggestions[0]) {
		jQuery(this.aSuggestions[0]).remove();
	};
	this.aSuggestions[0] = null;
	
	if(this.opt.frameForIE) {
		jQuery(this.iframe[0]).remove();
	};
	this.highlighted=null;

// 	if(this.aSuggestions[0] && this.aSuggestions[0].parentNode==document.body) {
// 		document.body.removeChild(this.aSuggestions[0]);
// 		if(this.opt.frameForIE)
// 			document.body.removeChild(this.iframe[0]);
// 	}
	// if the list object exists and have a valid parent node -- body --
};

parentObject.AutoSuggest.prototype.updateIframe = function(objCoords) {
	return this.iframe.css({left:objCoords.x, top:objCoords.y, width: (this.aSuggestions[0].clientWidth), height: (this.aSuggestions[0].clientHeight)});
};

parentObject.AutoSuggest.prototype.highlightText = function(sourceTxt, highlightTxt) {
	sourceTxt	= sourceTxt.replace(/<b>(.+)<\/b>/i, '$1');
	/*	removing previous highlights	*/
	var strippedTxt	= sourceTxt.replace(/<i>.+<\/i>/i, '');
	/*	removing the italic tag, if present	*/
	strippedTxt	= strippedTxt.replace(/<\/?[^>]+>/gi, '');
	var rExp	= new RegExp("("+highlightTxt+")", 'i');
	highlighedTxt	= strippedTxt.replace(rExp, "<b>$1</b>");
	return sourceTxt.replace(strippedTxt, highlighedTxt);
};
/*	highlighting the searched text in the source text	*/
parentObject.AS = parentObject.AutoSuggest;