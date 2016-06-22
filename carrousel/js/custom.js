// 2014-03-09 start
/* show window */
function showHover() {
	if (navigator.userAgent.indexOf("MSIE 8.0") == -1) {
		$('.item-group .btn-view').on('mouseenter', function(){
			$(this).children('.over').stop().fadeIn(500);
		});
		$('.item-group .btn-view').on('mouseleave', function(){
			$(this).children('.over').stop().fadeOut(300);
		});
	} else {
		$('.item-group .btn-view').on('mouseenter', function(){
			$(this).children('.over').stop().show();
		});
		$('.item-group .btn-view').on('mouseleave', function(){
			$(this).children('.over').stop().hide();
		});
	}
}

// fashion & style
function fashionHover() {
	if (navigator.userAgent.indexOf("MSIE 8.0") == -1) {
		$('.fashion_style .list-area ul li .thumb a').on('mouseenter', function(){
			$(this).children('.over').stop().fadeIn(500);
		});
		$('.fashion_style .list-area ul li .thumb a').on('mouseleave', function(){
			$(this).children('.over').stop().fadeOut(300);
		});
	} else {
		$('.fashion_style .list-area ul li .thumb a').on('mouseenter', function(){
			$(this).children('.over').stop().show();
		});
		$('.fashion_style .list-area ul li .thumb a').on('mouseleave', function(){
			$(this).children('.over').stop().hide();
		});
	}
}

/* luxury floor
$(function(){
	$('.floor-wrap .tabs a').each(function(i){
		$(this).click(function(){
			$('.grid_area > div').removeClass('on');
			$('#east, #west').css('display','none');
			$('.floor-wrap .tabs a').each(function(){
				var srcOff = $(this).find('img').attr("src").replace("on", "off");
				$(this).addClass('on').find('img').attr("src", srcOff);
				$(this).removeClass('on');
			});
			var srcOn = $(this).find('img').attr("src").replace("off", "on");
			$(this).addClass('on').find('img').attr("src", srcOn);
			i==0?$('#west').css('display','block'):$('#east').css('display','block') ;
			return false;
		});
	});

	$('.west-floor a').each(function(i){
		var j=i;
		$(this).click(function(){
			$('.grid_area > div').removeClass('on');
			$('.west_floor_list').hide();
			$('.west_floor'+j).show();
			$('.west-floor a').each(function(){
				$(this).removeClass('on');
			});
			$('.west-map').attr('src','/web/img/luxury/west_bg_'+j+'f.jpg');
			$(this).addClass('on');
			return false;
		});
	});

	$('.east-floor a').each(function(i){
		var j=i;
		$(this).click(function(){
			$('.grid_area > div').removeClass('on');
			$('.east_floor_list').hide();
			$('.east_floor'+j).show();
			$('.east-floor a').each(function(){
				$(this).removeClass('on');
			});
			$('.east-map').attr('src','/web/img/luxury/east_bg_'+j+'f.jpg');
			$(this).addClass('on');
			return false;
		});
	});
});
// 2014-03-09 end
*/
/* luxury notice show hide */
$(function(){
	$('.brd .inner-detail').hide();
	$('tr.subject, .close-bn').click(function() {
		$('.inner-detail').css({'border-bottom':'1px solid #e3e3e3','text-align':'left'});
		$('.brd .inner-detail').slideUp('fast');
		$('.selected-notice').remove();
		$('.brd tr.subject').css({'background':'#fff','font-weight':'normal'});
		var slidedownelement = $(this).next('tr').find('.inner-detail').eq(0);
		if(!slidedownelement.is(':visible')) {
			slidedownelement.slideDown('fast');
			$(this).css({'background':'#f5f5f5','font-weight':'bold'});
			$('.inner-detail .close-bn').css({'background':'#fff'});
		}
		/*var pos_n= $(this).next('tr').find('.inner-detail').position().top;
		$("html, body").animate({scrollTop:pos_n},'slow'); */
		$("html, body").scrollTop('.brd .inner-detail');
		//$(this).find('a').before('<span class="selected-notice">[선택됨]</span>');
		return false;
	});
});


function styleShare() {
	// 공유버튼 2014-04-22
	$('.share').on('mouseenter', function() {
		$(this).find('.btn-share img').css('margin-top', '-25px');
		$(this).find('.share_list').show();
	});
	$('.share').on('mouseleave', function() {
		$(this).find('.btn-share img').css('margin-top', '0');
		$(this).find('.share_list').hide();
	});
	$('.share .btn-share').on('focusin', function() {
		$(this).find('img').css('margin-top', '-25px');
		$(this).siblings('.share_list').show();
	});
	$('.share .s2').on('focusout', function() {
		$(this).parent().siblings('.btn-share').find('img').css('margin-top', '0');
		$(this).parent().hide();
	});
}

/*
	$('div.answer').hide();
	$('a.question').before('<span class="faqplusminus dark nounderline">[+]</span>');
	$('a.question').click(function() {
		$('div.answer').slideUp('fast');
		$('span.faqplusminus').html('[+]');
		var slidedownelement = $(this).closest('div.faq').find('div.answer').eq(0);
		if(!slidedownelement.is(':visible')) {
		slidedownelement.slideDown('fast');
		slidedownelement.parent().find('span.faqplusminus').html('[-]');
		}
	});
*/



/* leftmenu over / out*/
$(function(){
	$('aside div.leftmenu img, .share-toggle img, .lang-toggle img').mouseover(function(){
		if (!$(this).hasClass('active')){
			var image_name = $(this).attr('src').split('_off.')[0];
			var image_type = $(this).attr('src').split('off.')[1];
			$(this).attr('src', image_name + '_on.' + image_type);
		}
	}).mouseout(function(){
		if (!$(this).hasClass('active')){
			var image_name = $(this).attr('src').split('_on.')[0];
			var image_type = $(this).attr('src').split('_on.')[1];
			$(this).attr('src', image_name + '_off.' + image_type);
		}
	});
});