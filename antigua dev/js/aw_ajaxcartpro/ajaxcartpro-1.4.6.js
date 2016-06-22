/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */
 

Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7;

window.ACPTop = 200;

function aw_acp_getproduct(url, attrName) {
    if(typeof(attrName) == 'undefined') attrName = 'product';
    var res = url.match("/"+attrName+"/[0-9]*/");
    if(res && typeof res[0] != 'undefined') {
        res = res[0].substr(attrName.length+2, res[0].lastIndexOf('/')-(attrName.length+2));
        if(!isNaN(res)) return res;
    }
    return -1;
}

function aw_acp_in_array(needle, haystack) {
    if(typeof needle == 'undefined' || typeof haystack == 'undefined')
        return false;
    for(var i = 0; i<haystack.length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

if(!Prototype.Browser.IE6){
    oldSetLocation = setLocation;
    setLocation = function(url){
        if(window.location.href.match('https://') && !url.match('https://')){
            url = url.replace('http://','https://')
        }
        if(aw_acp_in_array(aw_acp_getproduct(url), AW_ACP.disabledForProducts) || url.search('noacp=1') != -1) {
            return oldSetLocation(url);
        }
        if(AW_ACP.isCartPage && ((url.search('/add') != -1 ) || (url.search('/remove') != -1 )) ){
            ajaxcartsend(url+'awacp/1/is_checkout/1', 'url', '', '');
        }else if (url.search('checkout/cart/add') != -1){
            url = url.replace('checkout/cart','ajaxcartpro/add');
            ajaxcartsend(url+'awacp/1', 'url', '', '');
        }else if (url.search('wishlist/index/cart') != -1) {
            var urlParts = url.split('?');
            var newUrl = '';
            if(aw_acp_in_array(aw_acp_getproduct(url, 'item'), AW_ACP.disabledForWLItems)) {
                newUrl = urlParts[0] + 'awacpskip/1';
                if (urlParts[1]) newUrl += '?' + urlParts[1];
                oldSetLocation(newUrl);
                return;
            }
            newUrl = urlParts[0] + 'awwishl/1/awacp/1';
            if (urlParts[1]) newUrl += '?' + urlParts[1];
            ajaxcartsendwishlist(newUrl, 'url', '', '');
        }else if (url.search('options=cart') != -1){
            ajaxcartsendconfigurable(url);
        }
        else
        {
            window.location.href = url;
        }
    }
}

function addSubmitEvent()
{
    try
    {
        if (typeof productAddToCartFormFromPopup != 'undefined' && !AW_ACP.disabled && !awacpclass.isCartConfigurePage())
        {
            productAddToCartFormFromPopup.submit = function(url){
                if(this.validator && this.validator.validate()){
                    ajaxcartsend('awacp=1', 'form', this, '');
                }
                return false;
            }
            productAddToCartFormFromPopup.form.onsubmit = function() {
                productAddToCartFormFromPopup.submit();
                return false;
            };
        }
        if (typeof productAddToCartFormOld != 'undefined' && !AW_ACP.disabled && !awacpclass.isCartConfigurePage()) {
            productAddToCartFormOld.submit = function(url){
                if(this.validator && this.validator.validate()){
                    ajaxcartsend('awacp=1', 'form', this, '');
                }
                return false;
            }

            productAddToCartFormOld.form.onsubmit = function() {
                productAddToCartFormOld.submit();
                return false;
            };
        }
        else if (typeof productAddToCartForm != 'undefined' && !AW_ACP.disabled && !awacpclass.isCartConfigurePage())
        {
            productAddToCartForm.submit = function(url){
                if(this.validator && this.validator.validate()){
                    ajaxcartsend('awacp=1', 'form', this, '');
                }
                return false;
            }

            productAddToCartForm.form.onsubmit = function() {
                productAddToCartForm.submit();
                return false;
            };
        }
    }
    catch(e){}
}

function addAcpSubmitEvent(removeFromWishlistUrl)
{
    if(typeof(removeFromWishlistUrl) == 'undefined') removeFromWishlistUrl = false;
    if (typeof productAddToCartFormAcp != 'undefined')
    {
        productAddToCartFormAcp.submit = function(url){
            if(this.validator && this.validator.validate()){
                if (AW_ACP.isCartPage) {
                    ajaxcartsend('awacp=1&is_checkout=1', 'form', this, '');
                } else {
                    if(removeFromWishlistUrl) {
                        removeFromWishlistUrl.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''));
                    }
                    ajaxcartsend('awacp=1', 'form', this, removeFromWishlistUrl);
                }
            }
            return false;
        }

        productAddToCartFormAcp.form.onsubmit = function() {
            productAddToCartFormAcp.submit();
            return false;
        };
    }
}

if(!Prototype.Browser.IE6){

    var cnt1 = 20;
    __intId = setInterval(
        /* Hangs event listener for @ADD TO CART@ links*/
        function(){
            cnt1--;
            if(typeof productAddToCartForm != 'undefined'){
                try {
                    // This fix is applied to magento <1.3.1
                    $$('#product_addtocart_form '+aw_addToCartButtonClass).each(function(el){
                        el.setAttribute('type', 'button')
                    })
                }catch(err){

                }
                
                if (AW_ACP.hasFileOption == false) addSubmitEvent();
                
                clearInterval(__intId);
            }
            if(!cnt1) clearInterval(__intId);
        },
        500
        );



    var cnt2 = 20;
    __intId2 = setInterval(
        /* This hangs event listener on @DELETE@ items from cart*/
        function(){    
            cnt2--;
            if(typeof aw_cartDivClass!= 'undefined' && $$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage)){
                updateDeleteLinks();
                clearInterval(__intId2);
            }
            if(!cnt2) clearInterval(__intId);
        },
        500
        );
}





function setPLocation(url, setFocus) {
    if (url.search('checkout/cart/add') != -1) { //CART ADD
        window.opener.focus();

        var sep = '?';
        if(url.indexOf('?') != -1){
            sep = '&';
        }
        if(typeof window.opener.AW_ACP.isCart === "undefined" ) {
            window.opener.ajaxcartsend(url+sep+'awacp=1', 'url', '');
        }
        else if(window.opener.AW_ACP.isCart == 0) {
            window.opener.ajaxcartsend(url+sep+'awacp=1', 'url', '');
        } else {
            window.opener.ajaxcartsend(url+sep+'awacp=1&is_checkout=1', 'url', '');
        }
    } else if(url.search('options=cart') != -1) {
        window.opener.ajaxcartsendconfigurable(url);
    } else {
        if(setFocus) {
            window.opener.focus();
        }
        window.opener.location.href = url;
    }
}

function ajaxcartsendwishlist(url, type, obj){
    url = getCommonUrl(url);
    if(window.location.href.match('http://') && AW_ACP.secureUrlOnFrontend && AW_ACP.wishlistVersionMatch)
    {
        url=url.replace("https://",'http://');
        url=url.replace('wishlist/index/cart','ajaxcartpro/wishlist/cart');
    }
    showProgressAnimation();
    new Ajax.Request(url, {
        onSuccess: function(resp) {
            try {
                if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
            } catch(e) {
                // win.location.href=url;
                hideProgressAnimation();
                return;
            }
            hideProgressAnimation();
            if (resp.r != 'success') {
                if (resp.redirect) {
                    if(resp.redirect.search('options=cart') != -1 || (typeof(resp.is_configurable) != 'undefined' && resp.is_configurable)) {
                        ajaxcartsendconfigurable(
                            resp.redirect.indexOf('?options=cart') ? resp.redirect : resp.redirect+'?options=cart',
                            typeof(resp.conf_remove_url) == 'undefined' ? null : resp.conf_remove_url
                            );
                    } else {
                        win.location.href = resp.redirect;
                    }
                } else {
                    win.location.href=url;
                }
            } else {
                if(AW_ACP.useConfirmation) {
                    showConfirmDialog(resp.product_name);
                }
                __onACPRender();
                updateCartView(resp);
                updateTopLinks(resp);
                updateWishlist(resp);
                updateWishlistTopLinks(resp);
                updateAddLinks();
            }
        }
    });
}

function ajaxcartsend(url, type, obj, removeFromWishlist){
    if(typeof(removeFromWishlist) == 'undefined' || removeFromWishlist == '') removeFromWishlist = false;
    url = getCommonUrl(url)

    showProgressAnimation();
    if (type == 'form') {
        try{
            var aForm = $('product_addtocart_form_acp') ? $('product_addtocart_form_acp') : $('product_addtocart_form');
        } catch(e){}
        if(aForm==null)
        {
            var aForm = $('product_addtocart_form_acp') ? $('product_addtocart_form_acp') : $('product_addtocart_form_from_popup');
            aForm.action=AW_ACP.cartURL+'add/';
        }
        nativeFormAction = aForm.action;
        var sep = '?';
        if(aForm.action.indexOf('?') != -1){
            sep = '&';
        }
        
        var url_temp = aForm.action;

        url_temp=ACPreplaceHttpsToHttp(url_temp);
        url_temp = url_temp.replace('checkout/cart','ajaxcartpro/add');
        aForm.action = url_temp;
	
        aForm.action += sep+url;
        if(removeFromWishlist) {
            new Ajax.Request(removeFromWishlist, {
                onComplete: function() {
                    ajaxcartsend(url, type, obj);
                }
            });
            return;
        }
        aForm.request({
            onCreate: function(){
                aForm.action = nativeFormAction;
            },
            onComplete:  function(resp) {
                if (typeof(resp.responseText) == 'string') {
                    try {
                        eval('resp = ' + resp.responseText);
                    } catch(e) {
                        if (obj.form.submit()){
                            return;
                        } else {
                            aForm.submit();
                            return;
                            if(!AW_ACP.isProductPage)
                                win.location.href = nativeFormAction;
                            return;
                        }
                    }
                }
                hideProgressAnimation();
                if (resp.r != 'success') {
                    if (resp.redirect) {
                        if(resp.redirect.search('options=cart') != -1 || (typeof(resp.is_configurable) != 'undefined' && resp.is_configurable)) {
                            ajaxcartsendconfigurable(resp.redirect.indexOf('?options=cart') ? resp.redirect : resp.redirect+'?options=cart');
                        } else {
                            obj.form.submit();
                        }
                    } else {
                        obj.form.submit();
                    }
                } else {
                    if($('acp_configurable_block')){
                        acp_remove_configurable_block();
                    }
                    __onACPRender();
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') != 1)) {
                        showConfirmDialog(resp.product_name);
                    }
                    updateCartView(resp);
                    updateWishlist(resp);
                    updateWishlistTopLinks(resp);
                }
            }
        })

    }
    if (type == 'url') {

        url=ACPreplaceHttpsToHttp(url);
        
        new Ajax.Request(url, {
            onSuccess: function(resp) {
                try {
                    if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
                } catch(e) {
                    win.location.href=url;
                    hideProgressAnimation();
                    return;
                }
                hideProgressAnimation();
                if (resp.r != 'success') {
                    if (resp.redirect) {
                        if(resp.redirect.search('options=cart') != -1 || (typeof(resp.is_configurable) != 'undefined' && resp.is_configurable)) {
                            ajaxcartsendconfigurable(resp.redirect.indexOf('?options=cart') ? resp.redirect : resp.redirect+'?options=cart');
                        } else {
                            win.location.href = resp.redirect;
                        }
                    } else {
                        win.location.href=url;
                    }
                } else {
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') == -1)) {
                        showConfirmDialog(resp.product_name);
                    }
                    __onACPRender();
                    updateCartView(resp);
                }
            }
        });
    }
}

function ACPreplaceHttpsToHttp(url){
    
    /*  from http to https */
    if(window.location.href.match('http://') && url.match('https://')){
        url = url.replace('https://','http://')
    }
    return url;    
}


function __onACPRender(){
    if(AW_ACP.onRender && AW_ACP.onRender.length){
        $A(AW_ACP.onRender).each(function(h){
            h(AW_ACP)
        })
    }
}

function addEffectACP(obj, effect)
{
    if (effect == 'opacity'){
        $(obj).hide();
        new Effect.Appear(obj);

    }
    if (effect == 'grow'){
        $(obj).hide();
        new Effect.BlindDown(obj);
    }
    if (effect == 'blink'){
        new Effect.Pulsate(obj);
    }
}


function updateDeleteLinks(){
    var tmpLinks = document.links;
    for (i=0; i<tmpLinks.length; i++){
        if (tmpLinks[i].href.search('checkout/cart/delete') != -1){
            url = tmpLinks[i].href.replace(/\/uenc\/.+,/g, "");
            var del = url.match(/delete\/id\/\d+\//g);
            var id = del[0].match(/\d+/g);
            if (window.location.protocol == 'https:'){
                aw_base_url = aw_base_url.replace("http:", "https:");
            }    
            if(!AW_ACP.isCartPage){
                tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'")';
            }else{
                tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'/is_checkout/1")';
            }
        }
    }
}

function updateTopLinks(resp){
    if(!awacpclass.isEE() && $$(aw_topLinkCartClass).length){
        $$(aw_topLinkCartClass)[0].title = $$(aw_topLinkCartClass)[0].innerHTML = resp.links;
    }
}

function updateWishlistTopLinks(resp){
    if($$(aw_topWishlistLinkCartClass).length && typeof(resp.wishlist_links) != 'undefined') {
        $$(aw_topWishlistLinkCartClass)[0].innerHTML = resp.wishlist_links;
        $$(aw_topWishlistLinkCartClass).first().title = resp.wishlist_links;
    }
}

window.updateBigCartView = function (resp){
    updateCartBar(resp);
    $$(aw_bigCartClass)[0].innerHTML = resp.cart
    if($('shopping-cart-table')){
        decorateTable('shopping-cart-table')
    }

    updateDeleteLinks();
    updateTopLinks(resp);
    updateAddLinks();

    awACPExtractScripts(resp.cart);
}

function showProgressAnimation(){
    if($$('.ajaxcartpro_confirm').first()) {
        $$('.ajaxcartpro_confirm').first().hide();
    }
    alignBlock($$('.ajaxcartpro_progress')[0], 0, 0, 'progress');
}

function showConfirmDialog(product_name){
    if (product_name) $('acp_product_name').innerHTML = product_name;
    block = $$('.ajaxcartpro_confirm')[0];
    alignBlock(block, 0, 0, 'confirmation');
    block.style.display = 'block';
    if (typeof($$('.ajaxcartpro_confirm .focus')[0]) != 'undefined') $$('.ajaxcartpro_confirm .focus')[0].focus();

    var ACPcountdown = $('ACPcountdown');
    if(typeof ACPcountdown != 'undefined' && AW_ACP.counterBegin>0)
    {
        ACPcountdown.innerHTML = AW_ACP.counterBegin;
        if (typeof __intId3 != 'undefined') clearInterval(__intId3);
        __intId3 = setInterval(
            function(){
                if ( parseInt(ACPcountdown.innerHTML) ){
                    ACPcountdown.innerHTML = parseInt(ACPcountdown.innerHTML)-1;
                }
                else
                { 
                    clearInterval(__intId3);
                    block.style.display = "none";
                    ACPcountdown.innerHTML = AW_ACP.counterBegin;
                }

            },
            1000
            );
    }
}

function hideProgressAnimation(){

    $$('.ajaxcartpro_progress')[0].style.display = 'none';
}

if(!Prototype.Browser.IE6){
    document.observe("dom:loaded", function() {
        updateAddLinks()
        
        // Some other onclicks
        if($('aw_acp_continue')) {
            $('aw_acp_continue').onclick = function(e){
                e = e||event;
                if(e.preventDefault)
                    e.preventDefault()
                $$('.ajaxcartpro_confirm')[0].style.display='none';
                return false;
            }
        }
        if($('aw_acp_checkout')) {
            $('aw_acp_checkout').onclick = function(e){
                $$('.ajaxcartpro_confirm')[0].style.display='none';
                return true;
            }
        }
        
        // Test for minicart
        
        if((typeof aw_cartDivClass != 'undefined') && ($$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage))){
            updateDeleteLinks();
        }
        
    })
}

function updateAddLinks(){
    var ats = document.links;
    for (i=ats.length-1; i>=0; i--) {
        if (ats[i].href.search('checkout/cart/add') != -1) {
            ats[i].onclick = function(link) {
                return function(){
                    setLocation(link)
                }
            }(ats[i].href);
            ats[i].href="javascript:void(0)";
            continue;
        }
        if (ats[i].href.search('wishlist/index/cart') != -1) {
            ats[i].onclick = function(link) {
                return function() {
                    setLocation(link)
                }
            }(ats[i].href);
            ats[i].href="javascript:void(0)";
            continue;
        }
        if(ats[i].href.search('paypaluk/express/start') != -1 || ats[i].href.search('paypal/express/start') != -1) {
            $$('#product_addtocart_form a').each(function(el) {
                if(el == ats[i]) {
                    ats[i].stopObserving('click');
                    ats[i].observe('click', function(event) {
                        $('pp_checkout_url').value = this.href;
                        productAddToCartForm.form.submit();
                        event.stop(); 
                    });
                }
            });
        }
    }
}

function getCommonUrl(url){
    if(window.location.href.match('www.') && url.match('http://') && !url.match('www.')){
        url = url.replace('http://', 'http://www.');
    }else if(!window.location.href.match('www.') && url.match('http://') && url.match('www.')){
        url = url.replace('www.', '');
    }
    return url;
}

var productAddToCartFormAcp;
function ajaxcartsendconfigurable(url, removeFromWishlistUrl) {
    if(typeof(removeFromWishlistUrl) == 'undefined') removeFromWishlistUrl = false;
    showProgressAnimation();
    urlToSend = url + '&ajaxcartpro=1';
    new Ajax.Request(urlToSend, {
        onSuccess: function(resp) {
            if (resp.responseText == 'false') {
                window.location = url;
            } else {
                var _div = new Element('div');
                var scripts = resp.responseText.extractScripts();
                _div.innerHTML = resp.responseText.stripScripts();
                $$('body').first().insert({
                    bottom: _div
                });
                _div.hide();
                
                /*tmpDiv = win.document.createElement('div');
                tmpDiv.innerHTML = resp.responseText.stripScripts();
                win.document.body.appendChild(tmpDiv);
                tmpDiv.hide();*/
                
                hideProgressAnimation();

                productAddToCartFormAcp = new VarienForm('product_addtocart_form_acp');
                decorateGeneric($$('#product-options-wrapper dl'), ['last']);
                addAcpSubmitEvent(removeFromWishlistUrl);
                
                Event.observe($$('#aw_acp_continue').last(), 'click', function() {
                    
                    acp_remove_configurable_block();
                    
                    awacpclass.hideMagentoMAPPopup();
                    return false;
                });

                for (var i=0; i<scripts.length; i++)
                {
                    if (typeof(scripts[i]) != 'undefined')
                    {
                        eval(scripts[i]);
                    }
                }
                if(typeof(optionsPrice) != 'undefined' && aw_acp_in_array(optionsPrice.productId, AW_ACP.disabledForProducts))
                    window.location = url;
                else {
                    _div.show();
                    showOptionsDialog();
                }
            }
        }
    });
}

function acp_remove_configurable_block(){
    try {
        $('product_addtocart_form_acp').reset();
    }
    catch(e){
         
    }
    if(typeof(opConfig) !== 'undefined' && opConfig != null) {
        opConfig.reloadPrice();
    }
    if(typeof(dConfigAcp) !== 'undefined' && dConfigAcp != null) {
        dConfigAcp.reloadPrice()
    }
    optionsPrice.changePrice('configAcp', 0);
    optionsPrice.reload();
    
    $$("#acp_configurable_block").each(function(item){
        item.remove();
    });
}

function showOptionsDialog()
{
    if($$('.ajaxcartpro_confirm').first())
        $$('.ajaxcartpro_confirm').first().hide();
    alignBlock($('acp_product_options'), 0, $('acp_product_options').offsetHeight, 'custom_options');
}

function alignBlock(block, width, height, blockType) {
    if (blockType == 'confirmation' && !AW_ACP.useConfirmation)
        return false;

    if (blockType == 'progress' && !AW_ACP.useProgress)
        return false;

    block.style.display = 'block';
    (width > 0)?(block.style.width = width + 'px'):(width = block.getWidth());
    (height > 0)?(block.style.height = height + 'px'):(height = block.getHeight());
    block.style.left = document.viewport.getWidth()/2 - width/2 + 'px';

    if (Prototype.Browser.IE && Prototype.Browser.IE6) {
        block.style.position = 'absolute';
        window.ACPTop = 200;
    }
    if (aw_ajaxcartpro_proganim == 'center') {
        if (!(Prototype.Browser.IE && Prototype.Browser.IE6)) {
            block.style.top = (document.viewport.getHeight()/2 - height/2) + 'px';
        } else {
            window.ACPTop = 200;
        }
    }
    if (aw_ajaxcartpro_proganim == 'top') {
        if (!(Prototype.Browser.IE && Prototype.Browser.IE6)) {
            block.style.top = '0px';
        } else {
            // IE7-
            window.ACPTop = 0;
        }
    }
    if (aw_ajaxcartpro_proganim == 'bottom') {
        block.style.bottom = '0px';
    }
}

function validateDownloadableCallback(elmId, result)
{
    var container = $('downloadable-links-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

function validateOptionsCallback(elmId, result)
{
    var container = $(elmId).up('ul.options-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

function acpSubmit()
{
    if(productAddToCartFormAcp.validator&&productAddToCartFormAcp.validator.validate())
    {
        awacpclass.hideMagentoMAPPopup();
        productAddToCartFormAcp.submit();
    }
}

function updateCustomBlock(selector, content) {
    var block = $$(selector).first();
    if(block) {
        block.replace(content);
        return true;
    }
    return false;
}

function updateCustomBlocks(custom) {
    for(var key in custom) {
        if(typeof custom[key].selector != 'undefined' && typeof custom[key].content != 'undefined')
            updateCustomBlock(custom[key].selector, custom[key].content);
    }
}

function awACPExtractScripts(strings) {
    var scripts = strings.extractScripts();
    scripts.each(function(script){
        try {
            eval(script.replace(/var /gi, ""));
        }
        catch(e){
            if(window.console) console.log(e.name);
        }
    });
}

AWACPClass = Class.create({
    initialize: function(className) {
        this._isEE = false;
        this.global = window;
        this.global[className] = this;
    },
    
    setIsEE: function(value) {
        this._isEE = typeof(value) != 'undefined' && value ? true : false;
    },
    
    isEE: function(value) {
        return this._isEE;
    },
    
    isCartConfigurePage: function() {
        return AW_ACP._isCartConfigurePage ? true : false;
    },
    
    hideMagentoMAPPopup: function() {
        if($('map-popup-close') && $('map-popup') && $('map-popup').visible()) {
            $('map-popup-close').click();
        }
    }
});
new AWACPClass('awacpclass');
