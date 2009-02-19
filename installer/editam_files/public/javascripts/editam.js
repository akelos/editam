var Rules = new Array();
var Onload = new Array();

window.onload = function(){
    css_browser_selectors();
    Rules.each(function(rule){
        EventSelectors.start(rule);
    });

    Ajax.Responders.register({
        onComplete: function() { EventSelectors.assign(Rules); }
    })

    Onload.each(function(OnloadEvents){
        for (key in OnloadEvents){
            eval('OnloadEvents.'+key+'();');
        }
    });

    flash();
}

var Cache = {
    _storage : new Array(),
    set: function(name, value){
        this._storage[name] = value;
    },
    get : function(name){
        return this._storage[name] ? this._storage[name] : false;
    },
    clear : function(name){
        this._storage[name] = null;
    }
}

ResizeableTextarea = Class.create();
ResizeableTextarea.prototype = {
    initialize: function(element, options) {
        this.element = $(element);
        this.size = parseFloat(this.element.getStyle('height') || '100');
        this.options = Object.extend({
            inScreen: true,
            resizeStep: 10,
            minHeight: this.size
        }, options || {});
        Event.observe(this.element, "keyup", this.resize.bindAsEventListener(this));
        if ( !this.options.inScreen ) {
            this.element.style.overflow = 'hidden';
        }
        this.element.setAttribute("wrap","virtual");
        this.resize();
    },
    resize : function(){
        this.shrink();
        this.grow();
    },
    shrink : function(){
        if ( this.size <= this.options.minHeight ){
            return;
        }
        if ( this.element.scrollHeight <= this.element.clientHeight) {
            this.size -= this.options.resizeStep;
            this.element.style.height = this.size+'px';
            this.shrink();
        }
    },
    grow : function(){
        if ( this.element.scrollHeight > this.element.clientHeight ) {
            if ( this.options.inScreen && (20 + this.element.offsetTop + this.element.clientHeight) > document.body.clientHeight ) {
                return;
            }
            this.size += (this.element.scrollHeight - this.element.clientHeight) + this.options.resizeStep;
            this.element.style.height = this.size+'px';
            this.grow();
        }
    }
}


function flash(message, timeout)
{
    if ($('js_flash')){
        if(message == '' || message == undefined){
            $('js_flash').hide();
        }else{
            $('js_flash').innerHTML = message;
            $('js_flash').show();
            if(timeout){
                window.setTimeout('$(\'js_flash\').hide();',timeout*1000);
            }
        }
    }
}



function id_of(element) {
    return element ? element.id.split('-')[1] : '';
}

// CSS Browser Selector   v0.2.5
// Documentation:         http://rafael.adm.br/css_browser_selector
// License:               http://creativecommons.org/licenses/by/2.5/
// Author:                Rafael Lima (http://rafael.adm.br)
function css_browser_selectors(){
  var ua = navigator.userAgent.toLowerCase();
  var is = function(t){
    return ua.indexOf(t) != -1; 
  };
  var h = document.getElementsByTagName('html')[0];
  var b = (!(/opera|webtv/i.test(ua))&&/msie (\d)/.test( ua )) ? ('ie ie'+RegExp.$1) : is('gecko/') ? 'gecko' : is('opera/9') ? 'opera opera9' : /opera (\d)/.test ( ua )? 'opera opera' + RegExp.$1 : is('konqueror') ? 'konqueror' : is('applewebkit/') ? 'webkit safari' : is('mozilla/') ? 'gecko' : '';
  var os = (is('x11')||is('linux')) ? ' linux' : is('mac') ? ' mac' : is('win') ? ' win' : '';
  var c = b+os+' js';
  h.className += h.className ? ' ' + c:c;
}

Object.extend(String.prototype, {

    upcase: function() {
        return this.toUpperCase();
    },

    downcase: function() {
        return this.toLowerCase();
    },

    strip: function() {
        return this.replace(/^\s+/, '').replace(/\s+$/, '');
    },

    toInteger: function() {
        return parseInt(this);
    },

    unnaccent : function(){
        return this.replace(/(À|Á|Â|Ã|Ä|Å|Æ)/g, 'A').replace(/(Ç)/g, 'C').replace(/(È|É|Ê|Ë)/g, 'E').replace(/(Ì|Í|Î|Ï)/g, 'I').replace(/(Ð)/g, 'D').replace(/(Ñ)/g, 'N').replace(/(Ò|Ó|Ô|Õ|Ö|Ø)/g, 'O').replace(/(Ù|Ú|Û|Ü)/g, 'U').replace(/(Ý)/g, 'Y').replace(/(Þ)/g, 'T').replace(/(ß)/g, 's').replace(/(à|á|â|ã|ä|å|æ)/g, 'a').replace(/(ç)/g, 'c').replace(/(è|é|ê|ë|ð)/g, 'e').replace(/(ì|í|î|ï)/g, 'i').replace(/(ñ)/g, 'n').replace(/(ò|ó|ô|õ|ö|ø)/g, 'o').replace(/(ù|ú|û|ü)/g, 'u').replace(/(ý|ÿ)/g, 'y').replace(/(þ)/g, 't');
    },

    toSlug: function() {
        return this != '/' ? this.strip().unnaccent().downcase().replace(/[^-a-z0-9~\s\.:;+=_]/g, '').replace(/[\s:;=+]+/g, '-').replace(/^[\-\.]*/, '').replace(/[\-\.]*$/, '').replace(/[\-\.]{2,}/g, '-') : this;
    }

});
