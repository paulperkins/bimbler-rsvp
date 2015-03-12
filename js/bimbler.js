    
/*
 * Bimbler JavaScript - helper JS / JQuery for RSVP functionality.
 */

jQuery(document).ready(function ($) {
	
		
	/*  Tabs widget
	/* ------------------------------------ */	
	(function() {
		var $tabsNav       = $('.bimbler-tabs-nav'),
			$tabsNavLis    = $tabsNav.children('li'),
			$tabsContainer = $('.bimbler-tabs-container');

		$tabsNav.each(function() {
			var $this = $(this);
			$this.next().children('.bimbler-tab').stop(true,true).hide()
			.siblings( $this.find('a').attr('href') ).show();
			$this.children('li').first().addClass('active').stop(true,true).show();
		});

		$tabsNavLis.on('click', function(e) {
			var $this = $(this);

			$this.siblings().removeClass('active').end()
			.addClass('active');
			
			$this.parent().next().children('.bimbler-tab').stop(true,true).hide()
			.siblings( $this.find('a').attr('href') ).fadeIn();
			e.preventDefault();
		}).children( window.location.hash ? 'a[href=' + window.location.hash + ']' : 'a:first' ).trigger('click');

	})();
	
});
    