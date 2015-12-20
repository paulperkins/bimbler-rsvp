<?php
/**
 * Bimbler Comments widget.
 *
 * Implements page comments using Framework7 components.
 *
 * @package Bimbler Comments
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Comments_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_Comments
	 */
	public function __construct() {
		parent::__construct( 'bimbler_comments_widget', 
							'Bimbler Comments Widget', 
							array (	'description' => 'Bimbler Comments Widget' )
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
			
			error_log ('Not logged in.');
			return false;
		}
	
		// Only proceed if this is a 'post' type of post.
		$post_type = get_post_type ($post_id);
	
		if ((!isset ($post_id)) || !isset ($post_type) || empty ($post_type)) {
			error_log ('Post type cannot be determined.');
			return false;
		}

		if ('tribe_events' == $post_type) {
			error_log ('This is a \''. $post_type . '\' post type - displaying.');
			return true;
		}

/*	
		$categories = wp_get_post_categories ($post_id);
			
		if (!isset ($categories)) {
			error_log ('No categories.');
			return $content;
		}
			
		foreach ($categories as $c) {
			$category = get_category ($c);
	
			error_log ('Checking category \''. $category->name . '\'.');
	
			// We need to get the parent category.
			if (isset ($category->category_parent)) {
	
				$parent = get_cat_name ($category->category_parent);
	
				// Stop here if this is a ride page.
				if ('Ride' == $parent) {
					return true;
				}
			}
		}*/
		
		error_log ('Not displaying comment block.');
			
		// Carry on - nothing to see here.
		return false;
	}
	
	function bimbler_get_avatar_img ($avatar) {
		
		preg_match( '#src=["|\'](.+)["|\']#Uuis', $avatar, $matches );
	
		return ( isset( $matches[1] ) && ! empty( $matches[1]) ) ?
		(string) $matches[1] : '';
	}
	
	/*
	 * 
	 */
	function get_event_comments ($event_id) {
		
		$args = array (
				'post_id' 	=> $event_id,
				'status'	=> 'approve',
				'order'		=> 'DESC'
				);
		
		return get_comments ($args);
	}
	
	function show_comments ($comment) {

		$bimbler_mobile_day_time_str = 'D j M g:ia';

		$content = '';

		$avatar = get_avatar ($comment->comment_author_email, null, null);
		$avatar_img = $this->bimbler_get_avatar_img($avatar);
		
		// Assume all messages are sent at different times.
		$content .= '	<div class="messages-date"><apan>' . date ($bimbler_mobile_day_time_str, strtotime($comment->comment_date)) . '</span></div>' . PHP_EOL;
		
		global $current_user;
		get_currentuserinfo();

		if ($comment->comment_author_email == $current_user->user_email) {
			$message_type = 'message-sent';
		} else {
			$message_type = 'message-received';
		}
		
		$content .= '	<div class="message ' . $message_type;
		
		if ('message-received' == $message_type) {
			$content .= ' message-with-avatar';
		}
		
		$content .= ' message-last message-with-tail message-first">' . PHP_EOL;

		if ('message-received' == $message_type) {
			$content .= '			<div class="message-name">' . $comment->comment_author . '</div>' . PHP_EOL;
		} else {
			$content .= '			<div class="message-name">You</div>' . PHP_EOL;
		}

		// Nicely format the comment.
		$comment_html = 	apply_filters('the_content', $comment->comment_content);
		
		// Remove any HTML we don't want.
		$comment_html = str_replace ('<p>', '', $comment_html);
		$comment_html = str_replace ('</p>', '', $comment_html);

		$content .= '			<div class="message-text">' . $comment->comment_content . '</div>' . PHP_EOL;

		if ('message-received' == $message_type) {
			$content .= '			<div style="background-image:url(' . $avatar_img . ')" class="message-avatar"></div>' . PHP_EOL;
		}
		
		$content .= '	</div>' . PHP_EOL;
		
		return $content;
	}
		
	
	/**
	 * Adds the comments tab.
	 *
	 * @param
	 */
	function render_comments ($event_id) {
		global $bimbler_mobile_time_str;
		global $bimbler_mobile_day_time_str;
		global $bimbler_mobile_date_str;
	
		$post_id = $event_id;
	
		$content = '';
	
		// Only show content to logged-in users, and only if we're on an event page.
		if (is_user_logged_in()) {
			
			$nonce = wp_create_nonce('bimbler_comment');
	
			$content .= '';
			
			// 'Post Comment' button panel.
			$content .= '    <div class="toolbar messagebar">' . PHP_EOL;
			$content .= '      <div class="toolbar-inner">' . PHP_EOL;
//			$content .= '        <textarea rows="2" placeholder="Comment" id="bimbler-comment"></textarea>' . PHP_EOL;
			$content .= '        <textarea placeholder="Comment" id="bimbler-comment"></textarea>' . PHP_EOL;
			$content .= '        			<button class="btn btn-warning comment-post-button" type="button"';
			$content .= ' data-event-id="' . $post_id . '" ';
			$content .= ' data-nonce="' . $nonce . '" ';
			$content .= ' data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Post">Post</button>' . PHP_EOL;
			$content .= '      </div>' . PHP_EOL;
			$content .= '    </div>' . PHP_EOL;

			// Main comments panel.
			$content .= '	<div class="panel panel-default">' . PHP_EOL;
			$content .= '		<div class="panel-heading">' . PHP_EOL;
			//$content .= '			<h4 class="panel-title">Comments</h4>' . PHP_EOL;
			$content .= '		</div>' . PHP_EOL;
			
			$content .= '	<div class="panel-body" style="padding:0px;">' . PHP_EOL;

			$content .= '<div class="messages messages-auto-layout">' .  PHP_EOL;

			$comments = $this->get_event_comments($post_id);
				
			foreach ($comments as $comment) {
				
				// Only show root-level comments.
				//if (0 == $comment->comment_parent) {
				
					$content .= $this->show_comments($comment);
					
				//}
			}

			$content .= '		</div>' . PHP_EOL;
			$content .= '	</div>' . PHP_EOL;
			
			$content .= '	</div>' . PHP_EOL;

		} else {
			$content .= '<h2>Please log in.</h2>' . PHP_EOL;
		}
	
		return $content;
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
		
		if (!$this->can_display_this($post_id)) {
			return;
		} 

		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? ' ' : $instance['title'], $instance, $this->id_base );
		
		$output = $before_widget."\n";
		if($title)
		{
			$output .= $before_title.$title.$after_title;
		}

		ob_start();
		
		?>

		<div class="entry themeform">
			<div class="section page-content" style="text-align: left; display: block; width: 100%; margin-left: auto; margin-right: auto;">
<?php
			echo $this->render_comments ($post_id);
?>
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
