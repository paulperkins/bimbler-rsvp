<?php
/**
 * Bimbler Sales widget.
 *
 *
 * @package Bimbler Sales
 * @subpackage 
 * @since 0.1
 */

class Bimbler_Sales_Widget extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return Bimbler_RSVP_Widget
	 */
	public function __construct() {
		parent::__construct( 'bimbler_sales_widget', 
							'Bimbler Sales Widget', 
							array (	//'classname'   => 'bimbler_rsvp_widget',
									'description' => 'Bimbler Sales Widget' )
							 );
	}

/*	function set_range () {
		$this->start_date    = strtotime( date( 'Y-01-01', current_time('timestamp') ) );
		$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
		$this->chart_groupby = 'month';
	} */
	
	/**
	 * Returns all the orders made by the user
	 *
	 * @param int $user_id
	 * @param string $status (completed|processing|canceled|on-hold etc)
	 * @return array of order ids
	 */
//	function fused_get_all_user_orders($user_id,$status='completed'){
	function fused_get_all_user_orders(){
/*		if(!$user_id)
			return false; */
	
		$orders=array();//order ids
		 
		$args = array(
				'numberposts'     => -1,
//				'meta_key'        => '_customer_user',
//				'meta_value'      => $user_id,
				'post_type'       => 'shop_order',
				'post_status'     => 'publish',
				'tax_query'=>array(
						array(
								'taxonomy'  =>'shop_order_status',
								'field'     => 'slug',
								'terms'     => array ('on-hold','pending','processing')
						) 
				)
		);
	
		$posts=get_posts($args);
		//get the post ids as order ids
		$orders=wp_list_pluck( $posts, 'ID' );
	
		return $orders;
	
	}
	
	/**
	 * Get all Products Successfully Ordered by the user
	 *
	 * @global type $wpdb
	 * @param int $user_id
	 * @return bool|array false if no products otherwise array of product ids
	 */
	function fused_get_all_products_ordered ($user_id=false,$status='completed'){
		$have_data = 0;
		
		$order_list = '(';
		
		//$orders=$this->fused_get_all_user_orders($user_id,'on-hold');
		$orders=$this->fused_get_all_user_orders();
		
		//print_r ('on-hold: '. $orders);
		
		if(!empty($orders))
		{
			$order_list .= join(',', $orders);
			$have_data = 1;
		}

/*		$orders=$this->fused_get_all_user_orders($user_id,'pending');

		//print_r ('pending: '. $orders);
		
		if(!empty($orders))
		{
			if ($have_data)
			{
				$order_list .= ',';
			}
			
			$order_list .= join(',', $orders);
			$have_data = 1;				
		}
		
		$orders=$this->fused_get_all_user_orders($user_id,'processing');
		
		//print_r ('processing: '. $orders);
		
		if(!empty($orders))
		{
			if ($have_data)
			{
				$order_list .= ',';
			}
			
			$order_list .= join(',', $orders);
			$have_data = 1;				
		} */
		
		$order_list .= ')';
		
		//so we have all the orders made by this user which was successfull
	
		//we need to find the products in these order and make sure they are downloadable
	
		// find all products in these order
	
		global $wpdb;
		$query_select_order_items="SELECT order_item_id as id FROM {$wpdb->base_prefix}woocommerce_order_items WHERE order_id IN {$order_list}";
	
//		$query_select_product_ids="SELECT meta_value as product_id FROM {$wpdb->base_prefix}woocommerce_order_itemmeta WHERE meta_key=%s AND order_item_id IN ($query_select_order_items)";
		$query_select_product_ids="SELECT i.order_item_name as item, COUNT(*) as num  FROM {$wpdb->base_prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m WHERE i.order_item_id = m.order_item_id AND m.meta_key='_product_id' AND m.order_item_id IN ($query_select_order_items) GROUP BY i.order_item_id ORDER BY 2 DESC";
		
		error_log ($query_select_product_ids);
		
//		$products=$wpdb->get_col($wpdb->prepare($query_select_product_ids,'_product_id'));
//		$products=$wpdb->get_row($wpdb->prepare($query_select_product_ids,'_product_id'));

		$products=$wpdb->get_results($query_select_product_ids);
/*		$sql  = 'SELECT posts.ID as id, posts.post_parent as parent '; 
		$sql .= 'FROM wp_posts as posts ';
		$sql .= 'INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id ';
		$sql .= 'INNER JOIN wp_postmeta AS postmeta2 ON posts.ID = postmeta2.post_id ';
		$sql .= 'WHERE 1=1 ';
		$sql .= 'AND posts.post_type IN (\'product\', \'product_variation\') ';
		$sql .= 'AND posts.post_status = \'publish\' ';
		$sql .= 'AND (postmeta.meta_key = \'_stock\' ';
		$sql .= 'AND CAST(postmeta.meta_value AS SIGNED) <= -1 '; 
		$sql .= 'AND postmeta.meta_value != \'\' ';
		$sql .= ') ';
		$sql .= 'AND (( postmeta2.meta_key = \'_manage_stock\' '; 
		$sql .= 'AND postmeta2.meta_value = \'yes\' ) '; 
		$sql .= 'OR ( posts.post_type = \'product_variation\' ) ';
		$sql .= ') ';
		$sql .= 'GROUP BY posts.ID '; */
		
//		print_r($query_select_product_ids);
		
		return $products;
	}
	
	/**
	 * Get report totals such as order totals and discount amounts.
	 *
	 * Data example:
	 *
	 * '_order_total' => array(
	 * 		'type'     => 'meta',
	 *    	'function' => 'SUM',
	 *      'name'     => 'total_sales'
	 * )
	 *
	 * @param  array $args
	 * @return array|string depending on query_type
	 */
	public function get_order_report_data( $args = array() ) {
		global $wpdb;
	
		$defaults = array(
				'data'         => array(),
				'where'        => array(),
				'where_meta'   => array(),
				'query_type'   => 'get_row',
				'group_by'     => '',
				'order_by'     => '',
				'limit'        => '',
				'filter_range' => false,
				'nocache'      => false,
				'debug'        => false
		);
	
		$args = wp_parse_args( $args, $defaults );
	
		extract( $args );
	
		if ( empty( $data ) )
			return false;
	
		$select = array();
	
		foreach ( $data as $key => $value ) {
			$distinct = '';
	
			if ( isset( $value['distinct'] ) )
				$distinct = 'DISTINCT';
	
			if ( $value['type'] == 'meta' )
				$get_key = "meta_{$key}.meta_value";
			elseif( $value['type'] == 'post_data' )
			$get_key = "posts.{$key}";
			elseif( $value['type'] == 'order_item_meta' )
			$get_key = "order_item_meta_{$key}.meta_value";
			elseif( $value['type'] == 'order_item' )
			$get_key = "order_items.{$key}";
	
			if ( $value['function'] )
				$get = "{$value['function']}({$distinct} {$get_key})";
			else
				$get = "{$distinct} {$get_key}";
	
			$select[] = "{$get} as {$value['name']}";
		}
	
		$query['select'] = "SELECT " . implode( ',', $select );
		$query['from']   = "FROM {$wpdb->posts} AS posts";
	
		// Joins
		$joins         = array();
		$joins['rel']  = "LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID";
		$joins['tax']  = "LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )";
		$joins['term'] = "LEFT JOIN {$wpdb->terms} AS term USING( term_id )";
	
		foreach ( $data as $key => $value ) {
			if ( $value['type'] == 'meta' ) {
	
				$joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
	
			} elseif ( $value['type'] == 'order_item_meta' ) {
	
				$joins["order_items"] = "LEFT JOIN {$wpdb->base_prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
				$joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->base_prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";
	
			} elseif ( $value['type'] == 'order_item' ) {
	
				$joins["order_items"] = "LEFT JOIN {$wpdb->base_prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
	
			}
		}
	
		if ( ! empty( $where_meta ) ) {
			foreach ( $where_meta as $value ) {
				if ( ! is_array( $value ) )
					continue;
	
				$key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'];
	
				if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {
	
					$joins["order_items"] = "LEFT JOIN {$wpdb->base_prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					$joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->base_prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";
	
				} else {
					// If we have a where clause for meta, join the postmeta table
					$joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
				}
			}
		}
	
		$query['join'] = implode( ' ', $joins );
	
		$query['where']  = "
		WHERE 	posts.post_type 	= 'shop_order'
		AND 	posts.post_status 	= 'publish'
		AND 	tax.taxonomy		= 'shop_order_status'
		AND		term.slug			IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "')
		";
	
		if ( $filter_range ) {
			$query['where'] .= "
			AND 	post_date >= '" . date('Y-m-d', $this->start_date ) . "'
			AND 	post_date < '" . date('Y-m-d', strtotime( '+1 DAY', $this->end_date ) ) . "'
			";
		}
	
		foreach ( $data as $key => $value ) {
			if ( $value['type'] == 'meta' ) {
	
				$query['where'] .= " AND meta_{$key}.meta_key = '{$key}'";
	
			} elseif ( $value['type'] == 'order_item_meta' ) {
	
				$query['where'] .= " AND order_items.order_item_type = '{$value['order_item_type']}'";
				$query['where'] .= " AND order_item_meta_{$key}.meta_key = '{$key}'";
	
			}
		}
	
		if ( ! empty( $where_meta ) ) {
			$relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';
	
			$query['where'] .= " AND (";
	
			foreach ( $where_meta as $index => $value ) {
				if ( ! is_array( $value ) )
					continue;
	
				$key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'];
	
				if ( strtolower( $value['operator'] ) == 'in' ) {
					if ( is_array( $value['meta_value'] ) )
						$value['meta_value'] = implode( "','", $value['meta_value'] );
					if ( ! empty( $value['meta_value'] ) )
						$where_value = "IN ('{$value['meta_value']}')";
				} else {
					$where_value = "{$value['operator']} '{$value['meta_value']}'";
				}
	
				if ( ! empty( $where_value ) ) {
					if ( $index > 0 )
						$query['where'] .= ' ' . $relation;
	
					if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {
						if ( is_array( $value['meta_key'] ) )
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						else
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";
	
						$query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
					} else {
						if ( is_array( $value['meta_key'] ) )
							$query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						else
							$query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";
	
						$query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
					}
				}
			}
	
			$query['where'] .= ")";
		}
	
		if ( ! empty( $where ) ) {
			foreach ( $where as $value ) {
				if ( strtolower( $value['operator'] ) == 'in' ) {
					if ( is_array( $value['value'] ) )
						$value['value'] = implode( "','", $value['value'] );
					if ( ! empty( $value['value'] ) )
						$where_value = "IN ('{$value['value']}')";
				} else {
					$where_value = "{$value['operator']} '{$value['value']}'";
				}
	
				if ( ! empty( $where_value ) )
					$query['where'] .= " AND {$value['key']} {$where_value}";
			}
		}
	
		if ( $group_by ) {
			$query['group_by'] = "GROUP BY {$group_by}";
		}
	
		if ( $order_by ) {
			$query['order_by'] = "ORDER BY {$order_by}";
		}
	
		if ( $limit ) {
			$query['limit'] = "LIMIT {$limit}";
		}
	
		$query          = apply_filters( 'woocommerce_reports_get_order_report_query', $query );
		$query          = implode( ' ', $query );
		$query_hash     = md5( $query_type . $query );
		$cached_results = get_transient( strtolower( get_class( $this ) ) );
	
		if ( $debug ) {
			var_dump( $query );
		}
	
		if ( $debug || $nocache || false === $cached_results || ! isset( $cached_results[ $query_hash ] ) ) {
			$cached_results[ $query_hash ] = apply_filters( 'woocommerce_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
			set_transient( strtolower( get_class( $this ) ), $cached_results, DAY_IN_SECONDS );
		}
	
		$result = $cached_results[ $query_hash ];
	
		return $result;
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
		
		if(0):
		?>
		
		<ul id="recent-comments">
				<?php 
				
				$prod_ids = $this->fused_get_all_products_ordered (1);

				if ($prod_ids && (count ($prod_ids) > 0))	{
//						print_r($prod_ids);
					
					foreach ($prod_ids as $prod_id) {
				?>
				
				<li class="recent-comments">ID: <?php echo $prod_id->item;?> Count: <?php echo $prod_id->num;?> </li>

				<?php 
					}
				}
				?>
			
		</ul><!--/.alx-posts-->
		
		<?php endif; ?>
		
		<div class="section" style="display: block;">
				<?php
				$top_sellers = $this->get_order_report_data( array(
					'data' => array(
						'_product_id' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => '',
							'name'            => 'product_id'
						),
						'_qty' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM',
							'name'            => 'order_item_qty'
						)
					),
					'order_by'     => 'order_item_qty DESC',
					'group_by'     => 'product_id',
					'limit'        => 12,
					'query_type'   => 'get_results',
					'filter_range' => false
				) );

				echo '<table class="tribe-events-calendar">';
				echo '<thead> <tr>';
				echo '<th id="tribe-events-qty" title="Item">Qty</th>';
				echo '<th id="tribe-events-monday" title="Item">Item</th>';
				echo '</tr><tbody class="hfeed vcalendar">';
				
				if ( $top_sellers ) {
						
					foreach ( $top_sellers as $product ) {
//						echo '<tr class="' . ( in_array( $product->product_id, $this->product_ids ) ? 'active' : '' ) . '">
						echo '<tr>';
						
						echo '<td class="tribe-events-thismonth tribe-events-future tribe-events-has-events mobile-trigger tribe-event-day-24 tribe-events-right">';
						echo '<div id="tribe-events-event-1" class="hentry type-tribe-events status-publish">';
						echo '<h3 class="tribe-events-month-event-title summary">' . $product->order_item_qty .'</h3>';
						echo '</div>';
						
						echo '<td class="tribe-events-thismonth">';
						echo '<div id="tribe-events-event-1" class="hentry vevent tribe-events-category- post-107 tribe_events type-tribe_events status-publish">';
						echo '<h3 class="tribe-events-month-event-title summary"><a href="' . add_query_arg( 'product_ids', $product->product_id ) . '">' . get_the_title( $product->product_id ) . '</a></h3>';
						echo '</div>';
						
						
						echo '</tr>';
					}
				} else {
					echo '<tr><td colspan="3">' . __( 'No products found in range', 'woocommerce' ) . '</td></tr>';
		
				}
				echo '</tbody>';
				echo '</table>';
				
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
