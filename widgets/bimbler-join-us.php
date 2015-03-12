<?php
/**
 * Bimbler Join Us widget.
 *
 *
 * @package Bimbler Join Us
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Join_Us_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_Join_Us_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_join_us_widget', 
							'Bimbler Join Us Widget', 
							array (	//'classname'   => 'bimbler_rsvp_widget',
									'description' => 'Bimbler Join Us Widget' )
							 );
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
		
		// Only display the widget if the user is not logged in.
		if (is_user_logged_in()) { return; } 
		
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$output = $before_widget."\n";

		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>
		
		<div class="entry themeform">
			<div class="section" style="text-align: center; display: block; width: 100%; margin-left: auto; margin-right: auto;">
				<form>
					<input type="button" value="Register" onclick="window.location.href='/wp-login.php?action=register'" class="bimbler-button">
					<input type="button" value="Log in" onclick="window.location.href='/wp-login.php'" class="bimbler-button">
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