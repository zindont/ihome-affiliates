<?php
/**
 * affiliates-admin-affiliates-edit.php
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
 * Show edit affiliate form.
 * @param int $affiliate_id affiliate id
 */
function affiliates_admin_affiliates_edit( $affiliate_id ) {

	global $wpdb;

	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}

	$affiliate = get_user_by( 'id', $affiliate_id );

	if ( empty( $affiliate ) ) {
		wp_die( __( 'No such affiliate.', 'affiliates' ) );
	}

	if ( isset($_POST[AFFILIATES_ADMIN_AFFILIATES_NONCE]) && 
		wp_verify_nonce( $_POST[AFFILIATES_ADMIN_AFFILIATES_NONCE], 'affiliates-edit' )
	) {
		$success = affiliates_admin_affiliates_edit_submit();
	
		if ($success) {
			$class = 'notice notice-success ';
			$message = __( 'Cập nhật thành công.', 'affiliates' );
		} else {
			$class = 'notice notice-error';
			$message = __( 'Cập nhật không thành công.', 'affiliates' );
		}

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 

	}
	include( AFFILIATES_CORE_LIB . '/templates/affiliates/affiliates_admin_edit.php' );

	affiliates_footer();
} // function affiliates_admin_affiliates_edit

/**
 * Handle edit form submission.
 * @return int error_value:
 * 		AFFILIATES_ADMIN_AFFILIATES_NO_ERROR  -- No errors
 * 		AFFILIATES_ADMIN_AFFILIATES_ERROR_NAME_EMPTY
 * 		AFFILIATES_ADMIN_AFFILIATES_ERROR_USERNAME
 */
function affiliates_admin_affiliates_edit_submit() {

	if (!isset($_POST['save']) || !isset($_REQUEST['user_id'])) {
		return;
	}
	
	$success = true;

	$user_id = $_REQUEST['user_id'];
	// Update Payment info
	if ( isset($_POST['payment']) && is_array($_POST['payment']) ) {
		foreach ($_POST['payment'] as $meta_key => $meta_value) {
			if ( $meta_value == get_user_meta( $user_id,  $meta_key, true ) ) {
			    continue;
			}

			$success = update_user_meta( $user_id, $meta_key, $meta_value );
		}
	}

	// Update affiliates status
	if ( isset($_POST['affiliates_status']) ) {
		if ( $_POST['affiliates_status'] != get_user_meta( $user_id,  'affiliates_status', true ) ) {
    		$success = update_user_meta( $user_id, 'affiliates_status', $_POST['affiliates_status'] );
		}
	}

	return $success;

} // function affiliates_admin_affiliates_edit_submit