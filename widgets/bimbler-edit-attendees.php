<?php
/**
 * Bimbler Edit Attendees widget.
 *
 *
 * @package Bimbler Sales
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Edit_Attendees_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_RSVP_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_edit_attendees_widget', 
							'Bimbler Edit Attendees Widget', 
							array (	//'classname'   => 'bimbler_rsvp_widget',
									'description' => 'Bimbler Edit Attendees Widget' )
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

		// Need to be admin to edit attendance.		
		if (!Bimbler_RSVP::get_instance()->can_modify_attendance(get_queried_object_id())) { return; }
		
		// Don't show if not on an actual event page.
		if (!(tribe_is_event() && is_single())) { return; }
			
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$output = $before_widget."\n";
		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>
				
		<div class="section" style="display: block;">
				<?php
				$this->add_rsvp_form();	
				?>
		</div>
		
		
		<?php

		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	
				
	}
	
	function get_user_list () {
		global $wpdb;
		
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
	
	/**
	 * Adds the RSVP buttons to the event.
	 *
	 * @param
	 */
	function add_rsvp_form() {
	
		global $wp_query;
		
		// Fix bug for erroneously showing widget on front page - user get_queried_object_id.
		$post_id = get_queried_object_id();
	
		//error_log ('add_rsvp_form: post ID '. $postid);
			
		// Only show content to logged-in users, and only if we're on an event page.
		if (is_user_logged_in() && (0 != $post_id)) {
	
			//$rsvp = $this->get_current_rsvp ($postid);
				
			$html  = '<div id="rsvp-form">';
			$html .= '<div id="respond" class="comment-respond">';
			$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
			//$html .= '<p>'. $status .'</p>';
			$html .= wp_nonce_field('rsvp', 'rsvp_nonce', true, true);
			$html .= '	<p class="form-submit">';
	
			$users = $this->get_user_list ();
			
			if (0 == $users) {
				error_log ('Could not get list of users for dropdown.');
				
				$html .= '<div class="bimbler-error-box error"><span>Error: </span>Could not populate user dropdown.</div>';
					
			} else {
				$html .= '<select id="rsvp_user" name="rsvp_user" class="bimbler-select2-attendees"  style="width: 238px;">';
				$html .= '<option>Select a person...</option>';
				
				foreach ($users as $user) {
					$html .= '<option value=' . $user->id . '>' . $user->first . ' ' . $user->last;
					$html .= ' (' . $user->display . ')</option>';
				}
				
				$html .= '</select>';
			}
			
			//$html .= '<div class="col-sm-5">';
			$html .= '<span>Guests:</span>';
			$html .= '		<select class="xform-control" id="rsvp_guests" name="rsvp_guests">';
			
			$i = 0;
			for ($i = 0; $i < 11; $i++) {
				$html .= '			<option>' . $i . '</option>';
			}
			$html .= '		</select>';
			
			//$html .= '</div>';
					
			$html .= '<br>';
				
			$html .= '  <input name="submit" type="submit" class="button-primary" id="submit" value="RSVP Yes" style="background: #6aab2d;">';
			$html .= '<input type="hidden" name="rsvp_post_id" id="rsvp_post_id" value="'. $post_id .'">';
	
			$html .= '	<input name="submit" type="submit" class="button-primary" id="submit" value="RSVP No" style="background: #f75300;">';
			$html .= '<input type="hidden" name="rsvp_post_id" id="rsvp_post_id" value="'. $post_id .'">';
	
			$html .= '<input type="hidden" name="accept_terms" value="accept" value="Y">';
			
			$html .= '	</p></form>';
			$html .= '</div> <!--#rsvp-respond-->';
			$html .= '</div> <!-- #rsvp-form -->';
	
			//				$html .= '<h3>Gallery</h3>'. wppa_albums(1);
			
			$html .= '
			<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				$(".bimbler-select2-attendees").select2();
			});
			
			</script>';
				
	
			echo $html;
	
		} // end if
	
	} // end add_rsvp_form
	
}
