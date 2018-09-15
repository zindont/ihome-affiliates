<?php
/**
 * affiliates-admin-affiliates-remove.php
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
 * @author Karim Rahimpur
 * @package affiliates
 * @since affiliates 1.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show form to remove an affiliate.
 * @param int $affiliate_id affiliate id
 */
function affiliates_admin_affiliates_remove( $affiliate_id ) {
	
	global $wpdb;
	
	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	$affiliate = affiliates_get_affiliate( intval( $affiliate_id ) );
	
	if ( empty( $affiliate ) ) {
		wp_die( __( 'No such affiliate.', 'affiliates' ) );
	}
	
	$affiliates_users_table = _affiliates_get_tablename( 'affiliates_users' );
	
	$affiliate_user = null;
	$affiliate_user_edit = '';
	$affiliate_user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $affiliates_users_table WHERE affiliate_id = %d", intval( $affiliate_id ) ) );
	if ( $affiliate_user_id !== null ) {
		$affiliate_user = get_user_by( 'id', intval( $affiliate_user_id ) );
		if ( $affiliate_user ) {
			if ( current_user_can( 'edit_user',  $affiliate_user->ID ) ) {
				$affiliate_user_edit = sprintf( __( 'Edit %s', 'affiliates' ) , '<a target="_blank" href="' . esc_url( "user-edit.php?user_id=$affiliate_user->ID" ) . '">' . $affiliate_user->user_login . '</a>' );
			} else {
				$affiliate_user_edit = $affiliate_user->user_login;
			}
		}
	}
	
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg( 'action', $current_url );
	$current_url = remove_query_arg( 'affiliate_id', $current_url );
	
	$output =
		'<div class="manage-affiliates">' .
		'<div>' .
			'<h1>' .
				__( 'Remove an affiliate', 'affiliates' ) .
			'</h1>' .
		'</div>' .
		'<form id="remove-affiliate" action="' . esc_url( $current_url ) . '" method="post">' .
		'<div class="affiliate remove">' .
		'<input id="affiliate-id-field" name="affiliate-id-field" type="hidden" value="' . esc_attr( intval( $affiliate_id ) ) . '"/>' .
		'<ul>' .
		'<li>' . sprintf( __( 'Name : %s', 'affiliates' ), wp_filter_kses( $affiliate['name'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Email : %s', 'affiliates' ), wp_filter_kses( $affiliate['email'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Username : %s', 'affiliates' ), wp_filter_kses( $affiliate_user_edit ) ) . '</li>' .
		'<li>' . sprintf( __( 'From : %s', 'affiliates' ), wp_filter_kses( $affiliate['from_date'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Until : %s', 'affiliates' ), wp_filter_kses( $affiliate['from_date'] ) ) . '</li>' .
		'</ul> ' .
		wp_nonce_field( 'affiliates-remove', AFFILIATES_ADMIN_AFFILIATES_NONCE, true, false ) .
		'<input class="button button-primary" type="submit" value="' . __( 'Remove', 'affiliates' ) . '"/>' .
		'<input type="hidden" value="remove" name="action"/>' .
		' ' .
		'<a class="cancel button" href="' . esc_url( $current_url ) . '">' . __( 'Cancel', 'affiliates' ) . '</a>' .
		'</div>' .
		'</div>' . // .affiliate.remove
		'</form>' .
		'</div>'; // .manage-affiliates
	
	echo $output;
	
	affiliates_footer();
} // function affiliates_admin_affiliates_remove

/**
 * Handle remove form submission.
 */
function affiliates_admin_affiliates_remove_submit() {
	
	global $wpdb;
	$result = false;
	
	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	if ( !wp_verify_nonce( $_POST[AFFILIATES_ADMIN_AFFILIATES_NONCE], 'affiliates-remove' ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	$affiliates_table = _affiliates_get_tablename( 'affiliates' );
	
	$affiliate_id = isset( $_POST['affiliate-id-field'] ) ? $_POST['affiliate-id-field'] : null;
	if ( $affiliate_id ) {
		$valid_affiliate = false;
		// do not mark the pseudo-affiliate as deleted: type != ...
		$check = $wpdb->prepare(
			"SELECT affiliate_id FROM $affiliates_table WHERE affiliate_id = %d AND (type IS NULL OR type != '" . AFFILIATES_DIRECT_TYPE . "')",
			intval( $affiliate_id ) );
		if ( $wpdb->query( $check ) ) {
			$valid_affiliate = true;
		}
		
		if ( $valid_affiliate ) {
			$result = false !== $wpdb->query(
				$query = $wpdb->prepare(
					"UPDATE $affiliates_table SET status = 'deleted' WHERE affiliate_id = %d",
					intval( $affiliate_id )
				)
			);
			do_action( 'affiliates_deleted_affiliate', intval( $affiliate_id ) );
		}
	}
	
	return $result;
	
} // function affiliates_admin_affiliates_remove_submit


/**
 * Shows form to confirm bulk-removal of affiliates.
 */
function affiliates_admin_affiliates_bulk_remove() {

	global $wpdb;
	
	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	$affiliate_ids = isset( $_POST['affiliate_ids'] ) ? $_POST['affiliate_ids'] : null;
	
	$affiliates = array();
	foreach ( $affiliate_ids as $affiliate_id ) {
		$affiliate = affiliates_get_affiliate( intval( $affiliate_id ) );
		if ( $affiliate ) {
			$affiliates[] = $affiliate;
		}
	}
	
	if ( sizeof( $affiliates ) == 0 ) {
		wp_die( __( 'There are no affiliates.', 'affiliates' ) );
	}
	
	$affiliates_users_table = _affiliates_get_tablename( 'affiliates_users' );
	
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg( 'action', $current_url );
	$current_url = remove_query_arg( 'affiliate_id', $current_url );
	
	$output =
	'<div class="manage-affiliates">' .
	'<div>' .
	'<h1>' .
	__( 'Remove affiliates', 'affiliates' ) .
	'</h1>' .
	'</div>' .
	'<form id="remove-affiliate" action="' . esc_url( $current_url ) . '" method="post">';
	
	$output .= '<p>';
	$output .= __( 'Please confirm removal of the following affiliates. This action cannot be undone.', 'affiliates' );
	$output .= '</p>';
	
	$output .= '<div class="affiliate remove">';
	
	foreach ( $affiliates as $affiliate ) {
		$output .= 	'<input id="affiliate_ids" name="affiliate_ids[]" type="hidden" value="' . esc_attr( intval( $affiliate['affiliate_id'] ) ) . '"/>';
	
		$affiliate_user_edit = '';
		$affiliate_user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $affiliates_users_table WHERE affiliate_id = %d", intval( $affiliate['affiliate_id'] ) ) );
		if ( $affiliate_user_id !== null ) {
			$affiliate_user = get_user_by( 'id', intval( $affiliate_user_id ) );
			if ( $affiliate_user ) {
				$affiliate_user_edit = $affiliate_user->user_login;
			}
		}
		
		$output .= '<ul>' .
		'<li>' . sprintf( __( 'Name : %s', 'affiliates' ), wp_filter_kses( $affiliate['name'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Email : %s', 'affiliates' ), wp_filter_kses( $affiliate['email'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Username : %s', 'affiliates' ), wp_filter_kses( $affiliate_user_edit ) ) . '</li>' .
		'<li>' . sprintf( __( 'From : %s', 'affiliates' ), wp_filter_kses( $affiliate['from_date'] ) ) . '</li>' .
		'<li>' . sprintf( __( 'Until : %s', 'affiliates' ), wp_filter_kses( $affiliate['from_date'] ) ) . '</li>' .
		'</ul> ';
		$output .= '<hr>';
	}
	
	$output .= '<input type="hidden" name="action" value="affiliate-action"/>';
	$output .= '<input type="hidden" name="bulk-action" value="remove-affiliate"/>';
	$output .= '<input type="hidden" name="confirm" value="1"/>';
	
	$output .= wp_nonce_field( 'admin', AFFILIATES_ADMIN_AFFILIATES_ACTION_NONCE, true, false ) .
	'<input class="button button-primary" type="submit" name="bulk" value="' . __( 'Remove', 'affiliates' ) . '"/>' .
	' ' .
	'<a class="cancel button" href="' . esc_url( $current_url ) . '">' . __( 'Cancel', 'affiliates' ) . '</a>' .
	'</div>' .
	'</div>' . // .affiliate.remove
	'</form>' .
	'</div>'; // .manage-affiliates
	
	echo $output;
	
	affiliates_footer();
	
} // function affiliates_admin_affiliates_bulk_remove

/**
 * Handle remove form submission.
 * @return array of deleted affiliates' ids
 */
function affiliates_admin_affiliates_bulk_remove_submit() {
	
	global $wpdb;
	$result = false;
	
	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	if ( !wp_verify_nonce( $_POST[AFFILIATES_ADMIN_AFFILIATES_ACTION_NONCE], 'admin' ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}
	
	$affiliates_table = _affiliates_get_tablename( 'affiliates' );
	
	$affiliate_ids = isset( $_POST['affiliate_ids'] ) ? $_POST['affiliate_ids'] : null;
	if ( $affiliate_ids ) {
		foreach ( $affiliate_ids as $affiliate_id ) {
			$valid_affiliate = false;
			// do not mark the pseudo-affiliate as deleted: type != ...
			$check = $wpdb->prepare(
					"SELECT affiliate_id FROM $affiliates_table WHERE affiliate_id = %d AND (type IS NULL OR type != '" . AFFILIATES_DIRECT_TYPE . "')",
					intval( $affiliate_id ) );
			if ( $wpdb->query( $check ) ) {
				$valid_affiliate = true;
			}
			
			if ( $valid_affiliate ) {
				$result = false !== $wpdb->query(
						$query = $wpdb->prepare(
								"UPDATE $affiliates_table SET status = 'deleted' WHERE affiliate_id = %d",
								intval( $affiliate_id )
								)
						);
				do_action( 'affiliates_deleted_affiliate', intval( $affiliate_id ) );
			}
		}
	}
	
	return $result;
} // function affiliates_admin_affiliates_bulk_remove_submit
