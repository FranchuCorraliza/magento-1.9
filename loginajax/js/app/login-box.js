(function($) {
	$(document).ready(function() {
		// Opens a modal window when clicking the "My Account" link
		// If #login-modal exists, then open the lightbox
		// Refer to lightbox_me jQuery Plugin (http://buckwilson.me/lightboxme/) for settings and documentation
		$('.links a:contains("My Account")').click(function(e) {
			if($('#login-modal').length) {
				$('#login-modal').lightbox_me({
					centered: true,
					onLoad: function() {
						initLoginBox();
					}
				});
 
				e.preventDefault();
			}
		});
	});
 
 	// Activates events on the login form & handles AJAX form posts with JSON data return
	function initLoginBox() {
		$('#already-registered-link').click(function(e) {
			$('#signup-box').hide();
			$('#login-box').show();
			e.preventDefault();
		});
 
		$('#need-account-link').click(function(e) {
			$('#signup-box').show();
			$('#login-box').hide();
			e.preventDefault();
		});
 
		$('#signup-form').unbind().submit(function() {
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					window.location.reload();
				} else {
					for(var i = 0; i < data.exceptions.length; i++) {
						alert(data.exceptions[i]);
					}
				}
			}, 'json');
		});
 
		$('#login-form').unbind().submit(function() {
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					window.location.reload();
				} else {
					for(var i = 0; i < data.exceptions.length; i++) {
						alert(data.exceptions[i]);
					}
				}
			}, 'json');
		});
	}
})(jQuery);