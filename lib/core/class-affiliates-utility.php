<?php
/**
 * class-affiliates-utility.php
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
 * Provides utility methods.
 */
class Affiliates_Utility {
		
	/**
	 * @var string captcha field id
	 */
	private static $captcha_field_id = 'lmfao';
	
	static function get_captcha_field_id() {
		return self::$captcha_field_id;
	}
		
	/**
	 * Filters mail header injection, html, ... 
	 * @param string $unfiltered_value
	 */
	static function filter( $unfiltered_value ) {
		$mail_filtered_value = preg_replace('/(%0A|%0D|content-type:|to:|cc:|bcc:)/i', '', $unfiltered_value );
		return stripslashes( wp_filter_nohtml_kses( Affiliates_Utility::filter_xss( trim( strip_tags( $mail_filtered_value ) ) ) ) );
	}
	
	/**
	 * Filter xss
	 * 
	 * @param string $string input
	 * @return filtered string
	 */
	static function filter_xss( $string ) {
		// Remove NUL characters (ignored by some browsers)
		$string = str_replace(chr(0), '', $string);
		// Remove Netscape 4 JS entities
		$string = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
		
		// Defuse all HTML entities
		$string = str_replace('&', '&amp;', $string);
		// Change back only well-formed entities in our whitelist
		// Decimal numeric entities
		$string = preg_replace('/&amp;#([0-9]+;)/', '&#\1', $string);
		// Hexadecimal numeric entities
		$string = preg_replace('/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $string);
		// Named entities
		$string = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $string);
		return preg_replace('%
		(
		<(?=[^a-zA-Z!/])  # a lone <
		|                 # or
		<[^>]*(>|$)       # a string that starts with a <, up until the > or the end of the string
		|                 # or
		>                 # just a >
		)%x', '', $string);
	}
		
	/**
	 * Returns captcha field markup.
	 * 
	 * @return captcha field markup
	 */
	static function captcha_get( $value ) {
		$style = 'display:none;';
		$field = '<input name="' . Affiliates_Utility::$captcha_field_id . '" id="' . Affiliates_Utility::$captcha_field_id . '" class="' . Affiliates_Utility::$captcha_field_id . ' field" style="' . $style . '" value="' . esc_attr( $value ) . '" type="text"/>';
		$field = apply_filters( 'affiliates_captcha_get', $field, $value );
		return $field;
	}

	/**
	 * Validates a captcha field.
	 * 
	 * @param string $field_value field content
	 * @return true if the field validates
	 */
	static function captcha_validates( $field_value = null ) {
		$result = false;
		if ( empty( $field_value ) ) {
			$result = true;
		}
		$result = apply_filters( 'affiliates_captcha_validate', $result, $field_value );
		return $result;
	}
	
	/**
	 * Retrieves the first post that contains $title.
	 * @param string $title what to search in titles for
	 * @param string $output Optional, default is Object. Either OBJECT, ARRAY_A, or ARRAY_N.
	 * @param string $post_type Optional, default is null meaning any post type.
	 */
	static function get_post_by_title( $title, $output = OBJECT, $post_type = null ) {
		global $wpdb;
		$post = null;
		if ( $post_type == null ) {
			$query = $wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%%%s%%'",
				$title
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%%%s%%' AND post_type= %s",
				$title,
				$post_type
			);
		}
		$result = $wpdb->get_row( $query );
		if ( !empty( $result ) ) {
			$post_id = $result->ID;
			$post = get_post( $post_id, $output );
		}
		return $post;
	}
	
	/**
	 * Verifies and returns formatted amount.
	 * @param string $amount
	 * @return string amount, false upon error or wrong format
	 */
	static function verify_referral_amount( $amount ) {
		$result = false;
		if ( is_numeric( $amount ) ) {
			$amount = sprintf( '%.' . ( affiliates_get_referral_amount_decimals() + 1 ) . 'F', $amount );
			if ( preg_match( "/([0-9,]+)?(\.[0-9]+)?/", $amount, $matches ) ) {
				if ( isset( $matches[1] ) ) {
					$n = str_replace(",", "", $matches[1] );
				} else {
					$n = "0";
				}
				if ( isset( $matches[2] ) ) {
					// exceeding decimals are TRUNCATED
					$d = substr( $matches[2], 1, affiliates_get_referral_amount_decimals() );
				} else {
					$d = "0";
				}
				if ( isset( $matches[1] ) || isset( $matches[2] ) ) {
					$result = $n . "." . $d;
				}
			}
		}
		return $result;
	}

	/**
	 * Verify and return currency id.
	 * @param string $currency_id
	 * @return string currency id or false on error
	 */
	static function verify_currency_id( $currency_id ) {
		if ( !empty( $currency_id ) ) {
			return substr( trim( strtoupper( $currency_id ) ), 0, AFFILIATES_REFERRAL_CURRENCY_ID_LENGTH );
		} else {
			return false;
		}
	}
	
	/**
	 * Verifies states and transition.
	 * 
	 * @param string $old_status
	 * @param string $new_status
	 * @return new status or false on failure to verify
	 */
	static function verify_referral_status_transition( $old_status, $new_status ) {
		$result = false;
		switch ( $old_status ) {
			case AFFILIATES_REFERRAL_STATUS_ACCEPTED :
			case AFFILIATES_REFERRAL_STATUS_CLOSED :
			case AFFILIATES_REFERRAL_STATUS_PENDING :
			case AFFILIATES_REFERRAL_STATUS_REJECTED :
				switch ( $new_status ) {
					case AFFILIATES_REFERRAL_STATUS_ACCEPTED :
					case AFFILIATES_REFERRAL_STATUS_CLOSED :
					case AFFILIATES_REFERRAL_STATUS_PENDING :
					case AFFILIATES_REFERRAL_STATUS_REJECTED :
						$result = $new_status;
						break; 
				}
				break;
		}
		return $result;
	}

	/**
	 * Verifies affiliate states.
	 *
	 * @param string $status
	 * @return status or false on failure to verify
	 */
	static function verify_affiliate_status( $status ) {
		$result = false;
		switch ( $status ) {
			case AFFILIATES_AFFILIATE_STATUS_ACTIVE :
			case AFFILIATES_AFFILIATE_STATUS_PENDING :
			case AFFILIATES_AFFILIATE_STATUS_DELETED :
				$result = $status;
				break;
		}
		return $result;
	}

	/**
	 * Get total price of tour transaction
	 */
	static function getTransactionDataByOrderID( $order_id ) {
		global $wpdb;
		$result = false;
		$query = "SELECT * FROM {$wpdb->prefix}tourmaster_order WHERE id = {$order_id} ";
		$result = $wpdb->get_row( $query );
		return $result;
	}

	/**
	 * Price format VND
	 */
	static function price_format( $number ) {
		$result = number_format($number, 0, ',', '.');
		$result = $result . ' VNĐ';
		return $result;
	}

	/**
	 * Get Aff code
	 */
	static function getAffCodeByUserId( $user_id ) {
		return affiliates_encode_affiliate_id( $user_id );
	}

	static function getPaymentInfoFields()	{
		$payment_info = array(
			'affiliates_bank_name' => __( 'Tên ngân hàng', 'affiliates' ),
			'affiliates_bank_branch' => __( 'Chi nhánh ngân hàng', 'affiliates' ),
			'affiliates_bank_account_name' => __( 'Tên tài khoản', 'affiliates' ),
			'affiliates_bank_account_numer' => __( 'Số tài khoản', 'affiliates' ),
		);

		return $payment_info;
	}

	static function getAffiliatesStatusFields()	{
		$statuses = array(
			'affiliates_status_pending' => __( 'Chờ duyệt', 'affiliates' ),
			'affiliates_status_approved' => __( 'Đã duyệt', 'affiliates' )
		);

		return $statuses;
	}

	static function getAffiliatesReferalByUserId($user_id)	{
        $response = new stdClass();
        $affiliates_referal_hash = get_user_meta( $user_id, 'affiliates_referal', true );

        $response->user_login = $affiliates_referal_hash;

        $affiliates_referal = get_users( array(
            'meta_query'   => array(
                array(
                    'key'     => 'affiliates_encoded_id',
                    'value'   => $affiliates_referal_hash,
                    'compare' => '='
                )                                                                        
            ),
        ) );

        $affiliates_referal = array_shift($affiliates_referal);

        if (isset($affiliates_referal->data)) {
        	$response = $affiliates_referal->data;
        }

        return $response;
	}

	static function deleteAffiliateCookies()	{
		// Aff encoded id
		$result = setcookie(
			AFFILIATES_COOKIE_NAME,
			"",
			time() - 3600,
			SITECOOKIEPATH,
			COOKIE_DOMAIN
		);

		// Hit hash
		$result = setcookie(
			AFFILIATES_HASH_COOKIE_NAME,
			"",
			time() - 3600,
			SITECOOKIEPATH,
			COOKIE_DOMAIN
		);

		return $result;
	}

	static function getTotalConversionInLastNumbersMonth($user_id, $last_n_month = 2) {
		$result = array();

		$timezone = 'Asia/Ho_Chi_Minh';
		// Setup date_before
		$date_before = new DateTime('now', new DateTimeZone($timezone));

		$date_after = new DateTime('now', new DateTimeZone($timezone));
		$date_after = $date_after->modify('-' . $last_n_month . ' months');

		$query_args = array(
			'post_type' => 'aff_conversion',
			'posts_per_page'   => -1,
			'author' => $user_id,
			'date_query' => array(
				'relation' => 'AND',
				array(
					'before'        => $date_before->format('Y-m-d 23:59:59'),
					'after'         => $date_after->format('Y-m-d 00:00:00'),
					'inclusive'     => true,
				),
			)
		);

		$aff_conversion = get_posts( $query_args );

		return count($aff_conversion);
		/*// Counting
		$total_commision = 0; // Hoa hong phat sinh
		$total_cancelled_commission = 0; // Hoa hong da huy
		$total_billing = 0; // Chuyen doi phat sinh

		array_walk($aff_conversion, function($item, $key) use (&$total_commision, &$total_cancelled_commission, &$total_billing){
			$order_id = get_post_meta( $item->ID, 'order_id', true );
			$transaction_data = self::getTransactionDataByOrderID($order_id);

			// Chuyen doi phat sinh
			if (isset($transaction_data->total_price)) {
				$total_billing += $transaction_data->total_price;
			}

			// Hoa hong phat sinh
			$commision_value = get_post_meta( $item->ID, 'commision_value', true );
			if ($commision_value) {
				$total_commision += $commision_value;
			}

			// Hoa hong da huy
			$commision_status = get_post_meta( $item->ID, 'affiliates_conversion_status', true );
			if ($commision_value && $commision_status == 'cancelled') {
				$total_cancelled_commission += $commision_value;
			}
		});
		
		return array(
			'total_commision' => $total_commision,
			'total_cancelled_commission' => $total_cancelled_commission,
			'total_billing' => $total_billing
		);*/
	}

	static function getOverviewInLastNumbersDate($last_n_date = 30)	{
		$result = array();

		$timezone = 'Asia/Ho_Chi_Minh';
		// Setup date_before
		$date_before = new DateTime('now', new DateTimeZone($timezone));

		$date_after = new DateTime('now', new DateTimeZone($timezone));
		$date_after = $date_after->modify('-' . $last_n_date . ' days');

		$query_args = array(
			'post_type' => 'aff_conversion',
			'posts_per_page'   => -1,
			'date_query' => array(
				'relation' => 'AND',
				array(
					'before'        => $date_before->format('Y-m-d 23:59:59'),
					'after'         => $date_after->format('Y-m-d 00:00:00'),
					'inclusive'     => true,
				),
			)
		);

		$aff_conversion = get_posts( $query_args );
		// var_dump($aff_conversion);
		// Counting
		$total_commision = 0; // Hoa hong phat sinh
		$total_cancelled_commission = 0; // Hoa hong da huy
		$total_billing = 0; // Chuyen doi phat sinh

		array_walk($aff_conversion, function($item, $key) use (&$total_commision, &$total_cancelled_commission, &$total_billing){
			$order_id = get_post_meta( $item->ID, 'order_id', true );
			$transaction_data = self::getTransactionDataByOrderID($order_id);

			// Chuyen doi phat sinh
			if (isset($transaction_data->total_price)) {
				$total_billing += $transaction_data->total_price;
			}

			// Hoa hong phat sinh
			$commision_value = get_post_meta( $item->ID, 'commision_value', true );
			if ($commision_value) {
				$total_commision += $commision_value;
			}

			// Hoa hong da huy
			$commision_status = get_post_meta( $item->ID, 'affiliates_conversion_status', true );
			if ($commision_value && $commision_status == 'cancelled') {
				$total_cancelled_commission += $commision_value;
			}
		});
		
		return array(
			'total_commision' => $total_commision,
			'total_cancelled_commission' => $total_cancelled_commission,
			'total_billing' => $total_billing
		);
	}

	static function getChartDataInLastNumbersDate($last_n_date = 30) {
		$result = array();

		$timezone = 'Asia/Ho_Chi_Minh';
		// Setup date_before
		$date_before = new DateTime('now', new DateTimeZone($timezone));

		$date_after = new DateTime('now', new DateTimeZone($timezone));
		$date_after = $date_after->modify('-' . $last_n_date . ' days');

		$query_args = array(
			'post_type' => 'aff_conversion',
			'date_query' => array(
				'relation' => 'AND',
				array(
					'before'        => $date_before->format('Y-m-d 23:59:59'),
					'after'         => $date_after->format('Y-m-d 00:00:00'),
					'inclusive'     => true,
				),
			)
		);

		$aff_conversion = get_posts( $query_args );

		$period = new DatePeriod(
		     $date_after,
		     new DateInterval('P1D'),
		     $date_before->modify('+1 day')
		);

		foreach ($period as $key => $date) {
			$transacion_total = self::getTotalTransactionByDate($date);

		    $date_data = new stdClass();
		    $date_data->period = $date->getTimestamp();
			
			$date_data->total_billing = $transacion_total->total_billing; // Chuyen doi phat sinh
			$date_data->total_commision = $transacion_total->total_commision; // Hoa hong phat sinh
			$date_data->total_cancelled_commission = $transacion_total->total_cancelled_commission; // Hoa hong da huy
		    
		    $result[] = $date_data;
		}

		return json_encode($result);
	}

	static function getTotalTransactionByDate($date = false)	{
		if (!$date) {
			$date = new DateTime('now');
		}

		$transacion_total = new stdClass();

		$transacion_total->total_billing = 0; // Chuyen doi phat sinh
		$transacion_total->total_commision = 0; // Hoa hong phat sinh
		$transacion_total->total_cancelled_commission = 0; // Hoa hong da huy

		$query_args = array(
			'post_type' => 'aff_conversion',
			'date_query' => array(
				'year' => $date->format('Y'),
				'month' => $date->format('m'),
				'day' => $date->format('d'),
			),
			'posts_per_page' => -1
		);

		$aff_conversion = get_posts( $query_args );

		if (!empty($aff_conversion)) {
			foreach ($aff_conversion as $key => $transaction) {
				$order_id = get_post_meta( $transaction->ID, 'order_id', true );
				$transaction_data = self::getTransactionDataByOrderID($order_id);
				// Get total billing
				$transacion_total->total_billing += $transaction_data->total_price;

				$commision_value = get_post_meta( $transaction->ID, 'commision_value', true ) ?: 0;
				// Get total comision
				$transacion_total->total_commision += $commision_value;

				// Get total canceled
				if (get_post_meta( $transaction->ID, 'affiliates_conversion_status', true ) == 'cancelled') {
					$transacion_total->total_cancelled_commission += $commision_value;
				}
			}
		}

		return $transacion_total;
	}

	static function getRefTreeNodeStruct($user_id, $max_depth = 3)	{
		$user_data = get_userdata($user_id);
		$struct_arr = array();

		/* Get the root */
			$node = array(
				'name' => $user_data->user_login ?: __( 'Referral', 'affiliates' )
			);

			$struct_arr['text'] = $node;
			$struct_arr['image'] = AFFILIATES_CORE_URL . '/images/people.png';
			// $struct_arr['stackChildren'] = 'true';
		/* End get the root */

		/* Get the children */
			$children_args = array (
			    'order' => 'ASC',
			    'orderby' => 'ID',
			    'fields' => 'ID',
			    'posts_per_page' => -1,
			    'meta_query' => array(
			        array(
			            'key'     => 'affiliates_referal',
			            'value'   => Affiliates_Utility::getAffCodeByUserId($user_id),
			            'compare' => '='
			        )
			    )
			);

			$childrens_query = new WP_User_Query($children_args);
			$childrens = $childrens_query->get_results();
		/* End get the children */
	 	$max_depth--;

	 	if ($max_depth > 0) {
	 		foreach ($childrens as $key => $wp_user_id) {
	 			$struct_arr['children'][] = self::getRefTreeNodeStruct($wp_user_id, $max_depth);
	 		}
	 	}		
	 	return $struct_arr;
	}
}// class Affiliates_Utility
