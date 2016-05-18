Wishlist_Panel = Class.create();

Wishlist_Panel.prototype = {
    initialize: function(parent){
        //$("loading-mask").hide();
		
		parent = typeof parent !== 'undefined' ? parent : false;
        this.dom_container = $('wishlistpanel');
        this.dom_content = $('wishlist-content');
        this.dom_label = $('wishlist-label');
		//this.dom_label_fixed = $('wishlist-label_fixed');
        this.dom_label2 = $('blackWish');
        this.empty_link = $('wishlist-empty');
        this.compare_link = $('wishlist-compare');
        this.wishlist_links = [];
		if(parent){
            this.wishlist_links = parent.select(wishlist_css_selector);
            this.iframe = parent;
            var innerDoc = parent.contentDocument || parent.contentWindow.document;
            var body =  innerDoc.querySelector('body');
            this.wishlist_links = body.select(wishlist_css_selector);
        }
		
		this.wishlist_links = this.wishlist_links.concat($$(wishlist_css_selector));
		this.readjustPanelWidth();
        this.setBinds();
        this.getData();
		if(typeof Cookie.get("wishlist_panel")!= "undefined" && Cookie.get("wishlist_panel") != dom_content.parentNode.visible()){
            this.togglePanel();
        }
	},

    bindHoverItems: function(){
        $$("a.product").each(
            function(e){
                e.observe('mouseover', function(){
                    content = this.select(".maximized")[0].innerHTML;
                    var x = this.offsetTop;
                    var y = this.offsetLeft-$$(".products")[0].scrollLeft;
                    //$('wishlist_maximized').setStyle({top:x+"px", left:y+"px"}).update("<a href='"+this.href+"'><div style='position:relative;'>"+content+"</div></a>").show();
                    $$("#wishlist_maximized .btn-remove").each(function(elem){
                        if(elem.getHeight() == 0){
                            elem.setStyle({marginBottom:"-20px"});
                        }
                        else{
                            elem.setStyle({marginBottom:"-"+elem.getHeight()+"px"});
                        }
                    });
                    Wishlist_Panel.prototype.bindDeleteButtons();
                });
            }

        );
        $('wishlist_maximized').observe('mouseleave', function(){
            this.hide();
        });

    },

    updateWishlistButtons: function(data){
		wishlist_links = wishlist_panel.wishlist_links;
		links = new Array();
        var re = /product\/(\d+)/;
        wishlist_links.each(
            function(link){
				
                //link.update(wishlist_addtext);
                link.removeClassName("bindRemove");
                link.removeAttribute("rel");
				link.removeClassName("deseado");
				
                if(re.exec(link.href)){
                    productId = re.exec(link.href)[1];
                }
                else{
                    productId = undefined;
                }
				if (document.getElementById('product-'+productId)!=null){
					document.getElementById('product-'+productId).className=document.getElementById('product-'+productId).className.replace(/\bpadre-deseado\b/,'');
				}else{
						link.update(wishlist_addtext);
					}
				if(typeof links[productId] != 'undefined'){
                    links[productId].push(link);
                }
                else{
                    links[productId] = [];
                    links[productId].push(link);
                }
            }
        );
        data.wishlist_items.each(function(e){
			if(typeof links[e.productId] != "undefined"){
				links[e.productId].forEach(function(link) {
					//link.update(wishlist_addedtext+" <span class='heart'>&nbsp;</span>");
					
					//Añadido para remarcar el elemeneto añadido al wishlist
                    link.addClassName("deseado");
					
					 if (document.getElementById('product-'+e.productId)!=null){
						document.getElementById('product-'+e.productId).className = document.getElementById('product-'+e.productId).className+' padre-deseado';
					}else{
						
						link.update(wishlist_removetext);
					}
					//-------------------------------------------------------
					
                    link.addClassName("bindRemove");
					link.setAttribute('rel', e.removeUrl);
                });
            }
        });
    },

    getData: function(){
        if(isSecure){
            wishlist_ajaxurl = wishlist_ajaxurl.replace("http:","https:")
        }
        new Ajax.Request(wishlist_ajaxurl, {
            method: 'post',
            parameters: {},
            onSuccess: (function (data) {
				data = data.responseText.evalJSON();
				Wishlist_Panel.prototype.updateWishlistButtons(data);
				Wishlist_Panel.prototype.reloadPanel(data);
				//Wishlist_Panel.prototype.bindHoverItems();				

            }).bind(this)
        });
    },

    readjustPanelWidth: function(){
        if(parseInt(wishlist_panel_width)>0){
            var parentWidth = parseInt(wishlist_panel_width);
        }
        else{
            var parentWidth = this.dom_container.parentNode.getWidth();
        }
        var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
        if(viewport.width<parentWidth){
            parentWidth = viewport.width;
        }
    },

    setBinds: function(){
		
        Event.observe(this.dom_label, 'click', this.togglePanel, dom_content=this.dom_content);
		//Event.observe(this.dom_label_fixed, 'click', this.togglePanel, dom_content=this.dom_content);
        this.wishlist_links.each((function(wishlist_link){
            wishlist_link.onclick = function(){return false;}
            Event.observe(wishlist_link, 'click', this.addToWishlist);
        }).bind(this));

        //this.empty_link.onclick = function(){return false;}
        //Event.observe(this.empty_link, 'click', this.emptyWishlist);
        //this.compare_link.onclick = function(){return false;}
        //Event.observe(this.compare_link, 'click', this.compareWishlist);

        this.bindDeleteButtons();
    },

    emptyWishlist: function(){
        $("loading-mask").show();
        var empty_url = this.href;
        if(isSecure){
            var empty_url = this.href.replace("http:","https:")
        }
        new Ajax.Request(empty_url, {
            method: 'post',
            parameters: {},
            onSuccess: (function (data) {
                data = data.responseText.evalJSON();
                Wishlist_Panel.prototype.updateWishlistButtons(data);
                Wishlist_Panel.prototype.reloadPanel(data);
                //Wishlist_Panel.prototype.bindHoverItems();
                $("loading-mask").hide();
            }).bind(this)
        });
    },

/*    compareWishlist: function(){
        $("loading-mask").show();
        new Ajax.Request(this.href, {
            method: 'post',
            parameters: {},
            onSuccess: (function (data) {
                $("loading-mask").hide();
                data = data.responseText;
                popWin(data,'compare','top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes');
            }).bind(this)
        });
    },*/

    togglePanel:function(){
        Effect.toggle(dom_content.parentNode, 'blind', { duration: 0.4, afterFinish: function () {
            if(dom_content.parentNode.visible()){
                Cookie.set("wishlist_panel", true);
                //$("wishlist-additional-buttons").fade({ duration: 0.4, from: 0, to: 1 });
            }
            else{
                Cookie.set("wishlist_panel", false);
                //$("wishlist-additional-buttons").fade({ duration: 0.4, from: 1, to: 0.000001 });
            }
        }
        });
    },

    bindDeleteButtons: function(){
        $$("#wishlistpanel .btn-remove").each((function(deleteBtn){
            deleteBtn.onclick = function(){return false};
            Event.observe(deleteBtn, 'click', Wishlist_Panel.prototype.deleteFromWishlist);
        }).bind(this));
    },

    deleteFromWishlist: function(){
        $("loading-mask").show();
        var delete_url = this.getAttribute("href");
        if(isSecure){
            var delete_url = this.getAttribute("href").replace("http:","https:")
        }
        new Ajax.Request(delete_url, {
            method: 'post',
            parameters: {},
            onSuccess: (function (data) {
                data = data.responseText.evalJSON();
                Wishlist_Panel.prototype.updateWishlistButtons(data);
                Wishlist_Panel.prototype.reloadPanel(data);
                //Wishlist_Panel.prototype.bindHoverItems();
                $("loading-mask").hide();
                $('wishlist_maximized').hide();
            }).bind(this)
        });
    },

    addToWishlist:function(){
        $("loading-mask").show();
		if(this.hasClassName("bindRemove")){
            action = this.readAttribute("rel");
		}
        else{
            action = this.href.replace("/add/","/addAjax/");			
		}
        if(isSecure){
		    action = action.replace("http:","https:")
        }
		if(typeof $$('#product_addtocart_form')[0] != 'undefined'){
		    var default_action = $('product_addtocart_form').action;
            $('product_addtocart_form').action = action;
            $('product_addtocart_form').request({
                //onFailure: function() {  },
                onSuccess: (function(data) {
		            data = data.responseText.evalJSON();
					if(data.success){
						//this.update(wishlist_addedtext+" <span class='heart'>&nbsp;</span>");
						Wishlist_Panel.prototype.updateWishlistButtons(data);
                        Wishlist_Panel.prototype.reloadPanel(data);
		                //Wishlist_Panel.prototype.bindHoverItems();
                        $("loading-mask").hide();
                    }
                    else{
		                $("loading-mask").hide();
                        alert_message = "";
                        data.messages.each(function(message){
                            alert_message += message+"\n";
                        });
                        alert(alert_message);
		            }
                }).bind(this)
            });
            $('product_addtocart_form').action = default_action;
        }
        else{
			
			new Ajax.Request(action, {
                method: 'post',
                parameters: {},
                onSuccess: (function (data) {
					data = data.responseText.evalJSON();
                    if(data.success){
                        //this.update(wishlist_addedtext+" <span class='heart'>&nbsp;</span>");
						
						Wishlist_Panel.prototype.updateWishlistButtons(data);
						
                        Wishlist_Panel.prototype.reloadPanel(data);
						
                       	// Wishlist_Panel.prototype.bindHoverItems();
					 	$("loading-mask").hide();
                    }
                    else{
                        $("loading-mask").hide();
                        alert_message = "";
                        data.messages.each(function(message){
                            alert_message += message+"\n";
                        });
                        alert(alert_message);
                    }
                }).bind(this)
            });
        }
    },

    reloadPanel:function(data){
		
        $$(".wishlist-top-title small")[0].update(data.wishlist_count);
        $$(".wishlist--title small")[0].update(data.wishlist_count);
		$$("#wishlist-content .products")[0].update("");
		if(data.wishlist_count > 0){
            if(data.wishlist_count == 1){
                $$("#wishlist-content .products")[0].update("<ul></ul><div class='no_items'>"+wishlist_firstitemtext+"</div>");
            }
            else{
                $$("#wishlist-content .products")[0].update("<ul></ul>");
            }
            //$('wishlist-additional-buttons').show();
        }
        else{
            $$("#wishlist-content .products")[0].update("<div class='no_items'>"+wishlist_noitemstext+"</div>");
            //$('wishlist-additional-buttons').hide();
        }
        var list_width = 0;
        var product_Ids = "";
        if(data.wishlist_items.length){
            var i = 0;
            data.wishlist_items.each(function(e){
                if(i!=0){
                    product_Ids +=",";
                }
                i++;
                list_width = list_width + 151;
                
                var item = "<li class='item' xmlns='http://www.w3.org/1999/html'><div class='miniwishlist-block-izq'><a href='"+ e.productUrl +"' title='" + e.productName + "' class='product-image miniwishlist-image'><img src='" + e.productImage + "' alt='" + e.productName + "'></a></div><div class='miniwishlist-block-der'><p class='product-name'><a href='" + e.productUrl + "'>" + e.productBrand + "</a></p><div class='miniwishlist--productname' style='color: #666666;font: normal 0.77em/100% 'open Sans';'>" + e.productName + "</div><div class='miniwishlist--price'><span class='price'>" + e.productPrice + "</span></div><a href='" + e.removeUrl + "' title='Remove This Item'  class='btn-remove'>Remove</a></div></li>";
                $$("#wishlist-content .products ul")[0].insert(item);
                $$(".btn-remove").each(function(elem){
                    if(elem.getHeight() == 0){
                        elem.setStyle({marginBottom:"-20px"});
                    }
                    else{
                        elem.setStyle({marginBottom:"-"+elem.getHeight()+"px"});
                    }
                });
                product_Ids += e.productId;
            });
        }
        if($$("#wishlistpanel #wishlist-content .products ul").length){
            $$("#wishlistpanel #wishlist-content .products ul")[0].setStyle({width: list_width+"px"});
        }
        if(parseInt(wishlist_panel_width)>0){
            var parentWidth = parseInt(wishlist_panel_width);
        }
        else{
            var parentWidth = $('wishlistpanel').parentNode.getWidth();
        }
        var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
        if(viewport.width<parentWidth){
            parentWidth = viewport.width;
        }
        if(typeof $$("#wishlist-content .no_items")[0] != "undefined"){
            if($$("#wishlist-content .products ul li").length){
                $$("#wishlist-content .no_items")[0].setStyle({width:parentWidth-200+"px"});
            }
            else{
                $$("#wishlist-content .no_items")[0].setStyle({width:parentWidth-50+"px"});
            }
        }
        this.updateCompareUrl(product_Ids);
        this.bindDeleteButtons();
    },

    updateCompareUrl: function(product_Ids){
        compare_url.replace("%id%",product_Ids);
        if(product_Ids != ""){
            compare_onclick = "popWin('"+compare_url.replace("%id%",product_Ids)+"','compare','top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes')";
        }
        else{
            compare_onclick = "";
        }
        var compare_link = $('wishlist-compare');
        if (compare_link) {
            compare_link.onclick = function(){return false;}
            compare_link.setAttribute("onclick", compare_onclick);
        }
    }
};

function isTouchDevice() {
    var el = document.createElement('div');
    el.setAttribute('ontouchstart', 'return;'); // or try "ontouchstart"
    return typeof el.ontouchstart === "function";
}

var wishlist_panel;
document.observe('dom:loaded', function () {
        if(typeof wishlist_panel == "undefined" && typeof wishlist_css_selector != 'undefined'){
            
			wishlist_panel = new Wishlist_Panel();
			
        }
        if(isTouchDevice()){
            document.addEventListener('focus',function(e){
                if(e.target.tagName=="INPUT"||e.target.tagName=="input"||e.target.tagName=="TEXTAREA"||e.target.tagName=="textarea"){
                    $$("body")[0].addClassName("fixfixed");
                }
            }, true);
            document.addEventListener('blur',function(e){
                if(e.target.tagName=="INPUT"||e.target.tagName=="input"||e.target.tagName=="TEXTAREA"||e.target.tagName=="textarea"){
                    $$("body")[0].removeClassName("fixfixed");
                }
            }, true);
        }
    }
);

Event.observe(window, "resize", function(){
    if(typeof wishlist_panel != "undefined" && typeof wishlist_css_selector != 'undefined'){
        wishlist_panel.readjustPanelWidth();
    }
});

var Cookie = {

    key: 'cookies',

    set: function(key, value) {
        var cookies = this.getCookies();
        cookies[key] = value;
        var src = Object.toJSON(cookies).toString();
        this.setCookie(this.key, src);
    },

    get: function(key){
        if (this.exists(key)) {
            var cookies = this.getCookies();
            return cookies[key];
        }
        if (arguments.length == 2) {
            return arguments[1];
        }
        return;
    },

    exists: function(key){
        return key in this.getCookies();
    },

    clear: function(key){
        var cookies = this.getCookies();
        delete cookies[key];
        var src = Object.toJSON(cookies).toString();
        this.setCookie(this.key, src);
    },

    getCookies: function() {
        return this.hasCookie(this.key) ? this.getCookie(this.key).evalJSON() : {};
    },

    hasCookie: function(key) {
        return this.getCookie(key) != null;
    },

    setCookie: function(key,value) {
        var expires = new Date();
        expires.setTime(expires.getTime()+1000*60*60*24*365)
        document.cookie = key+'='+escape(value)+'; expires='+expires+'; path=/';
    },

    getCookie: function(key) {
        var cookie = key+'=';
        var array = document.cookie.split(';');
        for (var i = 0; i < array.length; i++) {
            var c = array[i];
            while (c.charAt(0) == ' '){
                c = c.substring(1, c.length);
            }
            if (c.indexOf(cookie) == 0) {
                var result = c.substring(cookie.length, c.length);
                return unescape(result);
            };
        }
        return null;
    }
}
