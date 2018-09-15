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

global $current_user;

/**
* Start block
*/
tourmaster_user_content_block_start(array(
	'title' => esc_html__('Tài khoản đang tạm khóa', 'affiliates'),
	'title-link-text' => '',
	'title-link' => ''
));

?>

	<div id="pending-affiliate" class="overflow-hidden">
		<div class="jumbotron">
			<div class="display-1 text-center text-danger">
				<i class="fa fa-times-circle-o" aria-hidden="true"></i>
				<h3>Tài khoản của bạn sau <?php echo get_option( 'pending_delete_month', 2 ); ?> tháng không phát sinh doanh số nên đã chuyển sang trạng thái tạm khóa.</h3>
			</div>
			<p class="text-center">Vui lòng liên hệ BQT để biết thêm thông tin chi tiết.</p>
		</div>
	</div>

<?php 
tourmaster_user_content_block_end();
/**
* End content block
*/