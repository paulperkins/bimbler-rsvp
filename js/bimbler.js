    
/*
 * Bimbler JavaScript - helper JS / JQuery for RSVP functionality.
 */

jQuery(document).ready(function ($) {
	
	  	$('#linky').masonry({ singleMode: true });
	
		
		$('.rsvp-checkin-container').click (function () {
		
			var rsvp_id = $(this).attr('id');

			//alert ('Clicked ' + person_clicked);
					
			var debug_out = $("#bimbler-rsvp-debug");
			
			debug_out.html ('<p>You clicked ' + rsvp_id + '</p>');
			
			var indicators = ['<div class="rsvp-checkin-indicator-none"><i class="fa-question-circle"></i></div>',
			                  '<div class="rsvp-checkin-indicator-yes"><i class="fa-check-circle"></i></div>',
			                  '<div class="rsvp-checkin-indicator-no"><i class="fa-times-circle"></i></div>'];
			
			var wait = '<div class="rsvp-checkin-indicator-wait"><i class="fa fa-spinner fa-spin"></i></div>';
			
			var pick = Math.floor(Math.random()*(2-0+1)+0);
			var indicator = $("#rsvp-checkin-indicator-" + rsvp_id);
			
			// Set the indicator to an animation.
			indicator.html (wait);

            $.post(
            		RSVPAjax.ajaxurl,
            		{
            			action: 	'rsvpajax-submit', 
            			container: 	rsvp_id
            		},
            		function (response) {
            			console.log (response);
            			indicator.html(response);
            		}
            );
			//alert ('Called Ajax?');
		});
		
	
	
/*	    // This function will be executed when the user scrolls the page.
		$(window).scroll(function(e) {
		    // Get the position of the location where the scroller starts.
		    var scroller_anchor = $(".bimbler_scroll_anchor").offset().top;
		     
		    // Check if the user has scrolled and the current position is after the scroller start location and if its not already fixed at the top
		    if ($(this).scrollTop() >= scroller_anchor && $('.scroller').css('position') != 'fixed')
		    {    // Change the CSS of the scroller to hilight it and fix it at the top of the screen.
		        $('.alx-tabs-container').css({
		            //'background': '#CCC',
		            //'border': '1px solid #000',
		            'position': 'fixed',
		            'top': '0px'
		        });
		        // Changing the height of the scroller anchor to that of scroller so that there is no change in the overall height of the page.
		        $('.bimbler_scroll_anchor').css('height', '50px');
		    }
		    else if ($(this).scrollTop() < scroller_anchor && $('.alx-tabs-container').css('position') != 'relative')
		    {    // If the user has scrolled back to the location above the scroller anchor place it back into the content.
		         
		        // Change the height of the scroller anchor to 0 and now we will be adding the scroller back to the content.
		        $('.bimbler_scroll_anchor').css('height', '0px');
		         
		        // Change the CSS and put it back to its original position.
		        $('.alx-tabs-container').css({
		            //'background': '#FFF',
		            //'border': '1px solid #CCC',
		            'position': 'relative'
		        });
		    }
		}); */
	
});
    