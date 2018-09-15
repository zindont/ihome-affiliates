<?php
/**
 * 
 * Copyright (c) 2010, 2011 Linh Ân https://zindo.info
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
 * @since affiliates 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * LOADING THE BASE CLASS
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Affiliates_List_User_Table extends WP_List_Table {

	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'affiliates-user',     // Singular name of the listed records.
			'plural'   => 'affiliates-users',    // Plural name of the listed records.
			'ajax'     => true,       // Does this table support ajax?
		) );
	}

	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'user_login'   => _x( 'Tên đăng nhập', 'Column label', 'affiliates' ),
			'user_id'    => _x( 'User ID', 'Column label', 'affiliates' ),
			'user_email' => _x( 'Email', 'Column label', 'affiliates' ),
			'link' => _x( 'Link affiliate', 'Column label', 'affiliates' ),
			'affiliates_status' => _x( 'Trạng thái', 'Column label', 'affiliates' ),
		);

		return $columns;
	}

	protected function get_sortable_columns() {
		$sortable_columns = array(
			'user_id'    => array( 'user_id', false ),
			'user_login'   => array( 'user_login', false ),
			'user_email' => array( 'user_email', false ),
		);

		return $sortable_columns;
	}


	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'user_id';
			case 'user_login':
			case 'user_email':
			case 'link':
			case 'affiliates_status':
				return $item[ $column_name ];
			default:
				// return '';
				// return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['user_id']                // The value of the checkbox should be the record's ID.
		);
	}

	/**
	 * Get title column value.
	 *
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links are
	 * secured with wp_nonce_url(), as an expected security measure.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_user_login( $item ) {
		// var_dump($item);
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'user_id' => $item['user_id']
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( 
				wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'edit_affiliates_user_' . $item['user_id']) 
			),
			_x( 'Xem/Chỉnh sửa', 'affiliates' )
		);

		// Return the title contents.
		return sprintf( '%1$s %2$s',
			'<a href="' . wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'edit_affiliates_user_' . $item['user_id']) . '"><strong>' . $item['user_login'] . '</strong></a>',
			$this->row_actions( $actions )
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 25;

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		/*
		 * GET THE DATA!
		 */

		$data = $this->getAffiliatesList();

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for 
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */

		// If no sort, default to id.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'user_id'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : SORT_ASC; // WPCS: Input var ok.

		switch (strtolower($order)) {
			case 'asc':
				$order = SORT_ASC;
				break;
			case 'desc' :
				$order = SORT_DESC;
				break;
			default:
				$order = SORT_ASC;
				break;
		}

		array_multisort( array_column($data, $orderby), $order, $data );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Callback to allow sorting of data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to id.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'user_id'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}

	protected function getAffiliatesList() {
		$users = get_users( array(
			// 'role__in' => array(
			// 	'administrator',
			// 	'contributor'
			// ),
			'meta_query' => array(
				'relation' => 'OR', // Optional, defaults to "AND"
				array(
					'key'     => 'affiliates_status',
					'value'   => 'affiliates_status_pending',
					'compare' => '='
				),
				array(
					'key'     => 'affiliates_status',
					'value'   => 'affiliates_status_approved',
					'compare' => '='
				)				
			)
		) );

		// Prepare data
		$users = array_map(function($user){
			return array(
				'user_id' => $user->ID,
				'user_login' => $user->get('user_login'),
				'user_email' => $user->get('user_email'),
				'link' => '<span class="affiliate-link">' . affiliates_get_affiliate_url( get_bloginfo('url'), $user->ID ) . '</span>',
				'affiliates_status' => Affiliates_Utility::getAffiliatesStatusFields()[get_user_meta( $user->ID, 'affiliates_status', true ) ?: 'affiliates_status_pending']
			);
		}, $users);

		return $users;
	}
}

