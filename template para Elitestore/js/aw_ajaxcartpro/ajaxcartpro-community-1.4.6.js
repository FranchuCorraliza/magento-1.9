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

window.intPrevious = setInterval(function() {
	if(typeof AW_ACP != 'undefined' && document.body) {
        if(typeof aw_cartDivClass == 'undefined') {
            aw_cartDivClass =
            AW_ACP.theme == 'blank' ?
            '.block-cart' :
            '.mini-cart';

            if(!$$(aw_cartDivClass).length || !$$(aw_cartDivClass)[0].tagName) {
                aw_cartDivClass =  '.block-cart'
            }
        }
        if(typeof aw_topLinkCartClass == 'undefined') {
            aw_topLinkCartClass = '.top-link-cart';
        }
        if(typeof aw_addToCartButtonClass == 'undefined') {
            aw_addToCartButtonClass = '.form-button';
        }
        if(typeof aw_bigCartClass == 'undefined') {
            if (typeof($$('.layout-1column')[0]) != 'undefined')
                aw_bigCartClass = '.layout-1column';
            else if (typeof($$('.col-main')[0]) != 'undefined')
                aw_bigCartClass = '.col-main';
            else
                aw_bigCartClass = '.cart';
        }
        if(typeof aw_wishlistClass == 'undefined') {
            if (typeof($$('.my-wishlist')[0]) != 'undefined')
                aw_wishlistClass = '.my-wishlist';
            else
                aw_wishlistClass = '.padder';
        }
        if(typeof(aw_wishlistSidebarClass) == 'undefined') {
            aw_wishlistSidebarClass = '.block-wishlist';
        }

        if(typeof aw_topWishlistLinkCartClass == 'undefined') {
            if ($$('.top-link-wishlist a').length) aw_topWishlistLinkCartClass = '.top-link-wishlist a';
            else aw_topWishlistLinkCartClass = '.top-link-wishlist';
        }

        if (window.location.toString().search('/product_compare/') != -1) {
            win = window.opener;
        } else {
            win = window;
        }
        clearInterval(intPrevious)
    }
}, 500);

function ajaxcartprodelete(url) {
    showProgressAnimation();
    url = getCommonUrl(url);
    if(typeof aw_acp_retries == 'undefined') aw_acp_retries = 0;
	new Ajax.Request(url, {
        onSuccess: function(resp) {
            try {
                if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
            } catch(e) {
                return;
            }
            if(resp && resp.error && resp.error == 'quote error' && aw_acp_retries == 0) {
                aw_acp_retries = 1;
                return ajaxcartprodelete(url);
            }
            aw_acp_retries = 0;
            hideProgressAnimation();
            __onACPRender()
            updateCartView(resp, '');
        }
    });
}

function ajaxcartproshow(url) {
	url = getCommonUrl(url);
    if(typeof aw_acp_retries == 'undefined') aw_acp_retries = 0;
	new Ajax.Request(url, {
        onSuccess: function(resp) {
			try {
                if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
            } catch(e) {
                return;
            }
            if(resp && resp.error && resp.error == 'quote error' && aw_acp_retries == 0) {
                aw_acp_retries = 1;
                return ajaxcartprodelete(url);
            }
            aw_acp_retries = 0;
            __onACPRender()
            updateCartView(resp, '');
        }
    });
}

function updateCartBar(resp){
    var __cartObj = $$(aw_cartDivClass)[0];

    if(__cartObj)
    {
        if (typeof(__cartObj.length) == 'number') __cartObj = __cartObj[0];
        var oldHeight = __cartObj.offsetHeight;

        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.cartbar;
        $(tmpDiv).cleanWhitespace();

        var tmpParent = __cartObj.parentNode;
        tmpParent.replaceChild($(tmpDiv).select(aw_cartDivClass)[0], __cartObj);

        /* Details popup support */

        var __cartObj = $$(aw_cartDivClass)[0];
        var newHeight = __cartObj.offsetHeight;

        addEffectACP(__cartObj, aw_ajaxcartpro_cartanim);
        truncateOptions();
    }
    updateDeleteLinks();
    updateTopLinks(resp);
    if(typeof(resp.custom) != 'undefined') updateCustomBlocks(resp.custom);
}

function updateCartView(resp){
	if (AW_ACP.isCartPage) return updateBigCartView(resp);
	var __cartObj = $$(aw_cartDivClass)[0];
	if(__cartObj)
    {
        if (typeof(__cartObj.length) == 'number') __cartObj = __cartObj[0];
        var oldHeight = __cartObj.offsetHeight;
        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.cart;
        $(tmpDiv).cleanWhitespace();
        var tmpParent = __cartObj.parentNode;
        tmpParent.replaceChild($(tmpDiv).select(aw_cartDivClass)[0], __cartObj);
        //para modificar el numero del carrito cuando cambia el numero de productos
		numberProductCart(resp.count);
        /* Details popup support */

        var __cartObj = $$(aw_cartDivClass)[0];
        var newHeight = __cartObj.offsetHeight;
		addEffectACP(__cartObj, aw_ajaxcartpro_cartanim);
        truncateOptions();
    }
    updateDeleteLinks();
    updateTopLinks(resp);
    if(typeof(resp.custom) != 'undefined') updateCustomBlocks(resp.custom);
}

function updateWishlist(resp) {
    if(typeof(resp.wishlist) == 'undefined') return;
    var wishlistObj = $$(aw_wishlistClass)[0];
    if(wishlistObj) {
        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.wishlist;
        var tmpParent = wishlistObj.parentNode;
        tmpParent.replaceChild(tmpDiv.firstChild, wishlistObj);
    }
    var wishlistSidebar = $$(aw_wishlistSidebarClass).first();
    if(wishlistSidebar) {
        wishlistSidebar.replace(resp.wishlist_sidebar);
        updateAddLinks();
    }
}
