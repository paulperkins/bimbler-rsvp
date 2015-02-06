<?php
/*
	Bimbler Check In Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Borrowing heavily from Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxTabs
		@version 1.0
*/

class Bimbler_CheckIn_Widget extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	public function __construct() {
		
		//error_log ('Bimbler_Tabs_Widget: constructor');
		
		parent::__construct( 'bimbler_checkin_widget', 
							'Bimbler Check In Widget', 
							array('classname' => 'bimbler_checkin_widget') 
				);	
	}

/*  Create tabs-nav
/* ------------------------------------ */
	private function _create_tabs($tabs,$count) {
		// Borrowed from Jermaine Maree, thanks mate!
		$titles = array(
			'checkin'	=> 'Check In'
			//'past'		=> 'Past'
			);
		$icons = array(
			'checkin'	=> 'fa fa-check-square-o'
			//'past'		=> 'fa fa-clock-o'
			);
		$output = sprintf('<ul class="alx-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]);
		}
		$output .= '</ul>';
		return $output;
	}
	
	
	
	/**
	 * Adds the RSVP list to the event.
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
		//	if (is_user_logged_in() && is_single())
		if (1) //is_single())
		{
			$html  = '<div id="rsvp-list" class="widget">';
			$html .= '		    <h3 id="reply-title" class="comment-reply-title">Who\'s Coming</h3>';
			$html .= '<div id="AvatarListSide" class="AvatarListSide-wrap">';
			$html .= '	<form method="post" id="commentform" class="commentform" enctype="multipart/form-data">';
	
			$rsvps_y = $wpdb->get_results ($sql_y);
			$rsvps_n = $wpdb->get_results ($sql_n);
	
			error_log ('    Yes returned '. count ($rsvps_y) .' rows.');
			error_log ('    No returned '. count ($rsvps_n) .' rows.');
	
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
	
						$html .= '<li class="AvatarListSide"><div class="permalink"></div><a href="">'. $avatar .'<p>'. $user_info->user_login;
	
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
	
						//	$html .= '		    <li><div class="permalink"></div><a href="/living/diy-neon-classic-pottery"><img src="http://shopsweetthings.com/wp-content/uploads/2012/07/cover25.jpg" /><p>Person 2: <i>"Comment 2"</i></p></a></li>';
	
						$html .= '<li class="AvatarListSide"><div class="permalink"></div><a href="">'. $avatar .'<p>'. $user_info->user_login;
	
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
	
			return $html;
		}
	}
	
	
/*  Widget
/* ------------------------------------ */
public function widget($args, $instance) {
		extract( $args );

		// Get the post ID, and determine if it's a ride or not.
		// Only display if this is an event page.
		/*if (!$this->can_display_nexton ())
		{
			return false;
		}*/
		
		
		$instance['title']?NULL:$instance['title']='';
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();
		
		$time_str = 'j M g:ia';
		
/*  Set tabs-nav order & output it
/* ------------------------------------ */
	$tabs = array();
	$count = 0;
	$order = array(
		'checkin'	=> $instance['order_checkin']			
		//'past'		=> $instance['order_past']			
	);
	asort($order);
	foreach ( $order as $key => $value ) {
		if ( $instance[$key.'_enable'] ) {
			$tabs[] = $key;
			$count++;
		}
	}
	
	if ( $tabs && ($count > 1) ) 
	{ 
		$output .= $this->_create_tabs($tabs,$count); 
	}
	
	
?>
	<div class="alx-tabs-container">

		<?php 
			// Future.
		
			if($instance['future_enable']) { // Enabled? 
				
				?>
				<ul id="tab-future" class="alx-tab group avatars-enabled">
				
				<?php
				
				if ( function_exists( 'tribe_get_events' ) ) {
				
					$args = array(
							'eventDisplay'   => 'upcoming',
							'posts_per_page' => $instance['future_num']
					);
				
					if (1)// ! empty( $category ) ) 
					{
						$args['meta_query'] = array(
								array(
										'key'         => '_BimblerRidePage',
										'value'       => get_the_ID()
								)
						);
					}
				
					$posts = tribe_get_events( $args );
				}

				if (!$posts) {
					echo '<p>No up-coming instances of this ride.</p>';
				}
				else
				{
					foreach ($posts as $post)
					{
						$event_date = $post->EventStartDate;
						
						$rsvpd = Bimbler_RSVP::get_instance()->get_current_rsvp ($post->ID);
						$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
					?>
					<li>
  						<div class="tab-item-avatar">
						
							<?php if (!isset ($rsvpd)) {
								echo '<div class="rsvp_none">';
							}
							else {
								if('Y' == $rsvpd) {
									echo '<div class="rsvp_yes">';
								}
								else {
									echo '<div class="rsvp_no">'; 
								}
							}
						
								?>
		 					<p>&nbsp</p>
							</div>
						</div> 
					<div class="tab-item-inner group">
						<p class="tab-item-title"><a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $post->post_title; ?>"><?php echo $post->post_title; ?></a></p>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($time_str, strtotime($event_date)); ?>, <?php echo $num_rsvps; ?> attending.</p><?php } ?>
					</div>
					</li>
				<?php 
					} // foreach
				} // if posts				
				?>
				</ul> <!-- Future tab. -->
		<?php } ?>
		<!-- End tab. -->
		

		
		
		
	</div> <!-- alx-tabs-container -->

<?php
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	}
	
/*  Widget update
/* ------------------------------------ */
	public function update($new,$old) {
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);
		$instance['tabs_category'] = $new['tabs_category']?1:0;
		$instance['tabs_date'] = $new['tabs_date']?1:0;

		$instance['future_enable'] = $new['future_enable']?1:0;
		$instance['past_enable'] = $new['past_enable']?1:0;
		$instance['future_num'] = strip_tags($new['future_num']);
		$instance['past_num'] = strip_tags($new['past_num']);
		
		// Order
		$instance['order_future'] = strip_tags($new['order_future']);
		$instance['order_past'] = strip_tags($new['order_past']);
		return $instance;
	}

/*  Widget form
/* ------------------------------------ */
	public function form($instance) {
		// Default widget settings
		$defaults = array(
			'title' 			=> '',
			'tabs_category' 	=> 1,
			'tabs_date' 		=> 1,
		// Future events
			'future_enable' 	=> 1,
			'future_num' 		=> '5',
		// Future events
			'past_enable' 		=> 1,
			'past_num' 			=> '5',
				
		// Order
			'order_future' 		=> '1',
			'order_past' 		=> '2'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

	<style>
	.widget .widget-inside .bimbler-options-tabs .postform { width: 100%; }
	.widget .widget-inside .bimbler-options-tabs p { margin: 3px 0; }
	.widget .widget-inside .bimbler-options-tabs hr { margin: 20px 0 10px; }
	.widget .widget-inside .bimbler-options-tabs h4 { margin-bottom: 10px; }
	</style>
	
	<div class="bimbler-options-tabs">
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
		</p>

		<h4>Future Events</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('future_enable'); ?>" name="<?php echo $this->get_field_name('future_enable'); ?>" <?php checked( (bool) $instance["future_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('future_enable'); ?>">Enable future events</label>
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("future_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("future_num"); ?>" name="<?php echo $this->get_field_name("future_num"); ?>" type="text" value="<?php echo absint($instance["future_num"]); ?>" size='3' />
		</p>
		
		<hr>

		<h4>Past Events</h4>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('past_enable'); ?>" name="<?php echo $this->get_field_name('past_enable'); ?>" <?php checked( (bool) $instance["past_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('past_enable'); ?>">Enable past events</label>
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("past_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("past_num"); ?>" name="<?php echo $this->get_field_name("past_num"); ?>" type="text" value="<?php echo absint($instance["past_num"]); ?>" size='3' />
		</p>
		
		
		<h4>Tab Order</h4>
		
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_future"); ?>">Future Events</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_future"); ?>" name="<?php echo $this->get_field_name("order_future"); ?>" value="<?php echo $instance["order_future"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_past"); ?>">Past Events</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_past"); ?>" name="<?php echo $this->get_field_name("order_past"); ?>" value="<?php echo $instance["order_past"]; ?>" />
		</p>
		
		<hr>
		<h4>Tab Info</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('tabs_category'); ?>" name="<?php echo $this->get_field_name('tabs_category'); ?>" <?php checked( (bool) $instance["tabs_category"], true ); ?>>
			<label for="<?php echo $this->get_field_id('tabs_category'); ?>">Show categories</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('tabs_date'); ?>" name="<?php echo $this->get_field_name('tabs_date'); ?>" <?php checked( (bool) $instance["tabs_date"], true ); ?>>
			<label for="<?php echo $this->get_field_id('tabs_date'); ?>">Show dates</label>
		</p>
		
		<hr>
		
	</div>
<?php

	}

}
