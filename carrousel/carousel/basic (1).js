
$(function(){

	//약관
	var j=0;
	$('.sel_terms_view').click(function(){
		if(j%2 == 0){
			$('.sel_terms_list').slideDown('10');
		}else{
			$('.sel_terms_list').slideUp('10');
		}
		j++;
	});


	// 편의시설 Tab Menu 
	$('.service_tab a').each(function(n){
		$(this).click(function(){	
		 $('.service_tab a').removeClass('on');
		 $('.service_place').hide();
		 $(this).addClass('on');
		 $('.service_place').eq(n).show();
		});
	});

	$('.service_tab2 a').each(function(n){
		$(this).click(function(){	
			$('.service_tab2 a').removeClass('on');
			$(this).addClass('on');
			$('.obj_service_tb').hide();
			$('.obj_service_tb').eq(n).show();
		});
	});

	$('.faq_tab a').each(function(n){
		$(this).click(function(){	
			$('.faq_tab a').removeClass('on');
			$(this).addClass('on');
			$('.faq_brd').hide();
			$('.faq_brd').eq(n).show();
		});
	});

	// Tab Menu 
	$('.faq_list .board_contents').hide();
	$('.faq_list .board_list').each(function(n){
		$(this).click(function(){
			if($(this).hasClass('on')){
				 $('.faq_list .board_list').removeClass('on');
				 $('.faq_list .board_contents').slideUp();
				 $('.faq_list .board_list').css('border-bottom','1px solid #bebebe');
			}else{
				 $('.faq_list .board_list').removeClass('on');
				 $('.faq_list .board_contents').slideUp();
				 $('.faq_list .board_list').css('border-bottom','1px solid #bebebe');

				 $(this).addClass('on');
				 $('.faq_list .board_contents').eq(n).slideDown();
				 $('.faq_list .board_list').eq(n).css('border-bottom','1px solid #333');
				 $('.faq_list .board_list').eq(n-1).css('border-bottom','0');
			}		 
		});
	});

	//VIP
	$('.vip_tab a').each(function(n){
		$(this).click(function(){	
			$('.vip_tab a').removeClass('on');
			$(this).addClass('on');
			$('.vip_tab_info').hide();
			$('.vip_tab_info').eq(n).show();
		});
	});

	// 상품권 소개
	/*$('.voucher_tab a').each(function(n){
		$(this).click(function(){	
			 $('.voucher_tab a').removeClass('on');
			 $('.voucher').hide();
			 $(this).addClass('on');
			 $('.voucher').eq(n).show();
			 if(n==0){
				 $('.voucher_tab ul').css('background-position','0 0');
			 }else{
				  $('.voucher_tab ul').css('background-position','0 -42px');
			 }
		});
	});

	// 명절선물배송
	$('.delivery_tab a').each(function(n){
		$(this).click(function(){	
			 $('.delivery_tab a').removeClass('on');
			 $('.delivery_area').hide();
			 $(this).addClass('on');
			 $('.delivery_area').eq(n).show();
			 if(n==0){
				 $('.delivery_tab ul').css('background-position','0 0');
			 }else{
				  $('.delivery_tab ul').css('background-position','0 -42px');
			 }
		});
	});*/

	// Tab List , View 갤러리아 상품권 사용처 등 
	$('#tab_list a').each(function(n){
		$(this).click(function(){	
		 $('#tab_list a').removeClass('on');
		 $('.tab_view').hide();
		 $(this).addClass('on');
		 $('.tab_view').eq(n).show();
		});
	});

	// 명절선물배송 , 상품권 소개
	$('#tab_list2 a').each(function(n){
		$(this).click(function(){	
			 $('#tab_list2 a').removeClass('on');
			 $('.tab_view').hide();
			 $(this).addClass('on');
			 $('.tab_view').eq(n).show();
			 if(n==0){
				 $('#tab_list2 ul').css('background-position','0 0');
			 }else{
				  $('#tab_list2 ul').css('background-position','0 -42px');
			 }
		});
	});

	/*footer
	$('#footer .etc-menu a').each(function(n){
		$(this).mouseover(function(){	

			if(n == 4){
				$('#footer .etc-menu').css('background','url(img/common/etc_menu_bar.png) repeat-y 100% 0');
				$('#footer .etc-menu li').eq(n).css('background','url(img/common/etc_menu_bar.png) repeat-y 0 0');
			}else{
				$('#footer .etc-menu li').eq(n).css('background','url(img/common/etc_menu_bar.png) repeat-y 0 0');
				$('#footer .etc-menu li').eq(n+1).css('background','url(img/common/etc_menu_bar.png) repeat-y 0 0');
			}
		});
		$(this).mouseleave(function(){
			$('#footer .etc-menu').css('background','url(img/common/footer-widget-split.png) repeat-y 100% 0');
			$('#footer .etc-menu li').css('background','url(img/common/footer-widget-split.png) repeat-y 0 0');
		});
	});*/

	var p1=0;
	var p2=0;

	//about collaboration 
	$('#photo_1 p a').each(function(n){
		$(this).click(function(){	
			$('#photo_1 p a').removeClass('on');
			$(this).addClass('on');
			var w=-(n * 820) + 'px';
			$('#photo_1 ul').animate({'left':w});
			p1=n;
		});
	});

	var total1 = $('#photo_1 p a').length;
	$('#photo_1 span a').click(function(){
		if($(this).hasClass('bt_right')){
			p1++;
			if(p1>0 && p1<total1){
				var ww=-(p1 * 820) + 'px';	
				$('#photo_1 ul').animate({'left':ww});	
				$('#photo_1 p a').removeClass('on');
				$('#photo_1 p a').eq(p1).addClass('on');
			}
			if(p1 == total1) {p1=total1-1};
		}else{		
			p1--;			
			if(p1>-1 && p1<total1+1){	
				var ww=-(p1 * 820) + 'px';	
				$('#photo_1 ul').animate({'left':ww});	
				$('#photo_1 p a').removeClass('on');
				$('#photo_1 p a').removeClass('on');
				$('#photo_1 p a').eq(p1).addClass('on');
			}
			if(p1<0) {p1=0;}	
		}
	});

	$('#photo_2 p a').each(function(n){
		$(this).click(function(){	
			$('#photo_2 p a').removeClass('on');
			$(this).addClass('on');
			var w=-(n * 820) + 'px';
			$('#photo_2 ul').animate({'left':w});
			p2=n;
		});
	});	

	var total2 = $('#photo_2 p a').length;
	$('#photo_2 span a').click(function(){
		if($(this).hasClass('bt_right')){
			p2++;
			if(p2>0 && p2<total2){
				var ww=-(p2 * 820) + 'px';	
				$('#photo_2 ul').animate({'left':ww});	
				$('#photo_2 p a').removeClass('on');
				$('#photo_2 p a').eq(p2).addClass('on');
			}
			if(p2 == total2) {p2=total2-1};
		}else{		
			p2--;			
			if(p2>-1 && p2<total2+1){	
				var ww=-(p2 * 820) + 'px';	
				$('#photo_2 ul').animate({'left':ww});	
				$('#photo_2 p a').removeClass('on');
				$('#photo_2 p a').removeClass('on');
				$('#photo_2 p a').eq(p2).addClass('on');
			}
			if(p2<0) {p2=0;}	
		}
	});

	// design collaboration Tab Menu
	$('.about_tab a').each(function(n){
		$(this).click(function(){	
		 $('.about_area3').hide();
		 $('.about_area3').eq(n).show();
		 if(n == 0){
			$('.about_tab a').css('background-position','0 0');
		 }else if(n == 1){
			$('.about_tab a').css('background-position','0 -42px');
		 }else{
			$('.about_tab a').css('background-position','0 -84px');
		 }
		});
	});
/*	$('.share .btn a').each(function(n){
		$(this).click(function(){
			$('.share_list').css('display','none');
			$('.share .btn a').css('display','block');
			$('.share_list').eq(n).css('display','block');
			$(this).css('display','none');
		}); 
	});*/

});



