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
class Affiliates_Conversion_Commision extends Affiliates_Conversion {
	
	/**
	* affiliate conversion commision post type
	*/	
	public $commission_post_type = 'tour';

	function __construct() {
		// Custom metabox using CMB2
		add_action( 'cmb2_admin_init', array($this, 'affiliates_conversion_commision_register_metabox') );
	}

	public function affiliates_conversion_commision_register_metabox()	{
		$prefix = 'affiliates_conversion_';

		/**
		 * metabox = submitdiv
		 */
		$cmb_commision = new_cmb2_box( array(
			'id'            => $prefix . 'commision',
			'title'         => esc_html__( 'Cấu hình hoa hồng', 'affiliates' ),
			'object_types'  => array( $this->commission_post_type ), // Post type
			'context'    => 'side',
			'priority'   => 'high',
		) );

		// Commision value
		$cmb_commision->add_field( array(
			'name'       => esc_html__( 'Hoa hồng cho CTV (Cấp 1-Trực tiếp)', 'affiliates' ),
			'id'         => $prefix . 'commision_value',
			'desc'  	 => esc_html__( 'Hoa hồng cho cộng tác viên khi giới thiệu, đơn vị tính VNĐ', 'affiliates' ),
			'type'       => 'text',
			'default'    => 0,
			'attributes' => array(
				'type' => 'number',
				'min'  => 0
			)
		) );

		// Commision value 2
		$cmb_commision->add_field( array(
			'name'       => esc_html__( 'Hoa hồng cấp 2', 'affiliates' ),
			'id'         => $prefix . 'commision_value_level_2',
			'desc'  	 => esc_html__( 'Hoa hồng cho ref cấp 2, đơn vị tính VNĐ', 'affiliates' ),
			'type'       => 'text',
			'default'    => 0,
			'attributes' => array(
				'type' => 'number',
				'min'  => 0
			)
		) );

		// Commision value 3
		$cmb_commision->add_field( array(
			'name'       => esc_html__( 'Hoa hồng cấp 3', 'affiliates' ),
			'id'         => $prefix . 'commision_value_level_3',
			'desc'  	 => esc_html__( 'Hoa hồng cho ref cấp 3, đơn vị tính VNĐ', 'affiliates' ),
			'type'       => 'text',
			'default'    => 0,
			'attributes' => array(
				'type' => 'number',
				'min'  => 0
			)
		) );		
	}
}

new Affiliates_Conversion_Commision();
