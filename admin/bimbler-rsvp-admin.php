<?php
/**
 * Bimbler RSVP Admin
 *
 * @package   Bimbler_RSVP
 * @author    Paul Perkins <paul@paulperkins.net>
 * @license   GPL-2.0+
 * @link      http://bimblers.com/plugins
 * @copyright 2015 Paul Perkins
 */



/*
 * 
 */
function bimbler_rsvp_add_rsvp_meta_box () {

	
	add_meta_box(
		'bimbler_rsvp_meta_box',
		'RSVPs',
		'bimbler_rsvp_rsvp_meta_callback',
		TribeEvents::POSTTYPE
	);	
}


/*
 * 
 */
function bimbler_rsvp_rsvp_meta_callback () {

	global $wpdb;

	$li_style = 'style="margin: initial; padding: 0 0 0 20px; list-style-type: disc; display: list-item; list-style-position: inside; border-bottom-style: none;"';
	
	$rsvp_yes = Bimbler_RSVP::get_instance()->get_event_rsvp_object (get_the_ID(), 'Y');
	$rsvp_no = Bimbler_RSVP::get_instance()->get_event_rsvp_object (get_the_ID(), 'N');
	
	$html = '';
	
	foreach (['Y', 'N'] as $rsvp) {
	
		if ('Y' == $rsvp) {
			$rsvp_object = $rsvp_yes;
			 
			$html .= '<p>RSVP Yes: ' . count ($rsvp_object) . '</p>';
		} else {
			$rsvp_object = $rsvp_no;
				
			$html .= '<p>RSVP No: ' . count ($rsvp_object) . '</p>';
		}
		
		
		if (!empty ($rsvp_object)) {
		
			$html .= '<ul>' . PHP_EOL;
			
			foreach ($rsvp_object as $r) {
			
				$user_object = get_userdata ($r->user_id);
			
				$html .= '<li ' . $li_style . '>' . $user_object->first_name . ' ' . $user_object->last_name . '</li>'; 
			}
		
			$html .= '</ul>' . PHP_EOL;
		}
		
	}
	
	echo $html;
}

add_action( 'add_meta_boxes', 'bimbler_rsvp_add_rsvp_meta_box' );


?>