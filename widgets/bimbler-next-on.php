<?php
/*
	Bimbler Next On Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Borrowing heavily from Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxTabs
		@version 1.0
*/

class Bimbler_NextOn_Widget extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	public function __construct() {
		
		//error_log ('Bimbler_Tabs_Widget: constructor');
		
		parent::__construct( 'bimbler_nexton_widget', 
							'Bimbler Next On Widget', 
							array('classname' => 'bimbler_nexton_widget') 
				);	
	}

/*  Create tabs-nav
/* ------------------------------------ */
	private function _create_tabs($tabs,$count) {
		// Borrowed from Jermaine Maree, thanks mate!
		$titles = array(
			'future'	=> 'Future',
			'past'		=> 'Past'
			);
		$icons = array(
			'future'	=> 'fa fa-calendar',
			'past'		=> 'fa fa-clock-o'
			);
		$output = sprintf('<ul class="alx-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]);
		}
		$output .= '</ul>';
		return $output;
	}
	
	/*
	 * Determine if:
	 *  - The user is logged in
	 *  - The current post is a ride page
	 *  If so, then return true; return false otherwise.
	 *  
	 */
	function can_display_nexton ($post_id) {
		global $wp_query;
		
		// First, check if the user is logged in.
		if (!is_user_logged_in()) {
			return false;
		}
			
		// Fix bug for erroneously showing widget on front page - user get_queried_object_id.
		//$post_id = get_queried_object_id();
		
		// Only proceed if this is a 'post' type of post.
		$post_type = get_post_type ($post_id);
		
		if (!isset ($post_type) || empty ($post_type) || ('post' != $post_type)) {
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
	
/*  Widget
/* ------------------------------------ */
public function widget($args, $instance) {
		extract( $args );

		//var_dump ($args);
		//var_dump ($instance);
		
		global $wp_query;

		if (isset ($args['ride_page'])){
		
			$post_id = $args['ride_page'];
			
			if (0 == strlen ($post_id))
			{
				return false;
			}
		}
		else
		{
			// Fix bug for erroneously showing widget on front page - user get_queried_object_id.
			$post_id = get_queried_object_id();

			// Get the post ID, and determine if it's a ride or not.
			if (!$this->can_display_nexton ($post_id))
			{
				return false;
			}
		}
		
		
		$instance['title']?NULL:$instance['title']='';
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();
		
		$time_str = 'j M g:ia';
		$time_past_str = 'j M \'y g:ia';
		
/*  Set tabs-nav order & output it
/* ------------------------------------ */
	$tabs = array();
	$count = 0;
	$order = array(
		'future'	=> $instance['order_future'],			
		'past'		=> $instance['order_past']			
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
										'value'       => $post_id //get_the_ID()
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
		
		
		<?php 
			// Past.
		
			if($instance['past_enable']) { // Enabled? 
				
				?>
				<ul id="tab-past" class="alx-tab group avatars-enabled">
				
				<?php
				
				if ( function_exists( 'tribe_get_events' ) ) {
				
					$args = array(
							'eventDisplay'   => 'past', // TODO: Check this.
							'order' => 'ASC',
							'posts_per_page' => $instance['past_num']
					);
				
					if (1)// ! empty( $category ) ) 
					{
						$args['meta_query'] = array(
								array(
										'key'         => '_BimblerRidePage',
										'value'       => $post_id //get_the_ID()
								)
						);
					}
				
					$posts = tribe_get_events( $args );
				}

				if (!$posts) {
					echo '<p>No past instances of this ride.</p>';
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
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($time_past_str, strtotime($event_date)); ?>, <?php echo $num_rsvps; ?> attended.</p><?php } ?>
					</div>
					</li>
				<?php 
					} // foreach
				} // if posts				
				?>
				</ul> <!-- Past tab. -->
		<?php } ?>
		
		
		
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
