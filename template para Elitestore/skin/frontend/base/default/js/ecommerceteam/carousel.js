/**
 * Products Carousel - Magento Extension
 *
 * @package:     ProductsCarousel
 * @category:    EcommerceTeam
 * @copyright:   Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:     1.0.0
 */

jQuery(function($){
    if (typeof carousel_config == 'undefined') {
        return;
    }
    var ecommerceteam_carousel = {
        itemWidth:null,
        position:0,
        per_page:4,
        active_item:2,
        items:[],
        itemsCache:[],
        itemsHtml:[],
        url:null,
        selector:'',
        proccess:false,
        init:function(selector)
        {
            var ecommerceteam_carousel = this;
            this.itemWidth    = $(selector+' ul li').get(0).offsetWidth;
			//estos son los items alert(carousel_config.items);
            this.items    = carousel_config.items;
            this.per_page    = carousel_config.per_page;
            this.url        = carousel_config.url;
            this.active_item = carousel_config.active;
            this.selector    = selector;
            this.itemsHtml    = carousel_config.itemsHtml;
            this.itemsPreloaded    = carousel_config.itemsPreloaded;
            this.mousedown = false;
            if(this.items.length > this.per_page){
                
                $(selector+' ul li').each(function(i){
                    ecommerceteam_carousel.itemsCache[i] = $(this).clone(true);
                });
                //modifica el margen derecho e izquierdo del ul el 359 de abajo era 273
                $(selector+' ul').prepend('<li>&nbsp;</li>').css({marginLeft:'-'+(this.itemWidth+359)+'px',marginRight:'-'+this.itemWidth+'px'});
                
                $(selector+' .next-btn, '+selector+' .prev-btn').mousedown(function(){
                    ecommerceteam_carousel.mousedown = true;
                    if(ecommerceteam_carousel.proccess){
                        return false;
                    }
                    
                    ecommerceteam_carousel.proccess = true;
                    
                    if($(this).hasClass('prev-btn')){
                        ecommerceteam_carousel.prev();
                    }else{
                        ecommerceteam_carousel.next();
                    }
                    
                    return false;
                }).mouseup(function(){
                    ecommerceteam_carousel.mousedown = false;
                }).mouseout(function(){
                    ecommerceteam_carousel.mousedown = false;
                });
                
            }
            return this;
        },
        next:function()
        {
            if(this.position > this.items.length - 1){
                this.position = 0;
            }
            
            var next_item = this.position+this.per_page;
            
            if(next_item >= this.items.length){
                next_item = (this.position+this.per_page)-this.items.length;
            }
            
            
            if(this.itemsPreloaded.length && this.itemsPreloaded[this.items[next_item]]){
                
                ecommerceteam_carousel.animateNext();
                ecommerceteam_carousel.position++;
                $(ecommerceteam_carousel.selector+' ul').append(this.itemsPreloaded[this.items[next_item]].clone(true));
                return true;
            }
            
            if(this.itemsCache[next_item]){
                
                ecommerceteam_carousel.animateNext();
                ecommerceteam_carousel.position++;
                $(ecommerceteam_carousel.selector+' ul').append(this.itemsCache[next_item].clone(true));
                return true;
            }
            
            
            var content = $(ecommerceteam_carousel.itemsHtml[this.items[next_item]]);
            
            ecommerceteam_carousel.itemsCache[next_item] = content.clone(true);
            
            content.find('img').load(function(){
                ecommerceteam_carousel.animateNext();
                
            })
            
            $(ecommerceteam_carousel.selector+' ul').append(content);
            
            ecommerceteam_carousel.position++;
        
            
        },
        animateNext:function()
        {
            ecommerceteam_carousel.hideActive();
            
            $(ecommerceteam_carousel.selector + ' li:first').animate({marginLeft:-ecommerceteam_carousel.itemWidth}, 500, ecommerceteam_carousel.mousedown ? 'linear' : 'swing', function(){
                $(ecommerceteam_carousel.selector + ' li:first').remove()
                if(ecommerceteam_carousel.mousedown){
                    ecommerceteam_carousel.next();
                }else{
                    ecommerceteam_carousel.showActive();
                    ecommerceteam_carousel.proccess = false;
                }
            });
        },
        prev:function()
        {
            if(this.position < 1){
                this.position = this.items.length;
            }
            
            var prev_item = this.position-1;
            
            if(prev_item < 0){
                prev_item = this.items.length-1;
            }
            
            if(this.itemsPreloaded.length && this.itemsPreloaded[this.items[prev_item]]){
            
                ecommerceteam_carousel.position--;
                $(ecommerceteam_carousel.selector+' ul li:first').replaceWith(this.itemsPreloaded[this.items[prev_item]].clone(true));
                ecommerceteam_carousel.animatePrev();
                return true;
                
            }
            if(this.itemsCache[prev_item]){
                
                ecommerceteam_carousel.position--;
                $(ecommerceteam_carousel.selector+' ul li:first').replaceWith(this.itemsCache[prev_item].clone(true));
                ecommerceteam_carousel.animatePrev();
                return true;
            }
            
            var content = $(ecommerceteam_carousel.itemsHtml[this.items[prev_item]]);
            
            ecommerceteam_carousel.itemsCache[prev_item] = content.clone(true);
            
            content.find('img').load(function(){
                ecommerceteam_carousel.animatePrev();
            })
            
            $(ecommerceteam_carousel.selector+' ul li:first').replaceWith(content);
            
            ecommerceteam_carousel.position--;
        
        },
        animatePrev:function()
        {
            ecommerceteam_carousel.hideActive();
            
            $(ecommerceteam_carousel.selector + ' ul').prepend('<li style="margin-left:-'+ecommerceteam_carousel.itemWidth+'px">&nbsp;</li>').children('li:first').animate({marginLeft:0}, 500, ecommerceteam_carousel.mousedown ? 'linear' : 'swing', function(){
                $(ecommerceteam_carousel.selector + ' li:last').remove();
                ecommerceteam_carousel.showActive();
                
                if(ecommerceteam_carousel.mousedown){
                    ecommerceteam_carousel.prev();
                }else{
                    ecommerceteam_carousel.proccess = false;
                }
            });
        },
        showActive:function(callback)
        {
            var info = $(ecommerceteam_carousel.selector + ' li').eq(this.active_item).find('div.product-info');
            info.css({display:'block'});
            
            if(typeof callback == 'function'){
                callback();
            }
            
        },
        hideActive:function(callback)
        {
            var info = $(ecommerceteam_carousel.selector + ' li').eq(this.active_item).find('div.product-info');
            info.css({display:'none'});
            
            if(typeof callback == 'function'){
                callback();
            }
        }
    };
    
    ecommerceteam_carousel.init('div.ecommerceteam-carousel').showActive();
});