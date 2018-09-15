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

$aff_code = Affiliates_Utility::getAffCodeByUserId($current_user->ID);

$notification = false;
if ( isset($_POST['success']) && $_POST['success'] ) {
	$notification = esc_html__('Cập nhật thành công.', 'affiliates');
}

if ($notification) {
	tourmaster_user_update_notification($notification);
}

?>

<div id="affiliates-user-page" class="m-4">
	<nav>
		<div class="nav nav-tabs" id="affiliates-nav-tab" role="tablist">
			<a class="nav-item nav-link active" id="nav-general" data-toggle="tab" href="#general" role="tab" aria-controls="nav-general" aria-selected="true">
				<?php _e( 'Thông tin chung', 'affiliates' ) ?>
			</a>
			<a class="nav-item nav-link" id="nav-get-link" data-toggle="tab" href="#get-link" role="tab" aria-controls="nav-get-link" aria-selected="false">
				<?php _e( 'Lấy link', 'affiliates' ) ?>
			</a>
			<a class="nav-item nav-link" id="nav-ref-tree" data-toggle="tab" href="#ref-tree" role="tab" aria-controls="nav-ref-tree" aria-selected="false">
				<?php _e( 'Cây hoa hồng', 'affiliates' ) ?>
			</a>
		</div>
	</nav>
	<div class="tab-content border border-top-0 bg-white" id="nav-affiliates-tab-content">
		<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="nav-general">
			<?php
			/**
			* Start main content block
			*/
			tourmaster_user_content_block_start(array(
				'title' => esc_html__('Affiliates', 'affiliates'),
				'title-link-text' => '',
				'title-link' => ''
			));
			?>
				<div id="affiliates-manage" class="overflow-hidden">
					<form class="mt-1">
					    <div class="form-group row">
					        <label for="aff-code" class="col-sm-2 col-form-label"><?php _e( 'Mã giới thiệu', 'affiliates' ) ?></label>
					        <div class="col-sm-10">
					            <input disabled="disabled" type="text" class="form-control" id="aff-code" placeholder="<?php _e( 'Mã giới thiệu', 'affiliates' ) ?>" value="<?php echo $aff_code ?>">
					        </div>
					    </div>
					    <div class="form-group row">
					        <label for="aff-link" class="col-sm-2 col-form-label"><?php _e( 'Link giới thiệu', 'affiliates' ) ?></label>
					        <div class="col-sm-10">
					            <input disabled="disabled" type="url" class="form-control" id="aff-link" placeholder="<?php _e( 'Link giới thiệu', 'affiliates' ) ?>" value="<?php echo affiliates_get_affiliate_url( get_bloginfo('url'), $current_user->ID ) ?>">
					        </div>
					    </div>
						<?php 
							$affiliates_referal = Affiliates_Utility::getAffiliatesReferalByUserId($current_user->ID);
						?>
						<?php if ($affiliates_referal->user_login) : ?>						
							<div class="form-group row">
								<label for="affiliates-referal" class="col-sm-2 col-form-label"><?php _e( 'Người giới thiệu (referal)', 'affiliates' ) ?></label>
								<div class="col-sm-10">
									<input disabled="disabled" type="url" class="form-control" id="aff-link" placeholder="<?php _e( 'Người giới thiệu (referal)', 'affiliates' ) ?>" value="<?php echo $affiliates_referal->user_login ?>">	
								</div>
							</div>
						<?php endif; ?>	
					</form>
				</div>

			<?php 
			tourmaster_user_content_block_end();
			/**
			* End main content block
			*/

			/**
			* Start block payment info
			*/
			tourmaster_user_content_block_start(array(
				'title' => esc_html__('Thông tin thanh toán', 'affiliates'),
				'title-link-text' => '',
				'title-link' => ''
			));

			$payment_info = Affiliates_Utility::getPaymentInfoFields();
			?>

				<div id="payment" class="overflow-hidden">
					<form class="mt-1" method="POST">
						<?php foreach ($payment_info as $id => $text): ?>
						    <div class="form-group row">
						        <label for="<?php echo $id ?>" class="col-sm-2 col-form-label"><?php echo $text ?></label>
						        <div class="col-sm-10">
						            <input 
						            	type="text" 
						            	class="form-control" 
						            	name="payment[<?php echo $id ?>]" 
						            	id="<?php echo $id ?>" 
						            	placeholder="<?php echo $text ?>" 
						            	value="<?php echo get_user_meta( $current_user->ID, $id, true ); ?>"
					            	/>
						        </div>
						    </div>
					    <?php endforeach ?>
				     	<?php wp_nonce_field( 'save_payment_info_action' ); ?>
						<div class="form-group row">
							<div class="col-sm-10">
								<button type="submit" class="btn btn-primary"><?php _e( 'Lưu', 'affiliates' ); ?></button>
							</div>
						</div>		    
					</form>
				</div>

			<?php 
			tourmaster_user_content_block_end();
			/**
			* End content payment info block
			*/
			?>

		</div>
		<div class="tab-pane fade" id="get-link" role="tabpanel" aria-labelledby="nav-get-link">
			<?php
				// Get aff link block
				require AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates-get-aff-link.php';
			?>
		</div>
		<div class="tab-pane fade" id="ref-tree" role="tabpanel" aria-labelledby="nav-ref-tree">
			<?php
				// Cay hoa hong
				require AFFILIATES_CORE_LIB . '/templates/tourmaster/affiliates-referal-tree.php';
			?>			
		</div>
	</div>
</div>