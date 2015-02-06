<?php
/**
 * Bimbler Ajax widget.
 *
 *
 * @package Bimbler Ajax
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Ajax_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_RSVP_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_ajax_widget', 
							'Bimbler Ajax Widget', 
							array ('description' => 'Bimbler Ajax Widget' )
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
		global $rsvp_db_table;
		
		if ( !is_super_admin() ) return;
		
//		if ( is_cart() || is_checkout() ) return;
			
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$output = $before_widget."\n";
		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>
		
		
		<div class="quantity buttons_added">
			<input type="button" value="-" class="minus">
			<input type="number" step="1" name="quantity" value="1" title="Qty" class="input-text qty text" size="4" min="1">
			<input type="button" value="+" class="plus">
		</div>
		
		<ul id="recent-comments">
				
				<li class="recent-comments">Stuff 1</li>
				<li class="recent-comments">Stuff 2</li>
			
		</ul><!--/.alx-posts-->
		
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
//		$instance['number'] = empty( $new_instance['number'] ) ? 2 : absint( $new_instance['number'] );

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
//		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
//		$format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
		<?php
	}
}
