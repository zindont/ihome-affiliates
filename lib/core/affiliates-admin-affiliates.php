<?php
/**
 * affiliates-admin-affiliates.php
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

// Shows hits by affiliate

define( 'AFFILIATES_AFFILIATES_PER_PAGE', 10 );

define( 'AFFILIATES_ADMIN_AFFILIATES_NONCE_1', 'affiliates-nonce-1' );
define( 'AFFILIATES_ADMIN_AFFILIATES_NONCE_2', 'affiliates-nonce-2' );
define( 'AFFILIATES_ADMIN_AFFILIATES_FILTER_NONCE', 'affiliates-filter-nonce' );
define( 'AFFILIATES_ADMIN_AFFILIATES_ACTION_NONCE', 'affiliates-action-nonce' );

define( 'AFFILIATES_ADMIN_AFFILIATES_NO_ERROR', 001 );
define( 'AFFILIATES_ADMIN_AFFILIATES_ERROR_NAME_EMPTY', 002 );
define( 'AFFILIATES_ADMIN_AFFILIATES_ERROR_USERNAME', 003 );

require_once( AFFILIATES_CORE_LIB . '/class-affiliates-list-user-table.php' );
// require_once( AFFILIATES_CORE_LIB . '/class-affiliates-date-helper.php' );
// require_once( AFFILIATES_CORE_LIB . '/affiliates-admin-affiliates-add.php' );
require_once( AFFILIATES_CORE_LIB . '/affiliates-admin-affiliates-edit.php' );
// require_once( AFFILIATES_CORE_LIB . '/affiliates-admin-affiliates-remove.php' );

/**
 * Affiliate table and action handling.
 */
function affiliates_admin_affiliates() {

	// Switch to edit page
	if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['user_id']) ) {
		if ( get_user_by('id', $_REQUEST['user_id']) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'edit_affiliates_user_' . $_REQUEST['user_id'] )) {
			affiliates_admin_affiliates_edit($_REQUEST['user_id']);
			return;
		}
	}

	global $wpdb, $wp_rewrite, $affiliates_options;
	echo "<div class='wrap'>";
		echo '<h1 class="wp-heading-inline">' . __( 'Quản lý CTV', 'affiliates' ) . '</h1>';
		// Create an instance of our package class.
		$affiliates_table = new Affiliates_List_User_Table();
		// Fetch, prepare, sort, and filter our data.
		$affiliates_table->prepare_items();

		// Display table
		$affiliates_table->display();
	

		affiliates_footer();
	echo "</div>";
} // function affiliates_admin_affiliates()
