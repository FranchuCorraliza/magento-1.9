/* 
 * Author: Chees
 * Description: Home > Show Window 愿�� 湲곕낯 js
 */

// �덉쐢�꾩슦
var ShowWindow = {
    SHOW_SPEED : 900, // �몃옖吏�� �ㅽ뵾��
    isTrans : false, // �꾩옱 �몃옖吏�� 以묒씤吏, �몃옖吏�� 以묒뿉�� 踰꾪듉 �대깽�� 遺덇�
    init : function() {

        $('.show-window .btn-grid-1').on('click', function(){
            if ( ShowWindow.isTrans ) return false; // �몃옖吏�� 以묒뿉�� 踰꾪듉 �대깽�� 遺덇�
            if ( $(this).hasClass('active')) return false;
            $('.show-window .btns a').removeClass('active');
            $(this).addClass('active');
            GridBySlider.show();
            return false;
        });

        $('.show-window .btn-grid-2').on('click', function(){
            if ( ShowWindow.isTrans ) return false; // �몃옖吏�� 以묒뿉�� 踰꾪듉 �대깽�� 遺덇�
            if ( $(this).hasClass('active')) return false;
            $('.show-window .btns a').removeClass('active');
            $(this).addClass('active');
            GridByList.show();
            return false;
        });

        $('.grid-by-slider .good').on('mouseenter', function(){
            if ( $(this).hasClass('active') == false  ) $('.good-name', this).stop().fadeIn((window.underIE9 ) ? 1 : 200);
            $('a .over', this).stop().fadeIn((window.underIE9 ) ? 1 : 200);
        });

        $('.grid-by-slider .good').on('mouseleave', function(){
            if ( $(this).hasClass('active') == false ) $('.good-name', this).stop().fadeOut((window.underIE9 ) ? 1 : 200);
            $('a .over', this).stop().fadeOut((window.underIE9 ) ? 1 : 100);
        });

        $('.grid-by-list .good').on('mouseenter', function(){
            $('a .over', this).stop().fadeIn((window.underIE9 ) ? 1 : 200);
        });

        $('.grid-by-list .good').on('mouseleave', function(){
            $('a .over', this).stop().fadeOut((window.underIE9 ) ? 1 : 100);
        });
    },

    play : function() {
        GridBySlider.init();
        GridByList.init();
        GridBySlider.show(true);
    }
};

// �щ씪�대뜑 洹몃━�� 諛⑹떇
var GridBySlider = {
    GBS : null,
    selectedIndex : 0,
    slideContentTotalCount : 0,
    posX : 0,
    bar : null,
    slider : null,
    sliderContentContainer : null,
    oriX : 0,
    sliderWidth : 0,
    startX : 0,
    endX : 0,
    centerX : 0,
    CONTENT_W : 332,
    CONTENT_B_W : 332,
    contentW : 0,
    init : function() {
        GBS = GridBySlider;
        GBS.slideContentTotalCount = $('.show-window .slider-content-container .good').length;
        if ( GBS.slideContentTotalCount % 2 == 0 ) {
            $('.show-window .slider-content-container .good:last-child').remove();
            GBS.slideContentTotalCount = $('.show-window .slider-content-container .good').length;
        }
        GBS.bar = $('.show-window .slider .bar');
        GBS.slider = $('.show-window .slider');
        GBS.sliderContentContainer = $('.show-window .slider-content-container');
        GBS.oriX = parseInt(GBS.slider.offset().left);
        GBS.sliderWidth = GBS.slider.width() - GBS.bar.width();
        GBS.startX = 1;
        GBS.endX = GBS.sliderWidth - 1;
        GBS.centerX = (GBS.endX - GBS.startX) / 2;
        GBS.contentW = GBS.slideContentTotalCount * GBS.CONTENT_W - GBS.CONTENT_W;
        GBS.selectedIndex = Math.floor(GBS.slideContentTotalCount/2);
        GBS.setUI();
        GBS.setSlideContent();
        if ( $.browser.mobile ) GBS.setSwipe();

        $('.grid-by-slider').on('mousemove', function(e){
            if ( e.clientX < 100 ) {
                $('.slide-btn.prev', this).stop().fadeIn(300);
            } else {
                $('.slide-btn.prev', this).stop().fadeOut(200);
            }

            if ( parseInt($(window).innerWidth()) - e.clientX < 100 ) {
                $('.slide-btn.next', this).stop().fadeIn(300);
            } else {
                $('.slide-btn.next', this).stop().fadeOut(200);
            }
        });

        $('.grid-by-slider').on('mouseleave', function(e){
            $('.slide-btn.prev', this).stop().fadeOut(200);
            $('.slide-btn.next', this).stop().fadeOut(200);
        });

        $('.grid-by-slider .slide-btn.prev').on('click', function(e){
            GBS.goPrev();
            return false;
        });

        $('.grid-by-slider .slide-btn.next').on('click', function(e){
            GBS.goNext();
            return false;
        });
    },
    setUI : function() {
        GBS.posX = GBS.centerX;
        GBS.bar.css({'left':GBS.posX});
        GBS.bar.on('drag', function( ev, dd ){
            GBS.updateSlideContent(dd.offsetX);
        });

        GBS.bar.on('dragend', function( ev, dd ){
            GBS.updateSlideContent(dd.offsetX);
            GBS.setSlideContent();
        });

        GBS.sliderContentContainer.css({'margin-left': -GBS.CONTENT_W/2})
    },

    updateSlideContent : function(offsetX) {
        GBS.oriX = parseInt(GBS.slider.offset().left);
        GBS.posX = offsetX - GBS.oriX;
        GBS.posX = Math.max(GBS.startX, Math.min(GBS.posX,GBS.endX));
        GBS.bar.css({ left:GBS.posX });

        var per = GBS.posX / GBS.sliderWidth;
        var contentX = GBS.contentW * per;
        $('.show-window .slider-content').stop().animate({'left':-contentX}, 400 , 'easeOutQuint');
        GBS.selectedIndex = Math.round(per * (GBS.slideContentTotalCount-1)) ;
        GBS.selectedIndex = Math.min(GBS.selectedIndex, GBS.slideContentTotalCount-1);
        var selectedContent = $('.show-window .slider-content .good:eq(' + GBS.selectedIndex + ')');
        $('.show-window .slider-content .good').each(function(index){

            if ( index == selectedContent.index() ) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('selected active');
            }
        });
        $('.show-window .slider-content .good .good-name').hide().attr('style', '');
        selectedContent.addClass('active');
    },

    setSlideContent : function() {

        var per = GBS.selectedIndex / (GBS.slideContentTotalCount - 1);

        var selectedContent = $('.show-window .slider-content .good:eq(' + GBS.selectedIndex + ')');
        var contentX = GBS.CONTENT_W * GBS.selectedIndex;

        GBS.posX = Math.round(per * GBS.sliderWidth);
        GBS.posX = Math.max(GBS.startX, Math.min(GBS.posX, GBS.endX));
        GBS.bar.animate({'left':GBS.posX}, 200);


        $('.show-window .slider-content .good .good-name').hide().attr('style', '');
        $('.show-window .slider-content .good').removeClass('selected active');
        selectedContent.addClass('selected active');
        GBS.sliderContentContainer.stop().animate({'margin-left': -GBS.CONTENT_B_W/2}, 400);
        $('.show-window .slider-content').stop().animate({'left':-contentX}, 600 , 'easeOutQuad', function(){
            $('.good-name', selectedContent).stop().fadeIn( (window.underIE9 ) ? 1 : 200 ).attr('style', 'display:block !important');
        });
    },

    goNext : function() {
        GBS.selectedIndex ++;
        GBS.selectedIndex = Math.min(GBS.selectedIndex, GBS.slideContentTotalCount-1);
        GBS.setSlideContent();
    },


    goPrev : function() {
        GBS.selectedIndex --;
        GBS.selectedIndex = Math.max(0,GBS.selectedIndex);
        GBS.setSlideContent();
    },

    setSwipe : function() {

        $('.grid-by-slider .slider-content').swipe( {

            click:function(event, target) {
                var href = $(event.target).parent().attr('href');
                self.location.href = href;
            },

            swipe:function(event, direction, distance, duration, fingerCount) {
                if ( direction == 'left') {
                    GBS.goNext();
                }

                if ( direction == 'right') {
                    GBS.goPrev();
                }
            },
            triggerOnTouchEnd: true,
            excludedElements: 'a .good',
            allowPageScroll:"vertical",
            //Default is 75px, set to 0 for demo so any distance triggers swipe
            threshold:50
        });

    },

    show : function(isFirst) {

        var speed2 = (isFirst) ? ShowWindow.SHOW_SPEED * .8 : ShowWindow.SHOW_SPEED ;
        ShowWindow.isTrans = true;
        $('.grid-by-slider').stop().show().css({'top':0}).animate({'top':0}, ShowWindow.SHOW_SPEED , 'easeOutQuad', function(){
            $(this).addClass('active');
            ShowWindow.isTrans = false;
        });
        $('.grid-by-slider .slider').stop().css({'bottom':-900,'alpha':0}).animate({'bottom':62, 'alpha':1}, ShowWindow.SHOW_SPEED , 'easeOutQuad');
        $('.grid-by-slider .good').each(function(index){
            $(this).stop().css({'top':1400}).stop().delay(Math.abs(GBS.selectedIndex - index) * 100).animate({'top':0}, speed2, 'easeInOutQuint');
        });

        if ( isFirst ) return;

        $('.grid-by-list').removeClass('active');
        $('.grid-by-list').stop().animate({'top':0}, ShowWindow.SHOW_SPEED , 'easeInOutQuint', function(){
            $(this).hide();
            ShowWindow.isTrans = false;
        });

        $('.grid-by-list .list-index').stop().animate({'bottom':-50, 'alpha':0}, ShowWindow.SHOW_SPEED , 'easeInOutQuint');
        $('.grid-by-list .btn-prev, .grid-by-list .btn-next').fadeOut(ShowWindow.SHOW_SPEED/2);
        $('.grid-by-list .good-list li.active').each(function(index){
            $(this).stop().delay(index * 20).animate({'top':-500}, ShowWindow.SHOW_SPEED, 'easeInOutQuint');
        });
    }

};

// 洹몃━�� 由ъ뒪�� 諛⑹떇
var GridByList = {

    GBL : null,
    LIST_CONTENT_SIZE_W : 254,
    LIST_CONTENT_SIZE_H : 274,
    PAGE_PER_LIST : 8,
    PER_ROW : 4,
    listContentTotalCount : 0,
    listTotalPageCnt :  0,
    listPage : 0,
    isListMoving : false,

    init : function() {
        GBL = GridByList;
        GBL.listContentTotalCount = $('.grid-by-list .good-list li').length;
        GBL.listTotalPageCnt =  Math.floor(GBL.PAGE_PER_LIST / GBL.listContentTotalCount) + 1;
        GBL.setUI();
    },

    setUI : function() {

        for( var i = 0 ; i < GBL.listContentTotalCount ; ++i ) {
            var good = $('.grid-by-list .good-list li:eq(' + i + ')');
            var index = i;
            var top = Math.floor( index / GBL.PER_ROW ) * GBL.LIST_CONTENT_SIZE_H + 800;
            var left = ( index % GBL.PER_ROW ) * GBL.LIST_CONTENT_SIZE_W;
            good.css({'top':top, 'left':left});
        }

        $('.grid-by-list .list-index li').each(function(index){
            $(this).on('click', function(e){
                if ( GBL.isListMoving ) return false;
                if ( $(this).hasClass('active')) return false;
                GBL.listPage = index ;
                GBL.alignListContent();
                $('.grid-by-list .list-index li').removeClass('active');
                $(this).addClass('active');
                return false;
            });
        });

        $('.grid-by-list .btn-prev').on('click', function(){
            if ( GBL.isListMoving ) return false;
            GBL.listPage --;
            if ( GBL.listPage < 0 ) GBL.listPage = 2;
            $('.grid-by-list .list-index li:eq(' + GBL.listPage + ')').trigger('click');
            return false;
        });

        $('.grid-by-list .btn-next').on('click', function(){
            if ( GBL.isListMoving ) return false;
            GBL.listPage ++;
            if ( GBL.listPage == $('.grid-by-list .list-index li').length ) GBL.listPage = 0;
            $('.grid-by-list .list-index li:eq(' + GBL.listPage + ')').trigger('click');
            return false;
        });
    }

};



