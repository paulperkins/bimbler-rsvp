<?php
/**
 * Bimbler RSVP Settings
 *
 * @package   Bimbler_RSVP
 * @author    Paul Perkins <paul@paulperkins.net>
 * @license   GPL-2.0+
 * @link      http://bimblers.com/plugins
 * @copyright 2015 Paul Perkins
 */


/*
 * TODO: 
 *  - Maximum number of guest RSVPs.
 *  - Enable / disable tabs individually on single_event page.
 *  - Date format strings.
 *  - Display (and validate) Acceptance of Risk checkbox.
 *  - Text used in toastr popups.
 *  
 */

/*
 * 
 */
function bimbler_rsvp_create_admin_menu () {

	// Always add the top-level menu page as a container for other plugins.
/*	add_menu_page( 	'Bimblers',
					'Bimblers',
					'manage_options',
					'bimblers',
					'bimbler_display_options_page'); */
	
	add_submenu_page( 'bimblers', 
						'Bimbler RSVP', 
						'Bimbler RSVP', 
						'manage_options', 
						'bimbler-rsvp', 
						'bimbler_rsvp_display_options_page');
	
}


function bimbler_display_options_page () {

	?>
	<div class="wrap">
	
		<h2>Bimbler Plugin Options</h2>	
	
    	<?php settings_errors(); ?>
         
         <p>Welcome to the Bimblers plugin options page.</p>
		
	</div>

<?php 
}

function bimbler_rsvp_display_options_page () {
	
?>
	<div class="wrap">
	
		<h2>Bimbler RSVP Options</h2>	
	
    	<?php settings_errors(); ?>
         
        <form method="post" action="options.php">
            <?php settings_fields( 'bimbler_rsvp_options' ); ?>
            <?php do_settings_sections( 'bimbler_rsvp_options' ); ?>         
            <?php submit_button(); ?>
		</form>
		
	</div>

<?php 
}


function bimbler_rsvp_create_settings () {

	//error_log ('bimbler_create_rsvp_settings: Started.');
	
	// If the options don't exist, create them.
	if( false == get_option( 'bimbler_rsvp_options' ) ) {
		add_option( 'bimbler_rsvp_options' );
	}
	
	// First, we register a section. This is necessary since all future options must belong to one.
	add_settings_section(
		'general_settings_section',         // ID used to identify this section and with which to register options
		'Sandbox Options',                  // Title to be displayed on the administration page
		'sandbox_general_options_callback', // Callback used to render the description of the section
		'bimbler_rsvp_options'              // Page on which to add this section of options
	);
	
	add_settings_field(
		'show_header',                      // ID used to identify the field throughout the theme
		'Header',                           // The label to the left of the option interface element
		'sandbox_toggle_header_callback',   // The name of the function responsible for rendering the option interface
		'bimbler_rsvp_options',                          // The page on which this option will be displayed
		'general_settings_section',         // The name of the section to which this field belongs
		array(                              // The array of arguments to pass to the callback. In this case, just a description.
		'Activate this setting to display the header.'
				)
	);
	
	// Finally, we register the fields with WordPress
	register_setting(
		'bimbler_rsvp_options',
		'bimbler_rsvp_options'
			);
}


/**
 * This function provides a simple description for the General Options page.
 *
 * It is called from the 'sandbox_initialize_theme_options' function by being passed as a parameter
 * in the add_settings_section function.
 */
function sandbox_general_options_callback() {
	echo '<p>Select which areas of content you wish to display.</p>';
} // end sandbox_general_options_callback

/**
 * This function renders the interface elements for toggling the visibility of the header element.
 *
 * It accepts an array of arguments and expects the first element in the array to be the description
 * to be displayed next to the checkbox.
 */
function sandbox_toggle_header_callback($args) {
     
    // First, we read the options collection
    $options = get_option('bimbler_rsvp_options');
    
    //error_log ('Options: ' . print_r ($options, true));
     
    // Next, we update the name attribute to access this element's ID in the context of the display options array
    // We also access the show_header element of the options collection in the call to the checked() helper function
    $html = '<input type="checkbox" id="show_header" name="bimbler_rsvp_options[show_header]" value="1" ' . checked(1, (isset ($options['show_header']) ? $options['show_header'] : ''), false) . '/>'; 
     
    // Here, we'll take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="show_header"> '  . $args[0] . '</label>'; 
     
    echo $html;
     
} // end sandbox_toggle_header_callback

?>