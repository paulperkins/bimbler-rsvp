<?php
/**
 * Bimbler RSVP
 *
 * @package   Bimbler_RSVP
 * @author    Paul Perkins <paul@paulperkins.net>
 * @license   GPL-2.0+
 * @link      http://bimblers.com/plugins
 * @copyright 2014 Paul Perkins
 */

global $rsvp_db_version;
$rsvp_db_version = "0.3";

global $rsvp_db_table;
$rsvp_db_table = "bimbler_rsvp";
//$rsvp_db_table = "new_rsvp";

global $rsvp_comment_len;
$rsvp_comment_len = 128;

global $bimbler_timezone;
$bimbler_timezone = 'Australia/Brisbane';

/**
 * Include dependencies necessary.
 *
 */

//if ( current_user_can( 'manage_options' ) )  {
	// Settings page.
//	require_once( plugin_dir_path( __FILE__ ) . 'admin/bimbler-rsvp-settings.php' );

	// Meta boxes.	
//	require_once( plugin_dir_path( __FILE__ ) . 'admin/bimbler-rsvp-admin.php' );
//}




/**
 * Bimbler RSVP
 *
 * @package Bimbler_RSVP
 * @author  Paul Perkins <paul@paulperkins.net>
 */
class Bimbler_RSVP {

		public $pluginPath;
	
		public $email_html_head = '
<!DOCTYPE html> 
<html lang="en-US">

<head>
	<meta charset=\"UTF-8\">';
		
		public $email_style = "
		<style type=\"text/css\">
		body { font-family: 'Titillium', Arial, sans-serif; font-size: 18px; xline-height: 1.6em; }
 		p{ margin-bottom: 1em; font-family: 'Titillium', Arial;}
@font-face {
	font-family: 'Titillium';
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-light-webfont.eot');
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-light-webfont.svg#titillium-light-webfont') format('svg'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-light-webfont.eot?#iefix') format('embedded-opentype'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-light-webfont.woff') format('woff'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-light-webfont.ttf') format('truetype');
	font-weight: 300;
	font-style: normal;
}
@font-face {
	font-family: 'Titillium';
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-lightitalic-webfont.eot');
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-lightitalic-webfont.svg#titillium-lightitalic-webfont') format('svg'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-lightitalic-webfont.eot?#iefix') format('embedded-opentype'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-lightitalic-webfont.woff') format('woff'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-lightitalic-webfont.ttf') format('truetype');
	font-weight: 300;
	font-style: italic;
}
@font-face {
	font-family: 'Titillium';
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regular-webfont.eot');
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regular-webfont.svg#titillium-regular-webfont') format('svg'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regular-webfont.eot?#iefix') format('embedded-opentype'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regular-webfont.woff') format('woff'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regular-webfont.ttf') format('truetype');
	font-weight: 400;
	font-style: normal;
}
@font-face {
	font-family: 'Titillium';
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regularitalic-webfont.eot');
	src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regularitalic-webfont.svg#titillium-regular-webfont') format('svg'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regularitalic-webfont.eot?#iefix') format('embedded-opentype'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regularitalic-webfont.woff') format('woff'),
		 url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-regularitalic-webfont.ttf') format('truetype');
	font-weight: 400;
	font-style: italic;
}
@font-face {
    font-family: 'Titillium';
    src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-semibold-webfont.eot');
    src: url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-semibold-webfont.svg#titillium-semibold-webfont') format('svg'),
         url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-semibold-webfont.eot?#iefix') format('embedded-opentype'),
         url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-semibold-webfont.woff') format('woff'),
         url('http://bimblers.com/wp-content/themes/hueman/fonts/titillium-semibold-webfont.ttf') format('truetype');
	font-weight: 600;
	font-style: normal;
}			
		</style>	
			";
		
		public $p_style = '<p style="font-size:13px;line-height:18px;margin:0 0 10px;">';
		
		public $email_end_head = '</head><body>';
		
		public $email_html_foot = '</body></html>';
		
	
        /*--------------------------------------------*
         * Constructor
         *--------------------------------------------*/

        /**
         * Instance of this class.
         *
         * @since    1.0.0
         *
         * @var      object
         */
        protected static $instance = null;

        /**
         * Return an instance of this class.
         *
         * @since     1.0.0
         *
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

                // If the single instance hasn't been set, set it now.
                if ( null == self::$instance ) {
                        self::$instance = new self;
                } // end if

                return self::$instance;

        } // end get_instance

        /**
         * Notices to be displayed
         * @var array
         */
        protected $notices = array();


        /**
         * Define an admin notice
         *
         * @param string $key
         * @param string $notice
         * @return bool
         */
        public static function setNotice( $key, $notice ){
        	self::get_instance()->notices[ $key ] = $notice;
        	
        	//error_log ('Notices: ' . print_r (self::get_instance()->notices, true));
        	return true;
        }
        
        /**
         * Check to see if an admin notice exists
         *
         * @param string $key
         * @return bool
         */
        public static function isNotice( $key ) {
        	return !empty( self::get_instance()->notices[ $key ] ) ? true : false ;
        }
        
        /**
         * Remove an admin notice
         *
         * @param string $key
         * @return bool
         */
        public static function removeNotice( $key ){
        	if ( self::isNotice($key)) {
        		unset( self::get_instance()->notices[ $key ] );
        		return true;
        	} else {
        		return false;
        	}
        }
        
        /**
         * Get the admin notices
         *
         * @return array
         */
        public static function getNotices(){
        	return self::get_instance()->notices;
        }
        
        /**
         * Email Class.
         *
         * @return Bimbler_Email
         */
        public function mailer() {
//        	return Bimbler_Emails::instance();
        	return null;
        }
        

        private function _debug_log( $message )
        {
        	if( WP_DEBUG === true )
        	{
        		if( is_array( $message ) || is_object( $message ) ){
        			error_log( print_r( $message, true ) );
        		} else {
        			error_log( $message );
        		}
        	}
        }
        
        
        /**
         * Initializes the plugin by setting localization, admin styles, and content filters.
         */
        private function __construct() {
        	
        	//error_log ('In constructor');

        	$this->pluginPath = trailingslashit( dirname( dirname(__FILE__) ) ) .'bimbler-rsvp';
        	
        	// Hook to save RSVP data.
        	add_action( 'init' , array( $this, 'process_rsvp' ) );
			
        	
        	// Hook to display notices (before HTML is fully rendered - use JavaScript).
        	//add_action( 'tribe_events_before_html' , array( $this, 'show_notices' ) );

        	add_action( 'wp_footer' , array( $this, 'show_notices' ),100 );

        	// Add the RSVP buttons to the Event form.
        	//add_action( 'tribe_events_after_html' , array( $this, 'add_rsvp_form' ) );
        	
        	// Flow the ride page into the event page.
        	//add_action( 'tribe_events_after_html' , array( $this, 'show_ride_page' ) );
        	 
        	// Show the gallery.
        	//add_action( 'tribe_events_after_html' , array( $this, 'show_gallery' ) );
        	
        	// Show event hosts on the Event admin page.
        	add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_hosts' ) );
        	 
        	// Show the rides pages section on the Event admin page.
        	add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_admin_rides' ) );
        	 
        	// Show galleries section on the Event admin page.
        	add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_galleries' ) );
        	
        	// Show ride difficulty section on the Event admin page.
        	//add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_difficulty' ) );

        	// Show 'RSVPs Open' section on the Event admin page.
        	add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_rsvps_open' ) );
        	
        	// Show RWGPS map section on the Event admin page.
        	add_action( 'tribe_events_details_table_bottom' , array( $this, 'show_event_rwgps_map' ) );
        	
        	// Append ride map to ride pages.
        	add_action( 'the_content' , array( $this, 'add_ride_map' ) );
        	 
        	// Create the DB table if it doesn't exist.
        	register_activation_hook( __FILE__, array($this, 'rsvp_install' ) );

			// TODO: This can be removed once DB table structure stabilised.
        	$this->rsvp_install();

        	// Add stylesheet.
        	add_action( 'wp_enqueue_scripts', array ($this, 'add_stylesheet') );

        	// Add settings menu. Contained in bimbler-rsvp-settings.php.
        	//add_action( 'admin_menu', array ($this, 'create_admin_menu') );
//        	add_action( 'admin_menu', 'bimbler_rsvp_create_admin_menu');
        	
        	// Set up the settings. Contained in bimbler-rsvp-settings.php.
//        	add_action ('admin_init', 'bimbler_rsvp_create_settings');
        	 
        	// Save ride page in event page.
        	add_action( 'tribe_events_update_meta', array( $this, 'tribe_events_save_ride_page' ), 30, 2 );
        	
        	// Save gallery in event page.
        	add_action( 'tribe_events_update_meta', array( $this, 'tribe_events_save_gallery' ), 30, 2 );
        	
        	// Save RSVPs Open status in event page.
        	add_action( 'tribe_events_update_meta', array( $this, 'tribe_events_save_rsvps_open' ), 30, 2 );

        	// Save RWGPS in event page.
        	add_action( 'tribe_events_update_meta', array( $this, 'tribe_events_save_rwgps' ), 30, 2 );
        	
        	// Save hosts in event page.
        	add_action( 'tribe_events_update_meta', array( $this, 'tribe_events_save_hosts' ), 30, 2 );
        	
        	// TODO: Move to plugin.
        	add_action('wp_insert_comment', array ($this, 'comment_inserted'),50,2);
        	 
        	// Remove color scheme selection from profile page.
        	add_action('admin_head', array ($this, 'remove_admin_color_scheme'));
        	
        	// Monitor when photos are uploaded by users.
        	add_action ('ngg_after_new_images_added', array ($this, 'photo_uploaded'), 50 , 2);
        	
        	// Send welcome message to new users.
        	//add_action( 'edit_user_profile', array ($this, 'profile_updated'));

        	// Send welcome message to new users.
			add_action( 'new_user_approve_user_approved', array ($this, 'new_user_approved'));
        	
        	// Change 'Leave a Reply' on comment form.
        	add_filter('comment_form_defaults', array ($this, 'comment_reform'));

			// Display ride hosts as if they were part of the Events Calendar meta.
			add_filter ('tribe_events_single_event_the_meta_addon', array ($this, 'add_host_meta'));
        	
        	add_action ('woocommerce_admin_reports', array ($this, 'bimbler_add_order_report'), 99, 1);
        	
        	// Record each login.
        	add_action( 'wp', array ($this, 'wp_complete') );
        	
        	// Work around date UTC flaw.
        	add_action( 'init', array ($this, 'set_timezone'));
        	
			// Add Open Graph tags.
        	add_action('wp_head',array ($this, 'add_opengraph_tags'));

        	// Add style from settings rather than hard-coded in CSS.
        	add_action('wp_head',array ($this, 'add_dynamic_style'));
			
        	// Prevent cost fields from being displayed in editor.
			// TODO: should be named admin_SHOW_cost_field.
        	add_filter('tribe_events_admin_show_cost_field', array ($this, 'tribe_events_admin_hide_cost_field'));
			
			// Block spam comments - where author is not set.
			add_filter ('pre_comment_approved', array ($this, 'bimbler_validate_comment'), 50, 2);


			// Make sure iCal link returns events we want it to.
			add_filter ('tribe_ical_feed_month_view_query_args', array ($this, 'bimbler_tribe_ical_query'), 50, 2);


			// Shortcodes.

			// Full-width rendering of home page.
			add_shortcode( 'bimbler_render_full_width', array ($this, 'bimbler_render_full_width'));

        	 
        	// Widgets.
        	require_once( $this->pluginPath.'/widgets/bimbler-widgets.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-tabs.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-widget-sales.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-useradmin-widget.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-next-on.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-noodle.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-mobile-widget.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-edit-attendees.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-join-us.php' );
        	require_once( $this->pluginPath.'/widgets/bimbler-download-gps.php' );
//        	require_once( $this->pluginPath.'/widgets/bimbler-comments.php' );
        	 
        	add_action( 'widgets_init', array ($this, 'register_bimbler_rsvp_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_tabs_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_sales_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_useradmin_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_noodle_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_nexton_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_mobile_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_edit_attendees_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_join_us_widget') );
        	add_action( 'widgets_init', array ($this, 'register_bimbler_download_gps_widget') );
//        	add_action( 'widgets_init', array ($this, 'register_bimbler_comments_widget') );
        	 
		} // End constructor.
		

		/**
		 * Install the plugin - create RSVP table.
		 *
		 */
		function rsvp_install() {
			global $wpdb;
			global $rsvp_db_version;
			global $rsvp_db_table;
				
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
			event bigint (20) NOT NULL,
			user_id bigint (20) NOT NULL,
			rsvp char(1) NOT NULL,
			comment varchar(128),
			attended char (1),
			no_show char (1),
			email_notifications char(1) NOT NULL DEFAULT 'Y',
			guests INT NOT NULL DEFAULT 0,
			UNIQUE KEY id (id)
			);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		
			add_option( "rsvp_db_version", $rsvp_db_version );
  		}

  		/**
  		 * Enqueue plugin style-file
  		 */
  		function add_stylesheet() {
//  			wp_register_style( 'bimbler-rsvp-style', plugins_url('style.css', __FILE__) );
  			wp_register_style( 'bimbler-rsvp-style', plugins_url('style.css?v=' . time(), __FILE__) );
  			wp_enqueue_style( 'bimbler-rsvp-style' );
  			
  			wp_register_script ('bimbler-rsvp-script', plugin_dir_url( __FILE__ ) . 'js/bimbler.js', array( 'jquery' ) );
  			wp_enqueue_script( 'bimbler-rsvp-script');

			// Load FontAwesome.			  
	  		wp_enqueue_style('font-awesome-min', plugin_dir_url( __FILE__ ) .  'fa/css/font-awesome.min.css');

  			wp_register_style( 'bimbler-toastr-style', plugins_url('toastr.css', __FILE__) );
  			wp_enqueue_style( 'bimbler-toastr-style' );
  			
  			wp_register_script ('bimbler-toastr-script', plugin_dir_url( __FILE__ ) . 'js/toastr.js', array( 'jquery' ) );
  			wp_enqueue_script( 'bimbler-toastr-script');

// Commented for now - required for comments sidebar widget.

//  			wp_register_script ('bimbler-bootstrap-script', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ) );
//  			wp_enqueue_script( 'bimbler-bootstrap-script');

//			wp_enqueue_script( 'jquery' );

//  			wp_register_script ('bimbler-comments-script', plugin_dir_url( __FILE__ ) . 'js/bimbler-comments.js', array( 'jquery' ), false, true );
//  			wp_enqueue_script( 'bimbler-comments-script');
			  
  			// Select2 - event host multi-select.
  			// TODO: Move into admin code.
  			
  			// Only load if an admin user / can update eevents.
			if ($this->can_modify_attendance (get_queried_object_id())) {
	  			wp_register_style( 'bimbler-select2-style', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css' );
	  			wp_enqueue_style( 'bimbler-select2-style' );
	  			
	  			wp_register_script ('bimbler-select2-script', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js', array( 'jquery' ) );
	  			wp_enqueue_script( 'bimbler-select2-script');
  			} 
  		}
  		
  		/*
  		 * TODO: Move this to bimbler-users plugin.
  		 */
  		public function wp_complete() {
  			global $current_user;
  			//get_currentuserinfo();
			wp_get_current_user ();
  		
  			// User ID
  			$user_id = $current_user->ID;
  		
  			update_user_meta( $user_id, 'wp-last-login', time() );
  			update_user_meta ($user_id, 'bimbler-login-ip', $_SERVER["REMOTE_ADDR"]);
  		}
  		
  		/**
  		 *
  		 */
  		function comment_reform ($arg) {
  			$arg['title_reply'] = __('Leave a comment');
  			return $arg;
  		}
		  
		function bimbler_get_avatar_img ($avatar) {
		
			preg_match( '#src=["|\'](.+)["|\']#Uuis', $avatar, $matches );
		
			return ( isset( $matches[1] ) && ! empty( $matches[1]) ) ?
			(string) $matches[1] : '';
		}

	/*
	 *
	*/
	function get_venue_address ($event_id) {
			
		$locationMetaSuffixes = array( 'address', 'city', 'region', 'zip', 'country' );
		$address = "";
			
		$address .= tribe_get_address ($event_id);
		$address .= ' ' . tribe_get_city ($event_id);
		$address .= ' ' . tribe_get_region ($event_id);
		$address .= ' ' . tribe_get_zip ($event_id);
		$address .= ' ' . tribe_get_country ($event_id);
	
		return trim($address);
	}

		// Show ride hosts in the Events Calendar meta box.
		function add_host_meta ($content) {
			
			$post_id = get_queried_object_id();

			$meta_hosts_json = get_post_meta ($post_id, 'bimbler_ride_hosts', true);

  			if (0 < strlen ($meta_hosts_json)) {

				$meta_hosts = json_decode($meta_hosts_json);
				  
				if (count($meta_hosts) > 0) {

					$content .= '<div class="tribe-events-meta-group tribe-events-meta-group-organizer vcard" style="width:100%;">' . PHP_EOL;
					$content .= '	<h3 class="tribe-events-single-section-title">Host</h3>' . PHP_EOL;

					$content .= '<div id="AvatarListSide" class="AvatarListSide-wrap">' . PHP_EOL;

					$content .= '		    <ul>' . PHP_EOL;
			
					foreach ($meta_hosts as $host) {

						$user_info   = get_userdata ($host);

						$avatar = '';
							
						if (isset ($user_info->user_login)) {
							$avatar .= get_avatar ($user_info->id, null, null, $user_info->user_login);
						}
	
						
						$content .= '<li class="AvatarListSide bimbler-avatar-narrow bimbler-avatar-border">' . PHP_EOL;
						$content .= '							<img src="' . bimbler_get_avatar_img($avatar) . '" style="width:64 !important;  height:64 !important;" class="avatar avatar-96 wp-user-avatar wp-user-avatar-96 alignnone photo bimbler-whoswho-marker" ';
						$content .= 'id="user-avatar-' . $user_info->id . '" data-user-id="' . $user_info->id . '">' . PHP_EOL;

						if (isset ($user_info->user_nicename)) {
							$content .= '<p><a href="/profile/' . urlencode ($user_info->user_nicename) .'/">' . $user_info->nickname . '</a></p>' . PHP_EOL;
						}

						$content .= '</li>' . PHP_EOL;
					}
					
					$content .= '		    </ul>' . PHP_EOL;
					$content .= '</div>' . PHP_EOL;
					
					$content .= '</div>' . PHP_EOL;
				}
			 }
			
			return $content;
		}		  
		  
		  
  		/**
  		 *
  		 */
  		function remove_admin_color_scheme() {
  			global $_wp_admin_css_colors;
  			$_wp_admin_css_colors = 0;
  		}

  		/* 
  		 * Options Page Code.
  		 */
  		function options_menu() {
  			add_options_page( 'Bimbler RSVP Options', 'Bimbler RSVP', 'manage_options', 'bimbler_rsvp-id', array ($this, 'rsvp_plugin_options'));
//			add_submenu_page( 'tribe_settings_admin_slug', 'Bimbler RSVP', 'Bimbler RSVP', 'manage_options', 'bimbler_rsvp', array ($this, 'rsvp_plugin_options'));
  		}
  		
  		function rsvp_plugin_options() {
  			if ( !current_user_can( 'manage_options' ) )  {
  				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  			}
  			echo '<div class="wrap">';
  			echo '<p>Here is where the form would go if I actually had options.</p>';
  			echo '</div>';
  		}
  		/*
  		 * End Options Page Code.
  		*/
  		
  		/**
  		 * Returns the RWGPS ID for this ride, 0 otherwise.
  		 */
  		function get_rwgps_id ($event) 
  		{
  			$id = 0;
  		
  			// Get the RWGPS ID from the event meta in preference.
  			$meta = get_post_meta ($event, 'bimbler_rwgps_id', true);
  				
  			//error_log ('RWGPS ID for ride event ID '. $event . ' is \'' . $meta . '\'');
  				
  			if ((0 < strlen ($meta)) && ('0' != $meta)) {
  				return $meta;
  			}
  			
  			// If no event meta, get any associated ride page.
  			$ride = get_post_meta ($event, '_BimblerRidePage', true);
  			
  			//error_log ('Ride for post ID '. $event . ' is \'' . $ride . '\'');
  				
  			if (0 == strlen ($ride)) {
  				return 0;
  			}
  			
  			$meta = get_post_meta ($ride, 'bimbler_rwgps_id', true);
  			
  			//error_log ('RWGPS ID for ride post ID '. $ride . ' is \'' . $meta . '\'');
  			
  			if (0 < strlen ($meta)) {
  				$id = $meta;
  			}
  			
  			//error_log ('Returning ID '. $id);
  				
  			return $id;
  		}
  		
  		/**
  		 * Determines if the current user has RSVPd to this event.
  		 *
  		 * @param $event
  		 */
  		function get_current_rsvp ($event, $user_id = null)
  		{
  			global $wp_query;
  				
  			if (null === $user_id) {
  				global $current_user;
  				//get_currentuserinfo();
				wp_get_current_user ();
  				$user_id = $current_user->ID;
  				
  			}
  		
  			global $wpdb;
  			global $rsvp_db_table;
			  
			if(!is_numeric ($event)) {
				error_log ('get_current_rsvp: called with invalid event ID set');
				return null;
			}
			
			if(!is_numeric ($user_id)) {
				error_log ('get_current_rsvp: called with invalid user ID set');
				return null;
			}
			
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;
  				
//  			error_log ('Determining if user has RSVPd for this event.');
  				
  			// User ID
  		
  			$sql = 'SELECT * from '. $table_name;
  			$sql .= ' WHERE user_id = '. $user_id .' ';
  			$sql .= ' AND event = '. $event;
  			$sql .= ' ORDER BY id DESC';
  				
//			  error_log ('    '. $sql);
  				
  			$link = $wpdb->get_row ($sql);
  				
  			if (null === $link) {
//  				error_log ('   No previous RSVP');
  				
  				return null;
  			}
  			else {
//  				error_log ('  RSVP is: '. $link->rsvp);
  				
  				return $link->rsvp;
  			}
  		}
  		
  		/**
  		 * Returns details of the current RSVP.
  		 *
  		 * @param $event
  		 */
  		function get_current_rsvp_object ($event, $user_id = null)
  		{
  			global $wp_query;
  		
  			if (null === $user_id) {
  				global $current_user;
  				//get_currentuserinfo();
				wp_get_current_user ();
  				$user_id = $current_user->ID;
  			}
  		
  			global $wpdb;
  			global $rsvp_db_table;
  		
			if(!is_numeric ($event)) {
				error_log ('get_current_rsvp_object: called with invalid event ID set');
				return null;
			}
			
			if(!is_numeric ($user_id)) {
				error_log ('get_current_rsvp_object: called with invalid user ID set');
				return null;
			}
		  
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;
  		
  			$sql = 'SELECT * from '. $table_name;
  			$sql .= ' WHERE user_id = '. $user_id .' ';
  			$sql .= ' AND event = '. $event;
  			$sql .= ' ORDER BY id DESC';
  		
  			//			  error_log ('    '. $sql);
  		
  			$link = $wpdb->get_row ($sql);
  		
  			if (null == $link) {
  				return null;
  			}
  			else {
  				return $link;
  			}
  		}

  		/**
  		 * Returns the given event's RSVPs for attendees.
  		 *
  		 * @param $event
  		 */
  		function get_event_rsvp_object ($event, $rsvp = 'Y')
  		{
  			global $wpdb;
  			global $rsvp_db_table;
  		
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;
  		
			if(!is_numeric ($event)) {
				error_log ('get_event_rsvp_object: called with invalid event ID set');
				return null;
			}
			
			// TODO: Validate RSVP.
			/*if(!$this->is_char ($rsvp)) {
				error_log ('get_event_rsvp_object: called with invalid RSVP char set');
				return null;
			}*/
		  
  			/*$sql = 'SELECT * from '. $table_name;
  			$sql .= ' WHERE event = '. $event;
  			$sql .= ' AND rsvp = \'' . $rsvp . '\' ';
  			$sql .= ' ORDER BY id ASC';*/ 

  			$sql = 'SELECT r.* ';
  			$sql .= ' , u.user_nicename AS user_name ';
  			$sql .= ' FROM '. $table_name . ' r ';
  			$sql .= " , {$wpdb->base_prefix}users u ";
  			$sql .= ' WHERE r.event = '. $event;
  			$sql .= ' AND r.rsvp = \'' . $rsvp . '\' ';
  			$sql .= ' AND r.user_id = u.ID ';
  			$sql .= ' ORDER BY r.id ASC'; 
  			
  			//error_log ('DEBUG SQL: ' . $sql);
  			
  			$link = $wpdb->get_results ($sql);
  		
  			if (null == $link) {
  				return null;
  			}
  			else {
  				return $link;
  			}
  		}
  		
  		/**
  		 * Determines how many users have RSVPd to this event.
  		 *
  		 * @param $event
  		 */
  		function count_rsvps ($event)
  		{
  			global $wp_query;
  			global $wpdb;
  			global $rsvp_db_table;
  		
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			  
			if(!is_numeric ($event)) {
				error_log ('count_rsvps: called with no event ID set');
				return null;
			}
  		
  			$sql = 'SELECT COUNT(*) + SUM(guests) AS num ';
  			$sql .= ' FROM '. $table_name;
  			$sql .= ' WHERE rsvp = \'Y\'';
  			$sql .= ' AND event = '. $event;
  		
  			//			  error_log ('    '. $sql);
  		
  			$link = $wpdb->get_row ($sql);
  		
  			if (null == $link) {
  				return null;
  			}
  			
  			return $link->num;
  		}

  		/**
  		 * Determines how many users have RSVPd to this event.
  		 *
  		 * @param $event
  		 */
  		function count_no_rsvps ($event)
  		{
  			global $wp_query;
  			global $wpdb;
  			global $rsvp_db_table;
  		
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;

			if(!is_numeric ($event)) {
				error_log ('count_no_rsvps: called with no event ID set');
				return null;
			}
  		
  			$sql = 'SELECT COUNT(*) + SUM(guests) AS num ';
  			$sql .= ' FROM '. $table_name;
  			$sql .= ' WHERE rsvp = \'N\'';
  			$sql .= ' AND event = '. $event;
  		
  			//			  error_log ('    '. $sql);
  		
  			$link = $wpdb->get_row ($sql);
  		
  			if (null == $link) {
  				return null;
  			}
  				
  			return $link->num;
  		}
  		
  		
		/**
		 * Determines how many users came to this event.
		 *
		 * @param $event
		 */
		function count_attendees ($event)
		{
			global $wp_query;
			global $wpdb;
			global $rsvp_db_table;
		
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
		
			if(!is_numeric ($event)) {
				error_log ('count_attendees: called with no event ID set');
				return null;
			}

			$sql = 'SELECT COUNT(*) + SUM(guests) AS num ';
			$sql .= ' FROM '. $table_name;
			$sql .= ' WHERE rsvp = \'Y\'';
			$sql .= ' AND attended = \'Y\'';
			$sql .= ' AND event = '. $event;
		
			//			  error_log ('    '. $sql);
		
			$link = $wpdb->get_row ($sql);
		
			if (null == $link) {
				return null;
			}
				
			return $link->num;
		}

		/**
		 * Determines if event will be starting within an hour, or finished less than an hour ago.
		 * 
		 * @param unknown $event_id
		 * @return boolean
		 */
		function is_event_in_progress ($event_id) {

			$gmt_offset = ( get_option( 'gmt_offset' ) >= '0' ) ? ' +' . get_option( 'gmt_offset' ) : " " . get_option( 'gmt_offset' );
			$gmt_offset = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $gmt_offset );
			 
			// More than an hour until the start.
			if (strtotime( tribe_get_start_date( $event_id, false, 'Y-m-d G:i' ) . $gmt_offset ) - 3600 > time() ) {
				return false;
			}
			
			// Over an hour since event finished.
			if (strtotime( tribe_get_end_date( $event_id, false, 'Y-m-d G:i' ) . $gmt_offset ) + 3600 < time() ) {
				return false;
			}
				
			return true;
		}
		
  		/**
  		 * 
  		 * @param unknown $event_id
  		 * @return boolean
  		 */
  		function has_event_passed ($event_id) {
	  		// Check if event 3
	  		$gmt_offset = ( get_option( 'gmt_offset' ) >= '0' ) ? ' +' . get_option( 'gmt_offset' ) : " " . get_option( 'gmt_offset' );
	  		$gmt_offset = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $gmt_offset );
	  		
	  		if (strtotime( tribe_get_end_date( $event_id, false, 'Y-m-d G:i' ) . $gmt_offset ) <= time() ) {
	  			return true;
	  		}
			
	  		return false;
  		}
  		
  		/**
  		 * 
  		 * @param unknown $event_id
  		 * @return boolean
  		 */
  		function has_event_started ($event_id) {
	  		// Check if event 3
	  		$gmt_offset = ( get_option( 'gmt_offset' ) >= '0' ) ? ' +' . get_option( 'gmt_offset' ) : " " . get_option( 'gmt_offset' );
	  		$gmt_offset = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $gmt_offset );
	  		
	  		if (strtotime( tribe_get_start_date( $event_id, false, 'Y-m-d G:i' ) . $gmt_offset ) >= time() ) {
	  			return true;
	  		}
			
	  		return false;
  		}
  		
  		/**
  		 * Displays the RSVP list for the current event.
  		 * 
  		 * TODO: Move into render file.
  		 *
  		 * @param
  		 */
  		function show_rsvps () {
  			// The current Post (event) ID.
  			global $wp_query;
  			$postid = $wp_query->post->ID;
  			
  			global $wpdb;
  			global $rsvp_db_table;
  			
  			$table_name = $wpdb->base_prefix . $rsvp_db_table;
  				
  			$sql_y =  'SELECT * FROM '. $table_name;
  			$sql_y .= ' WHERE event = '. $postid;
  			$sql_y .= ' AND rsvp = \'Y\'';
  			$sql_y .= ' ORDER BY time DESC';
  			
  			$sql_n =  'SELECT * FROM '. $table_name;
  			$sql_n .= ' WHERE event = '. $postid;
  			$sql_n .= ' AND rsvp = \'N\'';
  			$sql_n .= ' ORDER BY time DESC';
  				
  			error_log ('Show RSVP list.');
  			
  			error_log ('    SQL Y: '. $sql_y);
  			error_log ('    SQL N: '. $sql_n);
  				
			// Only show content to logged-in users, and only if we're on an event page.
			if (is_user_logged_in() && is_single()) 
			{  					  					
				$html  = '<div id="rsvp-list">';
				$html .= '<div id="AvatarList" class="AvatarList-wrap">';
				$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
  				$html .= '		    <h3 id="reply-title" class="comment-reply-title">Who\'s Coming</h3>';
				
				$rsvps_y = $wpdb->get_results ($sql_y);
				$rsvps_n = $wpdb->get_results ($sql_n);
				
				//error_log ('    Yes returned '. count ($rsvps_y) .' rows.');
				//error_log ('    No returned '. count ($rsvps_n) .' rows.');
				
				if ((0 == $rsvps_y) || (0 == $rsvps_n)) 
				{
					$html .= '<p>Error in SQL.</p>';
				}
				else if ((0 == count ($rsvps_y)) && (0 == count ($rsvps_n)))
				{	
					$html .= '<p>No RSVPs yet.</p>';
				}					
				else
				{		
					// Show Yes RSVPs.
					$rsvps = $rsvps_y;
					
					$count = count($rsvps);
						
					if ($count > 0)
					{
						if (1 == $count) {
							$html .= '<p>'. count($rsvps) .' attendee:</p>';						
						} else {
							$html .= '<p>'. count($rsvps) .' attendees:</p>';						
						}
						
						$html .= '		    <ul>';
						
						foreach ( $rsvps as $rsvp) {
	
							$user_info   = get_userdata ($rsvp->user_id);
							$avatar = get_avatar ($rsvp->user_id, null, null, $user_info->user_login);
							$comment = stripslashes ($rsvp->comment); // De-escape the DB data.
							
							//	$html .= '		    <li><div class="permalink"></div><a href="/living/diy-neon-classic-pottery"><img src="http://shopsweetthings.com/wp-content/uploads/2012/07/cover25.jpg" /><p>Person 2: <i>"Comment 2"</i></p></a></li>';
							
							$html .= '<li class="AvatarList"><div class="permalink"></div><a href="">'. $avatar .'<p>'. $user_info->nickname;
							
	/*						if (strlen ($comment) > 0) {
								$html .= '<br><i>'. htmlentities ($comment, ENT_QUOTES) .'</i>';								
							} */
	
							$html .= '</p></a></li>';	
						}
	
						$html .= '		    </ul>';						
					}
					// Show No RSVPs.
					$rsvps = $rsvps_n;
						
					$count = count($rsvps);
					
					if ($count > 0)
					{
						if (1 == $count) {
							$html .= '<p>'. count($rsvps) .' not attending:</p>';
						} else {
							$html .= '<p>'. count($rsvps) .' not attending:</p>';
						}
					
						$html .= '		    <ul>';
					
						foreach ( $rsvps as $rsvp) {
					
							$user_info   = get_userdata ($rsvp->user_id);
							$avatar = get_avatar ($rsvp->user_id, null, null, $user_info->user_login);
							$comment = stripslashes ($rsvp->comment); // De-escape the DB data.
								
							$html .= '<li class="AvatarList"><div class="permalink"></div><a href="">'. $avatar .'<p>'. $user_info->nickname;
								
							/*						if (strlen ($comment) > 0) {
							 $html .= '<br><i>'. htmlentities ($comment, ENT_QUOTES) .'</i>';
							} */
					
							$html .= '</p></a></li>';
						}
					
						$html .= '		    </ul>';
					}
				} 
				
	  			$html .= '		</form>';
	  			$html .= '		    </div> <!-- #rsvp-list-->';
	  			$html .= '		</div><!-- #footer Wrap-->';
	  			
	  			echo $html;
  			}
  		}
  		
  		/*
  		 * Add the ride page to the event page meta box.
  		 * 
  		 * TODO: Add to admin file.
  		 * 
  		 */
  		function tribe_event_meta_filter ($event_id) {
  			
  			$event_id = get_the_ID();
  			
  			//error_log ('Getting page meta for event ID ' . $event_id);
  			
  			$meta_ride_page = get_post_meta ($event_id, '_BimblerRidePage', true);
  				
  			if (!isset ($meta_ride_page) || empty ($meta_ride_page)) {
  				//error_log ('No ride page for event ID ' . $event_id);
  				
  				return null;
  			}
  			
  			//error_log ('Got page meta ' . $meta_ride_page . ' for event ID ' . $event_id);
  			
  			$post_object = get_post ($meta_ride_page);
  				
  			if (!isset($post_object)) {
  				error_log ('Cannot get post object for event ID '. $meta_ride_page);
  				return null;
  			}
  			
  			$ride_url = get_permalink ($post_object->ID);
  			
  			$html = '';
  			
  			if (isset ($ride_url)) {
	  			$html  = '<div class="tribe-events-meta-group tribe-events-meta-group-ride-page">';
	  			$html .= '<h3 class="tribe-events-single-section-title">Ride Details</h3>';
	  			$html .= '<dl><dd class="fn org"><a href="' . $ride_url . '">Ride page</a></dd></dl>';
	  			$html .= '</div>';
  			}
  			  			
  			return $html;
  		}

  		
		/**
		 * Displays the RSVP buttons for the current event.
		 * 
		 * Logic exists in single_event.php in parallel, in addition to here.
		 *
		 * @param	
		 */
		function add_rsvp_form() {
		
			global $wp_query;
			$postid = $wp_query->post->ID;

			//error_log ('add_rsvp_form: post ID '. $postid);
			
			$rsvps_open = true;
			
			$meta_rsvps_open = get_post_meta ($postid, 'bimbler_rsvps_open', true);
				
			if ( isset($meta_rsvps_open)) {
				if ('No' == $meta_rsvps_open) {
					$rsvps_open = false;
				}
			}
			
			// Only show content to logged-in users, and only if we're on an event page.
			if (is_user_logged_in() && is_single() && !$this->has_event_passed ($postid)) {
				
				global $current_user;
				//get_currentuserinfo();
				wp_get_current_user ();
				
				if (!$rsvps_open) {
					$html  = '<div id="rsvp-form">';
					$html .= '<div id="respond" class="comment-respond">';
					$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
					$html .= '	<h3 id="reply-title" class="comment-reply-title">RSVP</h3>';
					$html .= '<p>RSVPs are no longer open.</p>';
					$html .= '	</form>';
					$html .= '</div> <!--#rsvp-respond-->';
					$html .= '</div> <!-- #rsvp-form -->';
					
					echo $html;
				} else {
				
				
				$user_id = $current_user->ID;
				
				$rsvp = $this->get_current_rsvp_object ($postid, $user_id);
				
				if (null == $rsvp) {
					$status = 'You have not RSVPd.';
				}
				else {
					if ('Y' == $rsvp->rsvp) {
						$status = 'You have RSVPd \'yes\'.';
					} else {
						$status = 'You have RSVPd \'no\'.';						
					}
				}
					
				$html  = '<div id="rsvp-form">';
				$html .= '<div id="respond" class="comment-respond">';
				$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
				$html .= '	<h3 id="reply-title" class="comment-reply-title">RSVP</h3>';
				$html .= '<p>'. $status .'</p>';
//				$html .= '<div class="woo-sc-box tick rounded full>'. $status .'</div>';
				$html .= wp_nonce_field('rsvp', 'rsvp_nonce', true, true);
//				$html .= '	<p class="comment-form-comment">RSVP Comment:<label for="comment">Comment</label><input type="text" id="comment" name="comment" aria-required="true"></input></p>';
				$html .= '	<p class="form-submit">';

				if ('Y' != $rsvp->rsvp) {
					$html .= '  <input type="checkbox" name="accept_terms" value="accept">Check here to confirm that you have read, understand and agree to the &#039;Assumption of Risk&#039; statement, that you have examined the proposed route, and that you are satisfied that you can complete the route.<br>';
					
/*					$html .= '
<button class="btn btn-default popover-default" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="It\'s so simple to create a tooltop for my website!" data-original-title="Twitter Bootstrap Popover">I\'m a Popover</button>							
'; */

/*					$html .= '
<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Tooltip on top">Tooltip on top</button>							
							';*/
				} 
				
				$html .= '<div class="col-sm-5">';
				$html .= '<span>Guests:</span>';
				//$html .= '	<div class="input-group">';
				//$html .= '		<span class="input-group-addon">Guests</span>';
				$html .= '		<select class="form-control" id="rsvp_guests" name="rsvp_guests"';
				if ('Y' == $rsvp->rsvp) {
					$html .= ' disabled';
				}
				$html .= '>';
				
				$i = 0;
				for ($i = 0; $i < 5; $i++) {
					$html .= '			<option';
					 
					if ($i == $rsvp->guests) {
						$html .= ' selected';
					}
					
					$html .= '>' . $i . '</option>';
				}
				$html .= '		</select>';

				//$html .= '		<div class="input-group-btn">';
				//$html .= '		<button type="button" class="btn btn-green btn-icon icon-left">RSVP Yes<i class="fa fa-icon-ok"></i></button>';
				//$html .= '		<button type="button" class="btn btn-red btn-icon icon-left">RSVP No<i class="fa fa-icon-remove"></i></button>';
				//$html .= '		<button type="button" class="btn btn-green">RSVP Yes</button>';
				//$html .= '		<button type="button" class="btn btn-red">RSVP No</button>';
				//$html .= '		</div>';
				
				//$html .= '	</div>'; 
				$html .= '</div>'; 
				
				$html .= '<p>&nbsp;</p>';
				
				$html .= '  <input class="form-control" name="submit" type="submit" id="submit" value="RSVP Yes" ';
				if ('Y' == $rsvp->rsvp) {
					$html .= ' style="background: #cccccc;" disabled ';
				}
				else {
					$html .= ' style="background: #6aab2d;"';
				}
				$html .= '>';
				
				
				$html .= '<input type="hidden" name="rsvp_post_id" id="rsvp_post_id" value="'. $postid .'">';
				
				
				$html .= '	<input class="form-control" name="submit" type="submit" id="submit" value="RSVP No" ';
				
				if ('N' == $rsvp->rsvp) {
					$html .= ' style="background: #cccccc;"  disabled ';
				}
				else {
					$html .= ' style="background: #f75300;"';
				}
				$html .= '><input type="hidden" name="rsvp_post_id" id="rsvp_post_id" value="'. $postid .'">';															
				
				$html .= '	</p></form>';
				$html .= '</div> <!--#rsvp-respond-->';
				$html .= '</div> <!-- #rsvp-form -->';
		
				echo $html;
				}
		
			} // end if
		
		} // end add_rsvp_form

		
		/*
		 * Returns a list of posts which are of type 'ride'.
		*
		* @param slug
		*/
		function get_posts_from_category_slug ($slug) {
			//			error_log ('get_posts for slug \''. $slug .'\'.');
		
			$args=array('category_name' 	=> 'ride', //$slug,
					'post_status' 		=> 'publish',
					'post_type' 		=> 'post',
					'orderby'			=> 'title',
					'order'				=> 'ASC',
					'posts_per_page'	=> -1
			);
				
			$my_posts = get_posts( $args );
		
			if( !isset ($my_posts) ) {
				error_log ('Cannot get_post for slug \''. $slug .'\'.');
					
				return null;
			}
		
			return $my_posts;
		}
		
		
		/*
		 * Return list of NGG galleries.
	 	 *
		 * @param slug
		 */
		 // TODO: This function doesn't do what the function name says - fix or remove.
		function get_ngg_galleries ($slug) {
			//			error_log ('get_posts for slug \''. $slug .'\'.');
		
			$args=array('category_name' 	=> 'ride', //$slug,
					'post_status' 		=> 'publish',
					'post_type' 		=> 'post',
					'orderby'			=> 'title',
					'order'				=> 'DESC',
					'posts_per_page'	=> -1
			);
				
			$my_posts = get_posts( $args );
		
			if( !isset ($my_posts) ) {
				error_log ('Cannot get_post for slug \''. $slug .'\'.');
					
				return null;
			}
		
			return $my_posts;
		}
		
		/*
		 * Adds a dropdown of rides pages to the Event Admin page.
		 * 
		 * TODO: Add to admin file.
		 * 
		 * @param post_id
		 */
		function show_event_admin_rides ($post_id) {
			$slug = 'ride';
			
			$ride_pages = $this->get_posts_from_category_slug ($slug);
			
			if (!isset ($ride_pages)) {
				return null;
			}
			
			$meta_ride_page = get_post_meta ($post_id, '_BimblerRidePage', true);

			//error_log ('Stored ride page: ' . $meta_ride_page);
			?>
				<table id="event_page" class="eventtable">
					<tr>
						<td colspan="2" class="tribe_sectionheader" ><h4><?php echo ('Ride Template'); ?></h4></td>
					</tr>
			
			<tr>
				<td style="width:172px;"><?php echo ('Ride Template:'); ?></td>
				<td>
					<select  id="RidePage" name="RidePage">
						<option value=""<?php if (!isset ($meta_ride_page)) echo ' selected="selected"' ?>>Select a ride</option>
					
			<?php 	
			foreach ($ride_pages as $ride_page) {
				$page_id = $ride_page->ID;
				$page_title = $ride_page->post_title;
								
			?>
					<?php
						if ( isset($meta_ride_page) && ($page_id == $meta_ride_page )) $selected = true;
						else $selected = false;
					?>
						<option value="<?php echo $page_id; ?>"<?php if ( $selected ) echo ' selected="selected"' ?>> <?php echo $page_title ?> </option>
			<?php 
			
			}
			?>
					</select>
				</td>
				</tr>
				
			</table>
			<?php 
		}
		
		/**
		 * 
		 */
		function get_galleries () {

			global $wpdb;

			$sql =  "SELECT * FROM {$wpdb->base_prefix}ngg_gallery ";
			$sql .= ' ORDER BY gid DESC';
	
	
			$galleries = $wpdb->get_results ($sql);

			if (!isset ($galleries)) {
				error_log ('Cannot get galleries.');
				return null;
			}

			return $galleries; 
		}
		
		/*
		 * Adds a dropdown of NG Galleries to the Event Admin page.
		 * 
		 * TODO: Add to admin file.
		 *
		 * @param post_id
		 */
		function show_event_galleries ($post_id) {
			
			// Only render the controls if the NextGen Gallery plugin is loaded.
			if (!class_exists ('C_Gallery_Storage')) {
				return;
			}
			
			$slug = 'ride';
				
			$galleries = $this->get_galleries();
				
			if (!isset ($galleries)) {
				return null;
			}
				
			$meta_gallery_id = get_post_meta ($post_id, 'bimbler_gallery_id', true);
				
			//error_log ('Stored gallery ID: ' . $meta_gallery_id);
			?>
						<table id="event_page" class="eventtable">
							<tr>
								<td colspan="2" class="tribe_sectionheader" ><h4><?php echo ('Image Gallery'); ?></h4></td>
							</tr>
					
					<tr>
						<td style="width:172px;"><?php echo ('Gallery:'); ?></td>
						<td>
							<select  id="Gallery" name="Gallery">
								<option value=""<?php if (!isset ($meta_gallery_id)) echo ' selected="selected"' ?>>Select a gallery</option>
								<option value="0">Create new gallery and attach</option>
								
			<?php 	
			foreach ($galleries as $gallery) {
				$gallery_id = $gallery->gid;
				$gallery_title = $gallery->title;
								
				if ( isset($meta_gallery_id) && ($gallery_id == $meta_gallery_id )) $selected = true;
				else $selected = false;
			?>
								<option value="<?php echo $gallery_id; ?>"<?php if ( $selected ) echo ' selected="selected"' ?>> <?php echo $gallery_title ?> </option>

			<?php 
			}
			?>
							</select>
						</td>
						</tr>
						
					</table>
			<?php 
		}
		
		/*
		 * Gets list of users.
		 *
		 * TODO: Add to admin file.
		 *
		 * @param post_id
		 */
		
		function get_user_list () {
			global $wpdb;
				
			//$table_name = $wpdb->base_prefix . 'bimblers_rsvp';
		
			/*SELECT u.id,
				m_f.meta_value AS FIRST,
				m_l.meta_value AS LAST,
				u.display_name AS DISPLAY
				FROM wp_users u,
				wp_usermeta m_f,
				wp_usermeta m_l
				WHERE u.id = m_f.user_id
				AND u.id = m_l.user_id
				AND m_f.meta_key = 'first_name'
				AND m_l.meta_key = 'last_name'
				ORDER BY FIRST, LAST, ID */
		
			$sql =  'SELECT u.id as id, ';
			$sql .= ' m_f.meta_value AS first, ';
			$sql .= ' m_l.meta_value AS last, ';
			$sql .= ' u.display_name AS display ';
			$sql .= " FROM {$wpdb->users} u, ";
			$sql .= " {$wpdb->usermeta} m, ";
			$sql .= " {$wpdb->usermeta} m_f, ";
			$sql .= " {$wpdb->usermeta} m_l ";
			$sql .= ' WHERE u.id = m.user_id ';
			$sql .= ' AND m.meta_key = \'wp_capabilities\' ';
			$sql .= ' AND m.meta_value NOT LIKE \'%unverified%\' ';
			$sql .= ' AND u.id = m_l.user_id ';
			$sql .= ' AND u.id = m_f.user_id ';
			$sql .= ' AND m_f.meta_key = \'first_name\' ';
			$sql .= ' AND m_l.meta_key = \'last_name\' ';
			//		$sql .= ' AND u.id NOT IN (33) ';
			$sql .= ' ORDER BY FIRST, LAST, ID ';
				
			//error_log ($sql);
			$users = $wpdb->get_results ($sql);
		
			return $users;
		}		

		/*
		 * Adds a dropdown of ride hosts to the Event Admin page.
		 *
		 * TODO: Add to admin file.
		 *
		 * @param post_id
		 */
		function show_event_hosts ($post_id) {
			$slug = 'ride';
		
			$galleries = $this->get_galleries();
		
			if (!isset ($galleries)) {
				return null;
			}
		
			$meta_hosts_json = get_post_meta ($post_id, 'bimbler_ride_hosts', true);
			
			$meta_hosts = json_decode($meta_hosts_json);
		
			//error_log ('Stored gallery ID: ' . $meta_gallery_id);
			?>
								<table id="event_page" class="eventtable">
									<tr>
										<td colspan="2" class="tribe_sectionheader" ><h4><?php echo ('Ride Hosts'); ?></h4></td>
									</tr>
<!--  									<tr>
										<td colspan="2" class="tribe_sectionheader" ><pre><?php //echo $meta_hosts_json; ?></pre></td>
									</tr> -->
									
							<tr>
								<td style="width:172px;"><?php echo ('Ride Hosts:'); ?></td>
								<td>
<?php 

			$users = $this->get_user_list ();
				
			if (0 == $users) {
				error_log ('Could not get list of users for dropdown.');
			
				echo '<div class="bimbler-error-box error"><span>Error: </span>Could not populate user dropdown.</div>';
					
			} else {
				echo '<select multiple="multiple" class="bimbler-select2-event-hosts"  style="width: 350px;" id="bimbler_ride_hosts" name="bimbler_ride_hosts[]">';
			
				foreach ($users as $user) {
					
					$selected = false;
					
					if (isset ($meta_hosts) && in_array  ($user->id, $meta_hosts)) {
						
						$selected = true;
						
					}
						
					echo '<option value=' . $user->id;

					if ($selected) echo ' selected="selected"';
					
					echo '>';
					
					echo  $user->first . ' ' . $user->last;
					echo ' (' . $user->display . ')</option>';
				}
			
				echo '</select>';
			}
?>								
								</td>
								</tr>
								
							</table>
							
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$(".bimbler-select2-event-hosts").select2();
	});
	
</script>
					<?php 
				}		

		/*
		 * Adds a dropdown of ride difficulty rating to the Event Admin page.
		 * 
		 * TODO: Add to admin file.
		 *
		 * @param post_id
		 */
		function show_event_difficulty ($post_id) {
		
			$meta_ride_difficulty = get_post_meta ($post_id, 'bimbler_ride_difficulty', true);
		
			//error_log ('Stored gallery ID: ' . $meta_gallery_id);
			?>
						<table id="event_page" class="eventtable">
							<tr>
								<td colspan="2" class="tribe_sectionheader" ><h4><?php echo ('Ride Difficulty'); ?></h4></td>
							</tr>
							
							<tr>
								<td style="width:172px;"><?php echo ('Difficulty:'); ?></td>
								<td style="font-family: FontAwesome;">
									<select  id="Difficulty" name="Difficulty">
										<option value=""<?php if (!isset ($meta_ride_difficulty)) echo ' selected="selected"' ?>>Select a difficulty</option>
									
				<?php 	
					for ($r = 0; $r <= 5; $r++) {
										
						if ( isset($meta_ride_difficulty) && ($r == $meta_ride_difficulty )) $selected = true;
						else $selected = false;
					?>
							<option value="<?php echo $r; ?>"<?php if ( $selected ) echo ' selected="selected"' ?>> 
							<?php 
								for ($i = 0; $i < $r; $i++) {
									print "&#xf005";
								} 
							?> </option>
		
					<?php 
					}
					?>
									</select>
								</td>
								</tr>
								
							</table>
					<?php 
		}
		
		/*
		 * Adds a dropdown to enable closing of RSVPs to the Event Admin page.
		 *
		 * TODO: Add to admin file.
		 * 
		 * @param post_id
		 */
		function show_event_rsvps_open ($post_id) {
	
			$rsvps_open = true;		

			$meta_ride_rsvps_open = get_post_meta ($post_id, 'bimbler_rsvps_open', true);
			
			if ( isset($meta_ride_rsvps_open)) {
				if ('No' == $meta_ride_rsvps_open) {
					$rsvps_open = false;
				}
			}
			
			//error_log ('Stored RSVPs Open: ' . $meta_ride_rsvps_open);
			?>
							<table id="event_page" class="eventtable">
								<tr>
									<td colspan="2" class="tribe_sectionheader" ><h4>RSVPs Open</h4></td>
								</tr>
								
								<tr>
									<td style="width:172px;">RSVPs Open:</td>
									<td>
										<select id="RSVPsOpen" name="RSVPsOpen">
											<option value="Yes"<?php if ( $rsvps_open ) echo ' selected="selected"'; ?>>Yes</option> 
											<option value="No"<?php if (!$rsvps_open ) echo ' selected="selected"'; ?>>No</option> 
										</select>
									</td>
								</tr>
									
							</table>
			<?php 
		}	

		/*
		 * Adds an input control to allow setting of the RWGPS map ID to the Event Admin page.
		 * 
		 * TODO: Add to admin file.
		 *
		 * @param post_id
		 */
		 
		 // TODO: post ID parameter not used - re-factor this and callers.
		function show_event_rwgps_map ($post_id) {
			
            /* IFrame plugin has changed its function names... */
/*			if (!function_exists ('iframe_unqprfx_embed_shortcode')) {
				return null;
			} */

			$rwgps_id = $this->get_rwgps_id (get_the_ID());
			
			//error_log ('Stored RSVPs Open: ' . $meta_ride_rsvps_open);
			?>
									<table id="event_page" class="eventtable">
										<tr>
											<td colspan="2" class="tribe_sectionheader" ><h4>Ride With GPS Map</h4></td>
										</tr>
										
										<tr>
											<td style="width:172px;">RWGPS ID:</td>
											<td><input tabindex="<?php tribe_events_tab_index(); ?>" type='text' id='RWGPSID' name='RWGPSID' size='25' value='<?php echo (isset($rwgps_id)) ? esc_attr($rwgps_id) : ''; ?>' placeholder='111111' /></td>
										</tr>
<?php
			if (0 != $rwgps_id) {

?>
										<tr>
											<td style="width:172px;">Route Map:</td>
											<td><img src="http://assets2.ridewithgps.com/routes/<?php echo $rwgps_id; ?>/thumb.png"></td>
										</tr>
<?php 
			}
?>										
										
									</table>
<?php 
		}	
		
		/*
		 * Saves the ride page post ID as the event's meta data.
		 * 
		 * TODO: Add to admin file.
		 * 
		 */
		function tribe_events_save_ride_page ($event_id) {
			
			if (!isset ($_POST['RidePage'])) {
				return null;
			}
			
			// error_log ('Saving ride page, page ' . $_POST['RidePage'] . ' for event '. $event_id);
			
			update_post_meta( $event_id, '_BimblerRidePage', $_POST['RidePage']);
		}
		
		/**
		 * create a new gallery & folder
		 *
		 * @class nggAdmin
		 * @param string $name of the gallery
		 * @param string $defaultpath
		 * @param bool $output if the function should show an error messsage or not
		 * @return
		 */
		function create_gallery($title, $defaultpath, $output = true) {
		
			global $user_ID;
			$fs       = C_Fs::get_instance();
			$storage  = C_Gallery_Storage::get_instance();
		
			// get the current user ID
			//get_currentuserinfo();
			wp_get_current_user ();
		
			//cleanup pathname
			$name = sanitize_file_name( sanitize_title($title)  );
			$name = apply_filters('ngg_gallery_name', $name);
			$txt = '';
		
			$galleryObj = new stdClass;
			$galleryObj->path = '';
			$nggRoot = $storage->get_gallery_abspath($galleryObj);
		
			// No gallery name ?
			if ( empty($name) ) {
				if ($output) nggGallery::show_error( __('No valid gallery name!', 'nggallery') );
				return false;
			}
		
			$galleryObj = new stdClass;
			$galleryObj->path = $fs->join_paths($defaultpath, $name);
			$gallery_path = $storage->get_gallery_abspath($galleryObj);
		
			// check for main folder
			if ( !is_dir($nggRoot) ) {
				if ( !wp_mkdir_p( $nggRoot ) ) {
					$txt  = __('Directory', 'nggallery').' <strong>' . esc_html( $nggRoot ) . '</strong> '.__('didn\'t exist. Please create first the main gallery folder ', 'nggallery').'!<br />';
					$txt .= __('Check this link, if you didn\'t know how to set the permission :', 'nggallery').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
					if ($output) nggGallery::show_error($txt);
					return false;
				}
			}
		
			// 1. Check for existing folder
			if ( is_dir($gallery_path) && !(SAFE_MODE) ) {
				$suffix = 1;
				do {
					$alt_name = substr ($name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "_$suffix";
					$galleryObj->path = $fs->join_paths($defaultpath, $alt_name);
					$gallery_path = $storage->get_gallery_abspath($galleryObj);
					$dir_check = is_dir($gallery_path);
					$suffix++;
				} while ( $dir_check );
				$name = $alt_name;
			}
		
			$thumb_path = $fs->join_paths($gallery_path, 'thumbs');
		
			// 2. Create new gallery folder
			if ( !wp_mkdir_p ($gallery_path) )
				$txt  = __('Unable to create directory ', 'nggallery') . esc_html($gallery_path) . '!<br />';
		
			// 3. Check folder permission
			if ( !is_writeable($gallery_path) )
				$txt .= __('Directory', 'nggallery').' <strong>' . esc_html($gallery_path) . '</strong> '.__('is not writeable !', 'nggallery').'<br />';
		
			// 4. Now create thumbnail folder inside
			if ( !is_dir($thumb_path) ) {
				if ( !wp_mkdir_p ($thumb_path) )
					$txt .= __('Unable to create directory ', 'nggallery').' <strong>' . esc_html($thumb_path) . '/thumbs !</strong>';
			}
		
			if (SAFE_MODE) {
				$help  = __('The server setting Safe-Mode is on !', 'nggallery');
				$help .= '<br />'.__('If you have problems, please create directory', 'nggallery').' <strong>' . esc_html($gallery_path) . '</strong> ';
				$help .= __('and the thumbnails directory', 'nggallery').' <strong>' . esc_html($thumb_path) . '</strong> '.__('with permission 777 manually !', 'nggallery');
				if ($output) nggGallery::show_message($help);
			}
		
			// show a error message
			if ( !empty($txt) ) {
				if (SAFE_MODE) {
					// for safe_mode , better delete folder, both folder must be created manually
					@rmdir($thumb_path);
					@rmdir($gallery_path);
				}
				if ($output) nggGallery::show_error($txt);

				error_log ($txt);

				return false;
			}
		
			// now add the gallery to the database
			$galleryID = nggdb::add_gallery($title, $defaultpath . $name, $title, 0, 0, $user_ID );
			// here you can inject a custom function
			do_action('ngg_created_new_gallery', $galleryID);
		
			// return only the id if defined
			if ($output == false)
				return $galleryID;
		
			if ($galleryID != false) {
				$message  = __('Gallery ID %1$s successfully created. You can show this gallery in your post or page with the shortcode %2$s.<br/>','nggallery');
				$message  = sprintf($message, $galleryID, '<strong>[nggallery id=' . $galleryID . ']</strong>');
				$message .= '<a href="' . admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $galleryID . '" >';
				$message .= __('Edit gallery','nggallery');
				$message .= '</a>';
		
				if ($output) nggGallery::show_message($message);
			}
			return true;
		}		
		
		/*
		 * Saves the gallery ID as the event's meta data.
		 * 
		 * TODO: Add to admin file.
		 * 
		*/
		function tribe_events_save_gallery ($event_id) {
			
			// Only try to save the gallery details if the NextGen Gallery plugin is loaded.
			if (!class_exists ('C_Gallery_Storage')) {
				return null;
			}
			
			global $ngg;
				
			if (!isset ($_POST['Gallery']) || (0 == strlen(($_POST['Gallery']))) || (!is_numeric($_POST['Gallery']))) {
				return null;
			}
			
			$gallery_id = $_POST['Gallery'];
			
			
			// Create new gallery.
			if (0 == $gallery_id) {
				error_log ('Auto-creating new gallery for event ' . $event_id);
				
				$post_object = get_post ($event_id);
				
				$date_str = 'Y.m.d';
				
				$event_date = tribe_get_start_date($event_id, false, $date_str);
				
				$gallery_name = $event_date . ' - ' . $post_object->post_title; 
				
				error_log ('Creating gallery "' . $gallery_name . '" in "' . $ngg->options['gallerypath'] . '"');
				
				//error_log (print_r ($ngg, true));
				
				$gallery_id = $this->create_gallery($gallery_name, $ngg->options['gallerypath'], false);
			}
				
			error_log ('Saving gallery page ' . $gallery_id . ' for event '. $event_id);
				
			update_post_meta( $event_id, 'bimbler_gallery_id', $gallery_id);
		}

		/*
		 * Saves the RWGPS map ID as the event's meta data.
		 * 
		 * TODO: Add to admin file.
		 * 
		*/
		function tribe_events_save_rwgps ($event_id) {
			
//			if (!function_exists ('iframe_unqprfx_embed_shortcode')) {
//				return null;
//			}
		
			if (!isset ($_POST['RWGPSID'])) {
				return null;
			}
		
			// error_log ('Saving RWGPS ID ' . $_POST['RWGPSID'] . ' for event '. $event_id);
		
			update_post_meta( $event_id, 'bimbler_rwgps_id', $_POST['RWGPSID']);
		}
		
		/*
		 * Saves the ride hosts into the event's meta data.
		 *
		 * TODO: Add to admin file.
		 *
		 */
		function tribe_events_save_hosts ($event_id) {
		
			//error_log ('Saving hosts: ' . print_r($_POST['bimbler_ride_hosts'], true));
			
			if (!isset ($_POST['bimbler_ride_hosts'])) {
				return null;
			}
			
			$hosts = json_encode($_POST['bimbler_ride_hosts']);
		
			update_post_meta( $event_id, 'bimbler_ride_hosts', $hosts);
			
			// Add an RSVP for event hosts.
			foreach ($_POST['bimbler_ride_hosts'] as $host) {

				// Only insert a new RSVP if this user is not already attending.				
				if (null === $this->get_current_rsvp($event_id, $host)) {
					error_log ('Adding RSVP for user ID ' . $host . ' as host of event ID ' . $event_id);

					$this->insert_rsvp ($event_id, $host, 'Y');
				}
			}
		}
		
		
		
		/*
		 * Saves the RSVPs Open status as the event's meta data.
		 * 
		 * TODO: Add to admin file.
		 * 
		*/
		function tribe_events_save_rsvps_open ($event_id) {
		
			if (!isset ($_POST['RSVPsOpen'])) {
				return null;
			}
		
			// error_log ('Saving gallery page ' . $_POST['Gallery'] . ' for event '. $event_id);
		
			update_post_meta( $event_id, 'bimbler_rsvps_open', $_POST['RSVPsOpen']);
		}
		
		
		/**
		 * Adds the ride page to the event.
		 *
		 * TODO: Check if now deprecated?
		 *
		 * @param
		 */
		function show_ride_page () {
			// The current Post (event) ID.
			global $wp_query;
			$post_id = $wp_query->post->ID;

			//return null;
			
			// Only show content to logged-in users, and only if we're on an event page.
			if (is_user_logged_in() && is_single()) {
	
				$meta_ride_page = get_post_meta ($post_id, '_BimblerRidePage', true);
				
				if (!isset ($meta_ride_page) || empty ($meta_ride_page)) {
					error_log ('No ride page for event ID ' . $post_id);
				
					// Nothing to do.
					return null;
				}
					
				//error_log ('Got page meta ' . $meta_ride_page . ' for event ID ' . $post_id);
					
				$post_object = get_post ($meta_ride_page);
				
				if (!isset($post_object)) {
					error_log ('Cannot get post object for event ID '. $meta_ride_page);
					return null;
				}

				//var_dump ($post_object->post_content);
				
				echo '<h3>Ride Details</h3>';
				
				echo apply_filters( 'the_content', $post_object->post_content);

				echo '<br><br><br>';
			}
		}
		
		
		/**
		 * Adds the photo gallery to the event.
		 *
		 * TODO: Check if now deprecated?
		 * 
		 * @param
		 */
		function show_gallery () {
			// The current Post (event) ID.
			global $wp_query;
			
			$gallery_id = 0;
			$postid = $wp_query->post->ID;
				
			// error_log ('Show Gallery.');
				
			$meta = get_post_meta ($postid, 'bimbler_gallery_id');
				
//			print_r ($meta);
			
			if (isset ($meta[0])) {
				$gallery_id = $meta[0];
			}
			
			// Only show content to logged-in users, and only if we're on an event page.
			if (is_user_logged_in() && is_single() && isset ($gallery_id)) {
					
				$html = '<div id="rsvp-gallery">';
				$html .= '<div class="comment-respond">';
				$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
				$html .= '		    <h3 id="reply-title" class="comment-reply-title">Gallery</h3>';
						
				if (0 != $gallery_id) {
					//$html .= do_shortcode ('[nggallery id='. $gallery_id .' display_type="photocrati-nextgen_basic_thumbnails"]');
					//$html .= do_shortcode ('[ngg_images gallery_ids="'. $gallery_id .'" display_type="photocrati-nextgen_basic_extended_album"]');

					$html .= do_shortcode ('[ngg_images gallery_ids="'. $gallery_id .'" display_type="photocrati-nextgen_basic_thumbnails"]');
						
					//$html .= nggShowGallery ($gallery_id, 'photocrati-nextgen_basic_thumbnails');
					//$html .= nggShowGallery ($gallery_id);
						
					$html .= '<br><br><br><h4>Upload an Image</h4>';
					$html .= do_shortcode ('[ngg_uploader id='. $gallery_id .']');
				}
				
				$html .= '		</form>';
				$html .= '		    </div>';
				$html .= '		</div> <!-- #rsvp-gallery-->';
		
				echo $html;
			}
		}
		
		
		/**
		 * Updates the current RSVP to the DB.
		 *
		 * @param	$event_id	The ID of the post.
		 * @param	$user_id	The user ID.
		 * @param	$rsvp		The new RSVP.
		 * @param	$comment	The new comment
		 */
		function update_rsvp ($event_id, $user_id, $rsvp, $comment, $guests = 0) {
		
			global $wpdb;
			global $rsvp_db_table;
			global $current_user;
				
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			//error_log ('Updating RSVP data');
			//error_log ('  User:    ' . $user_id);
			//error_log ('  RSVP:    ' . $rsvp);
			//error_log ('  Event:   ' . $event_id);
			//error_log ('  Comment: ' . $comment);
			//error_log ('  Guests:  ' . $guests); 
				
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();

			// If this is an admin update (user ID != current user ID), don't set the timestamp, and don't send an email notification.
			if ($user_id == $current_user->ID) {
						
				// Do nothing if this is a for re-submit (no change to RSVP or guest count).
				$current_rsvp = $this->get_current_rsvp_object ($event_id, $user_id);
				
				if (($current_rsvp->rsvp != $rsvp) || ($current_rsvp->guests != $guests)) {						
						
					error_log ('Updating RSVP: was \'' . $current_rsvp->rsvp . '\', now \'' . $rsvp . '\'; was ' . $guests . ' guests, now ' . $current_rsvp->guests . '.');	
						
					if ( false === $wpdb->update(
								$table_name,
								array ( 'rsvp'    	=> $rsvp,
										'comment'	=> $comment,
										'time'		=> Date("Y-m-d H:i:s"),
										'guests'	=> $guests
								),
								array (	'user_id'  	=> $user_id,
										'event'		=> $event_id),
								array ('%s', '%s', '%s', '%d')
								)){
						error_log ('    Could not update row: '. $wpdb->print_error());
					} else {
							
						//get_currentuserinfo();
						wp_get_current_user();
			
						// Only send an email for 'yes' RSVPs.
						if ('Y' == $rsvp) {
							// Send the email confirmation.
							if (class_exists('Bimbler_Reminders')) {
								Bimbler_Reminders::get_instance()->send_rsvp_confirmation($user_id, $event_id);
							}
						}
					}
				} else { // Do nothing.
					
					error_log ('RSVP submitted with no change - doing nothing.');
					
				}
				 
			} else {
				
				if ( false === $wpdb->update(
						$table_name,
						array ( 'rsvp'    	=> $rsvp,
								'comment'	=> $comment,
								'guests'	=> $guests
						),
						array (	'user_id'  	=> $user_id,
								'event'		=> $event_id),
						array ('%s', '%s', '%d')
				)){
							error_log ('    Could not update row: '. $wpdb->print_error());
								
				}
			} 
		}
		
		/**
		 * Inserts a new RSVP to the DB.
		 *
		 * @param	$event_id	The ID of the post.
		 * @param	$user_id	The user ID.
		 * @param	$rsvp		The new RSVP.
		 * @param	$comment	The new comment
		 */
		function insert_rsvp ($event_id, $user_id, $rsvp, $comment = '', $guests = 0) {
		
			global $wpdb;
			global $rsvp_db_table;
			global $current_user;
		
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			/*error_log ('Inserting RSVP data');
			error_log ('  User:    ' . $user_id);
			error_log ('  RSVP:    ' . $rsvp);
			error_log ('  Event:   ' . $event_id);
			error_log ('  Comment: ' . $comment);
			error_log ('  Guests:  ' . $guests); */
		
			if (false == $wpdb->insert($table_name,
							array (	'event'    => $event_id,   // bigint (20) NOT NULL,
									'user_id'  => $user_id,    // varchar (60) NOT NULL,
									'rsvp'     => $rsvp,       // char(1) NOT NULL
									'comment'  => $comment,
									'guests'   => $guests),					
							array ('%d', '%d', '%s', '%s', '%d')
						)){
				error_log ('   Could not insert row: '. $wpdb->print_error());
			}

			//get_currentuserinfo();
			wp_get_current_user();

			// Admin updates will not trigger a notification.
			if (('Y' == $rsvp) && ($user_id == $current_user->ID)) {
				// Send the email confirmation.
				if (class_exists('Bimbler_Reminders')) {
					Bimbler_Reminders::get_instance()->send_rsvp_confirmation($user_id, $event_id);
				}
			}
		}
		
		/*
		 * TODO: Add to render file. 
		 */
		function show_notices () {

			$output = '';

			$script_top = '
			<!-- toastr notices. -->
			<script type="text/javascript">
jQuery(document).ready(function($) 
{
	setTimeout(function()
	{			
		var opts = {
			"closeButton": true,
			"debug": false,
			"positionClass": "toast-top-right",
			"toastClass": "black",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "10000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
			';

			$script_bot = '
	}, 1000);
});
</script>
		 		';
			
			$output .= $script_top;
			
			$notices = $this->getNotices();
			
			foreach ($notices as $notice) {
				
				//toastr.success("You have been awarded with 1 year free subscription. Enjoy it!", "Account Subcription Updated", opts);

				$output .= 'toastr.' . $notice['type'];
				$output .= '("' . $notice['message'] . '", ';
				$output .= '"' . $notice['title'] . '", ';
				$output .= ' opts); ' . PHP_EOL;

			}
			//toastr.success("You have been awarded with 1 year free subscription. Enjoy it!", "Account Subcription Updated", opts);
			
			$output .= $script_bot;
				
			echo $output;
		}
		
		/**
		 * Saves the current RSVP to the DB.
		 *
		 * @param	$post_id	The ID of the post on which the comment is being added.
		 */
		function process_rsvp( $post_id ) {
			// TODO: Check if the user is logged-in - this functionality should only be accessible if they are.
/*			if (!is_user_logged_in())
			{
				error_log ('process_rsvp: Called by non logged-in user.');
				return;
			} */


//			error_log ('PHP timezone is set to "' . ini_get('date.timezone') . '".');
//			error_log ('Timezone is set to "' . $this->get_timezone_string() . '".');
//			error_log ('GMT offset is set to "' .get_option('gmt_offset') . '".');
			
			// Only save if we've been passed the 'nonce'... i.e. the event is being renderered as part of an 
			// RSVP update.
			//if ( ( is_single() || is_page() ) &&
			if ( // TODO: Flatten this out - return on validation failures.
				isset ($_POST['rsvp_post_id']) &&
				isset($_POST['rsvp_nonce']) &&
					wp_verify_nonce($_POST['rsvp_nonce'], 'rsvp')
			) {
				global $wpdb;
				global $rsvp_db_table;

				$accept = 'N';
			

				$event_id = $_POST['rsvp_post_id'];

				if (!is_numeric ($event_id)) {
					error_log ('process_rsvp: Non-numeric event ID.');
					return;
				}
				
				if (isset ($_POST['accept_terms']))
				{
					$accept = 'Y';
				}
				
				//error_log ('Accept is: '. $accept);
				
//				error_log ('Saving RSVP data');
//				error_log ('Submit: ' . $_POST['id']);
//				error_log ('_POST :' . print_r($_POST, true));
				
				$table_name = $wpdb->base_prefix . $rsvp_db_table;
				
				$rsvp = 'N';
				
				if ($_POST['submit'] == 'RSVP Yes')	{
					$rsvp = 'Y';
				}

				$tribe_ecp = TribeEvents::instance();
				
				// Only update DB if 'No' selected or both 'Yes' selected AND accept checkbox ticked.
				if (('Y' == $rsvp) && ('N' == $accept)) {
					$tribe_ecp->setNotice ('rsvp-accept','Please check the &quot;Assumption of Risk&quot; check-box to RSVP.');
					$tribe_ecp->setNotice ('rsvp-no','Your RSVP has not been updated.');
					
					$msg['type'] = 'error';
					$msg['title'] = 'Please check the &quot;Assumption of Risk&quot; check-box to RSVP.';
					$msg['message'] = 'Your RSVP has not been updated.';
					
					$this->setNotice ('rsvp-accept', $msg);
					//$this->setNotice('error', 'Please check the &quot;Assumption of Risk&quot; check-box to RSVP.');
				}
				else
				{ 
					if ('Y' == $rsvp)	{
						$tribe_ecp->setNotice ('rsvp-yes','You RSVPd \'yes\'.');

						$msg['type'] = 'success';
						$msg['title'] = 'RSVP Processed';
						$msg['message'] = 'See you there!';
							
						$this->setNotice ('rsvp-yes', $msg);
						
					} 
					else {
						$tribe_ecp->setNotice ('rsvp-no','You RSVPd \'no\'.');

						$msg['type'] = 'success';
						$msg['title'] = 'RSVP Processed';
						$msg['message'] = 'See you next time!';
							
						$this->setNotice ('rsvp-yes', $msg);
					}
					
					$event_id = $_POST['rsvp_post_id'];
	//				$comment = $_POST['comment'];
					$comment = ' ';

					if (isset ($_POST['rsvp_user'])) {
						$user_id = $_POST['rsvp_user'];
						
						if (!is_numeric ($user_id)) {
							error_log ('process_rsvp: Non-numeric user ID.');
							return;
						}
					} else {
						global $current_user;
						//get_currentuserinfo();
						wp_get_current_user();
							
						// User ID
						$user_id = $current_user->ID;
					}

					if (isset ($_POST['rsvp_guests'])) {
						$guests = $_POST['rsvp_guests'];

						if (!is_numeric ($guests)) {
							error_log ('process_rsvp: Non-numeric guest count.');
							return;
						}
					}
					else {
						$guests = 0;
					}
					
					//error_log ('Submitting ')
	
					if (null == $this->get_current_rsvp($event_id, $user_id)) {
						// New RSVP.
						$this->insert_rsvp ($event_id, $user_id, $rsvp, $comment, $guests);
					}
					else {
						// Updated RSVP.
						$this->update_rsvp  ($event_id, $user_id, $rsvp, $comment, $guests);
					}
					
					// Fire an email reminder.
				}				
			}
			/*else
			{
				error_log ('process_rsvp: Called directly');

			}*/
		} 
		

		/**
		 * Saves the current RSVP to the DB.
		 *
		 * @param	
		 */
		function process_ajax_rsvp( $ajax_post ) {
				
			// Only save if we've been passed the 'nonce'... i.e. the event is being renderered as part of an
			// RSVP update.
			// TODO: Flatten this out - return on validation failures.
			if ( ( is_single() || is_page() ) &&
				isset($ajax_post['rsvp_nonce']) &&
				wp_verify_nonce($ajax_post['rsvp_nonce'], 'rsvp')
			) {
				global $wpdb;
				global $rsvp_db_table;

				$accept = 'N';

				if (isset ($ajax_post['accept_terms']))
				{
					$accept = 'Y';
				}
	
				// error_log ('Accept is: '. $accept);

				// error_log ('Saving RSVP data');

				$table_name = $wpdb->base_prefix . $rsvp_db_table;

				$rsvp = 'N';

				if ($ajax_post['submit'] == 'RSVP Yes')	{
					$rsvp = 'Y';
				}

				$tribe_ecp = TribeEvents::instance();

				// Only update DB if 'No' selected or both 'Yes' selected AND accept checkbox ticked.
				if (('Y' == $rsvp) && ('N' == $accept)) {
					$tribe_ecp->setNotice ('rsvp-accept','Please check the &quot;Assumption of Risk&quot; check-box to RSVP.');
					$tribe_ecp->setNotice ('rsvp-no','Your RSVP has not been updated.');
								
					$msg['type'] = 'error';
					$msg['title'] = 'Please check the &quot;Assumption of Risk&quot; check-box to RSVP.';
					$msg['message'] = 'Your RSVP has not been updated.';
								
					$this->setNotice ('rsvp-accept', $msg);
					//$this->setNotice('error', 'Please check the &quot;Assumption of Risk&quot; check-box to RSVP.');
				}
				else
				{
					if ('Y' == $rsvp)	{
						$tribe_ecp->setNotice ('rsvp-yes','You RSVPd \'yes\'.');
		
						$msg['type'] = 'success';
						$msg['title'] = 'RSVP Processed';
						$msg['message'] = 'See you there!';
		
						$this->setNotice ('rsvp-yes', $msg);

					}
					else {
						$tribe_ecp->setNotice ('rsvp-no','You RSVPd \'no\'.');
		
						$msg['type'] = 'success';
						$msg['title'] = 'RSVP Processed';
						$msg['message'] = 'See you next time!';
							
						$this->setNotice ('rsvp-yes', $msg);
					}
				
					$event_id = $ajax_post['rsvp_post_id'];
	//				$comment = $_POST['comment'];
						$comment = ' ';
		
					if (isset ($ajax_post['rsvp_user'])) {
						$user_id = $ajax_post['rsvp_user'];
						} else {
							global $current_user;
							//get_currentuserinfo();
							wp_get_current_user();
				
							// User ID
							$user_id = $current_user->ID;
						}
	
						if (isset ($ajax_post['rsvp_guests'])) {
							$guests = $ajax_post['rsvp_guests'];
						}
						else {
							$guests = 0;
						}
							
					//error_log ('Submitting ')
	
					if (null == $this->get_current_rsvp($event_id, $user_id)) {
						// New RSVP.
						$this->insert_rsvp ($event_id, $user_id, $rsvp, $comment, $guests);
					}
					else {
						// Updated RSVP.
						$this->update_rsvp  ($event_id, $user_id, $rsvp, $comment, $guests);
					}
				}
			}
		}

		
		/*
		 * TODO: Move following notification code to a separate file.
		 */
		
		//
		// Returns an array of user IDs of those who have commented on a post.
		function get_post_comment_users ($post_id) {
		
			$user_list = array();
			
			$comments = get_comments ('post_id='. $post_id);
			
			if (!isset($comments)) {
				//error_log ('No comments for post ID '. $post_id);
				return null;
			}
			
			foreach ($comments as $comment) {
				//error_log ('User '. $comment->user_id . ' has commented on post '. $post_id);
				
				$user_list[] = $comment->user_id;
			}
			
			// TODO: Remove duplicates in $user_list array.
			
			return $user_list;
		}

		//
		// Returns an array of user IDs of those who created the post (will only ever contain a single element).
		function get_post_create_users ($post_id) {
		
			$user_list = array();
				
			$post_object = get_post ($post_id);
			
			if (!isset($post_object)) {
				error_log ('Cannot get post object for post ID '. $post_id);
				return null;
			}
				
			//error_log ('User '. $post_object->post_author . ' created post '. $post_id);
	
			$user_list[] = $post_object->post_author;
				
			return $user_list;
		}
		
		//
		// Returns an array of user IDs of those who have RSVPd to this post.
		function get_rsvpd_users ($post_id) {
			
			// Make sure we're dealing with an event post.
			if (!tribe_is_event ($post_id))
			{
				return null;
			}

			global $wp_query;
			global $wpdb;
			global $rsvp_db_table;
			
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			$sql = 'SELECT user_id ';
			$sql .= ' FROM '. $table_name;
			$sql .= ' WHERE rsvp = \'Y\'';
			$sql .= ' AND event = '. $post_id;
			
			//			  error_log ('    '. $sql);
			
			$rsvps = $wpdb->get_results ($sql);
			
			if (null == $rsvps) {
				return null;
			}
			
			$user_list = array ();
			
			foreach ($rsvps as $rsvp) {
				//error_log ('User '. $rsvp->user_id . ' has RSVPd to event '. $post_id);
				
				$user_list[] = $rsvp->user_id;
			} 
				
			return $user_list;
		}
		
		/*
		 * TODO:
		 */
		function get_event_host_users ($post_id) {
		
			$user_list = array ();
		
			// Make sure we're dealing with an event post.
			if (!tribe_is_event ($post_id))
			{
				return null;
			}
		
			// Get the Tribe Events Organiser.
			$organiser = tribe_get_organizer ($post_id);
		
			if (isset ($organiser)) {
		
				$organiser_user = get_user_by ('login', $organiser);
		
				if (isset ($organiser_user)) {
		
					$user_list[] = $organiser_user->ID;
		
				}
			}
		
			// Get the event hosts.
			$meta_hosts_json = get_post_meta ($post_id, 'bimbler_ride_hosts', true);
		
			if (isset ($meta_hosts_json)) {
		
				$meta_hosts = json_decode($meta_hosts_json);
		
				if (isset ($meta_hosts)) {
		
					foreach ($meta_hosts as $host) {
		
						$user_list[] = $host;
		
					}
				}
			}
			
			// TODO: Remove duplicates - same user may be Tribe organiser as well as event host.
		
			/*			error_log ('Event Hosts:');
		
			foreach ($user_list as $host) {
				
			error_log ('   ' . $host);
				
			} */
		
			return $user_list;
		}
			
		
		function get_from_address () {
			return 'website@bimblers.com';
		}
		
		function get_from_name () {
			return 'Brisbane Bimblers';
		}
		
		function get_content_type () {
			return 'text/html';
		}
		
		/**
		 * Send the email.
		 *
		 * @access public
		 * @param mixed $to
		 * @param mixed $subject
		 * @param mixed $message
		 * @param string $headers (default: "Content-Type: text/html\r\n")
		 * @param string $attachments (default: "")
		 * @param string $content_type (default: "text/html")
		 * @return void
		 */
		function send_email( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "", $content_type = 'text/html' ) {
		
			// Set content type
			$this->_content_type = $content_type;
		
			// Filters for the email
			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		
			// Send
			wp_mail( $to, $subject, $message, $headers, $attachments );
		
			// Unhook filters
			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		}
		

		// 
		// Create HTML message for comment notification messages.
		// Gets the content of the private post with the slug 'email-template-comment'.
		// To
		// Post title
		// Comment user
		// Comment text
		
		function build_comment_notification_email ($user_to, $post_title, $comment_user, $comment_text, $post_url) {
			$slug = 'email-template-comment'; // TODO: Move this into settings.
			
/*			$args=array(
					'name' => $slug,
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => 1
			);
			
			$my_posts = get_posts( $args );
			
			
			
			if( !isset ($my_posts) ) {
				error_log ('Cannot get_post for slug \''. $slug .'\'.');

				return null;
			}
			
			// print_r ($my_posts);
			
			error_log ('ID on the first post found ' . $my_posts[0]->ID);
			
			$email_content = apply_filters('the_content', get_post_field('post_content', $my_posts[0]->ID)); */
			
			$my_post = get_page_by_path($slug, OBJECT, 'post');

			if( !isset ($my_post) ) {
				error_log ('Cannot get_page_by_path for slug \''. $slug .'\'.');
			
				return null;
			}
				
			$email_content = apply_filters('the_content', get_post_field('post_content', $my_post->ID));

			// Add <BR> for newlines.
			$comment_text = str_replace ("\n", "<br>\n", $comment_text);
				
			
			// Replace fields.
			$email_content = str_replace ('[name]', $user_to, $email_content);
			$email_content = str_replace ('[user]', $comment_user, $email_content);
			$email_content = str_replace ('[post_title]', $post_title, $email_content);
			$email_content = str_replace ('[comment_content]', $comment_text, $email_content);
			$email_content = str_replace ('__POST_URL__', $post_url, $email_content);
				
			$email_content = str_replace ('<p>', $this->p_style, $email_content);
			
			return $this->email_html_head . PHP_EOL . $this->email_style . PHP_EOL . $email_content . PHP_EOL . $this->email_html_foot;
		}

		//
		// Create HTML message for photo upload notification messages.
		// Gets the content of the private post with the slug 'email-template-comment'.
		// To
		// Post title
		// Comment user
		// Comment text
		
		function build_photo_upload_notification_email ($user_to, $post_title, $comment_user, $post_url) {
			$slug = 'email-template-photo'; // TODO: Move this into settings.
			
			$my_post = get_page_by_path($slug, OBJECT, 'post');
		
			if( !isset ($my_post) ) {
				error_log ('Cannot get_page_by_path for slug \''. $slug .'\'.');
					
				return null;
			}
		
			$email_content = apply_filters('the_content', get_post_field('post_content', $my_post->ID));
		
			// Replace fields.
			$email_content = str_replace ('[name]', $user_to, $email_content);
			$email_content = str_replace ('[user]', $comment_user, $email_content);
			$email_content = str_replace ('[post_title]', $post_title, $email_content);
			$email_content = str_replace ('__POST_URL__', $post_url, $email_content);
		
			$email_content = str_replace ('<p>', $this->p_style, $email_content);
				
			return $this->email_html_head . PHP_EOL . $this->email_style . PHP_EOL . $email_content . PHP_EOL . $this->email_html_foot;
		}

		//
		// Returns an array of user IDs of those who have RSVPd to this post.
		function get_ngg_image_details ($image_id) {
				
			global $wp_query;
			global $wpdb;
			global $rsvp_db_table;
				
			$table_name = $wpdb->base_prefix . 'ngg_pictures';
				
			$sql = 'SELECT filename ';
			$sql .= ' FROM '. $table_name;
			$sql .= ' WHERE pid = '. $image_id;
				
			//			  error_log ('    '. $sql);
				
			$image = $wpdb->get_row ($sql);
				
			if (null === $image) {
				return null;
			}
		
			return $image->filename;
		}
		
		
		//
		// Create HTML message for image comment notification messages.
		// Gets the content of the private post with the slug 'email-template-comment'.
		// To
		// Post title
		// Comment user
		// Comment text
		function build_image_comment_notification_email ($user_to, $post_title, $comment_user, $comment_text, $post_url, $post_object) {
			$slug = 'email-template-image-comment'; // TODO: Move this into settings.

			// Link to image lightbox is the post excerpt.
			
			//
			// Excerpt: http://mac.bimblers.com
			//	/photos
			//		/gallery
			//			/newalbum
			// 				/2012-08-04-diy-bike-maintenance-course-0km-oily#gallery
			//					/06865d4babb25b40ce46801754e78045
			//						/61
			//							/comments
			//
			//	0: http:
			//	1: 
			//	2: mac.bimblers.com
			//	3: photos
			//	4: gallery
			//	5: newalbum
			//	6: 2012-08-04-diy-bike-maintenance-course-0km-oily#gallery
			//	7: 06865d4babb25b40ce46801754e78045
			//	8: 61
			//	9: comments
			//
			//<a href="http://mac.bimblers.com/wp-content/ngggallery/2012-08-04-diy-bike-maintenance-course-0km-oily/145369342.jpeg" title="" data-src="http://mac.bimblers.com/wp-content/ngggallery/2012-08-04-diy-bike-maintenance-course-0km-oily/145369342.jpeg" data-thumbnail="http://mac.bimblers.com/wp-content/ngggallery/2012-08-04-diy-bike-maintenance-course-0km-oily/thumbs/thumbs_145369342.jpeg" data-image-id="61" data-title="145369342" data-description="" class="nextgen_pro_lightbox" data-nplmodal-gallery-id="06865d4babb25b40ce46801754e78045" data-nplmodal-show-comments="1">
            //    <img title="145369342" alt="145369342" src="http://mac.bimblers.com/wp-content/ngggallery/2012-08-04-diy-bike-maintenance-course-0km-oily/thumbs/thumbs_145369342.jpeg" width="120" height="90" style="max-width:none;">
            //</a>			 
            
			$link = $post_object->post_excerpt;
           
			error_log ('Link to image lightbox (excerpt) is \'' . $link . '\'');
			
			// Get the notification post content.
			$my_post = get_page_by_path($slug, OBJECT, 'post');
		
			if( !isset ($my_post) ) {
				error_log ('Cannot get_page_by_path for slug \''. $slug .'\'.');
					
				return null;
			}
		
			$email_content = apply_filters('the_content', get_post_field('post_content', $my_post->ID));
		
			// Add <BR> for newlines.
			$comment_text = str_replace ("\n", "<br>\n", $comment_text);
				
			// Replace fields.
			$email_content = str_replace ('[name]', $user_to, $email_content);
			$email_content = str_replace ('[user]', $comment_user, $email_content);
			$email_content = str_replace ('[post_title]', $post_title, $email_content);
			$email_content = str_replace ('[comment_content]', $comment_text, $email_content);
			$email_content = str_replace ('__POST_URL__', $link, $email_content);
		
			//return $this->email_html_head . $this->email_style . $this->email_end_style. $email_content . $this->email_html_foot;
			return $this->email_html_head . $this->email_end_style. $email_content . $this->email_html_foot;
		}
		
		// 
		// Comment inserted - notify all those with an interest. This is:
		//  - Those who have also commented
		//  - If this is an event, those who have RSVPd
		//  - The post/event creator
		//  - If this is an event, the event host
		//  - The admin team
		//
		// Don't need to notify the person making this comment!
		//
		// TODO: Split this into a separate plugin.
		//
		function comment_inserted($comment_id, $dunno) {
			
			//error_log ('Comment added, ID '. $comment_id);
			
			$notify_users = array ();

			$comment_object = get_comment ($comment_id);
			
			if (!isset ($comment_object)) {
				error_log ('comment_inserted: cannot get comment object from comment ID '. $comment_id);
				return;
			}
			
			$comment_user = get_userdata ($comment_object->user_id);
			
			if (!isset ($comment_user)) {
				error_log ('Cannot get_userdata for user ID '. $comment_object->user_id);
				return null;
			}
			
			$comment_content = $comment_object->comment_content;
			$post_id = $comment_object->comment_post_ID;
			
			$post_object = get_post ($post_id);
			
			if (!isset ($post_object)) {
				error_log ('Cannot get post details for post ID '. $post_id);
				return null;
			}
			
			error_log ('Comment added to post '. $post_id .', type \'' . $post_object->post_type . '\' by user ID '. $comment_object->user_id);
				
			// Do not send comment notification emails when shop events occur,
			// such as stock amendments.
			if ($post_object->post_type == 'shop_order') {
				// Do nothing - return silently.
				return;
			}
			
			// Only send emails when comment is approved - avoid sending emails for spam comments.
			if ($post_object->post_status != 'publish') {
				// Do nothing - return silently.
				return;
			}

			// Always send mails to admin team. (Was Paul (user ID 1).)
			$notify_users = array_merge ($notify_users, Bimbler_RSVP::get_instance()->get_admin_users());
			
			$users_rsvp = $this->get_rsvpd_users ($post_id);
			$users_comment = $this->get_post_comment_users ($post_id);
			$users_create = $this->get_post_create_users ($post_id);
			$users_hosts = $this->get_event_host_users ($post_id);
			
			$post_title = $post_object->post_title; 
			$post_url = get_permalink ($post_id);
			
			if (isset ($users_rsvp)) {
				$notify_users = array_merge ($notify_users, $users_rsvp);
			}

			if (isset ($users_comment)) {
				$notify_users = array_merge ($notify_users, $users_comment);
			}

			if (isset ($users_create)) {
				$notify_users = array_merge ($notify_users, $users_create);
			}
				
			if (isset ($users_hosts)) {
				$notify_users = array_merge ($notify_users, $users_hosts);
			}
				
			foreach (array_unique ($notify_users) as $notify_user) {
				
				$user_object = get_userdata ($notify_user);
				
				if (!isset ($user_object)) {
					error_log ('Cannot get_userdata for user ID '. $notify_user);
					return null;
				}

				// Only send mails to users who want to be notified.
				$meta = get_user_meta ($notify_user, 'rpr_comment_notifications', true);
				
				// If meta data not set, then assume that user has not opted out.
				if ((0 == strlen ($meta)) || ('Yes' == $meta)) {
					
					error_log ('Notifying user '. $notify_user .' ('. $user_object->display_name .') at '. $user_object->user_email);

					// Remove 'xx_' to activate.
					if ($post_object->post_type == 'photocrati-comments') {

						$email_content = $this->build_image_comment_notification_email ($user_object->display_name,
																						$post_title,
																						$comment_user->display_name,
																						$comment_content,
																						$post_url,
																						$post_object);
						
						if (!isset ($email_content)) {
							error_log ('Cannot create comment notification email content.');
							return null;
						}
							
						$subject = 'Brisbane Bimblers - '. $comment_user->display_name .' commented on '. $post_title;
							
					
							
					} else { // Everything else.
					
						$email_content = $this->build_comment_notification_email (	$user_object->first_name, 
																					$post_title,
																					$comment_user->display_name,
																					$comment_content,
																					$post_url);
						
						if (!isset ($email_content)) {
							error_log ('Cannot create comment notification email content.');
							return null;
						}
						 
						$subject = 'Brisbane Bimblers - '. $comment_user->display_name .' commented on '. $post_title;
					}
					
					// Filters for the email
					add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
					add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
					add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
					
					wp_mail( $user_object->user_email, $subject, $email_content);
					
					// Unhook filters
					remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
					remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
					remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
				} 
				else {
					error_log ('Not notifying user '. $notify_user .' ('. $user_object->display_name .') at '. $user_object->user_email);
				}
			}
		}
		
		//
		// Create HTML message for new user notification messages.
		// Gets the content of the private post with the slug 'email-template-comment'.
		// To
		// Post title
		// Comment user
		// Comment text
		// TODO: Move this to Notifications plugin.
		//
		
		function build_new_user_notification_email ($user_to) {
			$slug = 'email-template-new-user'; // TODO: Move this into settings.

			$my_post = get_page_by_path($slug, OBJECT, 'post');
		
			if( !isset ($my_post) ) {
				error_log ('Cannot get_page_by_path for slug \''. $slug .'\'.');
					
				return null;
			}
		
			$email_content = apply_filters('the_content', get_post_field('post_content', $my_post->ID));
		
			// Add <BR> for newlines.
			//$comment_text = str_replace ("\n", "<br>\n", $comment_text);
		
				
			// Replace fields.
			$email_content = str_replace ('[name]', $user_to, $email_content);
			//$email_content = str_replace ('[user]', $comment_user, $email_content);
			//$email_content = str_replace ('[post_title]', $post_title, $email_content);
			//$email_content = str_replace ('[comment_content]', $comment_text, $email_content);
			//$email_content = str_replace ('__POST_URL__', $post_url, $email_content);
		
			//return $this->email_html_head . $this->email_style . $this->email_end_style. $email_content . $this->email_html_foot;
			//return $this->email_html_head . $email_content . $this->email_html_foot;
			
			// Add the list of events to the mail.
			// Get events from tomorrow onwards.
			$date_from = date('Y-m-d', strtotime('1 days'));
			
			$events_html = '';
			
			if (class_exists('Bimbler_Reminders')) {
				$events_html = Bimbler_Reminders::get_instance()->get_upcoming_events_html($date_from);
			}
							
			$email_content = str_replace ('<p>[upcoming_events_list]</p>', $events_html, $email_content);
			
			
			$email_content = str_replace ('<p>', $this->p_style, $email_content);
				
			return $this->email_html_head . PHP_EOL . $this->email_style . PHP_EOL . $email_content . PHP_EOL . $this->email_html_foot;
				
		}
		
		//
		// New user approved - notify all those with an interest. This is:
		//  - The user themselves
		//  - The admin team
		//
		// TODO: Split this into a separate plugin.
		//
		function user_approved ($user_id) {
			
			$new_user = get_userdata ($user_id);
				
			if (!isset ($new_user)) {
				error_log ('Cannot get_userdata for user ID '. $user_id);
				return null;
			}

			$notify_users = array ();
			$notify_users[] = $user_id;

			// Always send mails to admin team.
			$notify_users = array_merge ($notify_users, $this->get_admin_users());
		
			$email_content = $this->build_new_user_notification_email ($new_user->first_name);
			
			if (!isset ($email_content)) {
				error_log ('Cannot create comment notification email content.');
				return null;
			}

			$subject = 'Brisbane Bimblers - Welcome!';
				
			// Filters for the email
			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
			
			foreach (array_unique ($notify_users) as $notify_user) {
		
				$user_object = get_userdata ($notify_user);
		
				if (!isset ($user_object)) {
					error_log ('Cannot get_userdata for user ID '. $notify_user);
					return null;
				}
			
				error_log ('Notifying user '. $notify_user .' ('. $user_object->display_name .') at '. $user_object->user_email);
			
				wp_mail( $user_object->user_email, $subject, $email_content);
			}

			// Unhook filters
			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		}
		
		// This function is called at the bottom of the profile page, when another user 
		// (hopefully, only the admin) edits a user's profile.
		// In this instance we use it to determine when a new user has been approved.
		// For this, the flow is:
		//  - Hook fired with $user_object->caps holding an element of 'rpr_unverified'
		//  - We save the $user_object->caps transient, containing 'rpr_unverified'.
		//  - Admin updates the caps to 'subscriber' and saves the page.
		//  - Page save triggers the hook again, so we load the transient.
		//  - If the transient is found and contains 'rpr_unverified', and the new $user_object->caps 
		//    are set to 'subscriber' then we know that this user has just been approved. 
		//  - We then fire off the requisite emails.
		//
		//function profile_updated ($user_id) {
		function profile_updated ($user_object) {//, $old_user_data) {

			//$user_object = get_userdata($user_id);
			error_log (' ');
	
			// Get old user data from transient.
			error_log ('Getting transient data for user ID ' . $user_object->ID);
			$old_user_data = get_transient( 'bimbler_old_user_data_' . $user_object->ID );
			
			error_log ('Old caps: ' . json_encode ($old_user_data));
			error_log ('New caps: ' . json_encode ($user_object->caps));
				
			// We only want to send a notification if the caps have changed from 'unverified' to 'subscriber'.
			// Bug-fix - RPR seems to not be setting rpr_unverified.
			if ((!empty ($old_user_data) && array_key_exists ('rpr_unverified', $old_user_data)) && array_key_exists ('subscriber', $user_object->caps)) {

				error_log ('New user signed-up: ' . $user_object->first_name . ' ' . $user_object->last_name);
	
				$this->user_approved($user_object->ID);
			}

			// Save the caps for next time around the loop.			
			error_log ('Saving caps:' . json_encode($user_object->caps));
			set_transient( 'bimbler_old_user_data_' . $user_object->ID, $user_object->caps, 60 * 60 );
			error_log (' ');
		}
		
		// This function is called via a hook from the New User Approve plugin when a new user has been
		// approved by an admin.		
		function new_user_approved ($user_object) {

			error_log ('New user signed-up: ' . $user_object->first_name . ' ' . $user_object->last_name);

			$this->user_approved($user_object->ID);

		}
				
		/**
		 *
		 * @param $gallery_id
		 * @param $image_ids - array of images
		
		 */
		function __photo_uploaded($gallery_id, $image_ids) {
			error_log (count ($image_ids) . ' photo(s) uploaded to gallery ' . $gallery_id);
		}
		
		
		//
		// New photo uploaded - notify all those with an interest. This is:
		//  - Those who have also commented
		//  - If this is an event, those who have RSVPd
		//  - The post/event creator
		//  - If this is an event, the event host
		//  - The admin team
		//
		// Don't need to notify the person uploading the picture!
		//
		// TODO: Split this into a separate plugin.
		//
		function photo_uploaded($gallery_id, $image_ids) {
			
			error_log (count ($image_ids) . ' photo(s) uploaded to gallery ' . $gallery_id);

			$msg['type'] = 'success';
			$msg['title'] = 'Photo Uploaded';
			$msg['message'] = 'Your uploaded image will be visible shortly.';
				
			$this->setNotice ('photo-uploaded', $msg);
			
			$notify_users = array ();
			
			global $current_user;
			//get_currentuserinfo();
			wp_get_current_user ();
			$user_id = $current_user->ID;
				
			
			global $wp_query;
			$post_id = $wp_query->post->ID;
			
			// Work around bug.
			$post_id = get_queried_object_id();

			// If fired from the mobile site, we won't have the query object set.
			if (0 == $post_id) {
				$post_id = $_GET['event'];
				
			}
			//error_log ('Photo added to post '. $post_id .' by user ID '. $user_id);
		
			$post_object = get_post ($post_id);
				
			if (!isset ($post_object)) {
				error_log ('photo_uploaded: Cannot get post details for post ID '. $post_id);
				return null;
			}
			
			// Only notify user if this is a gallery attached to an event.
			if ($post_object->post_type != 'tribe_events') {
				error_log ('This is not an event - no need to notify.');
				return null;
			}
		
			// TOOD: Change this to get_admin_users.
			// Always send mails to Paul (user ID 1).
			$notify_users[] = 1;
				
			$users_rsvp = $this->get_rsvpd_users ($post_id);
			$users_comment = $this->get_post_comment_users ($post_id);
			$users_create = $this->get_post_create_users ($post_id);
			$users_hosts = $this->get_event_host_users ($post_id);
				
			$post_title = $post_object->post_title;
			$post_url = get_permalink ($post_id);
				
			if (isset ($users_rsvp)) {
				$notify_users = array_merge ($notify_users, $users_rsvp);
			}
		
			if (isset ($users_comment)) {
				$notify_users = array_merge ($notify_users, $users_comment);
			}
			
			if (isset ($users_create)) {
				$notify_users = array_merge ($notify_users, $users_create);
			}
			
			if (isset ($users_hosts)) {
				$notify_users = array_merge ($notify_users, $users_hosts);
			}
		
			foreach (array_unique ($notify_users) as $notify_user) {
			
				$user_object = get_userdata ($notify_user);
				
				if (!isset ($user_object)) {
					error_log ('Cannot get_userdata for user ID '. $notify_user);
					return null;
				}
		
				// Only send mails to users who want to be notified.
				$meta = get_user_meta ($notify_user, 'rpr_comment_notifications', true);
				
				if (isset ($user_object->user_email)) {
					// If meta data not set, then assume that user has not opted out.
					if ((0 == strlen ($meta)) || ('Yes' == $meta)) {
						
						error_log ('Notifying user '. $notify_user .' ('. $user_object->display_name .') at '. $user_object->user_email);
			
					
						$email_content = $this->build_photo_upload_notification_email (	$user_object->display_name,
																					$post_title,
																					$current_user->display_name,
																					$post_url);
				
						if (!isset ($email_content)) {
								error_log ('Cannot create photo upload notification email content.');
								return null;
						}
										
						$subject = 'Brisbane Bimblers - '. $current_user->display_name .' uploaded a picture to '. $post_title;
								
						// Filters for the email
						add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
						add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
						add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
					
						wp_mail( $user_object->user_email, $subject, $email_content);
					
						// Unhook filters
						remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
						remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
						remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
					}
					else {
						error_log ('Not notifying user '. $notify_user .' ('. $user_object->display_name .') at '. $user_object->user_email);
					}
				}
			}
		}		
		
		function get_cat_slug($cat_id) {
			$cat_id = (int)$cat_id;
			$category = &get_category($cat_id);
			return $category->slug;
		}
		


		/* 
		 * Process post content. If post has 'ride' as the parent category then add the ride map.
		*/
		function add_ride_map ($content) {
		
			//error_log ('Checking categories...');
		
			$categories = wp_get_post_categories (get_the_ID());
				
			if (!isset ($categories)) {
				//error_log ('No categories.');
				return $content;
			}
				
			foreach ($categories as $c) {
				$category = get_category ($c);
		
				//error_log ('Checking category \''. $category->name . '\'.');
		
				// We need to get the parent category.
				if (isset ($category->category_parent)) {
		
					$parent = get_cat_name ($category->category_parent);
						
					//error_log ('Checking parent \''. $parent . '\'.');
		
					if ('Ride' == $parent) {
						
						$rwgps_id = $this->get_rwgps_id (get_the_ID());
						
						if (0 != $rwgps_id) {

							//error_log ('Embedding map ID: ' . $rwgps_id);
						
							// [iframe src="//ridewithgps.com/routes/782261/embed" height="800px" width="100%" frameborder="0"]
							$iframe = sprintf('[iframe src="//ridewithgps.com/routes/%1$s/embed" height="800px" width="100%" frameborder="0"]', $rwgps_id);
						
							$content .= do_shortcode ($iframe);
						}
		
						return $content;
					}
				}
			}
				
			// Carry on - nothing to see here.
			return $content;
		}
				
		function show_icalendar_link () {
			$tribe_ecp = TribeEvents::instance();
			
			
			echo '<div class="tribe-events-cal-links">';
			//echo '<a class="tribe-events-gcal tribe-events-button" href="' . tribe_get_gcal_link() . '" title="' . __( 'Add to Google Calendar', 'tribe-events-calendar-pro' ) . '">+ Chocolate Giraffes </a>';
			echo '<a class="tribe-events-ical tribe-events-button" href="' . tribe_get_single_ical_link() . '">+ iCal Import </a>';
			echo '</div><!-- .tribe-events-cal-links -->';
			//echo tribe_get_ical_link();
			
			//$tribe_ecp->generate_ical_feed ();
			
			/*echo '	<a href="'. tribe_get_ical_link() .'" target="_blank"><span class="fa-stack fa-lg">';
			echo '	  <i class="fa fa-circle fa-stack-2x"></i>';
			echo '	  <i class="fa fa-flag fa-stack-1x fa-inverse"></i>';
			echo '	</span>';
			
			echo 'Bimbler iCal';
			echo '</a>'; */

		}
		
		/* TODO: Move register widget calls into a function. */
		
		// Register RSVP widget.
		function register_bimbler_rsvp_widget() {
			register_widget( 'Bimbler_RSVP_Widget' );
		}
		
		// Register tabs widget.
		function register_bimbler_tabs_widget() {
			register_widget( 'Bimbler_Tabs_Widget' );
		}
		
		// Register sales widget.
		function register_bimbler_sales_widget() {
			register_widget( 'Bimbler_Sales_Widget' );
		}
		
		// Register user admin widget.
		function register_bimbler_useradmin_widget() {
			register_widget( 'Bimbler_UserAdmin_Widget' );
		}

		function register_bimbler_noodle_widget() {
			register_widget( 'Bimbler_Noodle_Widget' );
		}
		
		function register_bimbler_nexton_widget() {
			register_widget( 'Bimbler_NextOn_Widget' );
		}
		
		function register_bimbler_mobile_widget() {
			register_widget( 'Bimbler_Mobile_Widget' );
		}
		
		function register_bimbler_edit_attendees_widget() {
			register_widget( 'Bimbler_Edit_Attendees_Widget' );
		}
		
		function register_bimbler_join_us_widget() {
			register_widget( 'Bimbler_Join_Us_Widget' );
		}

		function register_bimbler_download_gps_widget() {
			register_widget( 'Bimbler_Download_GPS_Widget' );
		}

		function register_bimbler_comments_widget() {
			register_widget( 'Bimbler_Comments_Widget' );
		}
		
		function display_admin_options_page () {
			echo '<div class="wrap"><h4>Options page goes here</h4></div>';
		}
		
/*		function display_rsvp_options_page () {
			echo '<h4>RSVP options page goes here</h4>';
		} */
		
		function display_email_options_page () {
			echo '<h4>Email options page goes here</h4>';
		}
		
		function create_admin_menu (){
			//  			add_options_page( 'Bimbler RSVP Options', 'Bimbler RSVP', 'manage_options', 'bimbler_rsvp-id', array ($this, 'rsvp_plugin_options'));
		
			// Main menu.
			add_menu_page( 	'Bimblers', 
							'Bimblers', 
							'manage_options', 
							'bimbler-rsvp', 
							array ($this, 'display_admin_options_page'));
		
			// Submenus.
//			add_submenu_page( 'bimbler-rsvp', 'RSVPs Config', 'RSVPs', 'manage_options', 'bimbler-rsvps', array ($this, 'display_rsvp_options_page'));
			//add_submenu_page( 'bimbler-rsvp', 'RSVP Emails', 'RSVP Emails', 'manage_options', 'bimbler-emails', array ($this, 'display_email_options_page'));
		}
		
		/**
		 * Determines how many users have RSVPd to this event.
		 * 
		 * TODO: Is this a duplicate? Contains no code for guests.
		 *
		 * @param $event
		 */
		
		public function get_user_events_attended ($user_id) 
		{
			global $wp_query;
			global $wpdb;
			global $rsvp_db_table;
		
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
		
			$sql = 'SELECT COUNT(*) AS num ';
			$sql .= ' FROM '. $table_name;
			$sql .= ' WHERE rsvp = \'Y\'';
			$sql .= ' AND user_id = '. $user_id;
			$sql .= ' AND attended = \'Y\'';
		
			//			  error_log ('    '. $sql);
		
			$link = $wpdb->get_row ($sql);
		
			if (null == $link) {
				return null;
			}
				
			return $link->num;
		
		}
		
		/*
		 * TODO: Is this a duplicate?
		 */
		public function get_user_rsvps ($user_id) 
		{
			global $wpdb;
			global $rsvp_db_table;
				
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			$sql =  'SELECT * FROM '. $table_name;
			$sql .= ' WHERE ';//--rsvp = \'Y\'';
			$sql .= ' user_id = ' . $user_id;
			$sql .= ' ORDER BY time DESC';
			
			//var_dump ($sql);
			
			$rsvps = $wpdb->get_results ($sql);
			
			if (null === $rsvps) {
				error_log ('get_rsvps: cannot get RSVPs.');
				$wpdb->print_error();
			}
			
			return $rsvps;
		}
		
		public function get_user_activity ($user_id)
		{
			global $wpdb;
			global $rsvp_db_table;
			
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
				
			$sql  =  '(SELECT '; // Comments.
			$sql .=  ' \'comment\' AS type, ';
			$sql .=  ' c.comment_post_id AS post_id, ';
			$sql .=  ' c.comment_date AS time, ';
			$sql .=  ' c.comment_ID AS other1, ';
			$sql .=  ' \'\' AS other2, ';
			$sql .=  ' \'\' AS other3 ';
			$sql .=  " FROM {$wpdb->comments} c, ";
			$sql .=  " {$wpdb->posts} p, ";
			$sql .=  " {$wpdb->users} u ";
			$sql .=  ' WHERE u.user_login = c.comment_author ';
			$sql .=  ' AND c.comment_post_ID = p.ID ';
  			$sql .=  ' AND p.post_type != \'shop_order\' ';
			$sql .=  ' AND u.id = ' . $user_id;
			$sql .=  ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // RSVPs.
			$sql .=  '		\'rsvp\' AS type, ';
			$sql .=  ' 		event AS post_id, ';
			$sql .=  ' 		time AS time, ';
			$sql .=  ' 		rsvp AS other1, ';
			$sql .=  ' 		\'\' AS other2, ';
			$sql .=  ' 		\'\' AS other3 ';
			$sql .=  " 		FROM {$wpdb->base_prefix}{$rsvp_db_table} ";
			$sql .=  ' 		WHERE user_id = ' . $user_id;
			$sql .=  ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // Joined.
			$sql .=  ' 		\'joined\' AS type, ';
			$sql .=  ' 		0 AS post_id, ';
			$sql .=  ' 		user_registered AS time, ';
			$sql .=  ' 		\'\' AS other1, ';
			$sql .=  ' 		\'\' AS other2, ';
			$sql .=  ' 		\'\' AS other3 ';
			$sql .=  " 		FROM {$wpdb->users} ";
			$sql .=  '		WHERE id = ' . $user_id;
			$sql .= ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // Photos.
			$sql .=  ' 		\'photo\' AS type, ';
			$sql .=  ' 		0 AS post_id, ';
			$sql .=  ' 		FROM_UNIXTIME(pic.updated_at) AS time, ';
			$sql .=  ' 		g.path AS other1, ';
			$sql .=  ' 		pic.filename AS other2, ';
			$sql .=  ' 		\'\' AS other3 ';
			$sql .=  " 		FROM {$wpdb->base_prefix}ngg_pictures pic, ";
			$sql .=  " 		{$wpdb->posts} p, ";
			$sql .=  "	 	{$wpdb->base_prefix}ngg_gallery g ";
			$sql .=  ' 		WHERE p.post_author = ' . $user_id;
			$sql .=  ' 		AND p.id = pic.extras_post_id ';
			$sql .= '		AND pic.exclude = 0 ';
			$sql .= ' 		AND g.gid = pic.galleryid ';
			$sql .= ' 		ORDER BY time DESC ';
			$sql .= ' 		LIMIT 20 ';
			//$sql .= ' 		AND g.gid > 102 ';
			$sql .= ' ) ';
			$sql .= 'UNION ';
			$sql .= '(SELECT '; // Last login.
			$sql .= '		\'login\' AS type, ';
			$sql .= ' 		0 AS post_id, ';
			$sql .= '		FROM_UNIXTIME(meta_value) AS time, ';
			$sql .= ' 		\'\' AS other1, ';
			$sql .= ' 		\'\' AS other2, ';
			$sql .= ' 		\'\' AS other3 ';
			$sql .= "		FROM {$wpdb->usermeta} ";
			$sql .= '		WHERE meta_key = \'wp-last-login\' ';
			$sql .= '		AND user_id = ' . $user_id;
			$sql .= ' ) ';
			$sql .= 'UNION ';
			$sql .= '(SELECT '; // Order.
	 		$sql .= '		\'order\' AS type, ';
	 		$sql .= ' 		ID AS post_id, ';
	 		$sql .= '		post_date AS time, ';
	 		$sql .= ' 		\'\' AS other1, ';
	 		$sql .= ' 		\'\' AS other2, ';
	 		$sql .= ' 		\'\' AS other3 ';
	 		$sql .= "		FROM {$wpdb->posts} ";
	 		$sql .= '		WHERE post_type = \'shop_order\' ';
	 		$sql .= '		AND post_status = \'publish\' ';
	 		$sql .= '		AND comment_status = \'open\' ';
	 		$sql .= '		AND post_author = ' . $user_id;
	 		$sql .= ' ) '; 
			$sql .= 'UNION ';
			$sql .= '(		select '; // Event attended.
			$sql .= ' 		\'attended\' AS type, ';
			$sql .= ' 		p.ID AS post_id, ';
			$sql .= '		wpm.meta_value AS time, ';
			$sql .= '		\'\' AS other_1, ';
			$sql .= ' 		\'\' AS other2, ';
			$sql .= ' 		\'\' AS other3 ';
			$sql .= "		from {$wpdb->postmeta} wpm, ";
			$sql .= "		{$wpdb->posts} p, ";
			$sql .= "		{$wpdb->base_prefix}{$rsvp_db_table} r ";
			$sql .= '		where p.ID = wpm.post_id ';
			$sql .= '		and r.user_id = ' . $user_id;
			$sql .= '		and r.event = p.ID ';
			$sql .= '		and wpm.meta_key = \'_EventEndDate\' ';
			$sql .= '		and r.rsvp = \'Y\' ';
			$sql .= '		and r.attended = \'Y\' ';
			$sql .= '		and wpm.meta_value < NOW() ';
			$sql .= ' ) '; 
			
			$sql .= ' 		ORDER BY time DESC; ';
				
			//var_dump ($sql);
				
			$activities = $wpdb->get_results ($sql);
				
			if (null === $activities) {
			//if (!defined ($activities)) {
				error_log ('get_user_activity: cannot get activity.');
				$wpdb->print_error();
			}
				
			return $activities;
		}
		

		public function get_timeline_activity ($days)
		{
			global $wpdb;
			global $rsvp_db_table;
				
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();			
			
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
		
			$sql  =  '(SELECT '; // Comments.
			$sql .=  ' \'comment\' AS type, ';
			$sql .=  ' c.comment_post_id AS post_id, ';
			$sql .=  ' c.comment_date AS time, ';
			$sql .=  ' u.id AS user_id, ';
			$sql .=  ' c.comment_ID AS other1, ';
			$sql .=  ' u.id AS other2, ';
			$sql .=  ' \'\' AS other3 ';
			$sql .=  " FROM {$wpdb->comments} c, ";
			$sql .=  " {$wpdb->posts} p, ";
			$sql .=  " {$wpdb->users} u ";
			$sql .=  ' WHERE u.user_login = c.comment_author ';
			$sql .=  ' AND c.comment_post_ID = p.ID ';
			$sql .=  ' AND p.post_type != \'shop_order\' ';
			$sql .=  ' AND c.comment_date >= DATE_ADD(NOW(), INTERVAL -' . $days . ' DAY) ';
			$sql .=  ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // RSVPs.
			$sql .=  '		\'rsvp\' AS type, ';
			$sql .=  ' 		event AS post_id, ';
			$sql .=  ' 		time AS time, ';
			$sql .=  ' 		user_id AS user_id, ';
			$sql .=  ' 		rsvp AS other1, ';
			$sql .=  ' 		user_id AS other2, ';
			$sql .=  ' 		\'\' AS other3 ';
			$sql .=  " 		FROM {$table_name} ";
			$sql .=  ' 		WHERE time >= DATE_ADD(NOW(), INTERVAL -' . $days . ' DAY) ';
			$sql .=  ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // Joined.
			$sql .=  ' 		\'joined\' AS type, ';
			$sql .=  ' 		0 AS post_id, ';
			$sql .=  ' 		user_registered AS time, ';
			$sql .=  ' 		ID AS user_id, ';
			$sql .=  ' 		ID AS other1, ';
			$sql .=  ' 		\'\' AS other2, ';
			$sql .=  ' 		\'\' AS other3 ';
			$sql .=  " 		FROM {$wpdb->users} ";
			$sql .=  '		WHERE user_registered >= DATE_ADD(NOW(), INTERVAL -' . $days . ' DAY) ';
			$sql .= ' ) ';
			$sql .=  ' UNION ';
			$sql .=  ' (SELECT '; // Photos.
			$sql .=  ' 		\'photo\' AS type, ';
			$sql .=  ' 		0 AS post_id, ';
			$sql .=  ' 		FROM_UNIXTIME(pic.updated_at) AS time, ';
			$sql .=  ' 		p.post_author AS user_id, ';
			$sql .=  ' 		g.path AS other1, ';
			$sql .=  ' 		pic.filename AS other2, ';
			$sql .=  ' 		p.post_author AS other3 ';
			$sql .=  " 		FROM {$wpdb->base_prefix}ngg_pictures pic, ";
			$sql .=  " 		{$wpdb->posts} p, ";
			$sql .=  "	 	{$wpdb->base_prefix}ngg_gallery g ";
			$sql .=  ' 		WHERE FROM_UNIXTIME(pic.updated_at) >= DATE_ADD(NOW(), INTERVAL -' . $days . ' DAY) ';
			$sql .=  ' 		AND p.id = pic.extras_post_id ';
			$sql .= '		AND pic.exclude = 0 ';
			$sql .= ' 		AND g.gid = pic.galleryid ';
			//$sql .= ' 		ORDER BY time DESC ';
			//$sql .= ' 		LIMIT 20 ';
			////$sql .= ' 		AND g.gid > 102 ';
			$sql .= ' ) ';
			$sql .= 'UNION ';
			$sql .= '(SELECT '; // Last login.
			$sql .= '		\'login\' AS type, ';
			$sql .= ' 		0 AS post_id, ';
			$sql .= '		FROM_UNIXTIME(meta_value) AS time, ';
			$sql .= ' 		user_id AS user_id, ';
			$sql .= ' 		\'\' AS other1, ';
			$sql .= ' 		\'\' AS other2, ';
			$sql .= ' 		\'\' AS other3 ';
			$sql .= "		FROM {$wpdb->usermeta} ";
			$sql .= '		WHERE meta_key = \'wp-last-login\' ';
			//$sql .= '		AND user_id = ' . $user_id;
			$sql .=  ' 		AND FROM_UNIXTIME(meta_value) >= DATE_ADD(NOW(), INTERVAL -' . $days . ' DAY) ';
			$sql .= ' ) ';
			
			$sql .= 'UNION ';
			$sql .= '(		select '; // Event attended.
			$sql .= ' 		\'attended\' AS type, ';
			$sql .= ' 		p.ID AS post_id, ';
			$sql .= '		wpm.meta_value AS time, ';
			$sql .=  ' 		r.user_id AS user_id, ';
			$sql .= '		\'\' AS other_1, ';
			$sql .= ' 		\'\' AS other2, ';
			$sql .= ' 		\'\' AS other3 ';
			$sql .= "		from {$wpdb->postmeta} wpm, ";
			$sql .= "		{$wpdb->posts} p, ";
			$sql .= "		{$table_name} r ";
			$sql .= '		where p.ID = wpm.post_id ';
			$sql .= '		and r.event = p.ID ';
			$sql .= '		and wpm.meta_key = \'_EventStartDate\' ';
			$sql .= '		and r.rsvp = \'Y\' ';
			$sql .= '		and r.attended = \'Y\' ';
			$sql .= '		and wpm.meta_value < NOW() ';
			$sql .= ' ) '; 
							
			$sql .= ' 		ORDER BY time DESC; ';
				
			//var_dump ($sql);
			//error_log ('SQL for timeline: ' . $sql);
		
			$activities = $wpdb->get_results ($sql);
		
			if (null === $activities) {
				error_log ('get_timeline_activity: cannot get activity.');
				$wpdb->print_error();
			}
		
			return $activities;
		}
		
		public function get_gallery_pic_count ($post_id) {
			global $wpdb;

			$meta_gallery_id = get_post_meta ($post_id, 'bimbler_gallery_id', true);

			if (!isset($meta_gallery_id) || (0 == $meta_gallery_id)) {
				//error_log ('No gallery set for event '. $post_id);

				return 0;
			}

			$sql =  'SELECT COUNT(p.pid) AS num_pics';
			$sql .= ' FROM ' . $wpdb->base_prefix . 'ngg_gallery AS g, ';
			$sql .=  $wpdb->base_prefix . 'ngg_pictures AS p ';
			$sql .= ' WHERE p.galleryid = g.gid ';
			$sql .= ' AND p.exclude = 0 ';
			$sql .= ' AND g.gid = ' . $meta_gallery_id;
			$sql .= ' GROUP BY g.gid ';
		
			$galleries = $wpdb->get_results ($sql);
		
			$pics = $wpdb->get_row ($sql);
			
			if (null == $pics) {
				return 0;
			}
			
			//error_log ('Gallery ' . $meta_gallery_id . ' has ' . $pics->num_pics . ' pics.');
			
			return $pics->num_pics;
		}
		
		// TODO: Move to bimbler-woofoo plugin.
		public function bimbler_add_order_report ($reports) {

			//error_log ('Reports: ' . print_r ($reports, true));
	
			$reports['stock']['reports']['items_to_order'] = array(
						'title'       => 'Items to Order',
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( 'WC_Admin_Reports', 'get_report' )
					);

			//error_log ('Reports: ' . print_r ($reports, true));
			
			return $reports;
		}
		
		/**
		 * Update timezone.
		 *
		 * @return null
		 */
		function set_timezone () {
			global $bimbler_timezone;
		
//			date_default_timezone_set($bimbler_timezone);
		}

		/**
		 * Returns an array of user IDs who have the administrator capability.
		 *
		 * @param	none
		 */
		function get_admin_users () {

			$users = get_users (array (	'role'		=> 'administrator',
										'fields'	=> 'ID'));

			return $users;
		}
				
						
		/*
		 * Generates CSS elements which contain the 'Primary Color' setting in the Theme's 'Styling' configuration.
		*/
		function add_dynamic_style () {
			 
			$output = '<style type="text/css">' . PHP_EOL;
			 
			//$colour = ot_get_option('color-1');
			$colour = '#dd9933';

			$output .= '[data-notifications]:after { background: ' . $colour . '; }' . PHP_EOL;
			$output .= '.bimbler-button { background: ' . $colour . ' !important; }' . PHP_EOL;
			 
			$output .= '</style>' . PHP_EOL;
			 
			echo $output;
		}
		 
		/*
		 * TODO: Add to admin file.
		 */
		// TODO: Should be called admin_SHOW_cost_field.
		function tribe_events_admin_hide_cost_field () {

			return false;
		}
		
        /*
         * Determines if the user can execute Ajax, and checks if the Ajax Bimbler plugin is loaded.
        */
        function can_modify_attendance ($event_id = null) {

                // Ajax module not loaded - no point in showing Ajax-enabled controls.
/*              if (!class_exists (BIMBLER_AJAX_CLASS)) {
                        error_log ('User can\'t run Ajax - BIMBLER_AJAX_CLASS not loaded - so cannot modify attendance.');
                        return false;
                } */

                // Admins can do everything!
                if (current_user_can ('manage_options')) {
                        //error_log ('User is admin - can modify attendance.');

                        return true;
                }

                // Event hosts can modify attendance.
                if (isset ($event_id)) {

                        global $current_user;
                        //get_currentuserinfo();
						wp_get_current_user ();

                        $host_users = Bimbler_RSVP::get_instance()->get_event_host_users ($event_id);

                        if (isset ($host_users) && in_array ($current_user->ID,$host_users)) {

                                //error_log ('This user is a host for event ' . $event_id . ' - can modify attendance.');

                                return true;

                        }
                }

                //error_log ('Current user cannot modify attendance.');

                return false;
        }

		function get_next_event () {
			// Fix-up timezone bug.
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();

			$posts = tribe_get_events(array(
					'eventDisplay'          => 'all',
					//'start_date'          => date('Y-m-d', strtotime('1 days')), // From tomorrow - we should already be at today's ride.
					'start_date'            => date('Y-m-d H:i:s'), // From now, not midnight - we should already be at today's ride.
					'posts_per_page'        => 1) ); 

			return $posts;
		}

		// Get future events - ignores in-flight events.		
		function get_future_events ($events_per_page) {
			
			// Fix-up timezone bug.
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();	

			$posts = tribe_get_events( array(
				'eventDisplay' 	=> 'custom',
				'posts_per_page'=>	-1,
				'meta_query' 	=> array(
						array(
								'key' 		=> '_EventStartDate',
								'value' 	=> date('Y-m-d H:i:s'), // Now onwards.
								'compare' 	=> '>=',
								'type' 		=> 'date'
						),
						'orderby' 	=> '_EventEndDate',
						'order'	 	=> 'ASC'
				)));

	
			return $posts;
		}
		
		function get_upcoming_events ($events_per_page) {
			
			// Fix-up timezone bug.
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();

			// Fallback, but sometimes gets the order wrong.
			$posts = tribe_get_events(array(
					'eventDisplay'          => 'all',
					'start_date'            => date('Y-m-d H:i:s'), // From now, not midnight - we should already be at today's ride.
					'posts_per_page'        => $events_per_page) ); 
			
/*			
			// This is the better technique, but does not show in-flight events.
			$posts = tribe_get_events( array(
					'eventDisplay' 	=> 'custom',
					'posts_per_page'=>	$events_per_page,
					'meta_query' 	=> array(
							array(
									//'key' 		=> '_EventStartDate',
									'key' 		=> '_EventEndDate',		// Events which will be ending after now - show in-flight events.
									'value' 	=> date('Y-m-d H:i:s'), // Now onwards.
									'compare' 	=> '>',
									'type' 		=> 'date'
							),
							'orderby' 	=> '_EventEndDate',
							'order'	 	=> 'ASC'
					)));
*/
	
			return $posts;
		}
		
		function get_past_events ($events_per_page) {
		
			// Fix-up timezone bug.
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();

			$posts = tribe_get_events( array(
					'eventDisplay' 	=> 'custom',
					'posts_per_page'=>	$events_per_page,
					'order'			=> 'DESC', 
					'meta_query' 	=> array(
							array(
									'key' 		=> '_EventStartDate',
									'value' 	=> date('Y-m-d H:i:s'), // Now onwards.
									'compare' 	=> '<=',
									'type' 		=> 'date'
							)
					)));
			
			return $posts;
		}
		
		function get_added_events ($events_per_page) {
		
			// Fix-up timezone bug.
//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();
							
			$posts = tribe_get_events( array(
					'eventDisplay'   => 'all', //'upcoming',
					'posts_per_page' => $events_per_page,
					'orderby'		=> 'post_date',
					'order'			=> 'DESC'
					));
			
			return $posts;
		}

		function get_next_ride_object () {
			
			$ride = new stdClass();
			
			$date_str = 'D j M';

//			date_default_timezone_set('Australia/Brisbane');
			$this->set_timezone ();

			// Get the details of the first ride.
			$get_posts = tribe_get_events(array( // Show next ride, even if in-progress.
					'eventDisplay'		=> 'all',
					'start_date' 		=> date('Y-m-d H:i:s'), // From now, not midnight - we should already be at today's ride.
					'posts_per_page' 	=> 1) );


/*			$get_posts = tribe_get_events( array(
					'eventDisplay' 	=> 'custom',
					'posts_per_page'=>	1,
					'meta_query' 	=> array(
							array(
									'key' 		=> '_EventStartDate',
									//'key' 		=> '_EventEndDate',		// Events which will be ending after now - show in-flight events.
									'value' 	=> date('Y-m-d H:i:s'), // Now onwards.
									'compare' 	=> '>',
									'type' 		=> 'date'
							),
							'orderby' 	=> '_EventEndDate',
							'order'	 	=> 'ASC'
					))); */
			
			$event = $get_posts[0];

			if (!isset ($event)) {
				return null;
			}
	
			$ride->post = $event;
		
			//$event->ID = 4258; // Testing.
			
			$ride->title = $event->post_title; 
			$ride->url 	= get_permalink ($event->ID);
			$ride->rwgps = $this->get_rwgps_id ($event->ID);
			
			// Get the excerpt if it exists, or use the event text otherwise.
			$ride->excerpt = $event->post_excerpt;
			
			if (empty ($ride->excerpt)) {
				$ride->excerpt = $event->post_content;
			}
			
			//Don't show the route if the user is not logged in.
			if (!is_user_logged_in()) {
				$ride->rwgps = 0;
			}
			
			$ride->start_date = tribe_get_start_date($event->ID, false, $date_str);
			$ride->end_date = tribe_get_end_date($event->ID, false, $date_str);
			
			return $ride;
		}		
		
		// Encourage facebook to use the bimbler flag image as the preferred preview image.
		function add_opengraph_tags()
		{
			$post_id = get_queried_object_id();
			$rwgps_id = $this->get_rwgps_id ($post_id);

			// If this is an event page, return the featured image, or the map.
			if (!empty ($rwgps_id)) {

				$post_object = get_post ($post_id);

//				error_log ('Sending map as OpenGraph image for post ID ' . $post_id . ' with RWGPS ID ' . $rwgps_id);

				$output  = '<meta property="og:image" content="http://ridewithgps.com/routes/full/' . $rwgps_id . '.png" />' . PHP_EOL;
				$output .= '<meta property="og:url" content="' . get_permalink($post_id) . '" />' . PHP_EOL;
				$output .= '<meta property="og:type" content="website" />' . PHP_EOL;
				$output .= '<meta property="og:title" content="' . $post_object->post_title . '" />' . PHP_EOL;
				$output .= '<meta property="og:description" content="' . $post_object->post_excerpt . '" />' . PHP_EOL;

			} else { // Main website logo.

				$output  = '<meta property="og:image" content="http://bimblers.com/wp-content/uploads/2014/04/bimbler_flag-520x245.jpeg" />' . PHP_EOL;
				$output .= '<meta property="og:url" content="http://bimblers.com" />' . PHP_EOL;
				$output .= '<meta property="og:type" content="website" />' . PHP_EOL;
				$output .= '<meta property="og:title" content="bimblers.com - Brisbane Bimblers Cycling" />' . PHP_EOL;
				$output .= '<meta property="og:description" content="The Brisbane Bimblers’ Cycling Group is a light-hearted group of cyclists who love to get out and about on two wheels, but don’t take themselves too seriously." />' . PHP_EOL;

			}

//                      $output .= '<meta property="og:url" content="http://bimblers.com" />' . PHP_EOL;
//                      $output .= '<meta property="og:type" content="website" />' . PHP_EOL;
//                      $output .= '<meta property="og:title" content="bimblers.com - Brisbane Bimblers Cycling" />' . PHP_EOL;
//                      $output .= '<meta property="og:description" content="The Brisbane Bimblers’ Cycling Group is a light-hearted group of cyclists who love to get out and about on two wheels, but don’t take themselves too seriously." />' . PHP_EOL;

			echo $output;
		}
		
		// Block spam comments - where author is not set.		
		function bimbler_validate_comment ( $approved , $comment_object ){
			
			// Must be a real user.
			if (!is_user_logged_in()) {
				$approved = 'spam';
				
				error_log ('bimbler_validate_comment: Spam comment detected - user not logged in.');
			}
			
			return $approved;
		 }

		 // There is no PHP is_char.
		 function is_char ($char) {
			 return preg_match('/[a-zA-Z]/', $char);
		 }

		 /*
		  * Return the timezone string. 
		  */
		 function get_timezone_string() {
		
			// if site timezone string exists, return it
			if ( $timezone = get_option( 'timezone_string' ) ) {
				return $timezone;
			}
		
			// get UTC offset, if it isn't set then return UTC
			if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
				return 'UTC';
			}
		
			// adjust UTC offset from hours to seconds
			$utc_offset *= 3600;
		
			// attempt to guess the timezone string from the UTC offset
			$timezone = timezone_name_from_abbr( '', $utc_offset, 0 );
		
			// last try, guess timezone string manually
			if ( false === $timezone ) {
				$is_dst = date( 'I' );
		
				foreach ( timezone_abbreviations_list() as $abbr ) {
					foreach ( $abbr as $city ) {
						if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
							return $city['timezone_id'];
						}
					}
				}
		
				// fallback to UTC
				return 'UTC';
			}
		
			return $timezone;
		}
		
		// Determines whether the current user can edit own events.
		function is_author () {
			return current_user_can ('edit_published_posts');
		}

		// Determines whether the current user can edit any events.
		function is_editor () {
			return current_user_can ('edit_others_posts');
		}


		function render_events_list ($posts) {
			$day_time_str = 'D j M g:ia';
			$time_str = 'D j M g:ia';
			$month_str = 'F';

			$content = '';

			if (!$posts) {

				return;
			}

			foreach ($posts as $post) {

					$event_date = $post->EventStartDate;
					
					$rsvpd = Bimbler_RSVP::get_instance()->get_current_rsvp ($post->ID);
					$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
					$rwgps_id = Bimbler_RSVP::get_instance()->get_rwgps_id ($post->ID);
					
					// Nothing found, use Tomewin.
					if (0 == $rwgps_id) {
						$rwgps_id = 5961603; 
					}

					if ((null === $num_rsvps)) {
						 $num_rsvps = 0;
					}

					$content .= '				<div class="xrow">' . PHP_EOL;
						
//					$content .= '					<div class="col-xs2 pull-left" style="height: 80px;">' . PHP_EOL;
					
//					$content .= '						<div class="rsvp-checkin-indicator-noajax">' . PHP_EOL;
					
//					$content .= '						<div class="avatar-clipped bimbler-spinner-target" style="background-image: url(\'//assets2.ridewithgps.com/routes/' . $rwgps_id . '/thumb.png\');"></div>' . PHP_EOL;
					
/*					if (!isset ($rsvpd)) {
						$content .= '						<div class="rsvp-checkin-indicator-none"><i class="fa-question-circle"></i></div>' . PHP_EOL;
					} else if ('Y' == $rsvpd) {
						$content .= '						<div class="rsvp-checkin-indicator-yes"><i class="fa-check-circle"></i></div>' . PHP_EOL;
					}
					else {
						$content .= '						<div class="rsvp-checkin-indicator-no"><i class="fa-times-circle"></i></div>' . PHP_EOL;
					} */
					
//					$content .= '						</div>' . PHP_EOL;
														
//					$content .= '					</div>' . PHP_EOL;
					
					$content .= '					<div class="col-xs-8" style="padding-left:10px;">' . PHP_EOL;

//					$conten .- ' <div>' . PHP_EOL;

/*<a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $title; ?>"><?php echo $title; ?></a> */


					$content .= '						<h3 xclass="sub-title" style="font-size: 30px; padding-bottom: 0px; text-align: center; text-overflow: none!important; color: #dd9933; margin-bottom: 0px;"><a href="' . tribe_get_event_link($post) . '">' . $post->post_title . '</a></h4>' . PHP_EOL;
					//$content .= '						<p class="xui-li-aside" style="text-align: right"><strong>' . date ($time_str, strtotime($event_date)) . '</strong></p>' . PHP_EOL;
					$content .= '						<p style="text-align: center; margin-bottom: 25px;"><strong>' . date ($time_str, strtotime($event_date)) . ', ' . $num_rsvps . ' attending</strong></p>' . PHP_EOL;
					$content .= '					</div>' . PHP_EOL;
					
//					$content .= '					<div class="col-xs-2 xbimbler-spinner-target">' . PHP_EOL;
//					$content .= '					</div>' . PHP_EOL;
					
					$content .= '				</div> <!-- /row -->' . PHP_EOL;



			} // foreach posts.

			echo $content; // We're capturing output into the OB.
		}
 
		function render_events_list_x ($posts) {
			
			$day_time_str = 'D j M g:ia';
			
			if ($posts) {

				foreach ($posts as $post)
				{
					$event_date = $post->EventStartDate;
					
					$rsvpd = Bimbler_RSVP::get_instance()->get_current_rsvp ($post->ID);
					$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
					$rwgps_id = Bimbler_RSVP::get_instance()->get_rwgps_id ($post->ID);
					
					// Nothing found, use Tomewin.
					if (0 == $rwgps_id) {
						$rwgps_id = 5961603; 
					}

					if ((null === $num_rsvps)) $num_rsvps = 0;
					
	//						print_r ($post);
				?>


				<li class="AvatarListSide">
					<div class="tab-item-avatar">
						<div class="rsvp-checkin-container">
							<img src="//assets2.ridewithgps.com/routes/<?php echo $rwgps_id; ?>/thumb.png" style="width:64 !important;  height:64 !important;" class="avatar avatar-96 wp-user-avatar wp-user-avatar-96 alignnone photo">
						
							<div class="rsvp-checkin-indicator">   

							<?php 
							// Only show RSVP indicators to logged-in users.
							if (is_user_logged_in()) {
								if (!isset ($rsvpd)) {
									echo '<div class="rsvp-indicator-none"><i class="fa-question-circle"></i></div>';
									$no_btn_state = '  ';
									$yes_btn_state = ' ';
								} else if ('Y' == $rsvpd) {
									echo '<div class="rsvp-indicator-yes"><i class="fa-check-circle"></i></div>';
									$yes_btn_state = ' disabled="disabled" ';
									$no_btn_state = ' ';
								}
								else {
									echo '<div class="rsvp-indicator-no"><i class="fa-times-circle"></i></div>';
									$no_btn_state = ' disabled="disabled" ';
									$yes_btn_state = ' ';
								}
							}
							?>
						
							</div> <!-- indicator -->
						</div> <!-- container -->
					</div> <!-- tab item avatar -->
					<div class="tab-item-inner group">
						<p class="tab-item-title"><a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $post->post_title; ?>"><?php echo $post->post_title; ?></a></p>
						<p class="tab-item-date"><?php echo date ($day_time_str, strtotime($event_date)); ?>, <?php echo $num_rsvps; ?> attending.</p>

						<?php if (is_user_logged_in()) {
								
							global $current_user;
							//get_currentuserinfo();
							wp_get_current_user ();
							
							// User ID
							$user_id = $current_user->ID;
							
							$nonce = wp_create_nonce('bimbler_rsvp');

						?>
						
						<form action="#" method="post" id="commentform" class="commentform" enctype="multipart/form-data">
						<?php wp_nonce_field('rsvp', 'rsvp_nonce', true, true); ?>
						<input type="hidden" name="rsvp_post_id" id="rsvp_post_id" value="<?php echo $post->ID; ?>">
						<input type="hidden" name="accept_terms" value="accept" value="Y">

						<div id="bimbler-rsvp-control" class="btn-group btn-group-xs" data-event-id="<?php echo  $post->ID; ?>" data-user-id="<?php echo $user_id; ?>" data-nonce="<?php echo  $nonce; ?>">
							<button type="submit" name="submit" value='RSVP Yes' style="background-color: #00a651 !important; border-color: #00a651 !important;" class="btn btn-success btn-xs rsvp-button" <?php echo $yes_btn_state; ?> data-rsvp="Y" id="bimbler-rsvp-yes" data-loading-text="<i class='fa fa-spinner fa-spin'></i> RSVP Yes">
								RSVP Yes
							</button>
							<button type="submit" name="submit" value='RSVP No' style="background-color: #cc2424 !important; border-color: #cc2424 !important;" class="btn btn-danger btn-xs rsvp-button" <?php echo $no_btn_state; ?> data-rsvp="N" id="bimbler-rsvp-no" data-loading-text="<i class='fa fa-spinner fa-spin'></i> RSVP No">
								RSVP No
							</button>
						</div>
						
						</form>
						
						<?php } ?>

					</div> <!-- inner group -->

				</li>

			<?php 
				} // foreach
			} // If posts
		} // End function


		function render_events () {

			ob_start ();

?>
<!--		<ul id="tab-events" class="bimbler-tab avatars-enabled group"> -->
<?php				


			$posts = tribe_get_events(array(
				'posts_per_page' 	=> 10, //$instance["events_num"],
//				'eventDisplay' 		=> 'upcoming',
				'eventDisplay' 		=> 'list',
				'tax_query' 		=> array(
											'relation' 	=> 'OR', 
											array(
												'taxonomy' => Tribe__Events__Main::TAXONOMY, //TribeEvents::TAXONOMY,
															'field' => 'slug',
															'terms' => 'bimble'),
											array(
												'taxonomy' => Tribe__Events__Main::TAXONOMY, //TribeEvents::TAXONOMY,
															'field' => 'slug',
															'terms' => 'social')
											)
											));
			

			if ($posts)
			{
				
				$this->render_events_list ($posts);
				
				
			} // if posts	

?>
<!--			</ul> -->
<?php


			$output = ob_get_clean();

			return $output;
		}

/*
[bgsection 
	pex_attr_title="Social Climbing" 
	pex_attr_subtitle="Our next ride - Sunday 26 June:" 
	pex_attr_undefined="undefined" 
	pex_attr_style="section-light" 
	pex_attr_bgcolor="000000" 
	pex_attr_image="/wp-content/nggallery/front-page-slider/peloton.jpeg" 
	pex_attr_imageopacity="0.6" 
	pex_attr_bgimagestyle="parallax-scroll" 
	pex_attr_titlecolor="ffffff" 
	pex_attr_textcolor="ffffff"]
<p style="text-align: center;"><a class="button">Details</a></p>
[/bgsection]';

*/

		function render_pexeto_box (
			$content,
			$pex_attr_title, 
			$pex_attr_subtitle, 
			$pex_attr_style, 
			$pex_attr_bgcolor, 
			$pex_attr_image, 
			$pex_attr_imageopacity, 
			$pex_attr_bgimagestyle, 
			$pex_attr_titlecolor, 
			$pex_attr_textcolor,
			$pex_content,
			$pex_attr_undefined = "undefined") {

			$content = '';

			$content .= '[bgsection ';
			$content .= 'pex_attr_title="' . $pex_attr_title . '" '; 
			$content .= 'pex_attr_subtitle="' . $pex_attr_subtitle . '" '; 
			$content .= 'pex_attr_undefined="' . $pex_attr_undefined . '" '; 
			$content .= 'pex_attr_style="' . $pex_attr_style . '" '; 
			$content .= 'pex_attr_bgcolor="' . $pex_attr_bgcolor . '" '; 
			$content .= 'pex_attr_image="' . $pex_attr_image . '" '; 
			$content .= 'pex_attr_imageopacity="' . $pex_attr_imageopacity . '" '; 
			$content .= 'pex_attr_bgimagestyle="' . $pex_attr_bgimagestyle . '" '; 
			$content .= 'pex_attr_titlecolor="' . $pex_attr_titlecolor . '" '; 
			$content .= 'pex_attr_textcolor="' . $pex_attr_textcolor . '"';
			$content .= ']';

			$content .=  $pex_content;

			$content .= '[/bgsection]';

//			error_log ($content);

			// Try this - just return the shortcode itself.
			$output = PHP_EOL . do_shortcode ($content) . PHP_EOL;
			//$output = $content;


			//$output = apply_filters( 'the_content', $content );


			//error_log ($output);

			return $output;
		}

		function render_pexeto_box_x (
			$content,
			$title, 
			$subtitle, 
			$style, 
			$bgcolor, 
			$image, 
			$imageopacity, 
			$bgimagestyle, 
			$titlecolor, 
			$textcolor,
			$pex_content,
			$pex_attr_undefined = "undefined") {

			$html = '';

			$add_class = $bgimagestyle=='static'?'':' '.$bgimagestyle;

			$html .= PHP_EOL . '<!-- Start bg section -->' . PHP_EOL;

			$html .= '<div class="section-full-width '.$style.$add_class.'" style="background-color:#'.$bgcolor.';">';
			if($image){
				$html.='<div style="background-image:url('.$image.'); opacity:'.$imageopacity.';'.
					' filter: alpha(opacity='.((float)$imageopacity*100).');" class="full-bg-image" ></div>';
			}
			$html.= '<div class="section-boxed" style="color:#'.$textcolor.';">';
			$subtitle = $subtitle ? '<h4 style="color:#'.$titlecolor.';" class="sub-title">'.$subtitle.'</h4>' : '';
			if($style=='section-light'){
				$html.=$subtitle;
			}
			$html.=$title ? '<h2 class="section-title" style="color:#'.$titlecolor.';">'.$title.'</h2>' : '';
			if($style!='section-light'){
				$html.=$subtitle;
			}

			$html.= apply_filters( 'the_content', $pex_content );
			$html.='</div></div>';
			$html .= PHP_EOL . '<!-- End bg section -->' . PHP_EOL;

			error_log ($html);

			return $html;

		}

		function bimbler_render_full_width ($atts, $post_content) {

			$date_str = 'D j M';

			$content = '';

			// Get the next rides. We'll only use the first.
			$bimbler_posts = $this->get_upcoming_events (1);

			$event = $bimbler_posts [0]; 				
			
			$ride_title = $event->post_title; 
			$ride_url 	= get_permalink ($event->ID);
			//$ride_rwgps = Bimbler_RSVP::get_instance()->get_rwgps_id ($event->ID);
			$ride_start_date = tribe_get_start_date($event->ID, false, $date_str);
			//$ride_excerpt = $event->post_excerpt; // Only use the excerpt - post_content is generally too long.

			// Get the most recent posts. We'll only use the first one.

			$args = array(
				'posts_per_page'   => 5,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => 'News',
				'orderby'          => 'date',
				'order'            => 'DESC',
				'include'          => '',
				'exclude'          => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'post',
				'post_mime_type'   => '',
				'post_parent'      => '',
				'author'	   => '',
				'post_status'      => 'publish',
				'suppress_filters' => true);
		
			$posts_array = get_posts( $args ); 

			$latest_post = $posts_array[0];

			$latest_post_url = get_permalink ($latest_post->ID);
			$latest_post_pic = $feat_image = wp_get_attachment_url( get_post_thumbnail_id($latest_post->ID) );


			$next_event_title = $ride_title; // 'Social Climbing';
			$next_event_subtitle = 'Our next ride - ' . $ride_start_date . ':'; //Sunday 26 June:';
			$next_event_style = 'section-light';
			$next_event_url = $ride_url; // '/event/thing/';
			$next_event_bg_pic = '/wp-content/nggallery/front-page-slider/peloton.jpeg';
			$next_event_bg_pic_mode = 'static'; // parallax-scroll';
			$next_event_bg_colour = '000000';
			$next_event_bg_opacity = '0.6';
			$next_event_title_colour = 'ffffff';
			$next_event_text_colour = 'ffffff';
			$next_event_style = 'section-light';
			$next_event_content = '<p style="text-align: center;"><a class="button" href="' . $ride_url  . '">Details</a></p>';

			$content .= $this->render_pexeto_box (	
				$post_content,		
				$next_event_title,
				$next_event_subtitle,
				$next_event_style,
				$next_event_bg_colour,
				$next_event_bg_pic,
				$next_event_bg_opacity,
				$next_event_bg_pic_mode,
				$next_event_title_colour,
				$next_event_text_colour,
				$next_event_content);


			$join_us_title = 'Join Us';
			$join_us_subtitle = '';
			$join_us_style = 'section-light2';
			$join_us_bg_pic = '';
			$join_us_bg_pic_mode = 'static';
			$join_us_bg_colour = 'c8e5e9';
			$join_us_bg_opacity = '0.1';
			$join_us_title_colour = 'dd9933';
			$join_us_text_colour = '777777';
			$join_us_content = '<p style="text-align: center;">We\'re a Brisbane-based group of social cyclists.</p>' . PHP_EOL . '<p style="text-align: center;">If you\'d like to join us, <a href="/about/">find out more here</a>.</p>';

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$join_us_title,
				$join_us_subtitle,
				$join_us_style,
				$join_us_bg_colour,
				$join_us_bg_pic,
				$join_us_bg_opacity,
				$join_us_bg_pic_mode,
				$join_us_title_colour,
				$join_us_text_colour,
				$join_us_content);

			$latest_post_title = $latest_post->post_title;
			$latest_post_subtitle = '';
			$latest_post_bg_pic = $latest_post_pic; //''; ///wp-content/nggallery/front-page-slider/dave_sworks_beach.jpg';
			$latest_post_bg_pic_mode = 'static';
			$latest_post_bg_colour = '2f2f2f';
			$latest_post_bg_opacity = '0.5';
			$latest_post_title_colour = 'ffffff';
			$latest_post_text_colour = 'ffffff';
			$latest_post_style = 'section-dark';
			$latest_post_content = $latest_post->post_excerpt; //'<p style="text-align: center;">Some text, with witty words and stuff...</p>' . $PHP_EOL . '<p style="text-align: center;">Words words... <a href="http://dfjkhdskjfhdsk">Read more...</a></p>';

			$latest_post_content .= PHP_EOL . '<p><a class="button" href="' . $latest_post_url  . '">Read more</a></p>';

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$latest_post_title,
				$latest_post_subtitle,
				$latest_post_style,
				$latest_post_bg_colour,
				$latest_post_bg_pic,
				$latest_post_bg_opacity,
				$latest_post_bg_pic_mode,
				$latest_post_title_colour,
				$latest_post_text_colour,
				$latest_post_content);


			$events_title = 'Up-Coming Events';
			$events_subtitle = '';
			$events_style = 'section-light2';
			$events_bg_pic = '';
			$events_bg_pic_mode = 'static';
			$events_bg_colour = 'c8e5e9';
			$events_bg_opacity = '0.1';
			$events_title_colour = 'dd9933';
			$events_text_colour = '777777';
			$events_content = $this->render_events ();

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$events_title,
				$events_subtitle,
				$events_style,
				$events_bg_colour,
				$events_bg_pic,
				$events_bg_opacity,
				$events_bg_pic_mode,
				$events_title_colour,
				$events_text_colour,
				$events_content);


			$kit_page_title = 'Bimbler Kit';
			$kit_page_subtitle = '';
			$kit_page_bg_pic = '/wp-content/uploads/2015/06/Toowoomba-Gang-Final-Crop.jpeg';
			$kit_page_bg_pic_mode = 'static';
			$kit_page_bg_colour = '000000';
			$kit_page_bg_opacity = '0.9';
			$kit_page_title_colour = 'ffffff';
			$kit_page_text_colour = 'ffffff';
			$kit_page_style = 'section-light2';
			$kit_page_content = '<p style="text-align: center;"><a class="button" href="https://bimblers.com/kit/">Shop</a></p>';

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$kit_page_title,
				$kit_page_subtitle,
				$kit_page_style,
				$kit_page_bg_colour,
				$kit_page_bg_pic,
				$kit_page_bg_opacity,
				$kit_page_bg_pic_mode,
				$kit_page_title_colour,
				$kit_page_text_colour,
				$kit_page_content);

			$photos_title = 'Recent Photos';
			$photos_subtitle = '';
			$photos_style = 'section-light2';
			$photos_bg_pic = '';
			$photos_bg_pic_mode = 'static';
			$photos_bg_colour = 'c8e5e9';
			$photos_bg_opacity = '0.1';
			$photos_title_colour = 'dd9933';
			$photos_text_colour = '777777';

			$gallery_shortcode = '[justified_image_grid preset=1 caption=off last_row=flexible ng_recent_images=yes]';
			$photos_content = '<div class="section-boxed">' . $gallery_shortcode . '</div>';

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$photos_title,
				$photos_subtitle,
				$photos_style,
				$photos_bg_colour,
				$photos_bg_pic,
				$photos_bg_opacity,
				$photos_bg_pic_mode,
				$photos_title_colour,
				$photos_text_colour,
				$photos_content);




			$footer_content = '<div style="display: table; margin: 0 auto !important; ">' . PHP_EOL;
			$footer_content .= '		<a href="http://www.blackchrome.com.au/portfolio/cycling-kits" target="_external"><img id="footer-logo" width="303" height="92" style="display: table; padding: 10px; margin: 0 auto !important;"' . PHP_EOL; 
			$footer_content .= 'src="//bimblers.com/wp-content/uploads/2014/09/Blackchrome-Sportswear-2014-logo-trans.png" ' . PHP_EOL;
			$footer_content .= '		alt="' . get_bloginfo('name') . '"></a>' . PHP_EOL;
			$footer_content .= '		<p>Proud ambassador for <a href="http://www.blackchrome.com.au/portfolio/cycling-kits" target="_external">Blackchrome Sportswear</a>.</p>' . PHP_EOL;
			$footer_content .= '	</div>' . PHP_EOL;				

			$footer_title = '';
			$footer_subtitle = '';
			$footer_style = 'section-light2';
			$footer_bg_pic = '';
			$footer_bg_pic_mode = 'static';
			$footer_bg_colour = '000000';
			$footer_bg_opacity = '0.1';
			$footer_title_colour = 'dd9933';
			$footer_text_colour = '777777';

			$content .= $this->render_pexeto_box (			
				$post_content,		
				$footer_title,
				$footer_subtitle,
				$footer_style,
				$footer_bg_colour,
				$footer_bg_pic,
				$footer_bg_opacity,
				$footer_bg_pic_mode,
				$footer_title_colour,
				$footer_text_colour,
				$footer_content);


		//	return $content;
			echo $content;
		}

		/* 
			New version of Tribe Events Calendar includes iCal functionality in the free version.
			Override query args to how we like them:
			 - all events 
			 - from 6 months back
			 - no end date. 
		*/
		function bimbler_tribe_ical_query ($args, $month) {

//			error_log ('iCal filter firing: ' . print_r ($args, true));

			// Remove hide_upcoming.
			unset ($args['hide_upcoming']);

			// Show all events, will be set to 'custom' by TEC.
			$args['eventDisplay'] = 'custom';

			// Show back 6 months and forward until the end of time.
            $date_from = date('Y-m-d', strtotime('-6 months'));
			$args['start_date'] = $date_from;

			// Remove end_date - will default to the end of the month.
			unset ($args['end_date']);

			// error_log ('iCal filter updated: ' . print_r ($args, true));

			return $args;
		}


} // End class
