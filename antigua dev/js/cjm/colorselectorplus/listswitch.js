// Copyright CJM Creative Designs
// Function to switch list and grid view images

function listSwitcher(a, id, src, lk) {
	
	//Set base image
	if (src) { $$('#the-' + id + ' a.product-image img').first().setAttribute("src", src); }
	
	//Set selected swatch
	$('ul-attribute' + lk + '-' + id).select('img', 'div').invoke('removeClassName', 'swatchSelected');
	a.addClassName('swatchSelected'); 
}
