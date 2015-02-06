<?php 
    /*
    Plugin Name: Bimbler RSVP
    Plugin URI: http://bimblers.com/plugins
    Description: Plugin for managing RSVPs for events.
    Author: Paul Perkins
    Version: 0.1
    Author URI: http://www.paulperkins.net
    */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
        die;
} // end if

require_once( plugin_dir_path( __FILE__ ) . 'class-bimbler-rsvp.php' );


Bimbler_RSVP::get_instance();
