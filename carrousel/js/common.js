/*
 * Author: Chees
 * Description: header,nav,footer 관련 사이트 공통 js
 */


// extra
/**
 * jQuery.browser.mobile (http://detectmobilebrowser.com/)
 *
 * jQuery.browser.mobile will be true if the browser is a mobile device
 *
 **/
(function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|Android|android|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);

if ( ! window.console ) console = { log: function(){} };

window.underIE9 = (navigator.userAgent.match(/MSIE\s(?!9.0)/)) ? true : false ;
// window.underIE9 = true;

$(function(){

	// 모바일 브라우져 혹은 메인을 제외한 페이지에서는 fixed된 헤더의 가로 스크롤을 활성화시켜준다.
	// fixed된 header가 가로 스크롤이 필요한 이유는 https://github.com/bigspotteddog/ScrollToFixed 참조
	if ($.browser.mobile || $('body').hasClass('home') == false ) $('#header').scrollToFixed();

	// 브라우져 가로가 1200이하일 경우 header 오른쪽 메뉴를 숨기기 위하여 body에 class를 세팅해줌
	$(window).on('resize', function(){
		var wh = parseInt($(window).width());
		if (wh < 1310) {
			$('body').addClass('under-1200').removeClass('over-1200');
		} else {
			$('body').removeClass('under-1200').addClass('over-1200');
		}
	});

	// 스킵메뉴안의 버튼들 세팅
	$('.skip-menu > a').on('click', function(e){
		$('.skip-menu').removeClass('active').blur();
		var target = $(this).attr('href');
		$(target).attr('tabindex', '-1');
	});

	// "tab"키로 포커스 이동시
	$('.skip-menu').on('focusin', function(){
		$(this).addClass('active');
	});

	$('.skip-menu').on('focusout', function(){
		$(this).removeClass('active');
	});


	$('#header .logo, #header .util-menu').on('focusin', function(){
		$('.gnb > li.opened').removeClass('active opened');
	});

	// #nav > .gnb 세팅
	$('.gnb > li').each(function(index,obj){
		var container = $(this);
		var subMenu = $('.sub-menu', container);
		$('a .over', container).height(0);
		var openEvenType = ( $.browser.mobile ) ? 'mouseenter' : 'mouseenter' ;
		container.on(openEvenType, function(e){
			showGnbLi(container);
			return false;
		});

		var closeEventType = ( $.browser.mobile ) ? 'mouseleave' : 'mouseleave' ;
		container.on(closeEventType, function(e){
			closeGnbLi(container);
			return false;
		});

		// tab키로 포커스 이동시
		container.on('focusin', function(e) {
			if ( container.hasClass('opened') == false ) {
				$('.gnb > li.opened').removeClass('active opened');
				container.addClass('active opened');
			}
		});

		container.on('focusout', function(e) {
			//container.removeClass('active');
		});
	});



	// 토글 메뉴를 가진 버튼들 세팅. 일반적으로 .toggle-menu안에 > a와 > .toggle-list의 구조를 가지고 있음.
	$('.toggle-menu').each(function(){

		var menu = $(this);
		menu.addClass('js-control').find('.toggle-list').slideUp(0);

		menu.on('focusout', function(e){
			menu.removeClass('active');
			return false;
		});

		menu.on('focusin', function(e){
			menu.addClass('active');
			return false;
		});


		$('> a', menu).on('click', function(){
			if ( $(menu).hasClass('opened')) {
				$(menu).removeClass('opened');
				$('.toggle-list', menu).stop().slideUp(100);
			} else {
				$(menu).addClass('opened');
				var delay = ( $(menu).attr('delay-in') ) ? parseInt($(menu).attr('delay-in')) : 0 ;
				$('.toggle-list', menu).stop().delay(delay).slideDown(200);
			}

			return false;
		});

		menu.on('mouseleave', function(){
			$('.toggle-list', menu).stop().slideUp(100);
			menu.removeClass('active opened');
		});

		$(' > a', menu).on('click', function(){
			return false;
		});
	});

	// 1200이하일 경우 헤더 오른쪽 util menu는 숨겨진 레이어 메뉴(panel-util-menu)로 바뀜 .panel-util-menu를 세팅함.
	$('.panel-util-menu').slideUp(0);
	$('.btn-show-util-menu').on('click', function(){
		$(this).toggleClass('active');
		if ( $(this).hasClass('active')) {
			$('.panel-util-menu').stop().slideDown(1000, 'easeInOutQuint');
		} else {
			$('.panel-util-menu').stop().slideUp(500, 'easeInOutQuint');
		}
		return false;
	});



	// 검색하기버튼을 누르면 검색창을 호출
	$('.util-menu .search a, .panel-util-menu .btn-search').on('click', function(){
		showSearch();
		return false;
	});

	// #nav의 메뉴 중에는 good-list-container를 가지는 메뉴들이 있음(m2,m3). good-list-container에 있는 good들을 세팅
	$('.good-list-container').each(function(){
		var container = $(this);
		var startIndex = $('.good-list', this).attr('start-index'); //starIndex로 시작포지션을 구분
		var goodLi = $('.good-list li', this);

		goodLi.each(function(index){
			var posX = 0;
			if ( index < startIndex ) {
				// index가 startIndex보다 작을 경우 버튼을 기준으로 왼쪽으로 세팅
				posX = index * (-161) + 160 * (startIndex-1);
			} else {
				// index가 startIndex보다 클 경우 버튼을 기준으로 오른쪽으로 세팅
				posX = (index-(startIndex-1)) * 161 + 160 * startIndex + 1 ;
			}
			$(this).css({'left':posX,'top':-161});
		});


	});


	// good-list-container안의 good-box들을 세팅
	$('.good-box').addClass('js-control').find('.good-name').slideUp(0);
	$('.good-box').on('mouseenter', function(){
		$('.good-name', this).stop().slideDown(300);
	});

	$('.good-box').on('mouseleave', function(){
		$('.good-name', this).stop().slideUp(200);
	});

	$(window).trigger('resize');

});


// gnb의 각 메뉴들을 펼쳐주는 함수
var showGnbLi = function(container) {

	if ($(this).hasClass('opened')) return false; // 이미 열려있다면 무시
	if ( $.browser.mobile ) closeGnbLi($('.gnb > li.opened')); // 기존에 열린 메뉴 닫음
	$(container).addClass('opened');

	var subMenu = $('.sub-menu', container),
		height = 230,
		speed = 400;

	$('a .over', container).stop().css({'height':0}).animate({'height':height}, speed);

	subMenu.stop().css({'height':0}).delay(50).animate({'height':160}, speed, function(){
	 	if($.browser.mobile)	$(container).addClass('active');
	});

	var goodList = $('.good-list-container', container);
	if ( goodList.length > 0 ) {
		goodList.stop().show().animate({'height':161},200, function(){
			var goodLi = $('.good-list li', goodList);
			var startIndex = $('.good-list', goodList).attr('start-index');
			goodLi.each(function(index){
				var delay = index * 60;
				if ( index < startIndex ) {
					delay = index * 60;
				} else {
					delay = (index - startIndex ) * 60 + 25 ;
				}
				$(this).stop(true,true).delay(delay).animate({'top':0},260);
			});
		});
	}
};

// gnb의 각 메뉴들을 닫아주는 함수
var closeGnbLi = function(container) {

	if ( container.hasClass('opened') == false ) return false;
	container.removeClass('opened active');

	var subMenu = $('.sub-menu', container),
		speed = 200;

	$('a .over', container).stop().animate({'height':'0'}, speed);
	subMenu.stop().animate({'height':0}, 100);

	var goodList = $('.good-list-container', container);
	if ( goodList.length > 0 ) {
		goodList.stop().animate({'height':0},500, function(){
			goodList.hide();
		});
		var goodLi = $('.good-list li', goodList);
		goodLi.each(function(index){
			var delay = (goodLi-index) * 30;
			$(this).stop(true,true).animate({'top':-161},100);
		});
	}
};

// todo : 개발에서 해당 url을 개발된 페이지로 바꿔야 함.
var SEARCH_URL = "/svc/search/web/kr/searchList.do";	 //	   /svc/search/web/kr/searchList.do


var showSearch = function() {
	if ( $('.search-page-loader').length > 0 ) {
		//이미 서치페이지가 로드되어 있으면 중복 로드를 피한다.
		return;
	}

	var searchPageLoader = $('<div></div>').addClass('search-page-loader').appendTo('body');

	searchPageLoader.load( SEARCH_URL + ' .search-container', function(data){
		
		$('html,body').css({'overflow':'hidden'});
		$(window).trigger('resize');
		var placeholderSupport = !!("placeholder" in document.createElement( "input" ));
		//$('.search-page .search-page-result input.keyword').placeholder();
		var speed = ( window.underIE9 ) ? 1 : 600 ;
		$('.search-container').hide().fadeIn(speed, 'easeOutQuad');
		if ( placeholderSupport ) $('.search-container input#search_frame_keyword').focus();
		$('.search-container .btn-close').on('click', function(){
			speed = ( window.underIE9 ) ? 1 : 300 ;
			$('.search-container').fadeOut(speed, 'easeOutQuad', function(){
				searchPageLoader.remove();
				$('html,body').css({'overflow':'auto','height':'auto'});
				$(window).trigger('resize');
			});
			return false;
		});

		var bSearching1 = false;
		var bSearching2 = false;
		var bSearching3 = false;

		$("#search_frame_keyword").on('keydown', function(e){
			if (e.keyCode == 13 && !bSearching1 && !bSearching2&& !bSearching3) {
				bSearching1 = true;
				bSearching2 = true;
				bSearching3 = true;
				$('#search_frame_submit').trigger("click");
			}
		});
		$("#mainsearch_brand_search_btn").on('click', function(){
			mainsearchNextPage('brand');
		});
		$("#mainsearch_fashion_search_btn").on('click', function(){
			mainsearchNextPage('fashion');
		});
		$("#mainsearch_window_search_btn").on('click', function(){
			mainsearchNextPage('window');
		});
		$('#search_frame_submit').on('click', function(){
			if($.trim($("#search_frame_keyword").val()).length == 0){
				alert("검색어를 입력하세요.");
				return;
			}

			$("#mainsearch_brand_num").val(1);
			$("#mainsearch_show_window_num").val(1);
			$("#mainsearch_fashion_style_num").val(1);
			$("#mainsearch_search_param2").val($("#search_frame_keyword").val());
	//alert($("#search_param2").val() );
			search_frame_keyword=$("#search_frame_keyword").val();
			hideAllHtmlPage();
			searchCount();
	
			//$(".search-result-box").load("/svc/search/web/kr/searchResultList.do")
		});
		var search_frame_keyword = "";
		var mainsearch_total_cnt=0;
		var mainsearch_brand_cnt=0;
		var mainsearch_window_cnt=0;
		var mainsearch_fashion_cnt=0;

		function searchCount(){
			$.ajax( // 화면의 데이타를 조회한다.
					{
						url:"/svc/search/web/kr/searchCount.do",
						dataType:"json",
						type:"POST",
						data:{
							curr_page : 1
							,search_param2 : search_frame_keyword

						},
						success:function( data ) {

		 					mainsearch_total_cnt = parseInt( data.dataObject.total );
		 					mainsearch_brand_cnt = parseInt( data.dataObject.brand_cnt );
		 					mainsearch_window_cnt = parseInt( data.dataObject.show_cnt );
		 					mainsearch_fashion_cnt = parseInt( data.dataObject.fashion_cnt );
		 					//alert("mainsearch_fashion_cnt="+mainsearch_fashion_cnt);
 		 					search_frame_keyword= search_frame_keyword.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
		 					 
		 					$("#result_count").html("<strong class=\"keyword\">"+search_frame_keyword+"</strong> 검색어로 총 <strong class=\"total-count\">"+mainsearch_total_cnt+"</strong> 건의 항목이 검색 되었습니다.");
		 					$("#result_count").show();
//alert(mainsearch_total_cnt); 
		 					//if( mainsearch_total_cnt > 0 ){
		 						//alert("mainsearch_brand_cnt="+$("#mainsearch_result_box").attr("style"));
		 						$("#mainsearch_result_box").show();
		 						//alert("mainsearch_brand_cnt="+$("#mainsearch_result_box").attr("style"));
		 						$("#mainsearch_brand_count").html(mainsearch_brand_cnt+"개");
		 						$("#mainsearch_window_count").html(mainsearch_window_cnt+"개");
		 						$("#mainsearch_fashion_count").html(mainsearch_fashion_cnt+"개");

		 						if( mainsearch_brand_cnt == 0 ){
		 							$("#mainsearch_result_brand").show();
 		 							bSearching1 = false;
		 						}else{
		 							$("#mainsearch_brand_search_box").show();
		 							keywordSearchLoadHtml("/svc/search/web/kr/searchBrandList.do" ,"#mainsearch_brand_num","#mainsearch_brand_search",mainsearch_brand_cnt,6);
		 						}
		 						if( mainsearch_window_cnt == 0 ){
		 							//alert(mainsearch_window_cnt);
		 							$("#mainsearch_result_window").show();
 	 								bSearching2 = false;
		 						}else{
		 							$("#mainsearch_window_search_box").show();
		 							keywordSearchLoadHtml("/svc/search/web/kr/searchShowWindowList.do" ,"#mainsearch_show_window_num","#mainsearch_window_search",mainsearch_window_cnt,4);
		 						}
		 						if( mainsearch_fashion_cnt == 0 ){
		 							$("#mainsearch_result_fashion").show();
	 								bSearching3 = false;
		 						}else{
		 							$("#mainsearch_fashion_search_box").show();
		 							keywordSearchLoadHtml("/svc/search/web/kr/searchFashionStyleList.do" ,"#mainsearch_fashion_style_num","#mainsearch_fashion_search",mainsearch_fashion_cnt,5);
		 						}
		 					//}

		 					//{"dataObject":{"mainsearch_total_cnt":575,"brand_cnt":479,"show_cnt":75,"fashion_cnt":21}}
						},
						error : function( e ) {
							//alert("조회 오류\n"+e.status);
 						}
					}
			);
		}
		function hideAllHtmlPage(){
 			$("#mainsearch_brand_search_box").hide();
			$("#mainsearch_fashion_search_box").hide();
			$("#mainsearch_window_search_box").hide();
			$("#mainsearch_result_brand").hide();
	 		$("#mainsearch_result_window").hide();
	 		$("#mainsearch_result_fashion").hide();			
  		}
		function mainsearchNextPage(name){
			//alert("click="+name);mainsearch_brand_search_loading_btn
			if(name=="brand"){
				keywordSearchLoadHtml("/svc/search/web/kr/searchBrandList.do" ,"#mainsearch_brand_num","#mainsearch_brand_search",mainsearch_brand_cnt,6);
			}else if(name=="window"){
				keywordSearchLoadHtml("/svc/search/web/kr/searchShowWindowList.do" ,"#mainsearch_show_window_num","#mainsearch_window_search",mainsearch_window_cnt,4);
			}else if(name=="fashion"){
				keywordSearchLoadHtml("/svc/search/web/kr/searchFashionStyleList.do" ,"#mainsearch_fashion_style_num","#mainsearch_fashion_search",mainsearch_fashion_cnt,5);
			}
		}
		function keywordSearchLoadHtml(url ,pageName,divName,cnt,limit){
			//alert(url)
			//$("#fashionNextPage").show();
			$(divName+"_btn").hide();
			$(divName+"_loading_btn").show();

			if(pageName=="#mainsearch_brand_num"){
				startMotion($('.motion-loading'));
			}else if(pageName=="#mainsearch_show_window_num"){
				startMotion($('.motion-loading2'));
			}else if(pageName=="#mainsearch_fashion_style_num"){
				startMotion($('.motion-loading3'));
			}
			$.ajax( // 화면의 데이타를 조회한다.
				{
					url:url,
					dataType:"html",
					type:"POST",
		 			data:{
						curr_page :  $(pageName).val()
						,search_param2 : search_frame_keyword

					},
					success:function( data ) {
						data = $.trim(data) ;
						 //alert("data="+data.length);

		 				if( data.length > 50 ){
							if( cnt > 0 ){
								//alert(" $(pageName).val() "+ $(pageName).val() )
			 					if( $(pageName).val() ==  "1"){
									$(divName).html(data);
				 				}else{
				 					$(divName).append(data);
				 				}
								$(divName+"_box").show();
 								if( cnt > limit ){
									$(divName+"_btn").show();
								}
								$(divName+"_btn").show();
								$(pageName).val(parseInt($(pageName).val())+1);

		 					}
						}else if(  $(pageName).val() != 1 ){
							alert("마지막 페이지 입니다.");
						}else{
							//검색결과가 없습니다.
							alert("검색결과가 없습니다.");

						}
		 				$(divName+"_btn").show();
		 				$(divName+"_loading_btn").hide();

		 				if(pageName=="#mainsearch_brand_num"){
							bSearching1 = false;
		 				}else if(pageName=="#mainsearch_show_window_num"){
							bSearching2 = false;
		 				}else if(pageName=="#mainsearch_fashion_style_num"){
							bSearching3 = false;
		 				}

						//$("#fashionNextPage").hide();

							//{"dataObject":{"mainsearch_total_cnt":575,"brand_cnt":479,"show_cnt":75,"fashion_cnt":21}}
					},
					error : function( e ) {
						//alert("조회 오류\n"+e.status);
						//$("#fashionNextPage").hide();
 		 				$(divName+"_btn").show();
		 				$(divName+"_loading_btn").hide();
		 				if(pageName=="#mainsearch_brand_num"){
							bSearching1 = false;
		 				}else if(pageName=="#mainsearch_show_window_num"){
							bSearching2 = false;
		 				}else if(pageName=="#mainsearch_fashion_style_num"){
							bSearching3 = false;
		 				}
					}
				}
			);

		}


	});
};
