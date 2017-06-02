/* Notification Pop up*/
(function ($) {
	var _notif_loaded = false,
		_notif_loading = false,
		_base_url, $_notif_pop, $loader;
	$(document).ready(function() {
		$notif_pop = $('#js-header-notif-pop');
		_base_url = $notif_pop.data('base_url');
		$loader = $notif_pop.find('.loader-container');

		$('#js-header-notif-menu').on('mouseover', function() {
			$notif_pop.show();
			if (!_notif_loaded && !_notif_loading) {
				showNotifLoader();
				loadNotifs();
			}
		});
		$('#js-header-notif-menu').on('mouseout', function() {
			$notif_pop.hide();
		});
		$('#js-header-notif-pop').on('click', '.icon-rem', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var id = $(this).data('id');
			deleteNotif(id);
		});
	});

	var loadNotifs = function() {
		_notif_loading = true;
		jQuery.ajax({
			url: _base_url + 'ajax_load',
			method: 'post',
			dataType: 'json',
			cache: true
		}).done(function(response) {
			if (response.status == 'success' && response.notif_html) {
				$notif_pop.find('.notif-results-main').html(response.notif_html);
			}
		}).fail(function() {

		}).always(function() {
			_notif_loaded = true;
			_notif_loading = false;
			hideNotifLoader();
		});
	};

	var deleteNotif = function(id) {
		jQuery.ajax({
			url: _base_url + 'notif_del',
			method: 'post',
			dataType: 'json',
			data: {id:id}
		}).done(function(response) {
			if (response.status == 'success') {
				var $item = $(".icon-rem[data-id='" + id + "']");
				$item.closest('.article-link').slideUp();
				$('.js-notif-unread-count').text(response.unread_count);
			} else {
				alert('Some error occurred.');
			}
		}).fail(function() {

		}).always(function() {
		});
	};

	var showNotifLoader = function() {
		$loader.show();
	};
	var hideNotifLoader = function() {
		$loader.hide();
	};

})(jQuery);
/* Notification Pop up*/
