<?php
/**
 * 
 * Copyright (c) 2010 - 2018 Linh Ân https://zindo.info
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Ân
 * @package affiliates
 * @since affiliates 2.5.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conversion post type / This post type save the database of conversion
 */
class Affiliates_Conversion_Post_Type extends Affiliates_Conversion {

	function __construct() {
		// Record the conversion
		add_action( 'init', array($this, 'affiliates_conversion_post_type_init'), 10, 1 );
		
		// Remove meta boxes
		add_action( 'add_meta_boxes', array( $this, 'affiliates_conversion_remove_meta_boxes' ), 90 );

		// Parent Menu Fix
		add_filter( 'parent_file', array($this, 'affiliates_conversion_parent_file' ) );
		
		// Disable quick edit for this post type
		add_filter( 'post_row_actions', array($this, 'affiliates_conversion_disable_quick_edit' ), 10, 2 );

		// Custom metabox using CMB2
		add_action( 'cmb2_admin_init', array($this, 'affiliates_conversion_register_metabox') );

		// Remove post-body-content using CSS
		add_action( 'admin_head', array($this, 'affiliates_conversion_admin_head') );

		// Filter the column
		add_filter( "manage_{$this->post_type}_posts_columns", array($this, 'affiliates_conversion_posts_columns'), 10, 1 );
		add_action( "manage_{$this->post_type}_posts_custom_column", array($this, 'affiliates_conversion_posts_columns_data'), 10, 2 );
	}

	public function affiliates_conversion_post_type_init() {
		register_post_type( 'aff_conversion',
			array(
				'labels' => array(
					'name' => __( 'Chuyển đổi', 'affiliates' ),
					'singular_name' => __( 'Chuyển đổi', 'affiliates' ),
					'edit_item' => __( 'Xem/Chỉnh sửa chuyển đổi', 'affiliates' )
				),
				'public' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => $this->post_type),
				'exclude_from_search' => true,
				'hierarchical'        => true,
		        'publicly_queryable'  => false,
				'capability_type' => 'post',
				'capabilities' => array(
					'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
					'delete_published_posts' => 'do_not_allow',
					'delete_posts' => 'do_not_allow',
					// 'delete_post' => 'do_not_allow',
				),
				'supports' => false,
				'map_meta_cap' => true,
				'show_in_menu' => false
			)
		);	
	}

	public function affiliates_conversion_disable_quick_edit( $actions = array(), $post = null ) {

	    // Abort if the post type is not "books"
	    if ( ! is_post_type_archive( $this->post_type ) ) {
	        return $actions;
	    }

	    // Remove the Quick Edit link
	    if ( isset( $actions['inline hide-if-no-js'] ) ) {
	        unset( $actions['inline hide-if-no-js'] );
	    }

	    // Return the set of links without Quick Edit
	    return $actions;

	}
 
	/**
	 * Fix Parent Admin Menu Item
	 */
	public function affiliates_conversion_parent_file( $parent_file ){
	 
	    /* Get current screen */
	    global $current_screen, $self;
	 
	    if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && $this->post_type == $current_screen->post_type ) {
	        $parent_file = 'affiliates-admin';
	    }
	 
	    return $parent_file;
	}

	public function affiliates_conversion_remove_meta_boxes() {
		$metabox_to_remove = array(
			'submitdiv' => 'side',
			'mymetabox_revslider_0' => 'normal'
		);

		foreach ($metabox_to_remove as $metabox => $context) {
			remove_meta_box( $metabox, $this->post_type, $context );
		}
	}

	/**
	* Metabox
	**/
	public function affiliates_conversion_register_metabox() {
		global $conversion_status;

		$prefix = 'affiliates_conversion_';

		/**
		 * metabox = submitdiv
		 */
		$cmb_submitdiv = new_cmb2_box( array(
			'id'            => $prefix . 'submitdiv',
			'title'         => esc_html__( 'Cập nhật', 'affiliates' ),
			'object_types'  => array( $this->post_type ), // Post type
			'context'    => 'side',
			'priority'   => 'high',
		) );

		// Post status
		$cmb_submitdiv->add_field( array(
			'name'       => esc_html__( 'Trạng thái', 'affiliates' ),
			'id'         => $prefix . 'status',
			'type'       => 'select',
		    'options'    => $conversion_status
		) );

		// Save button
		$cmb_submitdiv->add_field( array(
			'name'       => esc_html__( 'Cập nhật', 'affiliates' ),
			'id'         => $prefix . 'update_button',
			'type'       => 'text',
			'render_row_cb' => array($this, 'affiliates_conversion_update_button')
		) );

		/**
		 * metabox = main content
		 */
		$cmb_main = new_cmb2_box( array(
			'id'            => $prefix . 'main',
			'title'         => esc_html__( 'Chi tiết chuyển đổi', 'affiliates' ),
			'object_types'  => array( $this->post_type ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
		) );
		
		$cmb_main->add_field( array(
			'name'       => esc_html__( 'Bảng chi tiết chuyển đổi', 'affiliates' ),
			'id'         => $prefix . 'main_content',
			'type'       => 'text',
			'render_row_cb' => array($this, 'affiliates_conversion_main_content')
		) );

	}

	public function affiliates_conversion_update_button($field_args, $field) {
		include AFFILIATES_CORE_LIB . '/templates/conversion/affiliates_conversion_update_button.php';
	}

	public function affiliates_conversion_main_content($field_args, $field) {
		include AFFILIATES_CORE_LIB . '/templates/conversion/affiliates_conversion_main_content.php';
	}

	/**
	 * Remove admin head
	 */	
	public function affiliates_conversion_admin_head()	{
		global $pagenow, $post;
		
		if ($pagenow == 'post.php' && $post->post_type == $this->post_type) {
			echo '<style>#post-body-content {display: none !important;}</style>';
		}
	}

	/**
	 * Custom admin columns
	 */	
	public function affiliates_conversion_posts_columns($columns)	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Chuyển đổi', 'affiliates' ),
			'affiliate_id' => __( 'Cộng tác viên', 'affiliates' ),
			'order_date' => __( 'Thời gian đặt hàng', 'affiliates' ),
			'order_id' => __( 'Mã đơn hàng', 'affiliates' ),
			'status' => __( 'Trạng thái', 'affiliates' )
		);

		return $columns;
	}

	public function affiliates_conversion_posts_columns_data($columns, $post_id)	{
		global $conversion_status, $post;

		$prefix = 'affiliates_conversion_';
		$order_id = get_post_meta( $post_id, 'order_id', true );
		$transaction_data = Affiliates_Utility::getTransactionDataByOrderID($order_id);

		switch ($columns) {
			case 'affiliate_id':
				$affiliate_id = $post->post_author;
				if ($affiliate_id) {
					echo '<strong>' . get_the_author_meta('user_login', $affiliate_id) . '</strong>';
				}
				break;			
			case 'order_date':
				$order_date = $transaction_data->booking_date;
				if ($order_date) {
					echo $order_date;
				}
				break;
			case 'order_id':
				$order_id = get_post_meta( $post_id, 'order_id', true );
				if ($order_id) {

					echo '<a target="_blank" href="' . admin_url( 'admin.php?page=tourmaster_order&single=' . $order_id ) . '">' . '#' . $order_id . '</a>';
				}
				break;
			case 'status':
				$status = get_post_meta( $post_id, $prefix . 'status', true );
				if ($status) {
					echo $conversion_status[$status];
				}
				break;
			default:
				echo ($columns);
				break;
		}
	}	
}

new Affiliates_Conversion_Post_Type();
