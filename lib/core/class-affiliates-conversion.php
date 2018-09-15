<?php
/**
 * 
 * Copyright (c) 2010 - 2013 Linh Ân https://zindo.info
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
 * Hooks/functions for conversion
 */
class Affiliates_Conversion {

	/**
	* Default commison value
	*/
	protected $default_commision = 10;
	
	/**
	* Maximum referal level
	*/
	protected $maxRefLevel = 3;

	/**
	* affiliate conversion post type
	*/	
	public $post_type = 'aff_conversion';

	function __construct() {
		// Init variable
		add_action( 'init', array($this, 'affiliateGlobalVariable'), 10, 1 );

		// Record the conversion
		add_action( 'affiliate_record_conversion', array($this, 'affiliateRecordConversion'), 10, 1 );
	}

	public function affiliateGlobalVariable()	{
		/**
		* affiliate conversion status
		*/	
		global $conversion_status;

		$conversion_status = array(
	        'pending' => __( 'Chờ duyệt', 'affiliates' ),
	        'approved'   => __( 'Đã duyệt', 'affiliates' ),
	        'cancelled'     => __( 'Bị hủy', 'affiliates' )
	    );

	}

	public function affiliateRecordConversion($data)	{
		// Get the affiliate by hash
		$hit_data = $this->getHitDataFromCookie();

		$affiliate_id = $hit_data->affiliate_id;
		
		if (!$affiliate_id) {
			return;
		}

		$hit_datetime = $hit_data->datetime;

		$order_id = $data['insert_id'];
		$tour_id = $data['data']['tour_id'];
		$commision = $this->getItemCommistion($tour_id);

		$record_args = array(
			'post_type' => $this->post_type,
			'post_author' => $affiliate_id,
			'post_title' => '#' . $order_id . ' - ' . get_the_title( $data['data']['tour_id'] ),
			'post_status' => 'publish',
			'meta_input' => array(
				'order_id' => $order_id,
				'tour_id' => $tour_id,
				'affiliate_id' => $affiliate_id,
				'hit_datetime' => $hit_datetime,
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'commision' => $commision,
				'commision_value' => $this->calcOrderCommision($data['data']['total_price'], $commision),
				'commision_level' => 1,
				'affiliates_conversion_status' => 'pending'
			)
		);

		$aff_record_id = wp_insert_post($record_args);

		// TODO: Viet function apply commision cho ref
		// Tầng 1: 80k trực tiếp
		// tầng 2: 10k
		// tầng 3: 5k		
		$this->affiliateReferalConversion($aff_record_id, $record_args);
		// var_dump($aff_record_id);
		// var_dump($record_args);
		// var_dump($data);
		// exit();

		// TODO: Remove hash on order complete
		Affiliates_Utility::deleteAffiliateCookies();
	}

	private function affiliateReferalConversion($origin_conversion_id, $origin_args) {
		$commision_level = get_post_meta( $origin_conversion_id, 'commision_level', true );

		switch ($commision_level) {
			case 1:
				$post_parent = $origin_conversion_id;
				break;
			case 2:
				$post_parent = wp_get_post_parent_id($origin_conversion_id);
				break;
			default:
				$post_parent = 0;
				break;
		}


		if ($commision_level < $this->maxRefLevel) {
			$tour_id = $origin_args['meta_input']['tour_id'];
			$order_id = $origin_args['meta_input']['order_id'];
			
			// Apply post parent
			$origin_args['post_parent'] = $post_parent;
			
			// Modify commision level
			$commision_level++;
			$origin_args['meta_input']['commision_level'] = $commision_level;

			// Append title
			$origin_args['post_title'] = '#' . $order_id . ' - ' . get_the_title( $tour_id ) . ' [Hoa Hồng Cấp ' . $commision_level . ']';

			// Re-calculate commision value
			$commision = $this->getItemCommistion($tour_id, $commision_level);
			$transaction_data = Affiliates_Utility::getTransactionDataByOrderID($tour_id);

			$origin_args['meta_input']['commision_value'] = $this->calcOrderCommision($transaction_data->total_price, $commision);

			// Affiliates ID
			$affiliate_refs = Affiliates_Utility::getAffiliatesReferalByUserId($origin_args['meta_input']['affiliate_id']);
			$affiliates_id = $affiliate_refs->ID ?: FALSE;

			$origin_args['meta_input']['affiliate_id'] = $affiliates_id;
			$origin_args['post_author'] = $affiliates_id;
			if ($affiliates_id) {
				$ref_record_id = wp_insert_post($origin_args);
				// Recursive affiliateReferalConversion($new_id, $$origin_args)
				$this->affiliateReferalConversion($ref_record_id, $origin_args);				
			}
		}
	}

	private function getAffiliatesReferal($affiliate_id, $ref_level = 1) {
		$result = array();
		$result_recursive = array();

		$ref_user_data = Affiliates_Utility::getAffiliatesReferalByUserId($affiliate_id);
		
		// var_dump('ref lev:' . $ref_level);
		// var_dump('ref affiliate_id:' . $affiliate_id);
		if ($ref_level < $this->maxRefLevel && $ref_user_data->ID) {
			
			$result['ref_level_' . ($ref_level + 1)] = $ref_user_data->ID;

			// Recursive to next level
			$ref_level++;
			$result_recursive  = $this->getAffiliatesReferal($ref_user_data->ID, $ref_level);
		}

		$result = array_merge($result, $result_recursive);

		return $result;
	}

	private function getHitDataFromCookie() {
		global $wpdb;
		$hit_object = false;

		if (defined('AFFILIATES_HASH_COOKIE_NAME') && isset($_COOKIE[AFFILIATES_HASH_COOKIE_NAME])) {
			$hash = $_COOKIE[AFFILIATES_HASH_COOKIE_NAME];
			$query = "SELECT * FROM {$wpdb->prefix}aff_hits WHERE hash = '{$hash}'";
			$hit_object = $wpdb->get_row( $query );
		}

		return $hit_object;
	}

	/**
	* Function to calculate the commision
	* @author info@zindo.info
	* @param int $total_price
	* @param int $commision
	* @param bool $percent
	*/
	public function calcOrderCommision($total_price, $commision = 10, $percent = false) {
		if ($percent) {
			return $total_price * ($commision / 100);
		}
		
		return $commision;
	}

	/**
	* Function to get the commision percent from item by ID
	* @author info@zindo.info
	* @param int $id
	*/
	public function getItemCommistion($id, $level = 1)	{
		switch ($level) {
			case 2:
				$commision = (int)get_post_meta( $id, 'affiliates_conversion_commision_value_level_2', true );
				break;
			case 3:
				$commision = (int)get_post_meta( $id, 'affiliates_conversion_commision_value_level_3', true );
				break;			
			default:
				$commision = (int)get_post_meta( $id, 'affiliates_conversion_commision_value', true );
				break;
		}

		return $commision;
	}
}

new Affiliates_Conversion();
