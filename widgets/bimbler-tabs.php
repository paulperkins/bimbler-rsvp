<?php
/*
	BimblerTabs Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Borrowing heavily from Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxTabs
		@version 1.0
*/

class Bimbler_Tabs_Widget extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	//function Bimbler_Tabs_Widget() {
	public function __construct() {
		
		//error_log ('Bimbler_Tabs_Widget: constructor');
		
		parent::__construct( 'bimbler_tabs_widget', 
							'Bimbler Tabs Widget', 
							array(//'description' => 'List posts, comments, and RSVPs with or without tabs.', 
									'classname' => 'bimbler_tabs_widget') 
				);	
	}

	private function bimbler_get_avatar_img ($avatar) {
	
		preg_match( '#src=["|\'](.+)["|\']#Uuis', $avatar, $matches );
	
		return ( isset( $matches[1] ) && ! empty( $matches[1]) ) ?
		(string) $matches[1] : '';
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
			//'rsvps'  	=> 'fa fa-check-square-o',
			'rsvps'  	=> 'fa fa-users',
			'recent'   	=> 'fa fa-edit', //fa fa-clock-o',
			'comments' 	=> 'fa fa-comments-o',
			'events'	=> 'fa fa-calendar'
		);
		$output = sprintf('	<ul class="alx-tabs-nav group tab-count-%s">', $count) . PHP_EOL;
		foreach ( $tabs as $tab ) {
			$output .= sprintf('		<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]) . PHP_EOL;
		}
		$output .= '	</ul>' . PHP_EOL;
		return $output;
	}
	
/*  Widget
/* ------------------------------------ */
	public function widget($args, $instance) {
		extract( $args );
		
		if (empty ($instance['title'])) { $instance['title']=''; }
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();
		
		$time_str = 'j M g:ia';
		$day_time_str = 'D j M g:ia';
		$date_str = 'D j M';
		
/*  Set tabs-nav order & output it
/* ------------------------------------ */
	$tabs = array();
	$count = 0;
	$order = array(
		'rsvps'		=> (empty ($instance['order_rsvps']) ? 1 : $instance['order_rsvps']),
		'recent'	=> (empty ($instance['order_recent']) ? 1 : $instance['order_recent']),
		'comments'	=> (empty ($instance['order_comments']) ? 1 : $instance['order_comments']),
		'events'	=> (empty ($instance['order_events']) ? 1 : $instance['order_events']) 
			);
	asort($order);
	foreach ( $order as $key => $value ) {
		if ( !empty ($instance[$key.'_enable']) ) {
			$tabs[] = $key;
			$count++;
		}
	}
	
	if ( $tabs && ($count > 1) ) 
	{ 
		$output .= $this->_create_tabs($tabs,$count); 
	}
	

	$scroller_style = '';
	//$scroller_style = 'style="overflow: scroll; height:400px;"';
	
?>

	<div class="alx-tabs-container" <?php echo $scroller_style; ?>>

		<?php 
			// RSVPs.
			
			if(!empty ($instance['rsvps_enable'])) { // Popular posts enabled? 

				global $wpdb;
				global $rsvp_db_table;
			
				$table_name = $wpdb->base_prefix . $rsvp_db_table;
				
				$sql =  'SELECT * FROM '. $table_name;
				$sql .= ' WHERE rsvp = \'Y\'';
				$sql .= ' ORDER BY time DESC';
				$sql .= ' LIMIT '. $instance['rsvps_num'];

				
				$rsvps = $wpdb->get_results ($sql);
				
				//error_log ('Returned '. count ($rsvps) .' RSVPs.');
				
				if ($rsvps && (count ($rsvps) > 0))	
				{
		?>			
				
			<ul id="tab-rsvps" class="alx-tab group <?php if($instance['rsvps_thumbs']) { echo 'avatars-enabled'; } ?>">
				<?php

				if (!is_user_logged_in()) {
					echo '<div class="bimbler-alert-box notice"><span>Notice: </span>You must be logged in to view RSVPs.</div>';
				}
				else
				{				
					foreach ( $rsvps as $rsvp) 
					{
						$post = get_post ($rsvp->event);
						$title = $post->post_title;
						
						$user_info = get_userdata ($rsvp->user_id);
						
						if (!$user_info) {
							$user = '<A former member>';
							$user_nick = '<A former member>';
						} else {
							$user = $user_info->user_login;
							$user_nick = $user_info->nickname;
						}
						
						$time = $rsvp->time;
						$avatar = get_avatar ($rsvp->user_id);//, null, null, $user_info->user_login);
						
						$avatar_img = $this->bimbler_get_avatar_img ($avatar);
						
						$avatar_div = '<div class="avatar-clipped" style="background-image: url(\'' . $avatar_img . '\');"></div>';
						
						$num_rsvps = Bimbler_RSVP::get_instance()->count_rsvps ($post->ID);
						
						$start_date = tribe_get_start_date($rsvp->event, false, $date_str);
						
				?>
				<li>
					
					<?php 	if ($instance['rsvps_thumbs']) // Thumbnails enabled? 
							{
					?>
					<div class="tab-item-avatar">
						<a href="/profile/<?php echo $user_info->user_nicename; //echo urlencode ($user); ?>/" title="View <?php echo $user; ?>'s profile"><?php echo $avatar_div; ?></a>
					</div>
					<?php 
							} ?>
					
					<div class="tab-item-inner group">
						<div class="tab-item-name"><?php echo $user_nick; ?> RSVPd 'Yes' <?php if (current_user_can( 'manage_options')) { echo ' on ' . date ($time_str, strtotime($time)); } ?> to</div>
						<div class="tab-item-comment"><a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $title; ?>"><?php echo $title; ?></a></div>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo $start_date; //echo date ($time_str, strtotime($time)); ?>, <?php echo $num_rsvps; ?> attending</p><?php } ?>
					</div>
					
				</li>
				<?php 
						} // foreach. 
					} // if rsvps.
				} // User logged in.
				
			} // // RSVPs enabled.
				
				?>
			</ul><!--/.bimbler-tab-->
			
			
		<?php 
			// Recent posts.
		
			if(!empty ($instance['recent_enable'])) { // Recent posts enabled? ?>
			
			<?php $recent=new WP_Query(); ?>
			<?php $recent->query('showposts='.$instance["recent_num"].'&cat='.$instance["recent_cat_id"].'&ignore_sticky_posts=1');?>
			
			<ul id="tab-recent" class="alx-tab group <?php if($instance['recent_thumbs']) { echo 'avatars-enabled'; } ?>">
				<?php while ($recent->have_posts()): $recent->the_post(); ?>
				<li>
					<?php if($instance['recent_thumbs']) { // Thumbnails enabled? ?>
					<div class="tab-item-avatar">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php if ( has_post_thumbnail() ) { ?>
								<?php the_post_thumbnail('thumb-small'); ?>
							<?php } else {
								echo get_avatar(89); // Admin user - racing man.
							
								/*<img src="<?php echo get_template_directory_uri(); ?>/img/thumb-small.png" alt="<?php the_title(); ?>" /> */
							?>
							<?php } ?>
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
			if(!empty ($instance['comments_enable'])) { // Recent comments enabled? ?>

			<?php $comments = get_comments(array('number'=>$instance["comments_num"],'status'=>'approve','post_status'=>'publish')); ?>
			
			<ul id="tab-comments" class="alx-tab group <?php if($instance['comments_avatars']) { echo 'avatars-enabled'; } ?>">
				<?php foreach ($comments as $comment): ?>
				<li>
				
				<?php 
						$avatar = get_avatar ($comment->comment_author_email);//, null, null, $user_info->user_login);
						
						$avatar_img = $this->bimbler_get_avatar_img ($avatar);
						
						$avatar_div = '<div class="avatar-clipped" style="background-image: url(\'' . $avatar_img . '\');"></div>';
				
				?>
						<?php if($instance['comments_avatars']) { // Avatars enabled? ?>
						<div class="tab-item-avatar">
							<a href="/profile/<?php echo urlencode($comment->comment_author); ?>/" title="View <?php echo $comment->comment_author;?>'s profile.">
								<?php //echo get_avatar($comment->comment_author_email); ?>
								<?php echo $avatar_div; //echo get_avatar($comment->comment_author_email); ?>
							</a>
						</div>
						<?php } ?>
						
						<div class="tab-item-inner group">
							<?php $post = get_post($comment->comment_post_ID);	?>
							<?php $str=explode(' ',get_comment_excerpt($comment->comment_ID)); $comment_excerpt=implode(' ',array_slice($str,0,11)); if(count($str) > 11 && substr($comment_excerpt,-1)!='.') $comment_excerpt.='...' ?>					
							<div class="tab-item-name"><?php echo $comment->comment_author; ?> <?php echo 'said about \'' . $post->post_title . '\''; ?></div>
							<div class="tab-item-comment"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>"><?php echo $comment_excerpt; ?></a></div>
							<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($time_str, strtotime ($comment->comment_date)); //date ($time_str, strtotime($comment->comment_date)); ?></p><?php } ?>
							
						</div>

				</li>
				<?php endforeach; ?>
			</ul><!--/.bimbler-tab-->

		<?php } ?>

		<?php 

			// Events.
		
			if(!empty ($instance['events_enable'])) { // Tags enabled? 
				
				?>
<!-- 				<ul id="tab-events" class="alx-tab group"> -->
				<ul id="tab-events" class="alx-tab group avatars-enabled">
				
				<?php
				
				$posts = Bimbler_RSVP::get_instance()->get_upcoming_events($instance["events_num"]);

				if ($posts)
				{
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
			 					<img src="http://assets2.ridewithgps.com/routes/<?php echo $rwgps_id; ?>/thumb.png" style="width:64 !important;  height:64 !important;" class="avatar avatar-96 wp-user-avatar wp-user-avatar-96 alignnone photo">
	  						
	  							<div class="rsvp-checkin-indicator">   
	
	  							<?php 
	  							// Only show RSVP indicators to logged-in users.
	  							if (is_user_logged_in()) {
			  						if (!isset ($rsvpd)) {
										echo '<div class="rsvp-indicator-none"><i class="fa-question-circle"></i></div>';
									} else if ('Y' == $rsvpd) {
										echo '<div class="rsvp-indicator-yes"><i class="fa-check-circle"></i></div>';
									}
									else {
										echo '<div class="rsvp-indicator-no"><i class="fa-times-circle"></i></div>';
									}
								}
	  							?>
  							
								</div>
							</div>
						</div> 
						<div class="tab-item-inner group">
							<p class="tab-item-title"><a href="<?php echo tribe_get_event_link($post); ?>" rel="bookmark" title="<?php echo $post->post_title; ?>"><?php echo $post->post_title; ?></a></p>
							<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php echo date ($day_time_str, strtotime($event_date)); ?>, <?php echo $num_rsvps; ?> attending.</p><?php } ?>
						</div>
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
		$instance['events_num'] = strip_tags($new['events_num']);
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
			'events_enable' 	=> 1,
			'events_num' 		=> '5',
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
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("events_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("events_num"); ?>" name="<?php echo $this->get_field_name("events_num"); ?>" type="text" value="<?php echo absint($instance["events_num"]); ?>" size='3' />
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
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_events"); ?>">Events</label>
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
