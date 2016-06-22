
function slideContentChange(args) {
	
	/* indicator */
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button').removeClass('selected');
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button:eq(' + args.currentSlideNumber + ')').addClass('selected');

	jQuery('.sliderNavi .naviItem').removeClass('selected');
	jQuery('.sliderNavi .naviItem:eq(' + args.currentSlideNumber + ')').addClass('selected');

}

function slideContentChange2(args) {
	
	/* indicator */
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button').removeClass('selected');
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button:eq(' + args.currentSlideNumber + ')').addClass('selected');

	jQuery('.sliderNavi2 .naviItem2').removeClass('selected');
	jQuery('.sliderNavi2 .naviItem2:eq(' + args.currentSlideNumber + ')').addClass('selected');

}

function slideContentChange3(args) {
	
	/* indicator */
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button').removeClass('selected');
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button:eq(' + args.currentSlideNumber + ')').addClass('selected');

	jQuery('.sliderNavi3 .naviItem3').removeClass('selected');
	jQuery('.sliderNavi3 .naviItem3:eq(' + args.currentSlideNumber + ')').addClass('selected');

}

function slideContentChange4(args) {
	
	/* indicator */
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button').removeClass('selected');
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button:eq(' + args.currentSlideNumber + ')').addClass('selected');

	jQuery('.sliderNavi4 .naviItem4').removeClass('selected');
	jQuery('.sliderNavi4 .naviItem4:eq(' + args.currentSlideNumber + ')').addClass('selected');

}

function slideContentComplete(args) {
	
	/* animation */
	jQuery(args.sliderObject).find('.text1, .text2').attr('style', '');
	
	jQuery(args.currentSlideObject).children('.text1').animate({
		right: '70px',
		opacity: '1'
	}, 400, 'easeOutQuint');
	
	jQuery(args.currentSlideObject).children('.text2').delay(200).animate({
		right: '70px',
		opacity: '1'
	}, 400, 'easeOutQuint');
	
}

function slideContentLoaded(args) {
	
	/* animation */
	jQuery(args.sliderObject).find('.text1, .text2').attr('style', '');
	
	jQuery(args.currentSlideObject).children('.text1').animate({
		right: '70px',
		opacity: '1'
	}, 400, 'easeOutQuint');
	
	jQuery(args.currentSlideObject).children('.text2').delay(200).animate({
		right: '70px',
		opacity: '1'
	}, 400, 'easeOutQuint');
	
	/* indicator */
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button').removeClass('selected');
	jQuery(args.sliderObject).parent().find('.iosSliderButtons .button:eq(' + args.currentSlideNumber + ')').addClass('selected');
	
}
slide_on=true;