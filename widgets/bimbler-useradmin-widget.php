<?php
/**
 * Bimbler User Admin widget.
 *
 *
 * @package Bimbler User Admin
 * @subpackage 
 * @since 0.1
 */

class Bimbler_UserAdmin_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_Admin_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_useradmin_widget', 
							'Bimbler User Admin Widget', 
							array ('description' => 'Bimbler User Admin Widget' )
							 );
	}

	/*  Create tabs-nav
	 /* ------------------------------------ */
	private function _create_tabs($tabs,$count) {
		// Borrowed from Jermaine Maree, thanks mate!
		$titles = array(
				'newusers'		=> 'New Users',
				'allusers'		=> 'All Users'
		);
		$icons = array(
				'newusers'  	=> 'fa fa-star-o',
				'allusers'  	=> 'fa fa-users'
		);
		
		$output = sprintf('<ul class="alx-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]);
		}
		$output .= '</ul>';
		return $output;
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
	
		extract( $args );
		$instance['title']?NULL:$instance['title']='';
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();
		
		$time_str = 'j M g:ia';
				
		global $wpdb;
		global $rsvp_db_table;
		
		if ( !is_super_admin() ) return;

		$tabs = array();
		$count = 0;
		$order = array(
				'newusers'		=> $instance['order_newusers'],
				'allusers'		=> $instance['order_allusers']
		);
		asort($order);
		foreach ( $order as $key => $value ) {
			if ( $instance[$key.'_enable'] ) {
				$tabs[] = $key;
				$count++;
			}
		}
		
		if ( $tabs && ($count > 0) )
		{
			$output .= $this->_create_tabs($tabs,$count);
		}
?>

		<div class="alx-tabs-container">
		
		<?php
		// New users.
			
		if($instance['newusers_enable']) 
		{ 
		
			global $wpdb;
			
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			$sql =  'SELECT u.id as uid, ';
			$sql .= ' u.user_registered as reg_date ';
			$sql .= " FROM {$wpdb->users} u, ";
			$sql .= " {$wpdb->usermeta} m ";
			$sql .= ' WHERE u.id = m.user_id ';
			$sql .= ' AND m.meta_key = \'wp_capabilities\' ';
			$sql .= ' AND m.meta_value LIKE \'%unverified%\' ';
			$sql .= ' ORDER BY u.user_registered DESC';
			
//			error_log ($sql);
			
			$users = $wpdb->get_results ($sql);

			?>

			<ul id="tab-newusers" class="alx-tab group <?php if($instance['newusers_avatars']) { echo 'avatars-enabled'; } ?>">
			
			<?php
			
//			print_r ($users);
			
			if (!$users || (count ($users) == 0))	{
				echo '<p>No new users.</p>';
			}
			else
			{
					foreach ( $users as $user) {
						$user_info   = get_userdata ($user->uid);
						$username = $user_info->user_login;
						$user_email = $user_info->user_email;
						$user_person = $user_info->user_firstname . ' ' . $user_info->user_lastname;
						//$user_person = $user_info->nicename;
						$registered = $user->reg_date;
						$avatar = get_avatar ($user->uid, $size='150')
						
				?>
				<li>
				<?php 	if ($instance['newusers_avatars']) // Thumbnails enabled? 
						{
				?>
					<div class="tab-item-avatar">
						<?php echo $avatar; ?>
					</div>
					<?php 
						} ?>
					
					<div class="tab-item-inner group">
						<p class="tab-item-title"><?php echo $user_person . ' ('. $username .')'; ?></p>
						<p><?php echo $user_email;?></p>
						<?php if(1) /*$instance['tabs_date'])*/ { ?><p class="tab-item-date"><?php echo date ($time_str, strtotime($registered)); ?></p><?php } ?>
					</div>
					
				</li>
				<?php 
					} // foreach. 
			} // if new users.
			?>
			</ul>
		<?php
		} // // new users enabled.
		?>

		<?php
		// All users.
			
		if($instance['allusers_enable']) 
		{ 
		
			global $wpdb;
			
			$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
			$sql =  'SELECT u.id as uid, ';
			$sql .= ' u.user_registered as reg_date ';
			$sql .= " FROM {$wpdb->wp_users} u, ";
			$sql .= " {$wpdb->usermeta} m ";
			$sql .= ' WHERE u.id = m.user_id ';
			$sql .= ' AND m.meta_key = \'wp_capabilities\' ';
			$sql .= ' AND m.meta_value NOT LIKE \'%unverified%\' ';
			$sql .= ' ORDER BY u.user_registered DESC';

			//error_log ($sql);
			
			
			$users = $wpdb->get_results ($sql);
//			$users = array ();

			?>

			<ul id="tab-allusers" class="alx-tab group <?php if($instance['allusers_avatars']) { echo 'avatars-enabled'; } ?>">
			
			<?php
			
//			print_r ($users);
			
			if (!$users || (count ($users) == 0))	{
				echo '<p>No users.</p>';
			}
			else
			{
				echo '<p>'. count ($users) .' users.</p>';
				
					foreach ( $users as $user) {
						$user_info   = get_userdata ($user->uid);
						$username = $user_info->user_login;
						$user_email = $user_info->user_email;
						$user_person = $user_info->user_firstname . ' ' . $user_info->user_lastname;
						$registered = $user->reg_date;
						$avatar = get_avatar ($user->uid);
						
						$last_login = (int) get_user_meta ($user->uid, 'wp-last-login', true);
						
				?>
				<li>		
				<?php 	if ($instance['allusers_avatars']) // Thumbnails enabled? 
						{
				?>
					<div class="tab-item-avatar">
						<?php echo $avatar; ?>
					</div>
					<?php 
						} ?>
					
					<div class="tab-item-inner group">
						<p class="tab-item-title"><?php echo $user_person . ' ('. $username .')'; ?></p>
						<p><?php echo $user_email;?></p>
						<?php if(1) /*$instance['tabs_date'])*/ { ?><p class="tab-item-date">Join: <?php echo date ($time_str, strtotime($registered)); ?></p><?php } ?>
						<p class="tab-item-date">Last Login: <?php if (!isset ($last_login) || (0 == $last_login)) { echo 'Never'; } else { echo date ($time_str, $last_login); } ?></p>
					</div>
											
				</li>
				<?php 
					} // foreach. 
				} // if all users.
				?>
				</ul>
			<?php
			} // // all users enabled.
			?>			
			
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
	function update( $new, $old ) {
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);

		// New users
		$instance['newusers_enable'] = $new['newusers_enable']?1:0;
		$instance['newusers_avatars'] = $new['newusers_avatars']?1:0;
		
		// All users
		$instance['allusers_enable'] = $new['allusers_enable']?1:0;
		$instance['allusers_avatars'] = $new['allusers_avatars']?1:0;
		
		// Order
		$instance['order_newusers'] = strip_tags($new['order_newusers']);
		$instance['order_allusers'] = strip_tags($new['order_allusers']);
		
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
		// Default widget settings
		$defaults = array(
			'title' 			=> 'User Admin',

			// Recent posts
			'newusers_enable' 	=> 1,
			'newusers_avatars' 	=> 1,
			// Recent posts
			'allusers_enable' 	=> 1,
			'allusers_avatars' 	=> 1,
			// Order
			'order_newusers' 	=> '1',
			'order_allusers' 	=> '2'
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

		<h4>New Users</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('newusers_enable'); ?>" name="<?php echo $this->get_field_name('newusers_enable'); ?>" <?php checked( (bool) $instance["newusers_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_enable'); ?>">Enable new users</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('newusers_avatars'); ?>" name="<?php echo $this->get_field_name('newusers_avatars'); ?>" <?php checked( (bool) $instance["newusers_avatars"], true ); ?>>
			<label for="<?php echo $this->get_field_id('newusers_avatars'); ?>">Show avatars</label>
		</p>

		<h4>All Users</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('allusers_enable'); ?>" name="<?php echo $this->get_field_name('allusers_enable'); ?>" <?php checked( (bool) $instance["allusers_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_enable'); ?>">Enable all users</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('allusers_avatars'); ?>" name="<?php echo $this->get_field_name('allusers_avatars'); ?>" <?php checked( (bool) $instance["allusers_avatars"], true ); ?>>
			<label for="<?php echo $this->get_field_id('allusers_avatars'); ?>">Show avatars</label>
		</p>

		<hr>

		<h4>Tab Order</h4>
		
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_newusers"); ?>">New Users</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_newusers"); ?>" name="<?php echo $this->get_field_name("order_newusers"); ?>" value="<?php echo $instance["order_newusers"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_allusers"); ?>">All Users</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_allusers"); ?>" name="<?php echo $this->get_field_name("order_allusers"); ?>" value="<?php echo $instance["order_allusers"]; ?>" />
		</p>
		
		<hr>
		
	</div>
<?php 
	
	
	
	}
}
