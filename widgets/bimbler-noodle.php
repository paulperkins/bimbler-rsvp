<?php
/*
	Bimbler Noodle Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Borrowing heavily from Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxTabs
		@version 1.0
*/

class Bimbler_Noodle_Widget extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	//function Bimbler_Tabs_Widget() {
	public function __construct() {
		
		//error_log ('Bimbler_Tabs_Widget: constructor');
		
		parent::__construct( 'bimbler_noodle_widget', 
							'Bimbler Noodle Widget', 
							array(//'description' => 'List posts, comments, and RSVPs with or without tabs.', 
									'classname' => 'bimbler_noodle_widget') 
				);	
	}

/*  Create tabs-nav
/* ------------------------------------ */
	private function _create_tabs($tabs,$count) {
		// Borrowed from Jermaine Maree, thanks mate!
		$titles = array(
			'rsvps'		=> 'RSVPs',
			'recent'	=> 'Recent Posts',
			'comments'	=> 'Comments',
			'events'	=> 'Events'
		);
		$icons = array(
			'rsvps'  	=> 'fa fa-check-square-o',
			'recent'   	=> 'fa fa-edit', //fa fa-clock-o',
			'comments' 	=> 'fa fa-comments-o',
			'events'	=> 'fa fa-calendar'
		);
		$output = sprintf('<ul class="alx-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]);
		}
		$output .= '</ul>';
		return $output;
	}
	
/*  Widget
/* ------------------------------------ */
	public function widget($args, $instance) {
		extract( $args );
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
		'rsvps'		=> $instance['order_rsvps'],
		'recent'	=> $instance['order_recent'],
		'comments'	=> $instance['order_comments'],
		'events'	=> $instance['order_events']			
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
			// RSVPs.
			
			if($instance['rsvps_enable']) { // Popular posts enabled? 

				global $wpdb;
				global $rsvp_db_table;
			
				$table_name = $wpdb->base_prefix . $rsvp_db_table;
				
				$sql =  'SELECT * FROM '. $table_name;
				$sql .= ' WHERE rsvp = \'Y\'';
				$sql .= ' ORDER BY time DESC';
				$sql .= ' LIMIT '. $instance['rsvps_num'];

				
				$rsvps = $wpdb->get_results ($sql);
				
				error_log ('Returned '. count ($rsvps) .' RSVPs.');
				
				if ($rsvps && (count ($rsvps) > 0))	
				{
		?>			
				
			<ul id="tab-rsvps" class="alx-tab group <?php if($instance['rsvps_thumbs']) { echo 'avatars-enabled'; } ?>">
				<?php

					foreach ( $rsvps as $rsvp) 
					{
						$post = get_post ($rsvp->event);
						$title = $post->post_title;
						$user_info   = get_userdata ($rsvp->user_id);
						$user = $user_info->user_login;
						$time = $rsvp->time;
						$avatar = get_avatar ($rsvp->user_id);//, null, null, $user_info->user_login);
						
						$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
						
				?>
				<li>
					
					<?php 	if ($instance['rsvps_thumbs']) // Thumbnails enabled? 
							{
					?>
					<div class="tab-item-avatar">
						<a href="<?php echo tribe_get_event_link($post); ?>" title="<?php echo $title; ?>"><?php echo $avatar; ?></a>
					</div>
					<?php 
							} ?>
					
					<div class="tab-item-inner group">
						<div class="tab-item-name"><?php echo $user; ?> RSVPd 'Yes' to</div>
						<div class="tab-item-comment"><a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $title; ?>"><?php echo $title; ?></a></div>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($time_str, strtotime($time)); ?>, <?php echo $num_rsvps; ?> attending.</p><?php } ?>
					</div>
					
				</li>
				<?php 
					} // foreach. 
				} // if rsvps.
				
			} // // RSVPs enabled.
				
				?>
			</ul><!--/.bimbler-tab-->
			
			
		<?php 
			// Recent comments.
		
			if($instance['recent_enable']) { // Recent posts enabled? ?>
			
			<?php $recent=new WP_Query(); ?>
			<?php $recent->query('showposts='.$instance["recent_num"].'&cat='.$instance["recent_cat_id"].'&ignore_sticky_posts=1');?>
			
			<ul id="tab-recent" class="alx-tab group <?php if($instance['recent_thumbs']) { echo 'avatars-enabled'; } ?>">
				<?php while ($recent->have_posts()): $recent->the_post(); ?>
				<li>
					<?php if($instance['recent_thumbs']) { // Thumbnails enabled? ?>
					<div class="tab-item-avatar">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php if ( has_post_thumbnail() ): ?>
								<?php the_post_thumbnail('thumb-small'); ?>
							<?php else: ?>
								<img src="<?php echo get_template_directory_uri(); ?>/img/thumb-small.png" alt="<?php the_title(); ?>" />
							<?php endif; ?>
							<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-play"></i></span>'; ?>
							<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>'; ?>
							<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-star"></i></span>'; ?>
						</a>
					</div>
					<?php } ?>
					
					<div class="tab-item-inner group">
						<?php if($instance['tabs_category']) { ?><p class="tab-item-category"><?php the_category(' / '); ?></p><?php } ?>
						<p class="tab-item-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></p>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php the_time($time_str); ?></p><?php } ?>
					</div>
					
				</li>
				<?php endwhile; ?>
			</ul><!--/.bimbler-tab-->
		<?php } ?>

		<?php 
			// Comments.
			if($instance['comments_enable']) { // Recent comments enabled? ?>

			<?php $comments = get_comments(array('number'=>$instance["comments_num"],'status'=>'approve','post_status'=>'publish')); ?>
			
			<ul id="tab-comments" class="alx-tab group <?php if($instance['comments_avatars']) { echo 'avatars-enabled'; } ?>">
				<?php foreach ($comments as $comment): ?>
				<li>
						<?php if($instance['comments_avatars']) { // Avatars enabled? ?>
						<div class="tab-item-avatar">
							<a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
								<?php //echo get_avatar($comment->comment_author_email); ?>
								<?php echo get_avatar($comment->comment_author_email); ?>
							</a>
						</div>
						<?php } ?>
						
						<div class="tab-item-inner group">
							<?php $str=explode(' ',get_comment_excerpt($comment->comment_ID)); $comment_excerpt=implode(' ',array_slice($str,0,11)); if(count($str) > 11 && substr($comment_excerpt,-1)!='.') $comment_excerpt.='...' ?>					
							<div class="tab-item-name"><?php echo $comment->comment_author; ?> <?php echo 'said'; ?></div>
							<div class="tab-item-comment"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>"><?php echo $comment_excerpt; ?></a></div>
							<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($time_str, strtotime ($comment->comment_date)); //date ($time_str, strtotime($comment->comment_date)); ?></p><?php } ?>
							
						</div>

				</li>
				<?php endforeach; ?>
			</ul><!--/.bimbler-tab-->

		<?php } ?>

		<?php 

			// Events.
		
			if($instance['events_enable']) { // Tags enabled? 
				
				?>
<!-- 				<ul id="tab-events" class="alx-tab group"> -->
				<ul id="tab-events" class="alx-tab group avatars-enabled">
				
				<?php
				
				if ( function_exists( 'tribe_get_events' ) ) {
				
					$args = array(
							'eventDisplay'   => 'upcoming',
							'posts_per_page' => $instance["comments_num"]
					);
				
					if (1)// ! empty( $category ) ) 
					{
						$args['meta_query'] = array(
								array(
										'key'         => '_BimblerRidePage',
										'value'       => '139'
								)
						);
					}
				
					$posts = tribe_get_events( $args );
				}

				if ($posts)
				{
					foreach ($posts as $post)
					{
						$event_date = $post->EventStartDate;
						
						$rsvpd = Bimbler_RSVP::get_instance()->get_current_rsvp ($post->ID);
						$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
//						print_r ($post);
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
<!--  					<div class="bimbler-container">
						<div class="bimbler-tr"><p>TR</p></div>
						<div class="bimbler-left"><p>Left</p></div>
						<div class="bimbler-br"><p>BR</p></div>
					</div> -->
					</li>
				<?php 
					} // foreach
				} // if posts				
				?>
				</ul>
		<?php } ?>
	</div>

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
	// Recent posts
		$instance['recent_enable'] = $new['recent_enable']?1:0;
		$instance['recent_thumbs'] = $new['recent_thumbs']?1:0;
		$instance['recent_cat_id'] = strip_tags($new['recent_cat_id']);
		$instance['recent_num'] = strip_tags($new['recent_num']);
	// RSVPs
		$instance['rsvps_enable'] = $new['rsvps_enable']?1:0;
		$instance['rsvps_thumbs'] = $new['rsvps_thumbs']?1:0;
		$instance['rsvps_cat_id'] = strip_tags($new['rsvps_cat_id']);
		$instance['rsvps_time'] = strip_tags($new['rsvps_time']);
		$instance['rsvps_num'] = strip_tags($new['rsvps_num']);
	// Recent comments
		$instance['comments_enable'] = $new['comments_enable']?1:0;
		$instance['comments_avatars'] = $new['comments_avatars']?1:0;
		$instance['comments_num'] = strip_tags($new['comments_num']);
	// Events
		$instance['events_enable'] = $new['events_enable']?1:0;
	// Order
		$instance['order_recent'] = strip_tags($new['order_recent']);
		$instance['order_rsvps'] = strip_tags($new['order_rsvps']);
		$instance['order_comments'] = strip_tags($new['order_comments']);
		$instance['order_events'] = strip_tags($new['order_events']);
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
		// Recent posts
			'recent_enable' 	=> 1,
			'recent_thumbs' 	=> 1,
			'recent_cat_id' 	=> '0',
			'recent_num' 		=> '5',
		// RSVPs
			'rsvps_enable' 	=> 1,
			'rsvps_thumbs' 	=> 1,
			'rsvps_cat_id' 	=> '0',
			'rsvps_time' 		=> '0',
			'rsvps_num' 		=> '5',
		// Recent comments
			'comments_enable' 	=> 1,
			'comments_avatars' 	=> 1,
			'comments_num' 		=> '5',
		// Events
			'events_enable' 		=> 1,
		// Order
			'order_recent' 		=> '1',
			'order_rsvps' 		=> '2',
			'order_comments' 	=> '3',
			'order_events' 		=> '4',
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

		<h4>Recent Posts</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('recent_enable'); ?>" name="<?php echo $this->get_field_name('recent_enable'); ?>" <?php checked( (bool) $instance["recent_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('recent_enable'); ?>">Enable recent posts</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('recent_thumbs'); ?>" name="<?php echo $this->get_field_name('recent_thumbs'); ?>" <?php checked( (bool) $instance["recent_thumbs"], true ); ?>>
			<label for="<?php echo $this->get_field_id('recent_thumbs'); ?>">Show thumbnails</label>
		</p>	
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("recent_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("recent_num"); ?>" name="<?php echo $this->get_field_name("recent_num"); ?>" type="text" value="<?php echo absint($instance["recent_num"]); ?>" size='3' />
		</p>
		<p>
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("recent_cat_id"); ?>">Category:</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("recent_cat_id"), 'selected' => $instance["recent_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>		
		</p>
		
		<hr>
		<h4>RSVPs</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('rsvps_enable'); ?>" name="<?php echo $this->get_field_name('rsvps_enable'); ?>" <?php checked( (bool) $instance["rsvps_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('rsvps_enable'); ?>">Enable RSVPs</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('rsvps_thumbs'); ?>" name="<?php echo $this->get_field_name('rsvps_thumbs'); ?>" <?php checked( (bool) $instance["rsvps_thumbs"], true ); ?>>
			<label for="<?php echo $this->get_field_id('rsvps_thumbs'); ?>">Show thumbnails</label>
		</p>	
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("rsvps_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("rsvps_num"); ?>" name="<?php echo $this->get_field_name("rsvps_num"); ?>" type="text" value="<?php echo absint($instance["rsvps_num"]); ?>" size='3' />
		</p>
		<p>
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("rsvps_cat_id"); ?>">Category:</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("rsvps_cat_id"), 'selected' => $instance["rsvps_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>		
		</p>
		<p style="padding-top: 0.3em;">
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("rsvp_time"); ?>">RSVPs from:</label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id("rsvps_time"); ?>" name="<?php echo $this->get_field_name("rsvps_time"); ?>">
			  <option value="0"<?php selected( $instance["rsvps_time"], "0" ); ?>>All time</option>
			  <option value="1 year ago"<?php selected( $instance["rsvps_time"], "1 year ago" ); ?>>This year</option>
			  <option value="1 month ago"<?php selected( $instance["rsvps_time"], "1 month ago" ); ?>>This month</option>
			  <option value="1 week ago"<?php selected( $instance["rsvps_time"], "1 week ago" ); ?>>This week</option>
			  <option value="1 day ago"<?php selected( $instance["rsvps_time"], "1 day ago" ); ?>>Past 24 hours</option>
			</select>	
		</p>
		
		<hr>
		<h4>Recent Comments</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('comments_enable'); ?>" name="<?php echo $this->get_field_name('comments_enable'); ?>" <?php checked( (bool) $instance["comments_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_enable'); ?>">Enable recent comments</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('comments_avatars'); ?>" name="<?php echo $this->get_field_name('comments_avatars'); ?>" <?php checked( (bool) $instance["comments_avatars"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_avatars'); ?>">Show avatars</label>
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("comments_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("comments_num"); ?>" name="<?php echo $this->get_field_name("comments_num"); ?>" type="text" value="<?php echo absint($instance["comments_num"]); ?>" size='3' />
		</p>

		<hr>
		<h4>Events</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('events_enable'); ?>" name="<?php echo $this->get_field_name('events_enable'); ?>" <?php checked( (bool) $instance["events_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('events_enable'); ?>">Enable events</label>
		</p>
	
		<hr>
		<h4>Tab Order</h4>
		
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_recent"); ?>">Recent posts</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_recent"); ?>" name="<?php echo $this->get_field_name("order_recent"); ?>" value="<?php echo $instance["order_recent"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_rsvps"); ?>">RSVPs</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_rsvps"); ?>" name="<?php echo $this->get_field_name("order_rsvps"); ?>" value="<?php echo $instance["order_rsvps"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_comments"); ?>">Recent comments</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_comments"); ?>" name="<?php echo $this->get_field_name("order_comments"); ?>" value="<?php echo $instance["order_comments"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_tags"); ?>">Events</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_events"); ?>" name="<?php echo $this->get_field_name("order_events"); ?>" value="<?php echo $instance["order_events"]; ?>" />
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
