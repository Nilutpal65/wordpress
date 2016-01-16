(function($) {
    var tmpInternalWindowCounter = 0;
    var getDefaultWindowParams = function (opt) {
        var w = Math.round(screen.width * 0.65);
        var h = Math.round(screen.height * 0.65);
        var t = tmpInternalWindowCounter * 40;
        var l = tmpInternalWindowCounter * 40;
        tmpInternalWindowCounter++;
        return 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,top=' + t + ',left=' + l + ',width=' + w + ',height=' + h;
    };
    var focusWindows = function (windows) {
        var windowCounter = 0;
        for (var i in windows) {
            if (windows[i] && typeof(windows[i]) == 'object' && windows[i].focus) {
                var w = Math.round(screen.width * 0.7);
                var h = Math.round(screen.height * 0.7);
                var t = windowCounter * 40;
                var l = screen.width - w - windowCounter * 40;
                windowCounter++;

                focusWindow.call(this, windows[i]);
                try {
					windows[i].resizeTo(w, h);
				} catch(e){}
				try {
					windows[i].moveTo(l, t);
				} catch(e){}
            }
        }
    };
    var placeholderLink = function(url){
        var w = Math.round(screen.width * 0.7);
        var h = Math.round(screen.height * 0.7);
        var t = tmpInternalWindowCounter * 40;
        var l = screen.width - w - tmpInternalWindowCounter * 40;
        return {
            url: "placeholder.php",
            params: 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,top='+t+',left='+l+',width='+w+',height='+h
        };
    };
    var defaultOptions = {
        def : {
            type: 'popupwindow',
            when: 'trigger',
            windowParams: getDefaultWindowParams,
			afterTrigger: function(){}
        },
        mobile : {
            when: 'add'
        },
        chrome : {
            type: 'popunderwindow',
            when: 'add'
        },
        ie : {
            type: 'placeholderwindow',
            when: 'trigger',
            placeholder: placeholderLink,
            onTrigger: focusWindows
        },
        resetOnTrigger: false
    };

    var placeholders = [];

    var popupHandler = function(opt){
        if (!opt) {
            opt = {};
        }
        this.opt = extendObject({}, defaultOptions);
        this.opt = extendObject(this.opt, opt);

        var uniqId = 'inst_' + Math.round(Math.random() * 10000);
        this.windows = {};
        this.getUniqueId = function(){
            return uniqId + '_' + Math.round(Math.random() * 10000000)
        };
        this.add = function(url){
            if (!url) {
                return false;
            }
            var id = this.getUniqueId();
            this.windows[id] = url;
            if (this.getBrowserOpt('type') == 'placeholderwindow') {
                var cb = this.getBrowserOpt('placeholder');
                var plc = typeof(cb)=='function' ? cb(url) : false;
                if (!plc || !plc.url) {
                    plc = placeholderLink(url);
                }
                openPlaceholder.call(this, plc.url, id, plc.params);
            }
            if (this.getBrowserOpt('when') == 'add') {
                this.opt.resetOnTrigger = true;
                this.trigger(true);
                return false;
            }
            return id;
        };
        this.update = function(id, url){
            if (id && this.windows[id] && typeof(this.windows[id])=='string')
                this.windows[id] = url;
        };
        this.remove = function(id){
            if (id && this.windows[id]) {
                this.windows[id] = null;
                if (placeholders[id]) {
                    placeholders[id].close();
                    placeholders[id] = null;
                }
            }
        };
        this.trigger = function(skipCallback){
            var newWindows = {};
            var i;
            for(i in this.windows) {
                var url = this.windows[i];
                if (typeof(url)=='string') {
                    this.windows[i] = openLink.call(this, url, i);
                    if (this.getBrowserOpt('type') == 'popunderwindow' && this.windows[i]) {
                        blurWindow(this.windows[i]);
                    }
                    if (!this.opt.resetOnTrigger && this.getBrowserOpt('type') != 'placeholderwindow') {
                        newWindows[this.getUniqueId()] = url;
                    }
                }
            }
            for(i in newWindows) {
                this.windows[i] = newWindows[i];
            }
            if (this.getBrowserOpt('type') == 'popunderwindow') {
                window.focus();
            }
            tmpInternalWindowCounter = 0;
            if (!skipCallback) {
                for(var i in this.windows) {
                    var cb = this.getBrowserOpt('onTrigger');
                    if (typeof(cb)=='function') {
                        cb.call(this, this.windows);
                    }
                }
				var cb = this.getBrowserOpt('afterTrigger');
                if (typeof(cb)=='function') {
                    cb.call(this, this.windows);
                }
            }
        };
    };

    popupHandler.prototype.getBrowserOpt = function(name){
        var r = this.opt.def[name];
        if (typeof(r)=='undefined') {
            r = '';
        }
        if (this.browser.chrome() && this.opt.chrome[name]) {
            r = this.opt.chrome[name];
        }
        if (this.browser.ie() && this.opt.ie[name]) {
            r = this.opt.ie[name];
        }
        if (this.isMobile.any() && this.opt.mobile[name]) {
            r = this.opt.mobile[name];
        }
        return r;
    };


    popupHandler.prototype.isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
        },
        SmallScreen: function() {
            return window.innerWidth<768;
        },
        any: function() {
            return (this.SmallScreen() || this.Android() || this.BlackBerry() || this.iOS() || this.Opera() || this.Windows());
        }
    };

    popupHandler.prototype.browser = {
        chrome: function() {
            return !!window.chrome && !this.opera();
        },
        mozilla: function(){
            return typeof InstallTrigger !== 'undefined';
        },
        opera: function(){
            return !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        },
        ie: function(){
            return /*@cc_on!@*/false || !!document.documentMode;
        }
    };
    var openLink = function(url, target){
        var w = false;
        if (placeholders[target] && !placeholders[target].closed) {
            placeholders[target].document.location.replace(url);
            w = placeholders[target];
            placeholders[target] = null;
        } else {
            var wp = this.getBrowserOpt('windowParams');
            if (typeof(wp) == 'function') {
                wp = wp.call(this, this.opt);
            }
            w = window.open(url, target, wp)
        }
        return w;
    };
    var openPlaceholder = function(url, target, params) {
        var w = window.open(url, target, params);
        if (url == 'about:blank') {
            w.document.open();
            w.document.write("<html><head><title>Placeholder</title></head><body><h1>Placeholder</h1></body></html>");
            w.document.close();
        }
        blurWindow.call(this, w);
        placeholders[target] = w;
        return w;
    };
    var blurWindow = function(wnd) {
        if (typeof(wnd)!='object')
            return false;
        /*	window blur for each browser	*/
        wnd.blur();

        if(this.browser.chrome())
            wnd.top.blur();

        var extendPopUpWindow = function(WINwindow) {
            var newWinObj	= {
                sendFocus2Parent: function(b) {
                    this._handleMozzEngine();
                    try {
                        b.opener.window.focus();
                    } catch(c) {}
                },
                _handleMozzEngine: function() {
                    if(typeof window.mozPaintCount!=="undefined" || typeof navigator.webkitGetUserMedia==="function") {
                        var b	= this.window.open("about:blank");
                        b.close();
                    }
                }
            };
            try {
                for(var i in newWinObj)
                    WINwindow[i]	= newWinObj[i];
            } catch(e) {};
        };

        window.top.focus();
        extendPopUpWindow(wnd);
        try{
            wnd.sendFocus2Parent(wnd);
        } catch(h) {};
        try{
            window.top.open('javascript:window.focus();','_self');
        }catch(err){}
        return true;
    };
    var focusWindow = function(wnd){
        wnd.focus();
    };

    var extendObject = function(orig, nw){
        if (typeof(nw) !='object') {
            return nw;
        }
        if (typeof(orig)!='object'){
            orig = {};
        }
        for(var i in nw) {
            orig[i] = extendObject(orig[i], nw[i]);
        }
        return orig;
    };


    window.PPH = popupHandler;
})(jQuery)