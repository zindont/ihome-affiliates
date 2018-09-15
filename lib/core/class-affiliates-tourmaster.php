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

class Affiliates_Tourmaster {

	function __construct() {
		add_filter( 'tourmaster_user_nav_list', array($this, 'affiliates_tourmaster_user_nav_list'), 10, 1 );
		add_filter( 'tourmaster_user_content_template', array($this, 'affiliates_tourmaster_user_content_template'), 10, 2 );
		add_action( 'wp_enqueue_scripts', array($this, 'affiliates_tourmaster_enqueue_scripts'), 10, 1 );
		add_action( 'wp_print_scripts', array($this, 'affiliates_tourmaster_dequeue_scripts'), 10, 1 );

		// Handle save payment info
		add_action( 'init', array($this, 'affiliates_tourmaster_save_payment_info'), 10, 1 );
	}

	public function affiliates_tourmaster_user_nav_list($nav)	{
		// change-password's offset
		$offset = array_search('change-password', array_keys($nav));
		$affiliates = array('affiliates' => array(
				'title' => __( 'Affiliates', 'affiliates' ),
				'icon' => 'icon_document_alt'
			)
		);

		$return_nav = array_slice($nav, 0, $offset, true)
        + (array) $affiliates
        + array_slice($nav, $offset, null, true);
		
		return $return_nav;
	}

	public function affiliates_tourmaster_user_content_template($template, $page_type)	{
		if ('affiliates' == $page_type) {
			global $current_user;
			
			// Check register
			if ( 
			    isset( $_POST['affiliates_register_nonce'] ) 
			    && wp_verify_nonce( $_POST['affiliates_register_nonce'], 'affiliates_register' ) 
			) {
				// Update usermeta (affiliates_status)
				update_user_meta( $current_user->ID, 'affiliates_status', 'affiliates_status_pending' );

				// Referal
				if (isset($_POST['affiliates_referal'])) {
					update_user_meta( $current_user->ID, 'affiliates_referal', $_POST['affiliates_referal'] );
				}
			}			

			// Affiliate status
			$aff_status = get_user_meta( $current_user->ID, 'affiliates_status', true );

			// Check pending delete
			if ('affiliates_status_approved' == $aff_status) {
				$user_registered = date_create($current_user->user_registered);
				$interval = date_diff($user_registered, new DateTime('now'));

				if ( (int)$interval->format('%m') >= 6 ) {
					$count_conversions = Affiliates_Utility::getTotalConversionInLastNumbersMonth($current_user->ID, get_option( 'pending_delete_month', 2 ));
					
					if ($count_conversions == 0) {
						$aff_status = 'affiliates_status_pending_delete';
						update_user_meta( $current_user->ID, 'affiliates_status', 'affiliates_status_pending_delete' );
					}
				}
			}
			
			switch ($aff_status) {
				case 'affiliates_status_pending':
					$template = AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates-pending-status.php';
					break;
				case 'affiliates_status_pending_delete':
					$template = AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates-pending-delete.php';
					break;							
				case 'affiliates_status_approved':
					$template = AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates.php';
					break;		
				default: // No register. Show register page
					$template = AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates-register-page.php';
					break;
			}
		}

		return $template;
	}

	public function affiliates_tourmaster_enqueue_scripts()	{
		if (isset($_REQUEST['page_type']) && $_REQUEST['page_type'] == 'affiliates') {
			// Scripts
			wp_enqueue_script( 'clipboard', AFFILIATES_CORE_URL . '/js/clipboard.min.js', NULL, '2.0.1', true);
			wp_enqueue_script( 'raphael', AFFILIATES_CORE_URL . '/js/raphael.min.js', NULL, '2.0.1', true);
			wp_enqueue_script( 'treant', AFFILIATES_CORE_URL . '/js/Treant.js', array('raphael'), '2.0.1', true);
			
			wp_enqueue_script( 
				'affiliates-user-page', 
				AFFILIATES_CORE_URL . '/js/affiliates-user-page.js', 
				array('traveltour-script-core', 
					'jquery', 
					'clipboard', 
					'bootstrap',
					'treant'
				), 
				NULL, 
				true
			);
			wp_enqueue_script( 'bootstrap', AFFILIATES_CORE_URL . '/js/bootstrap.bundle.min.js', array('jquery', 'traveltour-script-core'), NULL , true);

			// Styles
			wp_enqueue_style( 'bootstrap', AFFILIATES_CORE_URL . '/css/bootstrap.min.css' );
			wp_enqueue_style( 'Treant', AFFILIATES_CORE_URL . '/css/Treant.css' );
			wp_enqueue_style( 'affiliates-user-page', AFFILIATES_CORE_URL . '/css/affiliates-user-page.css' );


			
		}
	}

	public function affiliates_tourmaster_dequeue_scripts()	{
		// dequeue
		wp_dequeue_script( q2w3_fixed_widget::ID );
	}

	public function affiliates_tourmaster_save_payment_info() {
		global $current_user;
		
		$success = false;

		if ( !isset($_POST['payment']) ) {
			return;
		}

		if ( 
		    ! isset( $_POST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_payment_info_action' ) 
		) {

		   // print 'Sorry, your nonce did not verify.';
		   return;

		} else {
			foreach ($_POST['payment'] as $affiliates_user_meta => $value) {
				if (empty($value)) {
					continue;
				}
				
				$success = update_user_meta( $current_user->ID, $affiliates_user_meta, $value );
			}

			$_POST['success'] = $success;
		}
	}
}

new Affiliates_Tourmaster();
