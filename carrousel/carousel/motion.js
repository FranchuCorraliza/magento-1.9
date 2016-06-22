/* 
 * Author: Chees
 * Description: Motion Object js
 */

 // var loops = {};

$(function(){
	$('.anim-obj').each(function(){
		var obj = $(this);
		startMotion(obj);
	});
});


function startMotion(obj) {
	var frame = $('> .frame', obj)[0];
	var fps = obj.attr('fps');
	var frames = obj.attr('frames');
	var imgW = obj.attr('img-width');
	var imgH = obj.attr('img-height');
	var motio = new Motio(frame, {
		fps: fps,
		frames: frames,
		width: imgW,
		height: imgH,
		vertical: true,
		jQuery: true
	});
	motio.play();
	frame.motio = motio;
}

function stopMotion(obj) {
	var frame = $('> .frame', obj)[0];
	frame.motio.toStart(true);
}


function resumeMotion(obj) {
	var frame = $('> .frame', obj)[0];
	frame.motio.play();
}