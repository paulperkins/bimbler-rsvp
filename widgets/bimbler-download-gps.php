<?php
/**
 * Bimbler Download GPS widget.
 *
 *
 * @package Bimbler Download GPS
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Download_GPS_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_Download_GPS_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_download_gps_widget', 
							'Bimbler Download GPS Widget', 
							array (	'description' => 'Bimbler Edit Attendees Widget' )
							 );
	}

	/*
	 * Determine if:
	*  - The user is logged in
	*  - The current post is a ride page
	*  If so, then return true; return false otherwise.
	*
	*/
	function can_display_this ($post_id) {
		global $wp_query;
	
		// First, check if the user is logged in.
		if (!is_user_logged_in()) {
			return false;
		}
			
		// Fix bug for erroneously showing widget on front page - user get_queried_object_id.
		//$post_id = get_queried_object_id();
	
		// Only proceed if this is a 'post' type of post.
		$post_type = get_post_type ($post_id);
	
		if ((!isset ($post_id)) || !isset ($post_type) || empty ($post_type) || ('post' != $post_type)) {
			//error_log ('This is a \''. get_post_type (get_the_ID()) . '\' post type.');
			return false;
		}
	
		$categories = wp_get_post_categories ($post_id);
			
		if (!isset ($categories)) {
			error_log ('No categories.');
			return $content;
		}
			
		foreach ($categories as $c) {
			$category = get_category ($c);
	
			//print '<p>Checking category '. $category->name . '</p>';
				
			//error_log ('Checking category \''. $category->name . '\'.');
	
			// We need to get the parent category.
			if (isset ($category->category_parent)) {
	
				$parent = get_cat_name ($category->category_parent);
	
				//print '<p>Checking parent ' . $parent . '</p>';
					
				// Stop here if this is a ride page.
				if ('Ride' == $parent) {
					return true;
				}
			}
		}
			
		// Carry on - nothing to see here.
		return false;
	}
	
	/**
	 * Output the HTML for this widget.
	 *
	 * @access public
	 * @since 0.1
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 */
	public function widget( $args, $instance ) {
	
		extract ($args);
		
		global $wpdb;
		global $wp_query;
		
		// Fix bug for erroneously showing widget on front page - user get_queried_object_id.
		$post_id = get_queried_object_id();
	
		$rwgps_id = Bimbler_RSVP::get_instance()->get_rwgps_id ($post_id);
		
		error_log ('RWGPS ID ' . $rwgps_id);

		if (0 == $rwgps_id) { return; }

		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$output = $before_widget."\n";
		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>

		<div class="entry themeform">
			<div class="section" style="text-align: left; display: block; width: 100%; margin-left: auto; margin-right: auto;">
				<form>
					<div class="row" style="padding-top: 2px;">
						<div class="col-sm-4">Garmin TCX:</div><div class="col-sm-5"><input type="button"  class="bimbler-button" title="Ideal for turn-by-turn navigation on Garmin units." value="Download" onclick="window.location.href='http://ridewithgps.com/routes/<?php echo $rwgps_id; ?>.tcx'" style="xbackground: #dd9933 !important;"></div>
					</div>
					<div class="row" style="padding-top: 2px;">
						<div class="col-sm-4">GPX:</div><div class="col-sm-5"><input type="button" title="For enabling track displays on non-Garmin units." value="Download" onclick="window.location.href='http://ridewithgps.com/routes/<?php echo $rwgps_id; ?>.gpx?sub_format=track'" class="bimbler-button"></div>
					</div>
					<div class="row" style="padding-top: 2px;">
						<div class="col-sm-4">Google KML:</div><div class="col-sm-5"><input type="button" title="For viewing in Google Earth." value="Download" onclick="window.location.href='http://ridewithgps.com/routes/<?php echo $rwgps_id; ?>.kml'" class="bimbler-button"></div>
					</div>
				</form>
			</div>
		</div>
<?php 			

		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	
				
	}

	/**
	 * Deal with the settings when they are saved by the admin.
	 *
	 * Here is where any validation should happen.
	 *
	 * @since 0.1
	 *
	 * @param array $new_instance New widget instance.
	 * @param array $instance     Original widget instance.
	 * @return array Updated widget instance.
	 */
	function update( $new_instance, $instance ) {
		$instance['title']  = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Display the form for this widget on the Widgets page of the Admin area.
	 *
	 * @since 0.1
	 *
	 * @param array $instance
	 */
	function form( $instance ) {
		$title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
		<?php
	}
	
}
