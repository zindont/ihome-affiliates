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
	'title' => esc_html__('Đăng ký affiliates', 'affiliates'),
	'title-link-text' => '',
	'title-link' => ''
));

?>

	<div id="register-affiliate" class="overflow-hidden">
		<form class="mt-1" method="POST">
			<div class="form-group">
				<p class="lead">
					<?php _e( 'Sau khi đăng ký, tài khoản của bạn sẽ trong trạng thái chờ duyệt. BQT sẽ xem xét và duyệt tài khoản của bạn trong thời gian sớm nhất. Mọi chi tiết xin vui lòng liên hệ hỗ trợ.', 'affiliates' ) ?>
				</p>
			</div>
		    <div class="form-group row">
		        <label for="aff-referal" class="col-sm-2 col-form-label"><?php _e( 'Mã giới thiệu', 'affiliates' ) ?></label>
		        <div class="col-sm-10">
		            <input type="text" class="form-control" id="aff-referal" name="affiliates_referal" placeholder="<?php _e( 'Nhập mã giới thiệu (nếu có)', 'affiliates' ) ?>" value="">
		        </div>
		    </div>			
			<div class="form-group text-center">
		    	<button type="submit" class="btn btn-primary w-50 m-auto" name="register" value="1"><?php _e( 'ĐĂNG KÝ LÀM CỘNG TÁC VIÊN', 'affiliates' ) ?></button>
		    </div>
		    <?php wp_nonce_field( 'affiliates_register', 'affiliates_register_nonce' ) ?>
		</form>
	</div>

<?php 
tourmaster_user_content_block_end();
/**
* End content block
*/