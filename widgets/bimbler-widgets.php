<?php
/**
 * Bimbler RSVP widget.
 *
 *
 * @package Bimbler RSVP
 * @subpackage 
 * @since 0.1
 */

class Bimbler_RSVP_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_RSVP_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_rsvp_widget', 
							'Bimbler RSVP Widget', 
							array (	//'classname'   => 'bimbler_rsvp_widget',
									'description' => 'Bimbler RSVP Widget' )
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
			
		
		
		
//		if ( is_cart() || is_checkout() ) return;
			
		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
//		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? $format_string : $instance['title'], $instance, $this->id_base );
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$table_name = $wpdb->base_prefix . $rsvp_db_table;
		
		$sql =  'SELECT * FROM '. $table_name;
		$sql .= ' WHERE rsvp = \'Y\'';
		$sql .= ' ORDER BY time DESC';
		$sql .= ' LIMIT '. $number;
		
		
		
		$output = $before_widget."\n";
		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>
		
		<ul id="recent-comments">
				<?php 
				
				$rsvps = $wpdb->get_results ($sql);
				//while ($posts->have_posts()): $posts->the_post(); 
				if ($rsvps && (count ($rsvps) > 0))	{
					foreach ( $rsvps as $rsvp) {
						$post = get_post ($rsvp->event);
						$title = $post->post_title;
						$user_info   = get_userdata ($rsvp->user_id);
						$user = $user_info->user_login;
						$user_nick = $user_info->nickname;
						$time = $rsvp->time;
						
				?>
				
				<li class="recent-comments">					
						<?php echo $user_nick .' RSVPd to ';?><a href="<?php echo tribe_get_event_link($post); // the_permalink(); ?>" rel="bookmark" title="<?php echo $title; //the_title(); ?>">
							<?php echo $title; //the_title(); ?></a>
  						<?php if(isset ($time)) { ?><p class="post-item-date"><?php echo $time;//the_time('j M, Y'); ?></p><?php } ?>
				</li>

				<?php 
					}
				}
				?>
			
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
		$instance['number'] = empty( $new_instance['number'] ) ? 2 : absint( $new_instance['number'] );

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
		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
//		$format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">Number of RSVPs to show:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3"></p>
		<?php
	}
}
