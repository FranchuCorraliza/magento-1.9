var GlaceAjaxProcess = Class.create();
GlaceAjaxProcess.prototype = {
	initialize: function(){
		this.browserTypeIE = GlaceAjax_182.browser.msie;
		this.browserVersion = GlaceAjax_182.browser.version;
		this.runLoader = false;
		this.runHidePopup = true;
		this.test = 0;
	},	  
    
	showPopup: function(id,action){
		if ($(id+'-popup-container').style.position=='absolute' || $(id+'-popup-container').style.position=='fixed'){
			//reposition popup;
			var block = $(id+'-popup-content');
			var blockContainer = $(id+'-popup-container');
			var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
			var width = viewport.width; // Usable window width
			var height = viewport.height; // Usable window height
			
			var boxHeight = block.getHeight();
			var boxWidth = block.getWidth();
			
			var top = boxHeight/2;
		
		    if (boxHeight>=height){
		    	blockContainer.style.position = 'absolute';
		    	block.setStyle({'margin' : '50px auto 50px' });
		    } else {
		    	blockContainer.style.position = 'fixed';
		    	block.setStyle({'top' : '50%', 'margin' : '-'+top+'px auto 0'});
		    }	
			
			var documentHeight = GlaceAjax_182(document).height();
			$(id+'-popup-container').setStyle({ "height" : 100+"%" });
			
			//start vitualimage loader		
			if (id=='ajaxcart-loading') {		
				this.runLoader = true;			
				ajaxcartTools.initPetal('#vitualimage-1', 0.10, 90, false);    
				ajaxcartTools.initPetal('#vitualimage-2', 0.05, 90, true);   
				ajaxcartTools.initPetal('#vitualimage-3', 0.10, 0, true); 
			}	
						
			//display success buttons
			if (id=='success' && action){
				this.countdownTimer(ajaxcart.autohideTime);
				GlaceAjax_182('#success-'+action+'-button').css({'display':'inline-block'});
			}
			
			//show popup
			$(id+'-popup-container').setStyle({ "left" : '0' });
					
			//run scale up effect
			if (id!='ajaxcart-loading') {		
				if (!ajaxcart.isMobile && !ajaxcart.isTablet && (!ajaxcartTools.browserTypeIE || (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion >= 10))) {	
			 		GlaceAjax_182('#'+id+'-popup-content').addClass('grow');
			 		setTimeout(function(){ GlaceAjax_182('#'+id+'-popup-content').addClass('shrink-to-normal'); }, 200);
			 	} else if (ajaxcart.isMobile || ajaxcart.isTablet) {
				 	GlaceAjax_182('#'+id+'-popup-content').addClass('grow-mobile');
			 	} else {
				 	GlaceAjax_182('#'+id+'-popup-content').addClass('shrink-to-normal');
			 	}
			 	
			    			 	
			 	//add popup background if enabled
				if (ajaxcart.showNotificationBkg == 1 && ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 8) {					
			    	GlaceAjax_182('#ac-popup-wrapper-bkg').show();
				} else if (ajaxcart.showNotificationBkg == 1) {				
			    	GlaceAjax_182('#ac-popup-wrapper-bkg').show();
			    	GlaceAjax_182('#ac-popup-wrapper-bkg').animate({'backgroundColor': 'rgba(' + ajaxcart.notificationWrapperBkg + ', 0.6)'},300);	
				}
			}
		}
	},

	hidePopup: function(id, instant){	
		if (this.runHidePopup && (!instant || (instant && !ajaxcart.ajaxCartRunning && !ajaxcartLogin.ajaxCartLoginRunning))) {			
			if (ajaxcart.showNotificationBkg == 1 && id!='ajaxcart-loading' && (id!='options' || (id=='options' && instant) || (id=='options' && ajaxcart.showNotification != 1)) && (id!='ajaxcart-login' || (id=='ajaxcart-login' && instant) || (id=='ajaxcart-login' && ajaxcart.showNotification != 1))) {	
		   		GlaceAjax_182('#ac-popup-wrapper-bkg').hide();				
				GlaceAjax_182('#ac-popup-wrapper-bkg').css({'backgroundColor': 'transparent'});
			} 
			
			if (id != 'ajaxcart-loading') {
				if (instant) {				
					GlaceAjax_182('#'+id+'-popup-content').removeClass('grow');	
					GlaceAjax_182('#'+id+'-popup-content').removeClass('grow-mobile');
					GlaceAjax_182('#'+id+'-popup-content').removeClass('shrink-to-normal');
				 	$(id+'-popup-container').setStyle({ "left" : '-999999px' }); 
				 	
					//clear options popup content
					if (id == 'options') {
						$('ajaxcart-options').update('');
					}
				} else {
					//hide popup
				 	GlaceAjax_182('#'+id+'-popup-content').addClass('shrink');
				 	setTimeout(function() { 		 		
						//hide popup
				 		$(id+'-popup-container').setStyle({ "left" : '-999999px' }); 
				 		GlaceAjax_182('#'+id+'-popup-content').removeClass('shrink'); 
						GlaceAjax_182('#'+id+'-popup-content').removeClass('shrink-to-normal');
						GlaceAjax_182('#'+id+'-popup-content').removeClass('grow');	
						GlaceAjax_182('#'+id+'-popup-content').removeClass('grow-mobile');	
						
						//clear options popup content
						if (id == 'options') {
							$('ajaxcart-options').update('');
						}
				 	}, 300);
				 }
			} else {
				//stop vitualimage loader			
			 	GlaceAjax_182('#'+id+'-popup-content').addClass('shrink');
			 	setTimeout(function() { 
			 		//reset to the initial state before stopping the loader		 		
					ajaxcartTools.resetPetals();
			 		
					//hide popup
			 		$(id+'-popup-container').setStyle({ "left" : '-999999px' }); 
			 		GlaceAjax_182('#'+id+'-popup-content').removeClass('shrink'); 
			 	}, 300);
			}		
		
			//hide success buttons
			if (id == 'success') {
				GlaceAjax_182('#success-cart-button').css({'display':'none'});
				GlaceAjax_182('#success-compare-button').css({'display':'none'});				
				GlaceAjax_182('#success-wishlist-button').css({'display':'none'});
				this.deactivateTimer();
				
				//clear popup messages
				$('ajaxcart-layout-messages').update('');
			}	
		} else {
			ajaxcartTools.runHidePopup = true;
		}
	},

	setLoadWaiting: function(step, enabled) {
		if (step == 'ajaxcart-loading'){
			if (!ajaxcart.addToCartButton){
				if (enabled) {
					this.showPopup('ajaxcart-loading');				
				} else {
					this.hidePopup('ajaxcart-loading', false);

					//enable all buttons, inputs, links etc.
					GlaceAjax_182('#ac-popup-top-bkg').hide();
				}			
			} else {
				if (enabled) {
					var cartButton = GlaceAjax_182(ajaxcart.addToCartButton);
					if (cartButton) {	   
						cartButton.replaceWith(ajaxcart.ajaxCartLoadingHtml); 
					}	
				} else {
					if ($('ajax-cart-please-wait') && (ajaxcart.hasError || $('product_addtocart_form'))) {	   
						GlaceAjax_182('#ajax-cart-please-wait').replaceWith(ajaxcart.addToCartButton.outerHTML);
					}					
					ajaxcart.hasError = false;
					ajaxcart.addToCartButton = false;
					//enable all buttons, inputs, links etc.
					GlaceAjax_182('#ac-popup-top-bkg').hide();
				}		
			}
		} else if ($(step+'-buttons-container')) {
			if (enabled) {
				var container = $(step+'-buttons-container');
				container.addClassName('disabled');
				container.setStyle({opacity:.5});
				Element.hide('login-button');
				Element.show(step+'-please-wait');
			} else {
				var container = $(step+'-buttons-container');
				container.removeClassName('disabled');
				container.setStyle({opacity:1});
				Element.hide(step+'-please-wait');
				Element.show('login-button');

				//enable all buttons, inputs, links etc.
				GlaceAjax_182('#ac-popup-top-bkg').hide();
			}		
		}
	},

	resetLoadWaiting: function(step){
		this.setLoadWaiting(step,false);
		ajaxcart.ajaxCartRunning = false;
	},		
	
	//vitualimage loader functions
	initPetal: function(id, speed, startDegree, rotateClockwise) {
		if (this.runLoader && GlaceAjax_182(id).length>0) {
		    startDegree += speed;
		
		    var r = 30;	    
		    var xcenter = parseInt(GlaceAjax_182('#ajaxcart-loading-popup-content').css('width'))/2; // Usable window width
			var ycenter = parseInt(GlaceAjax_182('#ajaxcart-loading-popup-content').css('height'))/2; // Usable window height
			
			if (rotateClockwise) {
			    var newLeft = Math.floor(xcenter + -(r* Math.cos(startDegree)));
				var newTop = Math.floor(ycenter + -(r * Math.sin(startDegree)));
			} else {
			    var newLeft = Math.floor(xcenter + -(r* Math.sin(startDegree)));
				var newTop = Math.floor(ycenter + -(r * Math.cos(startDegree)));			
			}
					
			var Angle = Math.atan2((ycenter - newTop), (xcenter - newLeft)) * (180 / Math.PI)-90;
	
		    GlaceAjax_182(id).css({'transform':'rotate('+Angle+'deg)'});
		    GlaceAjax_182(id).animate({
		        top: newTop,
		        left: newLeft
		    }, 10, function() {
		        ajaxcartTools.initPetal(id, speed, startDegree, rotateClockwise)
		    });	   
		}
	},
	
	resetPetals: function() {
		ajaxcartTools.runLoader = true;
		ajaxcartTools.initPetal('#vitualimage-1', 0.10, 90, false);    
		ajaxcartTools.initPetal('#vitualimage-2', 0.05, 90, true);   
		ajaxcartTools.initPetal('#vitualimage-3', 0.10, 0, true); 
		ajaxcartTools.runLoader = false;
	},
	
	//automatically close success popup functions	
	countdownTimer: function(closeTime){
	    GlaceAjax_182("#countdownToClose").html(" (" + ajaxcart.autohideTime + ")");
	    
	    var closeValue = parseInt(closeTime) - 1;
	    this.countdown = setInterval(function(){
	    	GlaceAjax_182("#countdownToClose").html(" (" + closeValue + ")");
	    	if (closeValue == 0) {
	    		clearInterval(ajaxcartTools.countdown); 
	    		ajaxcartTools.hidePopup('success', true);
	    	} else {
	    		closeValue--;
	    	}
	    }, 1000);
	},
	
	deactivateTimer: function(){
	    clearInterval(ajaxcartTools.countdown);
	},
	
	updateSection: function(content, id, js_remove) {
		//console.log(content);
        var js_scripts = content.extractScripts();
        // allow script to remove ajax cart at top
        content = content.stripScripts();
        //console.log(content);
        
        var updateWindow = ajaxcart.updateWindow;
        if (id == 'ajaxcart-login-popup-content' || id == 'ajaxcart-options' || id == ajaxcart.comparePopup) {
        	updateWindow = window;
        }
        
        var updateElement = updateWindow.document.getElementById(id);
        GlaceAjax_182(updateElement).html(content);
        // nguyen tien thanh
        if((js_remove != null)&&(js_remove !== 'undefined'))
        	GlaceAjax_182(updateElement).append(js_remove);

        if (js_scripts != null && js_scripts != ''){
			for (var i=0; i< js_scripts.length; i++){
				if (typeof(js_scripts[i]) != 'undefined'){
					var js_script = js_scripts[i];
					this.jsEval(js_script, updateWindow);
					
					//reinitialize the ajaxcart js for the compare popup as well (not just the parent window); runs when ajax login is called from the compare popup
					if (id == 'ajaxcart-qty-js' && window.opener!=null) {
						this.jsEval(js_script, window);
					}
				}
			}		
		}
	},
	
	//evaluates the scripts from the content of an ajax request
	jsEval: function(src, updateWindow){
    	
		if (updateWindow.execScript) {
    	    updateWindow.execScript(src);
    	    return;
    	}
    	var run = function() {
    	    updateWindow.eval.call(updateWindow,src);
    	};
    	run();
	},

	preloadImages: function(content) {	
		var preload = new Array();		
		
		for (var i=0;i<content.length;i++) {
	    	if (content[i]) {					
	    	    var source = (content[i] || '').toString();
	    	    var urlArray = [];
	    	    var url;
	    	    var matchArray;
	    	
	    	    // Regular expression to find FTP, HTTP(S) and email URLs.
	    	    var regexToken = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
	    	
	    	    // Iterate through any URLs in the content.
	    	    while( (matchArray = regexToken.exec( source )) !== null )
	    	    {
	    	        var token = matchArray[0];
	    	        if (token.indexOf('/thumbnail/') != -1) {
	    	        	urlArray.push( token );
	    	        }
	    	    }
	    		
	    		//preloading the images in the dom
	    		var images = new Array();
	    		for (var j = 0, il = urlArray.length; j < il; j++) {
	    		    images[j] = new Image();
	    		    images[j].src = urlArray[j];
	    		}
	    	}
	    }
	},
	
	getProductIdFromUrl: function(url,param){
		var urlArray = url.split("/");
		var productIdKey = 0;
		for (var i=0;i<urlArray.length;i++){
			if (urlArray[i] == param){
				productIdKey = i+1;
				break;
			}
		}
		return urlArray[productIdKey];
	},

	//sets the viewport meta tag for mobile devices
	setViewportMeta: function() {
		var viewportMeta = GlaceAjax_182('meta[name="viewport"]').attr('content');
		if (viewportMeta!=undefined) {
			var viewportMetaArray = viewportMeta.split(',');
			for (var i = viewportMetaArray.length - 1; i >= 0; i--) {
				if (viewportMetaArray[i].indexOf('width=') != -1) {
					viewportMetaArray[i] = 'width='+window.innerWidth+'px';
				}
				if (viewportMetaArray[i].indexOf('height=') != -1) {
					viewportMetaArray[i] = 'height='+window.innerHeight+'px';
				}
			};
			viewportMetaString = viewportMetaArray.join(',');
			GlaceAjax_182('meta[name="viewport"]').attr('content', viewportMetaString);
		} else {
			GlaceAjax_182('head').append('<meta name="viewport" content="width='+window.innerWidth+'px,height='+window.innerHeight+'px"/> ');
		}
	}	
}

var ajaxcartTools = new GlaceAjaxProcess();

var AjaxcartLogin = Class.create();
AjaxcartLogin.prototype = {
	//main functions
	initialize: function(urls){
        this.loginUrl = urls.login;     
        this.postLoginUrl = urls.post_login;
        this.logoutUrl = urls.logout;
        this.logoutText = urls.logout_text;
        this.failureUrl = window.location.href; 
        this.loginPostResponse = false;
        this.onSave = this.save.bindAsEventListener(this);
    },	  
	
	save: function(transport){
		if (transport){
			try{
				response = eval('(' + transport + ')');
			}
			catch (e) {
				response = {};
			}
		}
		
		if (response.error){		
			ajaxcartTools.resetLoadWaiting('ajaxcart-loading');
			ajaxcartTools.resetLoadWaiting('login-mini');
			ajaxcartLogin.ajaxCartLoginRunning = false;
			
			if ((typeof response.message) == 'string') {
				alert(response.message);
			} else {
				alert(response.message.join("\n"));
			}
			
			return false;
		}	

		//redirect in case ajax expired
		if (response.redirect) {
			location.href = response.redirect;
            return;
        }
        
        //update ajaxcart blocks
		//console.log(response);
		if (response.update_section) {
			if (response.update_section.html_login) {
				ajaxcartTools.updateSection(response.update_section.html_login, 'ajaxcart-login-popup-content');
				//focus on the email input and make the login form submit when the "enter" key is pressed
				GlaceAjax_182("#mini-login").focus();
				// GlaceAjax_182("#mini-login").keypress(function( event ) {
			 //    	if ( event.which == 13 ) {
			 //   			GlaceAjax_182('#login-button').click();
			 //    	}
				// });
				GlaceAjax_182("#mini-password").keypress(function( event ) {
			    	if ( event.which == 13 ) {
			   			GlaceAjax_182('#login-button').click();
			    	}
				});
			} else if (response.update_section.welcome) {    		      		    
    		    //save response in variable for later use in ajaxcart class to update all the blocks at once, after the product has been added to wishlist
				this.loginPostResponse = response;
				this.isLoggedIn = true;
				ajaxcart.addToWishlist(this.addToWishlistUrl);	
								
				//update wishlist urls
				var wishlistElements = ajaxcart.updateWindow.document.getElementsByTagName('a');
				var wishlistUrl, newWishlistUrl;
				for ( var i = 0; i<wishlistElements.length; i++ ) {
					if (wishlistElements[i].href != null) {
						wishlistUrl = wishlistElements[i].getAttribute("href").toString();
						if ( wishlistUrl.search("ajaxcartLogin.loadLoginPopup") != -1 ) {
							newWishlistUrl = wishlistUrl.replace("javascript:ajaxcartLogin.loadLoginPopup('","").replace("');","");
							wishlistElements[i].setAttribute("href","javascript:ajaxcart.addToWishlist('"+ newWishlistUrl +"');");
						} 	
					} 	
				}				
			}			
		}
		
		ajaxcartTools.resetLoadWaiting('ajaxcart-loading');
		
		if (response.popup) {
			setTimeout(function() {
				ajaxcartTools.showPopup(response.popup);
			},300);
		}
	},	
	
	failure: function(){
		location.href = this.failureUrl;
	},
	
	loadLoginPopup: function(url) {
		ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);		
		this.addToWishlistUrl = url.replace("javascript:ajaxcartLogin.loadLoginPopup('","").replace("');","");

		var formData = {};
		formData.redirect_url = window.location.href;
		
		GlaceAjax_182(this).ajaxSubmit({ 
		    url: ajaxcartLogin.loginUrl,
		    type: 'post',
		    data: formData,
		    dataType: 'text',
		    success: function(response) {
		        ajaxcartLogin.save(response);
		    },		
		    error: function() {
		        ajaxcartLogin.failure();
		    }
		}); 
	},	
	
	postLogin: function() {
    	var dataForm = new VarienForm('login-form-validate', true);
        if (dataForm.validator.validate()){
			var redirectUrl = window.location.href;
			if (!$$('#login-form-validate #redirect_url')[0]) {
				$('login-form-validate').insert('<input type="hidden" value="'+ redirectUrl +'" id="redirect_url" name="redirect_url" />');
			}
			//if compare popup add param to load new compare popup html as well
			if (window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup) && !$$('#login-form-validate #is_compare_popup')[0]) {
				$('login-form-validate').insert('<input type="hidden" value="1" id="is_compare_popup" name="is_compare_popup" />');
			}
			//if cart page, add param to load new cart page html as well
			if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart')!=-1 && !$$('#login-form-validate #is_cart_page')[0]) {
				$('login-form-validate').insert('<input type="hidden" value="1" id="is_cart_page" name="is_cart_page" />');
			}

			this.ajaxCartLoginRunning = true;
			ajaxcartTools.setLoadWaiting('login-mini', true);

			//disable all buttons, inputs, links etc.
			GlaceAjax_182('#ac-popup-top-bkg').show();

			GlaceAjax_182('#login-form-validate').ajaxSubmit({ 
			    url: ajaxcartLogin.postLoginUrl,
			    type: 'post',
			    dataType: 'text',
			    success: function(response) {
			        ajaxcartLogin.save(response);
			    },		
			    error: function() {
			        ajaxcartLogin.failure();
			    }
			}); 
		}
	},
	
	updateLoginLink: function() {   	
    	var logoutTxt = this.logoutText;
    	var logoutLink = this.logoutUrl;
    	GlaceAjax_182('#ac-links a', ajaxcart.updateWindow.document).each(function() {
		    var href = GlaceAjax_182(this).attr('href');
		    if (href != null && href != '' && href.indexOf('/customer/account/login/') != -1) {
		    	GlaceAjax_182(this).html(logoutTxt);
		    	GlaceAjax_182(this).attr('href', logoutLink);
		    }    			
		});
	}
}

var Ajaxcart = Class.create();
Ajaxcart.prototype = {
	initialize: function(initConfig){
        this.initUrl = initConfig.urls.initialize;      
        this.updateCartUrl = initConfig.urls.updateCart;    
        this.clearCartUrl = initConfig.urls.clearCart; 
        this.addWishlistItemToCartUrl = initConfig.urls.addWishlistItemToCart;  
        this.addAllItemsToCartUrl = initConfig.urls.addAllItemsToCart;
        this.failureUrl = window.location.href; 
        this.onSave = this.save.bindAsEventListener(this);
		this.ajaxCartRunning = false;
		this.addToCartButton = false;
		
		this.showNotification = initConfig.configuration.show_notification; //Enables Notification Pop-Up
		this.autohideTime = initConfig.configuration.autohide_notification_time; //Sets the autohide time for the Success Pop-up
		this.showNotificationBkg = initConfig.configuration.notification_bkg; //Enables the dark Pop-Up background
		this.notificationWrapperBkg = initConfig.configuration.notification_wrapper_bkg; //Enables the dark Pop-Up background
		this.boxShadowColor = initConfig.configuration.box_shadow_color; //image hover background color
		this.successPopupWidth = initConfig.configuration.success_popup_width; //Success Popup width
		this.isMobile = initConfig.configuration.is_mobile;
		this.isTablet = initConfig.configuration.is_tablet;
		
		this.categoryList = 'ac-product-list'; //Sets the Cateory page CLASS to initialize the Ajax Cart 
		this.cartSidebar = 'ac-cart-sidebar'; //Sets the Cart Sidebar CLASS to initialize the Ajax Cart 
		this.compareSidebar = 'ac-compare-sidebar'; //Sets Compare Sidebar CLASS to initialize the Ajax Cart 
		this.comparePopup = 'ac-compare-popup'; //Sets Compare Sidebar CLASS to initialize the Ajax Cart 
		this.wishlistSidebar = 'ac-wishlist-sidebar'; //Sets the Wishlist Sidebar CLASS to initialize the Ajax Cart 
		this.cartPage = 'ac-cart'; //Sets the Cart page CLASS to initialize the Ajax Cart 
		this.wishlistPage = 'ac-wishlist'; //Sets the Wishlist page CLASS to initialize the Ajax Cart 
		this.lastCartSidebar = ($(this.cartSidebar+'1')) ? 1 : 0;
		this.lastCompareSidebar = ($(this.compareSidebar+'1')) ? 1 : 0;
		this.lastWishlistSidebar = ($(this.wishlistSidebar+'1')) ? 1 : 0;
		
		this.categoryQty = initConfig.qtys.category_qty; //Enables Qty input on Category Page
		this.categoryQtyButtons = initConfig.qtys.category_qty_buttons; //Enables Qty input buttons on Category Page
		this.productQtyButtons = initConfig.qtys.product_qty_buttons; //Enables Qty input buttons on Product Page
		this.popupQtyButtons = initConfig.qtys.popup_qty_buttons; //Enables Qty input buttons on Pop Up
		this.cartPageQtyButtons = initConfig.qtys.cartpage_qty_buttons; //Enables Qty input buttons on Cart Page
		this.sidebarQty = initConfig.qtys.sidebar_qty; //Enables "Update Cart" button on sidebar
		this.wishlistPageQtyButtons = initConfig.qtys.wishlist_qty_buttons; //Enables Qty input buttons on Cart Page
		
		this.updateWindow = (window.opener != null && window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(this.comparePopup)) ? window.opener : window;
		this.dragdropCategory = initConfig.dragdrop.dragdrop_enable_category; //Enables Drog & Drop on Category Page
		this.dropEffect = initConfig.dragdrop.dragdrop_drop_effect; //Sets the Drop effect
		this.enableTooltip = initConfig.dragdrop.tooltip_enable; //Enables the tooltip
    },	
	
	//UPDATE functions
	//these functions will run once the specific tags are loaded in the DOM and each time their content is updated
	updateAjaxCartBlocks: function(response){		
		//set the viewport meta tag for mobile devices; required for drag and drop
		if ((ajaxcart.isMobile || ajaxcart.isTablet) && this.dragdropCategory) {
			ajaxcartTools.setViewportMeta();
			Hammer(window).on("pinch", function(event) {
			    setTimeout(function(){ajaxcartTools.setViewportMeta()},300);
			}, false);

			Hammer(window).on("doubletap", function(event) {
			    setTimeout(function(){ajaxcartTools.setViewportMeta()},300);
			});

			window.addEventListener("orientationchange", function() {
			    setTimeout(function(){ajaxcartTools.setViewportMeta()},300);
			}, false);
		}

		//RESET PETAL LOADER	 		
		ajaxcartTools.resetPetals();
						    				
		//UPDATE CART/COMPARE/WISHLIST 		
		//Change add to cart button
		GlaceAjax_182('button').livequery(function(){ 	
			ajaxcart.updateButton(this);
		})	
		//update the parent window as well if actions are performed from the compare popup
		if (window.opener != null) {
			GlaceAjax_182('button', ajaxcart.updateWindow.document).livequery(function(){ 	
				ajaxcart.updateButton(this);
			})		
		}
				    
		//Change cart/wishlist/compare links + add product id attribute to images, used for drag and drop
		GlaceAjax_182('a').livequery(function(){ 	
			ajaxcart.updateLink(this);
		})			
		//update the parent window as well if actions are performed from the compare popup
		if (window.opener != null) {
			GlaceAjax_182('a', ajaxcart.updateWindow.document).livequery(function(){ 	
				ajaxcart.updateLink(this);
			})		
		}
			
		//UPDATE CATEGORY PAGE
		GlaceAjax_182('.' + ajaxcart.categoryList + ' li').livequery(function(){ 					
			var li = this;
			var buttons = li.getElementsByTagName('button');
			var isSaleable = false;
			
			for ( var i = 0; i<buttons.length; i++ ) {
			    if (buttons[i].onclick != null && buttons[i].onclick != '') {
			    	var onclickCartAction = buttons[i].getAttributeNode("onclick").nodeValue.toString();    	
			    	if ( onclickCartAction.indexOf("checkout/cart/add") != -1 || onclickCartAction.indexOf("ajaxcart/index/init") != -1) {
			    		isSaleable = true;
			    		
			    		//add qty inputs, increase/decrease buttons
			    		var productId = ajaxcartTools.getProductIdFromUrl(onclickCartAction,'product');
			    		ajaxcart.addQtyBoxHtml('ac-product-list', buttons[i], productId);
			    		
			    		//add productId attribute to image for drag and drop	
			    		if (ajaxcart.dragdropCategory == 1){
							GlaceAjax_182(li).find('img').each(function() {
								var src = GlaceAjax_182(this).attr('src');
					            if (!GlaceAjax_182(this).parent().attr("product") && src != '' && src != null && src.indexOf('media/catalog/product/')!=-1){
					    			GlaceAjax_182(this).wrap('<div></div>');
					        		GlaceAjax_182(this).parent().attr("product",productId);	
					        	}
							});
			        	}
			    	}
			    }
			}
			
			//if not isSaleable, add productId attribute to image for drag and drop	
			if (!isSaleable && ajaxcart.dragdropCategory == 1) {
			    var links = li.getElementsByTagName('a');					
			    for ( var i = 0; i<links.length; i++ ) {
			    	if (links[i].href != null && links[i].href != '') {
			    		link = links[i].href.toString();
			    	    if (link.indexOf("ajaxcart/wishlist/add/") != -1 || link.indexOf("wishlist/index/add/") != -1 || link.indexOf("ajaxcart/product_compare/add/") != -1 || link.indexOf("catalog/product_compare/add/") != -1) {
			    	    	//add productId attribute to image for drag and drop	
			    	    	var productId = ajaxcartTools.getProductIdFromUrl(links[i].href,'product');
			    			GlaceAjax_182(li).find('img').each(function() {
								var src = GlaceAjax_182(this).attr('src');
					            if (!GlaceAjax_182(this).parent().attr("product") && src != '' && src != null && src.indexOf('media/catalog/product/')!=-1){
					    			GlaceAjax_182(this).wrap('<div></div>');
					        		GlaceAjax_182(this).parent().attr("product",productId);	
					        	}
							});
			    		    break;
			    	    }
			        }
			    }
			}
			
			//ajaxcart.makeItemDraggable(li, isSaleable);
		        
		    ajaxcart.updateWindow.dispatchLiveUpdates('list_item', li);
		})
		
		//UPDATE PRODUCT PAGE
		GlaceAjax_182('#product_addtocart_form').livequery(function(){ 
			//Change add to cart button		
			var formElement = GlaceAjax_182('#product_addtocart_form');
			var formAction = String(formElement.attr("action"));
			var productId = ajaxcartTools.getProductIdFromUrl(formAction,'product');
			    
			//add increase/decrease buttons to qty input
			if ($('qty')) {	
			    ajaxcart.addQtyBoxHtml('ac-product-view', $('qty'), productId);	
			    $('qty').value = productMinMax[productId]['min']; 
			} else {
			    var inputElements = $('product_addtocart_form').getElementsByTagName('input');
			    for ( var j = 0; j<inputElements.length; j++ ) {
			    	if (inputElements[j].name != null && inputElements[j].name != '') {
			    		var qtyName = inputElements[j].getAttributeNode("name").nodeValue.toString();						
			    		if ( qtyName.search("super_group") != -1 ) {			
			    			var productId = qtyName.replace("super_group[","").replace("]","");
			    			ajaxcart.addQtyBoxHtml('ac-product-view-grouped', inputElements[j], productId);												
			    		}
			    	}
			    }		
			}
		        
		    ajaxcart.updateWindow.dispatchLiveUpdates('product', false);
		})	
		
		//UPDATE CART SIDEBAR
		GlaceAjax_182('.'+ajaxcart.cartSidebar +' div', ajaxcart.updateWindow.document).livequery(function(){	
			for (var i=0;i<=ajaxcart.lastCartSidebar;i++) {
				//add tooltip
				if (i==ajaxcart.lastCartSidebar && ajaxcart.enableTooltip != 0 && ajaxcart.dragdropCategory == 1 && !GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar + ' span.tooltip-sidebar', ajaxcart.updateWindow.document).length > 0){		
					GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar + ' div', ajaxcart.updateWindow.document).first().append('<span class="tooltip-sidebar">' + Translator.translate('Buy Me!').stripTags() + '</span>');
				}
				
				if(i==ajaxcart.lastCartSidebar && ajaxcart.dragdropCategory == 1) {
				    ajaxcart.makeCartDroppableArea();		
				}
				
				//add update/empty sidebar cart buttons to cart sidebar, on first load
			    if($(ajaxcart.cartSidebar + i) && $$('.ajaxcart-qty-input')[0] && !$$('#'+ ajaxcart.cartSidebar + i +' #ajaxcart-actions')[0]){
			    	var actionsContainer = '<div class="actions" id="ajaxcart-actions" style="border-bottom:none;">';
			    	actionsContainer += '<button onclick="ajaxcart.emptyCartConfirmation();" class="button left" title="' + Translator.translate('Empty Cart').stripTags() + '" type="button"><span><span>' + Translator.translate('Empty Cart').stripTags() + '</span></span></button>';
			    	if (ajaxcart.sidebarQty == 1){
			    		actionsContainer += '<button onclick="ajaxcart.updateQty(\'\',false,\'' + ajaxcart.cartSidebar + i + '\')" class="button" title="' + Translator.translate('Update Cart').stripTags() + '" type="button"><span><span>' + Translator.translate('Update Cart').stripTags() + '</span></span></button>';
			    	}		
			    	actionsContainer += '</div>';
			    	
			    	GlaceAjax_182('#'+ajaxcart.cartSidebar + i +' div').first().append(actionsContainer);
			    }	
			    
			    truncateOptions();		
			        
			    ajaxcart.updateWindow.dispatchLiveUpdates('cart_sidebar', false);
			}	
		})
		
		//UPDATE CART PAGE
		GlaceAjax_182('#'+ajaxcart.cartPage+' input', ajaxcart.updateWindow.document).livequery(function(){	
			//add increase/decrease buttons to qty inputs
			var input = GlaceAjax_182(this);
			if (input.attr('name') != null && input.attr('name') != '' && input.attr('name').indexOf('cart') != -1 && input.attr('name').indexOf('[qty]') != -1) {	
			    var itemId = input.attr('name').replace("cart[","").replace("][qty]","");
			    input.attr('id','cart-qty-' + itemId);
			    ajaxcart.addQtyBoxHtml('ac-cart-page', GlaceAjax_182(this), 'item'+itemId);	
			}
		        
		    ajaxcart.updateWindow.dispatchLiveUpdates('cart', false);
		})
		
		//UPDATE WISHLIST SIDEBAR
		GlaceAjax_182('.'+ajaxcart.wishlistSidebar +' div', ajaxcart.updateWindow.document).livequery(function(){	
			for (var i=0;i<=ajaxcart.lastWishlistSidebar;i++) {
				//add tooltip
				if (i==ajaxcart.lastWishlistSidebar && ajaxcart.enableTooltip != 0 && ajaxcart.dragdropCategory == 1 && !GlaceAjax_182('#'+ajaxcart.wishlistSidebar + ajaxcart.lastWishlistSidebar +' span.tooltip-sidebar', ajaxcart.updateWindow.document).length > 0){		
					GlaceAjax_182('#'+ajaxcart.wishlistSidebar+ ajaxcart.lastWishlistSidebar +' div', ajaxcart.updateWindow.document).first().append('<span class="tooltip-sidebar">' + Translator.translate('Wish Me!').stripTags() + '</span>');
				}
				
				if(i==ajaxcart.lastWishlistSidebar && ajaxcart.dragdropCategory == 1) {
				    ajaxcart.makeWishlistDroppableArea();		
				}	
			        
			    ajaxcart.updateWindow.dispatchLiveUpdates('wishlist_sidebar', false);	
			}
		})
		
		//UPDATE WISHLIST PAGE
		GlaceAjax_182('#'+ajaxcart.wishlistPage+' input', ajaxcart.updateWindow.document).livequery(function(){	
			//add increase/decrease buttons to qty inputs
			var input = GlaceAjax_182(this);
			if (input.attr('name') != null && input.attr('name') != '' && input.attr('name').indexOf('qty[') != -1) {	
			    var itemId = input.attr('name').replace("qty[","").replace("]","");
			    input.attr('id','wishlist-qty-' + itemId);
			    ajaxcart.addQtyBoxHtml('ac-wishlist-page', GlaceAjax_182(this), 'witem'+itemId);	
			}
			
		    truncateOptions();
		        
		    ajaxcart.updateWindow.dispatchLiveUpdates('wishlist', false);
		})
		
		//UPDATE COMPARE SIDEBAR
		GlaceAjax_182('.'+ajaxcart.compareSidebar +' div', ajaxcart.updateWindow.document).livequery(function(){	
			for (var i=0;i<=ajaxcart.lastCompareSidebar;i++) {
				//add tooltip
				if (i==ajaxcart.lastCompareSidebar && ajaxcart.enableTooltip != 0 && ajaxcart.dragdropCategory == 1 && !GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar + ' span.tooltip-sidebar', ajaxcart.updateWindow.document).length > 0){		
					GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar + ' div', ajaxcart.updateWindow.document).first().append('<span class="tooltip-sidebar">' + Translator.translate('Compare Me!').stripTags() + '</span>');
				}
				
				if(ajaxcart.dragdropCategory == 1) {
				    ajaxcart.makeCompareDroppableArea();		
				}
			        
			    ajaxcart.updateWindow.dispatchLiveUpdates('compare_sidebar', false);
			}
		})
	},		
	
	//change the normal link with the ajaxcart equivalent link
	updateLink: function(a) {
		var link, newLink;
		
		if (a.href != null && a.href != '') {	
		    link = a.href.toString();
		    	
		    //Change cart links
		    if ( link.indexOf("checkout/cart/delete") != -1 ) {
				var itemId = ajaxcartTools.getProductIdFromUrl(link,'id');
				a.setAttribute("href","javascript:ajaxcart.removeCartItem("+itemId+");");
				if (a.onclick == null) {
					a.setAttribute("onclick","return confirm('Are you sure you would like to remove this item from the shopping cart?');");	
					//IE7 fix
					if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
						a.outerHTML = a.outerHTML;
					}
				}
			} 	
			if ( link.indexOf("checkout/cart/configure/") != -1 ) {
		        newLink = link.replace("checkout/cart/configure/","ajaxcart/cart/configure/");
		        a.setAttribute("href","javascript:ajaxcart.configureCartItem('"+newLink+"');");
		    } 		
		
		    //Change compare links
		    if ( link.indexOf("catalog/product_compare/add/") != -1 ) {
			    var productId = ajaxcartTools.getProductIdFromUrl(link,'product');
		        newLink = link.replace("catalog/product_compare/add/","ajaxcart/product_compare/add/");
		        a.setAttribute("href","javascript:ajaxcart.addToCompare('"+ newLink +"');");
		        
		        //add compare link to drag and drop array
		        dragDropProducts[productId] = ( typeof dragDropProducts[productId] != 'undefined' && dragDropProducts[productId] instanceof Array ) ? dragDropProducts[productId] : []
		        dragDropProducts[productId]['compare'] = newLink;
		    } 				
		    if ( link.indexOf("catalog/product_compare/clear/") != -1 ) {
		        newLink = link.replace("catalog/product_compare/clear/","ajaxcart/product_compare/clear/");
		        a.setAttribute("href","javascript:ajaxcart.removeCompareItems('"+ newLink +"');");
		    } 	
		    if ( link.indexOf("catalog/product_compare/remove/") != -1 ) {
		        newLink = link.replace("catalog/product_compare/remove/","ajaxcart/product_compare/remove/");
		        a.setAttribute("href","javascript:ajaxcart.removeCompareItem('"+newLink+"');");
		    } 	
		    
		    //Change compare popup remove links
			if (a.onclick && a.onclick != null && a.onclick != '') {
				var onClick = a.getAttributeNode("onclick").nodeValue.toString();
				if (onClick.indexOf("catalog/product_compare/remove/") != -1) {
			        newLink = onClick.replace("catalog/product_compare/remove/","ajaxcart/product_compare/remove/").replace("removeItem('","").replace("');","");
					a.setAttribute("onclick","ajaxcart.removeCompareItem('"+newLink+"');");
					
		    		//ie7 fix
					if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
		    			var buttonHtml = a.outerHTML;
						a.outerHTML = buttonHtml;
					}
				}
		    } 			
		    
		    //Change wishlist links
		    if(ajaxcartLogin.isLoggedIn){
		        if ( link.indexOf("wishlist/index/add/") != -1 ) {
			    	var productId = ajaxcartTools.getProductIdFromUrl(link,'product');
		            newLink = link.replace("wishlist/index/add/","ajaxcart/wishlist/add/");
		            a.setAttribute("href","javascript:ajaxcart.addToWishlist('"+ newLink +"');");
		            //remove onclick from compare popup
		            a.setAttribute("onclick","");
		        
			        //add wishlist link to drag and drop array
					dragDropProducts[productId] = ( typeof dragDropProducts[productId] != 'undefined' && dragDropProducts[productId] instanceof Array ) ? dragDropProducts[productId] : []
			        dragDropProducts[productId]['wishlist'] = newLink;			        
		        } else if (a.onclick && a.onclick.toString().indexOf("wishlist/index/add/") != -1) {
		        	//update add to wishlist link for compare popup
		        	var onClick = a.getAttributeNode("onclick").nodeValue.toString();
			        var productId = ajaxcartTools.getProductIdFromUrl(onClick,'product');
		            newLink = onClick.replace("setPLocation('","").replace("', false)","").replace("', true)","").replace("wishlist/index/add/","ajaxcart/wishlist/add/");
		            a.setAttribute("onclick","ajaxcart.addToWishlist('"+ newLink +"');");
		        }	
		        if ( link.indexOf("wishlist/index/cart") != -1 ) {
		            newLink = link.replace("wishlist/index/cart/","ajaxcart/wishlist/cart/");
		            a.setAttribute("href","javascript:ajaxcart.addWishlistItemToCart('"+newLink+"', false);");
		        } 
		        if ( link.indexOf("wishlist/index/configure") != -1 ) {
		            newLink = link.replace("wishlist/index/configure/","ajaxcart/wishlist/configure/");
		            a.setAttribute("href","javascript:ajaxcart.configureWishlistItem('"+newLink+"');");
		        } 
		        if ( link.indexOf("wishlist/index/remove") != -1 ) {
		            newLink = link.replace("wishlist/index/remove/","ajaxcart/wishlist/remove/");
		            a.setAttribute("href","javascript:ajaxcart.removeWishlistItem('"+newLink+"');");
		        } 	
		        if ( link.indexOf("ajaxcartLogin.loadLoginPopup") != -1 ) {
					newLink = link.replace("javascript:ajaxcartLogin.loadLoginPopup('","").replace("');","");
					a.setAttribute("href","javascript:ajaxcart.addToWishlist('"+ newLink +"');");
				} 
				//update move to wishlist link from shopping cart page
				if ( link.indexOf("wishlist/index/fromcart/") != -1 ) {
			        newLink = link.replace("wishlist/index/fromcart/","ajaxcart/wishlist/fromcart/");
			        a.setAttribute("href","javascript:ajaxcart.moveToWishlist('"+newLink+"');");
			    } 	
		    } else {
		        if ( link.indexOf("wishlist/index/add/") != -1 ) {
			    	var productId = ajaxcartTools.getProductIdFromUrl(link,'product');
		            newLink = link.replace("wishlist/index/add/","ajaxcart/wishlist/add/");
		            a.setAttribute("href","javascript:ajaxcartLogin.loadLoginPopup('"+newLink+"');");
		            //remove onclick from compare popup
		            a.setAttribute("onclick","");
		        
			        //add wishlist link to drag and drop array
					dragDropProducts[productId] = ( typeof dragDropProducts[productId] != 'undefined' && dragDropProducts[productId] instanceof Array ) ? dragDropProducts[productId] : []
			        dragDropProducts[productId]['wishlist'] = newLink;
		        } 	
		    } 		
		    
	    	ajaxcart.updateWindow.dispatchLinkUpdates(a, onClick);	    
		}
	},
	
	//change the normal button with the ajaxcart equivalent button
	updateButton: function(button) {
		if (button.onclick != null && button.onclick != '') {
	    	var onClick = button.getAttributeNode("onclick").nodeValue.toString();
	    	if ( onClick.indexOf("checkout/cart/add") != -1 && onClick.indexOf("/product/") != -1 ) {
	    		if ( onClick.indexOf("setPLocation") != -1 ) {
		    		var url = onClick.replace("setPLocation('","").replace("', false)","").replace("', true)","").replace("checkout/cart/add","ajaxcart/index/init");
		    	} else {
		    		var url = onClick.replace("setLocation('","").replace("')","").replace("checkout/cart/add","ajaxcart/index/init");
		    	}
			    var productId = ajaxcartTools.getProductIdFromUrl(url,'product');
	    		button.setAttribute("onclick","ajaxcart.initAjaxcart('"+ url +"', this, false);");
	    		
	    		//ie7 fix
				if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
	    			var buttonHtml = button.outerHTML;
					button.outerHTML = buttonHtml;
				}
	    			    		
		        //add cart link to drag and drop array
		        dragDropProducts[productId] = ( typeof dragDropProducts[productId] != 'undefined' && dragDropProducts[productId] instanceof Array ) ? dragDropProducts[productId] : []
		        dragDropProducts[productId]['cart'] = url;
	    	} else if ( onClick === 'productAddToCartForm.submit(this)' || onClick === 'productAddToCartForm.submit()' ) {
				button.setAttribute("onclick","ajaxcart.initAjaxcart(false, this, false);");
	    		
	    		//ie7 fix
				if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
	    			var buttonHtml = button.outerHTML;
					button.outerHTML = buttonHtml;
				}
	    	} else if ($('ac-wishlist') && onClick.indexOf("this.name='do'") != -1) {	
				GlaceAjax_182(button).click(function( event ) {
				    event.preventDefault();
				    ajaxcart.updateWishlistItems();
				});
			} else if (onClick.indexOf("addWItemToCart(") != -1) {
	    		var itemId = onClick.replace("addWItemToCart(","").replace(");","").replace(")","");
		    	button.setAttribute("onclick","ajaxcart.initAddWishlistItemToCart("+itemId+");");
	    		
	    		//ie7 fix
				if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
	    			var buttonHtml = button.outerHTML;
					button.outerHTML = buttonHtml;
				}
	    	} else if (onClick.indexOf("addAllWItemsToCart(") != -1) {
		    	button.setAttribute("onclick","ajaxcart.addAllWishlistItemsToCart();");
	    		
	    		//ie7 fix
				if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
	    			var buttonHtml = button.outerHTML;
					button.outerHTML = buttonHtml;
				}
	    	}
	    	
	    	ajaxcart.updateWindow.dispatchButtonUpdates(button, onClick);
	    } else if (GlaceAjax_182(button).val() != null && GlaceAjax_182(button).val() != '' && GlaceAjax_182(button).val()=='update_qty') {
	    	//change cart page update cart button
		    GlaceAjax_182(button).click(function( event ) {
			    event.preventDefault();
			    ajaxcart.updateCartQty('',false);
			});
	    } else if (GlaceAjax_182(button).val() != null && GlaceAjax_182(button).val() != '' && GlaceAjax_182(button).val()=='empty_cart') {
	       	//change cart page empty cart button
		    GlaceAjax_182(button).click(function( event ) {
			    event.preventDefault();
			    ajaxcart.emptyCartConfirmation();
			});
	    } else if ($('ac-cart') && GlaceAjax_182(button).hasClass('btn-update')) {
	    	//change cart page update cart button
		    GlaceAjax_182(button).click(function( event ) {
			    event.preventDefault();
			    ajaxcart.updateCartQty('',false);
			});
	    } else if ($('ac-wishlist') && GlaceAjax_182(button).attr('name')=='do') {
	    	//change cart page update cart button
		    GlaceAjax_182(button).click(function( event ) {
			    event.preventDefault();
				ajaxcart.updateWishlistItems();
			});
	    }
	},  
	
	//update blocks with ajax loaded content
	updateSections: function(response){
		//update layout messages
		if (response.update_section.html_layout_messages) {
		    $('ajaxcart-layout-messages').update(response.update_section.html_layout_messages);
		}	
		
		if (response.update_section.html_options_layout_messages) {
		    $('ajaxcart-options-layout-messages').update(response.update_section.html_options_layout_messages);
		}
		
		//Update siderbar blocks
		if (!ajaxcartLogin.loginPostResponse) {		
			//reinitialize the ajaxcart js if an item is added/updated while on the shopping cart page
			if (response.update_section.html_ajaxcart_js) {
				ajaxcartTools.updateSection(response.update_section.html_ajaxcart_js,'ajaxcart-qty-js');
			}	
			
			//update options popup
			if (response.update_section.html_options) {
				ajaxcartTools.updateSection(response.update_section.html_options,'ajaxcart-options');
				this.updateOptionsPopupAddToCartButton(response);
			}
		
			//preload cart/wishlist sidebar images
			if (response.update_section.html_cart) {
				ajaxcartTools.preloadImages(response.update_section.html_cart);
			}
			
			// Update count product
			
			if (response.update_section.html_cart_count) {
				if(GlaceAjax_182('.skip-cart'))
					GlaceAjax_182('.skip-cart').html(response.update_section.html_cart_count);
			}
			
			if (response.update_section.html_wishlist) {
				ajaxcartTools.preloadImages(response.update_section.html_wishlist);
			}
			
			setTimeout(function() {
				ajaxcart.updateCart(response);
				ajaxcart.updateCartPage(response);			
				ajaxcart.updateWishlist(response);
				ajaxcart.updateWishlistPage(response);	
				ajaxcart.updateCompare(response);				
				ajaxcart.updateComparePopup(response);
					
				//this function is used to launch specific events for custom themes
				ajaxcart.updateWindow.dispatchBlockUpdates(response);			
			},300);
		} else {	
			//close login popup
			this.successPopupDelay = 200;
			ajaxcartTools.hidePopup('ajaxcart-login', false);		
			
			//preload cart/wishlist sidebar images			
			if (ajaxcartLogin.loginPostResponse.update_section.html_cart) {
				ajaxcartTools.preloadImages(ajaxcartLogin.loginPostResponse.update_section.html_cart);
			}
			if (ajaxcartLogin.loginPostResponse.update_section.html_wishlist) {
				ajaxcartTools.preloadImages(ajaxcartLogin.loginPostResponse.update_section.html_wishlist);
			}
			
			//delay the update of the blocks in order for the images to have time to be added to the DOM
			setTimeout(
				function(){		
					 
	    		    //update login link
	    			ajaxcartLogin.updateLoginLink();	 
	    				
					//update welcome message
					if (ajaxcartLogin.loginPostResponse.update_section.welcome && ajaxcart.updateWindow.document.getElementById('ac-welcome-message')) {
						ajaxcart.updateWindow.document.getElementById('ac-welcome-message').innerHTML = ajaxcartLogin.loginPostResponse.update_section.welcome;
					}	
					
					//reinitialize the ajaxcart js
					if (ajaxcartLogin.loginPostResponse.update_section.html_ajaxcart_js) {
						ajaxcartTools.updateSection(ajaxcartLogin.loginPostResponse.update_section.html_ajaxcart_js,'ajaxcart-qty-js');
					}		
					
					ajaxcart.updateCart(ajaxcartLogin.loginPostResponse);
					ajaxcart.updateCartPage(ajaxcartLogin.loginPostResponse);	
					ajaxcart.updateWishlist(response);		
					ajaxcart.updateWishlistPage(response);
					ajaxcart.updateCompare(ajaxcartLogin.loginPostResponse);
					ajaxcart.updateComparePopup(ajaxcartLogin.loginPostResponse);	
					
					//this function is used to launch specific events for custom themes
					ajaxcart.updateWindow.dispatchBlockUpdates(ajaxcartLogin.loginPostResponse);	
					
					//reset values
					ajaxcartLogin.loginPostResponse = false;	
					ajaxcartLogin.ajaxCartLoginRunning = false;
				
					//enable all buttons, inputs, links etc.
					GlaceAjax_182('#ac-popup-top-bkg').hide();
				}
				,300
			);
		}
	},			
	
	//update add to cart button from the product options popup
	updateOptionsPopupAddToCartButton: function(response){
		var buttonElements = $('ajaxcart-options').getElementsByTagName('button');
		var link = response.form_action, 
			onClick;
           	
		for ( var i = 0; i<buttonElements.length; i++ ) {
			if (buttonElements[i].onclick != null && buttonElements[i].onclick != '') {
				var onClick = buttonElements[i].getAttributeNode("onclick").nodeValue;
				if ( onClick === 'productAddToCartForm.submit(this)' || onClick === 'productAddToCartForm.submit()' ) {
					if (link.indexOf('/wishlist/index/cart/')!=-1) {						
		            	onClick = "ajaxcart.addWishlistItemToCart('" + link.replace("wishlist/index/cart/","ajaxcart/wishlist/cart/") + "skip_popup/1/', this);";
		            } else if (link.indexOf('/wishlist/index/updateItemOptions/')!=-1) {
		            	onClick = "ajaxcart.updateWishlistItem('" + link.replace("wishlist/index/updateItemOptions/","ajaxcart/wishlist/updateItemOptions/") + "skip_popup/1/', this);";
		            	GlaceAjax_182(buttonElements[i]).html("<span><span>" + Translator.translate('Update Wishlist') + "</span></span>");
		            } else if (link.indexOf('checkout/cart/updateItemOptions')!=-1) {
		            	onClick = "ajaxcart.updateCartItem('" + link.replace("checkout/cart/updateItemOptions/","ajaxcart/cart/updateItemOptions/") + "close_popup/1/', this);";
				        GlaceAjax_182(buttonElements[i]).html("<span><span>" + Translator.translate('Update Cart') + "</span></span>");
				    } else {
		            	onClick = "ajaxcart.initAjaxcart('"+ link +"', this, true);";
		            }
		            
					buttonElements[i].setAttribute("onclick", onClick);
					//IE7 fix, so the add to cart button will be add to the DOOM
					if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
						buttonElements[i].outerHTML = buttonElements[i].outerHTML;
					}					
				} 
			}
		}
		if (response.product_id) {
			this.itemProductId = response.product_id;
		}
		if ($$('#options_addtocart_form #qty')[0]){	
			ajaxcart.addQtyBoxHtml('ac-options-popup', $$('#options_addtocart_form #qty')[0], response.product_id);
			if (response.qty) {		
				if (isNaN(response.qty)){
					$('popup-qty').value = productMinMax[response.product_id]['min'];
				} else {
					$('popup-qty').value = response.qty;
				}
			}
		} else {
			var inputElements = $('ajaxcart-options').getElementsByTagName('input');
			for ( var j = 0; j<inputElements.length; j++ ) {
				if (inputElements[j].name != null && inputElements[j].name != '') {
					var qtyName = inputElements[j].getAttributeNode("name").nodeValue.toString();						
					if ( qtyName.search("super_group") != -1 ) {			
						var productId = qtyName.replace("super_group[","").replace("]","");
						ajaxcart.addQtyBoxHtml('ac-options-popup-grouped', inputElements[j], productId);	
						if (response.qty) {		
							if (isNaN(response.qty)){
							    inputElements[j].value = productMinMax[productId]['min'];
							} else {
							    inputElements[j].value = response.qty;
							}	
						}		
					}
				}
			}		
		}	
	},
	
	//QTY functions
	addQtyBoxHtml: function(section, element, productId) {
		var addInput = 0;
		var addInputButtons = 0;
		
		if (section=='ac-product-list') {
			var name = "qty-"+ productId;
			addInput = ajaxcart.categoryQty;
			if (addInput==1) {
				addInputButtons = ajaxcart.categoryQtyButtons;
			} 
		} else if (section=='ac-product-view') {
			var name = "qty";
			addInputButtons = ajaxcart.productQtyButtons;
		} else if (section=='ac-product-view-grouped') {
			var name = 'grouped-qty-' + productId;
			element.setAttribute('id',name);
			addInputButtons = ajaxcart.productQtyButtons;
		} else if (section=='ac-cart-page') {
			var itemId = element.attr('id').replace(/\D/g, '' );
			var name = "cart-qty-"+itemId;
			addInputButtons = ajaxcart.cartPageQtyButtons;
		} else if (section=='ac-wishlist-page') {
			var itemId = element.attr('id').replace(/\D/g, '' );
			var name = "wishlist-qty-"+itemId;
			addInputButtons = ajaxcart.wishlistPageQtyButtons;
		} else if (section=='ac-options-popup') {
			addInputButtons = ajaxcart.popupQtyButtons;
			var name = "popup-qty";
			element.setAttribute('id',name);
		} else if (section=='ac-options-popup-grouped') {
			var name = 'popup-grouped-qty-' + productId;
			element.setAttribute('id',name);
			addInputButtons = ajaxcart.popupQtyButtons;
		}	
					
		var qtyBoxHtml = '';
		if (addInput == 1 && addInputButtons == 1){
		    qtyBoxHtml = "<span class='ajaxcart-qty'><input name='"+name+"' id='"+name+"' type='text' value='"+ productMinMax[productId]['min'] +"' class='input-text qty'><span class='qty-control-box'><button type='button' class='increase' href='javascript:void(0)' onclick='ajaxcart.qtyUp(" + productId + ",\""+name+"\", this);'><span>+</span></button><button type='button' class='decrease' href='javascript:void(0)' onclick='ajaxcart.qtyDown(" + productId + ",\""+name+"\", this);'><span>-</span></button></span></span>";
		} else if (addInput == 1) {
		    qtyBoxHtml = "<span class='ajaxcart-qty'><input name='"+name+"' id='"+name+"' type='text' value='"+ productMinMax[productId]['min'] +"' class='input-text qty'></span>";
		} else if (addInputButtons == 1) {
		    qtyBoxHtml = "<span class='qty-control-box'><button type='button' class='increase' href='javascript:void(0)' onclick='ajaxcart.qtyUp(\"" + productId + "\",\""+name+"\",this);'><span>+</span></button><button type='button' class='decrease' href='javascript:void(0)' onclick='ajaxcart.qtyDown(\"" + productId + "\",\""+name+"\",this);'><span>-</span></button></span>";
		}		
		
		if (section=='ac-product-list') {
			if ($('ac-list-qty-'+productId)) {
				$('ac-list-qty-'+productId).innerHTML = qtyBoxHtml;
			} else {
			    element.outerHTML += qtyBoxHtml;
			}
		} else if (section=='ac-cart-page' || section=='ac-wishlist-page') {
			element.wrap('<span class="ajaxcart-qty"></span>');
			element.after(qtyBoxHtml);
		} else {
			element.outerHTML = '<span class="ajaxcart-qty">'+ element.outerHTML + qtyBoxHtml + '</span>';
		} 		
	},
	
	//increase/decrease cart qty functions	
	qtyUp: function(productId, qtyElementId, button){  
		var qtyElement = GlaceAjax_182("#" + qtyElementId);
		
		if (GlaceAjax_182(button).closest(".ac-product-list").find("#" + qtyElementId).length) {
			qtyElement = GlaceAjax_182(button).closest(".ac-product-list").find("#" + qtyElementId);
		}
	
		var oldValue = parseFloat(qtyElement.val());
		
		if (isNaN(oldValue)){
			alert(Translator.translate('The requested quantity is not available.').stripTags());
			qtyElement.val(productMinMax[productId]['min']);
			return;
		}
		
		if (oldValue<productMinMax[productId]['min'] && (oldValue+1) != productMinMax[productId]['min']) {
			alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
			qtyElement.val(productMinMax[productId]['min']);
			return;
		}		
		
		if ((oldValue+productMinMax[productId]['inc'])<=productMinMax[productId]['max']) {
			if(qtyElementId.search("sidebar-qty-") != -1 ){
				this.updateQty(qtyElementId,true,button);
			} else if( qtyElementId.search("cart-qty-") != -1 ){
				this.updateCartQty(qtyElementId,true);
			} else if ( qtyElementId.search("wishlist-qty-") != -1 ) {
				this.updateWishlistQty(qtyElementId,true);
			} else {
				qtyElement.val(oldValue + productMinMax[productId]['inc']);
			}
		} else {
			alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
			qtyElement.val(productMinMax[productId]['max']);
		}
    }, 
	
	qtyDown: function(productId, qtyElementId, button){
		var qtyElement = GlaceAjax_182("#" + qtyElementId);
		
		if (GlaceAjax_182(button).closest(".ac-product-list").find("#" + qtyElementId).length) {
			qtyElement = GlaceAjax_182(button).closest(".ac-product-list").find("#" + qtyElementId);
		}
		
		var oldValue = parseFloat(qtyElement.val());
		if (isNaN(oldValue)){
			alert(Translator.translate('The requested quantity is not available.').stripTags());
			qtyElement.val(productMinMax[productId]['min']);
			return;
		}
		
		if (oldValue>productMinMax[productId]['max']) {
			alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
			qtyElement.val(productMinMax[productId]['max']);
			return;
		}		
		
		if ((oldValue-productMinMax[productId]['inc'])>=productMinMax[productId]['min']) {
			if(qtyElementId.search("sidebar-qty-") != -1){
				this.updateQty(qtyElementId,false,button);
			} else if( qtyElementId.search("cart-qty-") != -1 ){
				this.updateCartQty(qtyElementId,false);
			} else if ( qtyElementId.search("wishlist-qty-") != -1 ) {
				this.updateWishlistQty(qtyElementId,false);
			} else {
				qtyElement.val(oldValue - productMinMax[productId]['inc']);
			}
		} else {
			if(qtyElementId.search("sidebar-qty-") != -1){
				if (oldValue==1) {
					if (confirm(Translator.translate('Are you sure you would like to remove this item from the shopping cart?').stripTags())){
						this.updateQty(qtyElementId,false,button);
					}
				} else {
					alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
				}
			} else if( qtyElementId.search("cart-qty-") != -1 ){				
				if (oldValue==1) {
					if (confirm(Translator.translate('Are you sure you would like to remove this item from the shopping cart?').stripTags())){
						this.updateCartQty(qtyElementId,false);
					}
				} else {
					alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
				}
			} else if( qtyElementId.search("wishlist-qty-") != -1 ){				
				if (oldValue==1) {
					if (confirm(Translator.translate('Are you sure you would like to remove this item from the wishlist?').stripTags())){
						this.updateWishlistQty(qtyElementId,false);
					}
				} else {
					alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
				}
			} else {		
				alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
				qtyElement.val(productMinMax[productId]['min']);
			}	
		}
    },
	
	//DRAG & DROP functions
	//Apply drag and drop functionality to category list item
	makeItemDraggable: function(li, isSaleable) { 
		if (ajaxcart.dragdropCategory == 1){
			GlaceAjax_182(li).find('img').each(function() {
				var img = GlaceAjax_182(this);
				var src = img.attr('src');
	
	            if (src != '' && src != null && src.indexOf('media/catalog/product/')!=-1){
				    var imgContainer = img.parent();
				    
				    var width = img.width();
				    var height = img.height();
				    var spread = Math.round(width/2 + 1);
				    if (height>=width) {
				    	var spread = Math.round(height/2 + 1);
				    } 
				    
				    //add hover
				    imgContainer.css({'width':width,'height':height});
				    imgContainer.append('<div class="draggable-bkg"><div class="draggable-content"><div class="draggable-image"></div><div class="draggable-text">' + Translator.translate('Drag me').stripTags() + '</div></div></div>');
				    
				    var running = false;
				    //add product image hover box-shadow effects
				    imgContainer.hover(function(){
				    	if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 8){
					    	GlaceAjax_182(this, this).find('.draggable-bkg').css({'display' : 'block'});				    		
				    	} else {
				    		running = true;
							GlaceAjax_182(this, this).find('.draggable-bkg').css({'box-shadow': '0px 0px 0px '+spread+'px rgba('+ajaxcart.boxShadowColor+',0.8) inset'});
						}
				    }, function() {
				    	if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 8){
					    	GlaceAjax_182(this, this).find('.draggable-bkg').css({'display' : 'none'});				    		
				    	} else {
					    	running = false;
					    	var draggableBkg = GlaceAjax_182(this, this).find('.draggable-bkg');
					    	if ((draggableBkg.css('box-shadow')).indexOf(spread+'px')!=-1) {
					    		draggableBkg.css({'box-shadow': '0px 0px 0px '+spread+'px rgba('+ajaxcart.boxShadowColor+',0.8) inset'});
					    		setTimeout(function() {
					    			if (!running) {
					    				draggableBkg.addClass('no-transition');
					    				draggableBkg.css({'box-shadow': '0px 0px 0px -1px rgba('+ajaxcart.boxShadowColor+',0.8) inset'});	
					    				draggableBkg.css('box-shadow');				
					    				draggableBkg.removeClass('no-transition');
					    			}
					    		}, 300);					
					    	} else {					
					    		draggableBkg.css({'box-shadow': '0px 0px 0px -1px rgba('+ajaxcart.boxShadowColor+',0.8) inset'});					
					    	}
					    }
				    })	
				    
				    imgContainer.draggable({ 
				    	appendTo: "body", 
				    	helper: "clone", 
				    	revert: true,
				    	containment : "document",
				    	start: function(event, ui) {
				    		//activate tooltip
				    		if (ajaxcart.enableTooltip){	
				    			if (!isSaleable) {
				    				GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar +' span.tooltip-sidebar').remove();
				    			}
				    			
								if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 8){
				    				GlaceAjax_182('span.tooltip-sidebar').css({'display': 'block'});
				    				GlaceAjax_182('span.tooltip-sidebar').css({'top': '-35'});
				    			} else {
				    				GlaceAjax_182('span.tooltip-sidebar').animate({opacity: "show", top: "-35"}, "fast");
				    			}
				    		}
				    		
					    	GlaceAjax_182('.ui-draggable-dragging').css({'z-index':'9999'});
					    	
							if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 9){
					    		imgContainer.css({'display' : 'none'});
					    		
					    		imgContainer.find('.draggable-bkg').css({'display' : 'none'});
					    		GlaceAjax_182('.ui-draggable-dragging').find('.draggable-bkg').css({'display' : 'none'});
					    	} else {
					    		imgContainer.css({'opacity' : '0'});
					    		//add image clone hover effects
								GlaceAjax_182('.ui-draggable-dragging').css({'width':'auto','height':'auto'});
					    		GlaceAjax_182('.ui-draggable-dragging').addClass('mouse-over');
					    	}
					    	
					    	//ajaxcart.dragContainer is used in jquert-ui.js
				    		ajaxcart.dragContainer = imgContainer;			    		
				    	},
				    	stop: function(event, ui) {		
							if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion <= 9){	
								ajaxcart.dragContainer.fadeIn();
							} else {
							    imgContainer.css({'opacity' : '1'});
						    	imgContainer.css({'transform' : 'scale(1)'});						    	
						    }
					    	
					    	imgContainer.find('.draggable-bkg').removeAttr('style');
					    	img.removeAttr('style');
				    	}
				    })
				}
			})	
		}
	},	
	
	//Sets the cart sidebar as a droppable area	
	makeCartDroppableArea: function() { 
	    GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar).droppable({
	    	accept: function() { return true; },
	    	over: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['cart']){
	    			GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar + ' div').first().addClass('draggable-over');
	    		}
	    	},
	    	out: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['cart']){
	    			GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar + ' div').first().removeClass('draggable-over');
	    		}
	    	},
	    	drop: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['cart']){
		    		ajaxcart.itemDropped = true;
		    		ajaxcart.initAjaxcart(dragDropProducts[ui.draggable.attr('product')]['cart'], ui.draggable, false);		 
	    			GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar + ' div').first().removeClass('draggable-over');
	    		}
	    	}
	    })		
	},
	
	//Sets the compare sidebar as a droppable area
	makeCompareDroppableArea: function() { 
	    GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar).droppable({
	    	accept: function() { return true; },
	    	over: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['compare']){
	    			GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar + ' div').first().addClass('draggable-over');
	    		}
	    	},
	    	out: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['compare']){
		    		GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar + ' div').first().removeClass('draggable-over');
		    	}
	    	},
	    	drop: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['compare']){
	    			ajaxcart.itemDropped = true;
					ajaxcart.addToCompare(dragDropProducts[ui.draggable.attr('product')]['compare']);
					GlaceAjax_182('#'+ajaxcart.compareSidebar + ajaxcart.lastCompareSidebar + ' div').first().removeClass('draggable-over');
				}
	    	}
	    })
	},
	
	//Sets the wishlist sidebar as a droppable area
	makeWishlistDroppableArea: function() { 
	    GlaceAjax_182('#'+ajaxcart.wishlistSidebar + ajaxcart.lastWishlistSidebar).droppable({
	    	accept: function() { return true; },
	    	over: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['wishlist']){
	    			GlaceAjax_182('#'+ajaxcart.wishlistSidebar + ajaxcart.lastWishlistSidebar +' div').first().addClass('draggable-over');
	    		}
	    	},
	    	out: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['wishlist']){
		    		GlaceAjax_182('#'+ajaxcart.wishlistSidebar + ajaxcart.lastWishlistSidebar +' div').first().removeClass('draggable-over');
		    	}
	    	},
	    	drop: function( event, ui ) {
	    		if(ui.draggable.attr('product') && dragDropProducts[ui.draggable.attr('product')]['wishlist']){
		    		ajaxcart.itemDropped = true;
					ajaxcart.addToWishlist(dragDropProducts[ui.draggable.attr('product')]['wishlist']);
					GlaceAjax_182('#'+ajaxcart.wishlistSidebar + ajaxcart.lastWishlistSidebar +' div').first().removeClass('draggable-over');
				}
	    	}
	    })
	},
	
	//MAIN functions
	save: function(transport){
		if (transport){
			try{
				response = eval('(' + transport + ')');
			}
			catch (e) {
				response = {};
			}
		}
		
		if (response.error){
			this.hasError = true;
			ajaxcartTools.resetLoadWaiting('ajaxcart-loading');
			
			if ((typeof response.message) == 'string') {
				alert(response.message);
			} else {
				alert(response.message.join("\n"));
			}
			return false;
		}
		
		if (response.notice){
			if ((typeof response.message) == 'string') {
				alert(response.notice_message);
			} else {
				alert(response.notice_message.join("\n"));
			}
		}
		
		if (response.redirect) {
			location.href = response.redirect;
            return;
        }
        
        this.successPopupDelay = 300;
        
        //update ajaxcart blocks
		if (response.update_section) {
			this.updateSections(response);
		}	
		
		ajaxcartTools.resetLoadWaiting('ajaxcart-loading');
		
		//close options popup
		if (response.close_popup && GlaceAjax_182('#'+response.close_popup+'-popup-container').css("left")=='0px') {	
			var instant = (response.popup == "success") ? false : true;
			ajaxcartTools.hidePopup(response.close_popup, instant);
			this.successPopupDelay = 200;
		}
		
		if (response.popup) {
			if (response.popup == "success" && this.showNotification == 1) {
				setTimeout(function() {ajaxcartTools.showPopup(response.popup,response.is_action);}, ajaxcart.successPopupDelay);
			} else if (response.popup != "success") {
				setTimeout(function() {ajaxcartTools.showPopup(response.popup,response.is_action);},300);
			}			
		}
	},	
	
	failure: function(){
		location.href = this.failureUrl;
	},
	
	//CART functions	
	//add product to cart via ajax
	initAjaxcart: function(url,button,isPopup){
		//if popup or product page
		if (isPopup || (!url && $('product_addtocart_form'))){
			if (!url && $('product_addtocart_form')){
				var formElement = GlaceAjax_182('#product_addtocart_form');
				var formAction = String(formElement.attr("action"));
			    var productId = ajaxcartTools.getProductIdFromUrl(formAction,'product');
			} else {
				var productId = ajaxcartTools.getProductIdFromUrl(url,'product');
			}
			
			if ($('qty')) {
				var qtyElement = $('qty');									
				// Verify if the qty has an invalid entry
				var oldValue = parseFloat(qtyElement.value);
				if (isNaN(oldValue)){
				    qtyElement.value = productMinMax[productId]['min'];
				}			
				// Verify the minim qty added to the cart
				if ( qtyElement.value <productMinMax[productId]['min']) {
				    alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
				    qtyElement.value = productMinMax[productId]['min'];
				    return;
				}
				// Verify if the qty added to the cart is smaller than the maximum qty
				if (qtyElement.value>productMinMax[productId]['max']) {
				    alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
				    qtyElement.value = productMinMax[productId]['max'];
				    return;
				} 	
			}
			
			if($('is_grouped_qty')){
				var inputElements = document.getElementsByTagName('input');
				for ( var j = 0; j<inputElements.length; j++ ) {
					if (inputElements[j].name != null && inputElements[j].name != '') {
						var qtyName = inputElements[j].getAttribute("name").toString();
						var result = qtyName.search("super_group");
						var productId = qtyName.replace("super_group[","").replace("]","");
						
						if ( result != -1 ) {
							var qty = inputElements[j].value;
							// Verify if the qty has an invalid entry
							var oldValue = parseFloat(qty);
							if (isNaN(oldValue)){
								inputElements[j].value = productMinMax[productId]['min'];
							}	
							// Verify the minim qty added to the cart
							if ( oldValue <productMinMax[productId]['min']) {
								alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
								inputElements[j].value = productMinMax[productId]['min'];
								return;
							}
							// Verify if the qty added to the cart is smaller than the maximum qty
							if (oldValue>productMinMax[productId]['max']) {
								alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
								inputElements[j].value = productMinMax[productId]['max'];
								return;
							} 							
						}
					}
				}				
			}					
			
			if (!url && $('product_addtocart_form')){		
				formId = 'product_addtocart_form';								
				var addToCartUrl = this.initUrl;
			} else {
				formId = 'options_addtocart_form';		
				//if cart page, add param to load new cart page html as well	
				if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart')!=-1 && !$$('#' + formId + ' #is_cart_page')[0]) {
					$(formId).insert('<input type="hidden" value="1" id="is_cart_page" name="is_cart_page" />');
				}	
				
				//if compare popup add param to load the success popup
				if (window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup) && !$$('#'+formId+' #is_compare_popup')[0]) {
					$(formId).insert('<input type="hidden" value="1" id="is_compare_popup" name="is_compare_popup" />');
				}	
				var addToCartUrl = url;
			}
				
			var ajaxcartProductAddToCartForm = new VarienForm(formId);
			
			if (!$$('#' + formId + ' #redirect_url')[0]) {
				$(formId).insert('<input type="hidden" value="'+ window.location.href +'" id="redirect_url" name="redirect_url" />');
			}		
			
			if (ajaxcartProductAddToCartForm.validator.validate() && !this.ajaxCartRunning) {
				if (button && !disablePopupProductLoader) {
					this.addToCartButton = button;
				}
				this.ajaxCartRunning = true;
				ajaxcartTools.setLoadWaiting('ajaxcart-loading',true);
				
				//disable all buttons, inputs, links etc.
				GlaceAjax_182('#ac-popup-top-bkg').show();

				GlaceAjax_182('#'+formId).ajaxSubmit({ 
			        url:  addToCartUrl,      
			        type: 'post',
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		} else {
			var productId = ajaxcartTools.getProductIdFromUrl(url,'product');
			if ($('qty-' + productId)) {
				qtyElement = GlaceAjax_182(button).closest(".ac-product-list").find('#qty-' + productId);
			
				var qty = qtyElement.val();
			
				// Verify if the qty has an invalid entry
				var oldValue = parseFloat($('qty-'+productId).value);
				if (isNaN(oldValue)){
					qtyElement.val(productMinMax[productId]['min']);
				}
				// Verify the minim qty added to the cart
				if ( qty < productMinMax[productId]['min']) {
					alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
					qtyElement.val(productMinMax[productId]['min']);
					return;
				}		
				// Verify if the qty added to the cart is smaller than the maximum qty
				if (qty>productMinMax[productId]['max']) {
					alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
					qtyElement.val(productMinMax[productId]['max']);
					return;
				} 
			} else {
				var qty = productMinMax[productId]['min'];				
			}
			
			if (!this.ajaxCartRunning) {
				var formData = {};
				formData.qty = qty;
				formData.redirect_url = window.location.href;
				
				//if cart page, add param to load new cart page html as well	
				if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart')!=-1) {
					formData.is_cart_page = true;
				}
				
				//if compare popup add param to load the success popup
				if (window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup)) {
					formData.is_compare_popup = true;
				}

				if (!ajaxcart.itemDropped) {
					var productImageContainer = GlaceAjax_182('div[product="'+productId+'"]');
					productImageContainer.removeAttr('style');
					var clone = productImageContainer.clone().appendTo(productImageContainer.parent());
					var cartSidebar = GlaceAjax_182('#'+ajaxcart.cartSidebar + ajaxcart.lastCartSidebar);

					//hide original product image
	 				productImageContainer.css({'display' : 'none', 'opacity' : '0', 'transform' : 'scale(0)'});
					productImageContainer.find('img').css({'border-radius' : '50%'});

					setTimeout(function() { clone.addClass('over'); },10);

					setTimeout(function() {						    	
						var productImageOffset = clone.offset();
						var cartSidebarOffset = cartSidebar.offset();
						if(cartSidebarOffset)
						{
						    var cartX = cartSidebarOffset.left - productImageOffset.left;
						    var cartY = cartSidebarOffset.top + cartSidebar.height()/2 - 50 - productImageOffset.top;
	
						    ajaxcart.dragContainer = clone;			    
	
						    clone.animate(
								{crSpline: GlaceAjax_182.crSpline.buildSequence([[0, 0], [cartX/4, cartY-150], [cartX/4*2, cartY-250], [cartX/4*3, cartY-150], [cartX, cartY]])}, 
								800,
								function() {
									clone.remove();
									//show original product image
									productImageContainer.show();
									productImageContainer.removeAttr('style');
									productImageContainer.find('img').removeAttr('style');
								}
							);
						}
					}, 300);
				}	

				this.ajaxCartRunning = true;
				ajaxcartTools.setLoadWaiting('ajaxcart-loading',true);		
				
				GlaceAjax_182(this).ajaxSubmit({ 
			        url:  url,      
			        type: 'post',
			        data: formData,
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		}
	},	
	
	//update cart sidebar block
	updateCart: function(response){
		//console.log(response);
		//console.log(ajaxcart.cartSidebar+i);
		if (response.update_section.html_cart) {
			for (var i=0;i<response.update_section.html_cart.length;i++) {
				if (response.update_section.html_cart[i] && this.updateWindow.document.getElementById(ajaxcart.cartSidebar+i)) {
		    		//add empty/update cart actions to sidebar
			    	var content = response.update_section.html_cart[i];  			     
			    					 			
		    		if (content.indexOf("ajaxcart-qty-input") != -1) { 
			    	    var actionsContainer = '<div class="actions" id="ajaxcart-actions" style="border-bottom:none;">';
					    actionsContainer += '<button onclick="ajaxcart.emptyCartConfirmation();" class="button left" title="' + Translator.translate('Empty Cart').stripTags() + '" type="button"><span><span>' + Translator.translate('Empty Cart').stripTags() + '</span></span></button>';
					    if (ajaxcart.sidebarQty == 1){
					    	actionsContainer += '<button onclick="ajaxcart.updateQty(\'\',false,\'' + ajaxcart.cartSidebar + i + '\')" class="button" title="' + Translator.translate('Update Cart').stripTags() + '" type="button"><span><span>' + Translator.translate('Update Cart').stripTags() + '</span></span></button>';
					    }		
					    actionsContainer += '</div>';
					    // nguyen tien thanh
					    var js_cart=content.extractScripts();
					    var js_remove='<script stype="text/javascript">'+js_cart[0]+'</' + 'script>';
					    //console.log(test);
				    	var wrapped = GlaceAjax_182('<div>'+content+'</div>');
						wrapped.find('div').first().append(actionsContainer);
						//console.log('<script stype="text/javascript">'+js_cart[0]+'</' + 'script>');
						//wrapped.find('.minicart-wrapper').first().append('<script stype="text/javascript">'+js_cart[0]+'</' + 'script>');
						content = wrapped.html();
						//content.find('.minicart-wrapper').first().append('<script stype="text/javascript">'+js_cart[0]+'</' + 'script>');
						// add javascript
						//GlaceAjax_182('#'+ajaxcart.cartSidebar+i+'.minicart-wrapper').append();
			    	}  		
					
					//flash cart sidebar
					var cartSidebar = this.updateWindow.document.getElementById(ajaxcart.cartSidebar+i);	
			    	if (window.opener != null && window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup)) {
					    GlaceAjax_182(cartSidebar).hide().delay(15).fadeIn(300);
					} else {
					    GlaceAjax_182(cartSidebar).hide().fadeIn(300);					
					}
			    	
			    	//console.log(content);
					ajaxcartTools.updateSection(content,(ajaxcart.cartSidebar+i),js_remove);	
			    }
		    }
	    }
		// Update count product
		if (response.update_section.html_cart_count) {
			if(GlaceAjax_182('.skip-cart'))
				GlaceAjax_182('.skip-cart').html(response.update_section.html_cart_count);
		}
		//This is for adding custom scrollbar
		//jQuery('.quick-cart-container .scrollable').mCustomScrollbar();
		
		
	    this.updateCartLink(response);
	},

	//update cart link
	updateCartLink: function(response){
		//console.log(response);
		if (response.update_section.html_cart_link) {
			if (!cartLink) {
	    		GlaceAjax_182('#ac-links a', this.updateWindow.document).each(function() {
					var href = GlaceAjax_182(this).attr('href');
					if (href != null && href != '' && href.indexOf('/checkout/cart/') != -1) {
						GlaceAjax_182(this).html(response.update_section.html_cart_link);
					}    			
				});
			} else {
				GlaceAjax_182(cartLink).html(response.update_section.html_cart_link);
			}
    	}
	},	

	//remove cart item
	removeCartItem: function(itemId){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			if($(ajaxcart.cartPage)) {
				formData[GlaceAjax_182('#cart-qty-'+itemId).attr('name')] = 0;
			} else {
				formData[GlaceAjax_182('#sidebar-qty-'+itemId).attr('name')] = 0;
			}
			
			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);			
			
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  this.updateCartUrl,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 		
		} 	
    },
	
	//empty cart
	emptyCart: function(url){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart/')!=-1) {
				formData.is_cart_page = true;
			}
		
			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
												 
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
				data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },

	emptyCartConfirmation: function(){
		if(confirm(Translator.translate("Are you sure you would like to remove all the items from the shopping cart?").stripTags())){
			ajaxcart.emptyCart(this.clearCartUrl);
		}	
	},	
	
	//update cart qty
	updateQty: function(qtyElementId,isIncrease,button){ 
		if (!this.ajaxCartRunning) {
			if (typeof button == 'string') {
				var formId = button;
			} else {
				var formId = GlaceAjax_182(button).closest(".ac-cart-sidebar").attr('id');
			}
			
			this.ajaxCartRunning = true;
			
			if(qtyElementId != ''){
				var itemId = 'item'+qtyElementId.replace('sidebar-qty-', '');
				if (isIncrease){
					$(qtyElementId).value = parseFloat($(qtyElementId).value) + productMinMax[itemId]['inc'];
				} else {
					$(qtyElementId).value = parseFloat($(qtyElementId).value) - productMinMax[itemId]['inc'];
				}
			}
			
			var formData = {};
			formData.redirect_url = window.location.href;
			GlaceAjax_182('#'+formId+' :input').each(function() {
				if (qtyElementId!='' && GlaceAjax_182(this).attr('name') && GlaceAjax_182(this).attr('name')==GlaceAjax_182('#'+qtyElementId).attr('name')) {
					formData[GlaceAjax_182(this).attr('name')] = GlaceAjax_182(this).val();	
				} else if (qtyElementId=='' && GlaceAjax_182(this).attr('name')) {
					formData[GlaceAjax_182(this).attr('name')] = GlaceAjax_182(this).val();						
				}
			}); 

			if (qtyElementId != '' && !isIncrease && $(qtyElementId).value == 0){
				$(qtyElementId).value = 1;
			}
			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);	
												 
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  this.updateCartUrl,      
			    type: 'post',
				data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },
	
	//SHOPPING CART PAGE functions 		
	//update cart page blocks
	updateCartPage: function(response){
		if (response.update_section.html_cart_page && this.updateWindow.document.getElementById(ajaxcart.cartPage)) {
    		ajaxcartTools.updateSection(response.update_section.html_cart_page,ajaxcart.cartPage);	
	    }
	},
	
	//update cart page qty
	updateCartQty: function(qtyElementId,isIncrease){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			if(qtyElementId != ''){
				var itemId = 'item'+qtyElementId.replace('cart-qty-', '');
				if (isIncrease){
					$(qtyElementId).value = parseFloat($(qtyElementId).value) + productMinMax[itemId]['inc'];
				} else {
					$(qtyElementId).value = parseFloat($(qtyElementId).value) - productMinMax[itemId]['inc'];
				}
			}

			var formElements = document.getElementsByTagName('form');
			for ( var i = 0; i<formElements.length; i++ ) {
				if (formElements[i].action != null && formElements[i].action != '') {
					var formUrl = formElements[i].getAttribute("action").toString();
					if ( formUrl.search("checkout/cart/updatePost/") != -1 ) {
						var form = formElements[i];
						break;
					} 	
				} 	
			} 			
			
			var formData = {};
			formData.redirect_url = window.location.href;
			GlaceAjax_182(form).find('input').each(function() {
				if (qtyElementId!='' && GlaceAjax_182(this).attr('name') && GlaceAjax_182(this).attr('name')==GlaceAjax_182('#'+qtyElementId).attr('name')) {
					formData[GlaceAjax_182(this).attr('name')] = GlaceAjax_182(this).val();	
				} else if (qtyElementId=='' && GlaceAjax_182(this).attr('name') && (GlaceAjax_182(this).attr('type') != 'checkbox' || (GlaceAjax_182(this).attr('type') == 'checkbox' && GlaceAjax_182(this).is(':checked')))) {
					formData[GlaceAjax_182(this).attr('name')] = GlaceAjax_182(this).val();						
				}
			});  
			
			if (qtyElementId != '' && !isIncrease && $(qtyElementId).value == 0){
				$(qtyElementId).value = 1;
			}
			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
			
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  this.updateCartUrl,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },	
    
    //edit cart item from shopping cart page
    configureCartItem: function(url) {
	    if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;

			var formData = {};
			formData.redirect_url = window.location.href;

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
	
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },
	
	//update cart item; used in edit cart item popup on shopping cart page
	updateCartItem: function(url, button){ 
		if (!this.ajaxCartRunning) {	
			var formId = 'options_addtocart_form';	
			productId = this.itemProductId;	
			
			if ($('popup-qty')) {
			    var qtyElement = $('popup-qty');									
			    // Verify if the qty has an invalid entry
			    var oldValue = parseFloat(qtyElement.value);
			    if (isNaN(oldValue)){
			        qtyElement.value = productMinMax[productId]['min'];
			    }			
			    // Verify the minim qty added to the cart
			    if ( qtyElement.value <productMinMax[productId]['min']) {
			        alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
			        qtyElement.value = productMinMax[productId]['min'];
			        return;
			    }
			    // Verify if the qty added to the cart is smaller than the maximum qty
			    if (qtyElement.value>productMinMax[productId]['max']) {
			        alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
			        qtyElement.value = productMinMax[productId]['max'];
			        return;
			    } 	
			}
			
			if($('is_grouped_qty')){
			    var inputElements = document.getElementsByTagName('input');
			    for ( var j = 0; j<inputElements.length; j++ ) {
			    	if (inputElements[j].name != null && inputElements[j].name != '') {
			    		var qtyName = inputElements[j].getAttribute("name").toString();
			    		var result = qtyName.search("super_group");
			    		var productId = qtyName.replace("super_group[","").replace("]","");
			    		
			    		if ( result != -1 ) {
			    			var qty = inputElements[j].value;
			    			// Verify if the qty has an invalid entry
			    			var oldValue = parseFloat(qty);
			    			if (isNaN(oldValue)){
			    				inputElements[j].value = productMinMax[productId]['min'];
			    			}	
			    			// Verify the minim qty added to the cart
			    			if ( oldValue <productMinMax[productId]['min']) {
			    				alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
			    				inputElements[j].value = productMinMax[productId]['min'];
			    				return;
			    			}
			    			// Verify if the qty added to the cart is smaller than the maximum qty
			    			if (oldValue>productMinMax[productId]['max']) {
			    				alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
			    				inputElements[j].value = productMinMax[productId]['max'];
			    				return;
			    			} 							
			    		}
			    	}
			    }				
			}				
				
			//if cart page, add param to load new cart page html as well	
			if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart')!=-1 && !$$('#'+formId+' #is_cart_page')[0]) {
				$(formId).insert('<input type="hidden" value="1" id="is_cart_page" name="is_cart_page" />');
			}		
				
			if (!$$('#'+formId+' #redirect_url')[0]) {
				$(formId).insert('<input type="hidden" value="'+ window.location.href +'" id="redirect_url" name="redirect_url" />');
			}
			
			var ajaxcartProductAddToCartForm = new VarienForm(formId);
			
			if (ajaxcartProductAddToCartForm.validator.validate()) {
				this.ajaxCartRunning = true;
				if (button) {
					this.addToCartButton = button;
				}
				ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
				
				//disable all buttons, inputs, links etc.
				GlaceAjax_182('#ac-popup-top-bkg').show();

				GlaceAjax_182('#'+formId).ajaxSubmit({ 
			        url:  url,      
			        type: 'post',
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		}
    }, 

	//update configurable product options from shopping cart page
	updateItemOptions: function(selectElement){ 
		if (!this.ajaxCartRunning && selectElement.value != '') {
			this.ajaxCartRunning = true;
			
			var formElements = document.getElementsByTagName('form');
			for ( var i = 0; i<formElements.length; i++ ) {
				if (formElements[i].action != null && formElements[i].action != '') {
					var formUrl = formElements[i].getAttribute("action").toString();
					if ( formUrl.search("checkout/cart/updatePost/") != -1 ) {
						var form = formElements[i];
						break;
					} 	
				} 	
			} 	
						
			var formData = {};
			formData.redirect_url = window.location.href;
			GlaceAjax_182(form).find('select').each(function() {
				formData[GlaceAjax_182(this).attr('name')] = GlaceAjax_182(this).val();
			}); 
			
			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
	
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  this.updateCartUrl,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		} 	
    },  
    
    //move cart item to wishlist
    moveToWishlist: function(url){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.is_cart_page = true;

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
			
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    }, 
	
	//WISHLIST functions	
	//add product to wishlist via ajax
	addToWishlist: function(url){
		if ($('product_addtocart_form')){			
			if ($('qty')){
				var formAction = String(GlaceAjax_182('#product_addtocart_form').attr("action"));
			    var productId = ajaxcartTools.getProductIdFromUrl(formAction,'product');
			    
				var qty = $('qty').value;
				// Verify if the qty has an invalid entry
				var oldValue = parseFloat($('qty').value);
				if (isNaN(oldValue)){
					$('qty').value = productMinMax[productId]['min'];
				}			
				// Verify the minim qty added to the cart
				if ( qty <productMinMax[productId]['min']) {
					$('qty').value = productMinMax[productId]['min'];
				}
			}	
			
			if($('is_grouped_qty')){
				var inputElements = document.getElementsByTagName('input');
				for ( var j = 0; j<inputElements.length; j++ ) {
					if (inputElements[j].name != null && inputElements[j].name != '') {
						var qtyName = inputElements[j].getAttribute("name").toString();
						var result = qtyName.search("super_group");
						var productId = qtyName.replace("super_group[","").replace("]","");
						
						if ( result != -1 ) {
							var qty = inputElements[j].value;
							// Verify if the qty has an invalid entry
							var oldValue = parseFloat(qty);
							if (isNaN(oldValue)){
								inputElements[j].value = productMinMax[productId]['min'];
							}	
							// Verify the minim qty added to the cart
							if ( oldValue <productMinMax[productId]['min']) {
								inputElements[j].value = productMinMax[productId]['min'];
							}
						}
					}
				}				
			}				
			
			var ajaxcartProductAddToCartForm = new VarienForm('product_addtocart_form');
			if (!this.ajaxCartRunning) {
				this.ajaxCartRunning = true;

				var redirectUrl = window.location.href;
				if (!$$('#product_addtocart_form #redirect_url')[0]) {
					$('product_addtocart_form').insert('<input type="hidden" value="'+ redirectUrl +'" id="redirect_url" name="redirect_url" />');				
				}
				
				if (!ajaxcartLogin.loginPostResponse) {
					ajaxcartTools.setLoadWaiting('ajaxcart-loading',true);
				}
				
				GlaceAjax_182('#product_addtocart_form').ajaxSubmit({ 
			        url:  url,      
			        type: 'post',
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		} else {
			var productId = ajaxcartTools.getProductIdFromUrl(url,'product');
			if ($('qty-' + productId)) {
				var qty = $('qty-' + productId).value;
				// Verify if the qty has an invalid entry
				var oldValue = parseFloat($('qty-'+productId).value);
				if (isNaN(oldValue)){
					$('qty-'+productId).value = productMinMax[productId]['min'];
				}
				// Verify the minim qty added to the cart
				if ( qty <productMinMax[productId]['min']) {
					$('qty-' + productId).value = productMinMax[productId]['min'];
				}
			} else {
				var qty = 1;
			}
			
			if (!this.ajaxCartRunning) {	
				var formData = {};
				formData.redirect_url = window.location.href;	
				formData.qty = qty;		
			
				//if wishlist page, add param to load new wishlist page html as well	
				if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
					formData.is_wishlist_page = true;	
				}
					
				this.ajaxCartRunning = true;
				if (!ajaxcartLogin.loginPostResponse) {
					ajaxcartTools.setLoadWaiting('ajaxcart-loading',true);
				}
				
				GlaceAjax_182(this).ajaxSubmit({ 
			        url:  url,      
			        type: 'post',
				    data: formData,
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		}
	},	
	
	//add wishlist item to cart
	addWishlistItemToCart: function(url, button){ 
		if (!this.ajaxCartRunning) {			
			if (button) {
				var formId = 'options_addtocart_form';	
				productId = this.itemProductId;	
				
				if ($('popup-qty')) {
					var qtyElement = $('popup-qty');									
					// Verify if the qty has an invalid entry
					var oldValue = parseFloat(qtyElement.value);
					if (isNaN(oldValue)){
					    qtyElement.value = productMinMax[productId]['min'];
					}			
					// Verify the minim qty added to the cart
					if ( qtyElement.value <productMinMax[productId]['min']) {
					    alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
					    qtyElement.value = productMinMax[productId]['min'];
					    return;
					}
					// Verify if the qty added to the cart is smaller than the maximum qty
					if (qtyElement.value>productMinMax[productId]['max']) {
					    alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
					    qtyElement.value = productMinMax[productId]['max'];
					    return;
					} 	
				}
				
				if($('is_grouped_qty')){
					var inputElements = document.getElementsByTagName('input');
					for ( var j = 0; j<inputElements.length; j++ ) {
						if (inputElements[j].name != null && inputElements[j].name != '') {
							var qtyName = inputElements[j].getAttribute("name").toString();
							var result = qtyName.search("super_group");
							var productId = qtyName.replace("super_group[","").replace("]","");
							
							if ( result != -1 ) {
								var qty = inputElements[j].value;
								// Verify if the qty has an invalid entry
								var oldValue = parseFloat(qty);
								if (isNaN(oldValue)){
									inputElements[j].value = productMinMax[productId]['min'];
								}	
								// Verify the minim qty added to the cart
								if ( oldValue <productMinMax[productId]['min']) {
									alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['min'] + '.');
									inputElements[j].value = productMinMax[productId]['min'];
									return;
								}
								// Verify if the qty added to the cart is smaller than the maximum qty
								if (oldValue>productMinMax[productId]['max']) {
									alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax[productId]['max'] + '.');
									inputElements[j].value = productMinMax[productId]['max'];
									return;
								} 							
							}
						}
					}				
				}			
			} else {
				if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
					var formId = 'wishlist-view-form';
					
					//validate qty
					var itemId = ajaxcartTools.getProductIdFromUrl(url,'item');
					if ($('wishlist-qty-' + itemId)) {
						var qty = $('wishlist-qty-' + itemId).value;
					
						// Verify if the qty has an invalid entry
						var oldValue = parseFloat($('wishlist-qty-'+itemId).value);
						if (isNaN(oldValue)){
							$('wishlist-qty-'+itemId).value = productMinMax['witem'+itemId]['min'];
						}
						// Verify the minim qty added to the cart
						if ( qty < productMinMax['witem'+itemId]['min']) {
							alert(Translator.translate('The minimum quantity allowed for purchase is ').stripTags() + productMinMax['witem'+itemId]['min'] + '.');
							$('wishlist-qty-' + itemId).value = productMinMax['witem'+itemId]['min'];
							return;
						}		
						// Verify if the qty added to the cart is smaller than the maximum qty
						if (qty>productMinMax['witem'+itemId]['max']) {
							alert(Translator.translate('The maximum quantity allowed for purchase is ').stripTags() + productMinMax['witem'+itemId]['max'] + '.');
							$('wishlist-qty-' + itemId).value = productMinMax['witem'+itemId]['max'];
							return;
						} 
					}
				} else {
					var formId = 'options_addtocart_form';				
				}
			}
				
			//if cart page, add param to load new cart page html as well	
			if (ajaxcart.updateWindow.location.href.indexOf('checkout/cart')!=-1 && !$$('#'+formId+' #is_cart_page')[0]) {
				$(formId).insert('<input type="hidden" value="1" id="is_cart_page" name="is_cart_page" />');
			}			
			
			//if wishlist page, add param to load new wishlist page html as well	
			if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0 && !$$('#'+formId+' #is_wishlist_page')[0]) {
				$(formId).insert('<input type="hidden" value="1" id="is_wishlist_page" name="is_wishlist_page" />');
			}
				
			if (!$$('#'+formId+' #redirect_url')[0]) {
				$(formId).insert('<input type="hidden" value="'+ window.location.href +'" id="redirect_url" name="redirect_url" />');
			}
			var ajaxcartProductAddToCartForm = new VarienForm(formId);
			
			if (ajaxcartProductAddToCartForm.validator.validate()) {
				this.ajaxCartRunning = true;
				if (button) {
					this.addToCartButton = button;
				}
				ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);

				//disable all buttons, inputs, links etc.
				GlaceAjax_182('#ac-popup-top-bkg').show();

				GlaceAjax_182('#'+formId).ajaxSubmit({ 
			        url:  url,      
			        type: 'post',
			        dataType: 'text',
			        success: function(response) {
				        ajaxcart.save(response);
				    },		
			        error: function() {
				        ajaxcart.failure();
				    }
			    }); 
			}
		}
    }, 
	
	initAddWishlistItemToCart: function(itemId) {
	   var url = this.generateWishlistItemUrl(itemId).replace("wishlist/index/cart/","ajaxcart/wishlist/cart/");
	   
	   this.addWishlistItemToCart(url, false);
    },
	
	//generate add wishlist item to cart url; used on wishlist page
	generateWishlistItemUrl: function(itemId) {
       var url = this.addWishlistItemToCartUrl;
       url = url.gsub('%item%', itemId);
       var form = $('wishlist-view-form');
       if (form) {
           var input = form['qty[' + itemId + ']'];
           if (input) {
               var separator = (url.indexOf('?') >= 0) ? '&' : '?';
               url += separator + input.name + '=' + encodeURIComponent(input.value);
           }
       }
       
       return url;
    },
    
    addAllWishlistItemsToCart: function() {
	   if (!this.ajaxCartRunning) {
	   		var url = this.generateAddAllItemsToCartUrl();
	   
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.redirect_url = window.location.href;

			//if wishlist page, add param to load new wishlist page html as well	
			if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
				formData.is_wishlist_page = true;
			}

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
			
			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },
	
	//generate add all wishlist items to cart url; used on wishlist page
	generateAddAllItemsToCartUrl: function() {  
  	   var url = this.addAllItemsToCartUrl;       
       var form = $('wishlist-view-form');
       if (form) {     
           var separator = (url.indexOf('?') >= 0) ? '&' : '?';
           $$('#wishlist-view-form .qty').each(
               function (input, index) {
                   url += separator + input.name + '=' + encodeURIComponent(input.value);
                   separator = '&';
               }
           );
       }      
       
       return url;
    },
    
    configureWishlistItem: function(url) {
	    if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.redirect_url = window.location.href;

			//if wishlist page, add param to load new wishlist page html as well	
			if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
				formData.is_wishlist_page = true;
			}

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);

			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,    
			    data: formData,  
			    type: 'post',
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },
    
    updateWishlistItem: function(url, button) {
    	this.addWishlistItemToCart(url, button);
    },
    
    updateWishlistItems: function() {
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formId = 'wishlist-view-form';
			var formAction = String(GlaceAjax_182('#'+formId).attr("action"));
			var url = formAction.replace("wishlist/index/update/","ajaxcart/wishlist/update/");		
			
			//if wishlist page, add param to load new wishlist page html as well	
			if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
				$(formId).insert('<input type="hidden" value="1" id="is_wishlist_page" name="is_wishlist_page" />');
			}
				
			if (!$$('#'+formId+' #redirect_url')[0]) {
				$(formId).insert('<input type="hidden" value="'+ window.location.href +'" id="redirect_url" name="redirect_url" />');
			}

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
			
			GlaceAjax_182('#'+formId).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    },
    
	//update wishlist page qty with increase/decrease buttons
	updateWishlistQty: function(qtyElementId,isIncrease){ 			
		if(qtyElementId != ''){
			var itemId = 'witem'+qtyElementId.replace('wishlist-qty-', '');
		    if (isIncrease){
		    	$(qtyElementId).value = parseFloat($(qtyElementId).value) + productMinMax[itemId]['inc'];
		    } else {
		    	$(qtyElementId).value = parseFloat($(qtyElementId).value) - productMinMax[itemId]['inc'];
		    }
		}	
		
		this.updateWishlistItems();
		
		if (qtyElementId != '' && !isIncrease && $(qtyElementId).value == 0){
		    $(qtyElementId).value = 1;
		}
    },	
	
	//remove wishlist item
	removeWishlistItem: function(url){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.redirect_url = window.location.href;			

			//if wishlist page, add param to load new wishlist page html as well	
			if (GlaceAjax_182(('#' + ajaxcart.wishlistPage), ajaxcart.updateWindow.document).length > 0) {
				formData.is_wishlist_page = true;
			}

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);

			GlaceAjax_182(this).ajaxSubmit({ 
			    url:  url,      
			    type: 'post',
			    data: formData,
			    dataType: 'text',
			    success: function(response) {
			        ajaxcart.save(response);
			    },		
			    error: function() {
			        ajaxcart.failure();
			    }
			}); 
		}
    }, 
	
	//update wishlist sidebar block
	updateWishlist: function(response){	
		if (response.update_section.html_wishlist) {
			for (var i=0;i<response.update_section.html_wishlist.length;i++) {	
				if (response.update_section.html_wishlist[i]) {
		    		if (this.updateWindow.document.getElementById(ajaxcart.wishlistSidebar + i)) {	
						ajaxcartTools.updateSection(response.update_section.html_wishlist[i],(ajaxcart.wishlistSidebar + i));					
			    	} else if(GlaceAjax_182('.sidebar', this.updateWindow.document).length > 0) {
						var sidebar = GlaceAjax_182('.sidebar', this.updateWindow.document);	
						sidebar.first().append(response.update_section.html_wishlist[i]);
					}
					
					/*
		else if(this.updateWindow.document.getElementById(ajaxcart.cartSidebar)) {
						var cart = GlaceAjax_182('#'+ajaxcart.cartSidebar, this.updateWindow.document);
			    	    cart.after(response.update_section.html_wishlist);
					}
		*/
					
					//flash wishlist block
					var wishlistSidebar = this.updateWindow.document.getElementById(ajaxcart.wishlistSidebar + i);
			    	if (window.opener != null && window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup)) {
					    GlaceAjax_182(wishlistSidebar).hide().delay(15).fadeIn(300);
					} else {
					    GlaceAjax_182(wishlistSidebar).hide().fadeIn(300);					
					}
			    }
			}
		}
	    this.updateWishlistLink(response);	    
	},	
	
	//update wishlist page blocks
	updateWishlistPage: function(response){
		if (response.update_section.html_wishlist_page && this.updateWindow.document.getElementById(ajaxcart.wishlistPage)) {
    		ajaxcartTools.updateSection(response.update_section.html_wishlist_page, ajaxcart.wishlistPage);	
	    }
	},
	
	//update wishlist link
	updateWishlistLink: function(response){
    	if (response.update_section.html_wishlist_link) {
    		if (!wishlistLink) {
	    		GlaceAjax_182('#ac-links a', this.updateWindow.document).each(function() {
					var href = GlaceAjax_182(this).attr('href');
					if (href != null && href != '' && href.indexOf('/wishlist/') != -1) {
						GlaceAjax_182(this).html(response.update_section.html_wishlist_link);
					}    			
				}); 
			} else {
				GlaceAjax_182(wishlistLink).html(response.update_section.html_wishlist_link);
			}
    	}
	},		
	
	//COMPARE functions
	addToCompare: function(url){
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;

			var formData = {};
			formData.redirect_url = window.location.href;

			ajaxcartTools.setLoadWaiting('ajaxcart-loading',true);
			
			GlaceAjax_182(this).ajaxSubmit({ 
		        url:  url,      
		        type: 'post',
			    data: formData,
		        dataType: 'text',
		        success: function(response) {
			        ajaxcart.save(response);
			    },		
		        error: function() {
			        ajaxcart.failure();
			    }
		    }); 
		}
	},

	//update compare sidebar block
	updateCompare: function(response){
		if (response.update_section.html_compare) {
			for (var i=0;i<response.update_section.html_compare.length;i++) {
				if (response.update_section.html_compare[i] && this.updateWindow.document.getElementById(ajaxcart.compareSidebar+i)) {
		    		ajaxcartTools.updateSection(response.update_section.html_compare[i],(ajaxcart.compareSidebar+i));
					
					//flash compare block
					var compareSidebar = this.updateWindow.document.getElementById(ajaxcart.compareSidebar+i);
			    	if (window.opener != null && window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup)) {
					    GlaceAjax_182(compareSidebar).hide();
					    setTimeout(function() {GlaceAjax_182(compareSidebar).fadeIn(300);}, 30);
					} else {
					    GlaceAjax_182(compareSidebar).hide().fadeIn(300);					
					}		
			    }
		    }
		}
	    
	    //update compare button from success popup
		if (response.update_section.compare_onclick) {
			$('success-compare-button').setAttribute("onclick",response.update_section.compare_onclick);
			
		    //ie7 fix
			if (ajaxcartTools.browserTypeIE && ajaxcartTools.browserVersion == '7.0'){
		    	var buttonHtml = $('success-compare-button').outerHTML;
				$('success-compare-button').outerHTML = buttonHtml;
			}
		}
	},	

	//update compare popup block
	updateComparePopup: function(response){
		if (response.update_section.html_compare_popup && $(ajaxcart.comparePopup)) {
    		ajaxcartTools.updateSection(response.update_section.html_compare_popup,ajaxcart.comparePopup);

			//flash compare popup block
			GlaceAjax_182('#'+ajaxcart.comparePopup).hide().fadeIn(300);	
	    }
	},
	
	//remove compare sidebar item
	removeCompareItem: function(url){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.redirect_url = window.location.href;
			
			//if compare popup add param to load new compare popup html as well
			if (window.location.href.indexOf('catalog/product_compare/index/')!=-1 && $(ajaxcart.comparePopup)) {
				formData.is_compare_popup = true;
			}

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
	
			GlaceAjax_182(this).ajaxSubmit({ 
		        url:  url,      
		        type: 'post',
			    data: formData,
		        dataType: 'text',
		        success: function(response) {
			        ajaxcart.save(response);
			    },		
		        error: function() {
			        ajaxcart.failure();
			    }
		    }); 
		}	
    },	
	
	//remove all compare sidebar items
	removeCompareItems: function(url){ 
		if (!this.ajaxCartRunning) {
			this.ajaxCartRunning = true;
			
			var formData = {};
			formData.redirect_url = window.location.href;

			ajaxcartTools.setLoadWaiting('ajaxcart-loading', true);
			
			GlaceAjax_182(this).ajaxSubmit({ 
		        url:  url,      
		        type: 'post',
			    data: formData,
		        dataType: 'text',
		        success: function(response) {
			        ajaxcart.save(response);
			    },		
		        error: function() {
			        ajaxcart.failure();
			    }
		    }); 
		}
    }		
}
