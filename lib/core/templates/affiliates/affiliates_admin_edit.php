<?php
/**
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

$user_data = $affiliate->data;
$payment_info_fields = Affiliates_Utility::getPaymentInfoFields();
$affiliates_status_fields = Affiliates_Utility::getAffiliatesStatusFields();

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Xem/Chỉnh sửa ' . $user_data->user_login, 'affiliates' ); ?></h1>

    <hr class="wp-header-end">

    <form name="post" method="post" id="post">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div id="affiliates_submitdiv" class="postbox  cmb2-postbox">
                            <h2 class="hndle ui-sortable-handle"><span>Cập nhật</span></h2>
                            <div class="inside">
                                <div class="cmb2-wrap form-table">
                                    <div id="cmb2-metabox-affiliates_conversion_submitdiv" class="cmb2-metabox cmb-field-list">
                                        <div class="update_button wide">
                                            <button type="submit" class="button save_order button-primary" name="save" value="Cập nhật">Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="affiliates_submitdiv" class="postbox  cmb2-postbox">
                            <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Trạng thái affiliates', 'affiliates' ); ?></span></h2>
                            <div class="inside">
                                <div class="cmb2-wrap form-table">
									<fieldset>
										<legend class="screen-reader-text"><?php _e( 'Trạng thái affiliates', 'affiliates' ); ?></legend>
                                        <?php foreach ($affiliates_status_fields as $key => $text): ?>
    										<input 
                                                type="radio" 
                                                name="affiliates_status" 
                                                class="post-format" 
                                                id="<?php echo 'afiliates-status-' . $key ?>" 
                                                value="<?php echo $key ?>" 
                                                <?php checked( $key, get_user_meta( $user_data->ID, 'affiliates_status', true ), true ); ?>
                                            />
                                            <label for="<?php echo 'afiliates-status-' . $key ?>" class="post-format-icon post-format"><?php _e( $text, 'affiliates' ); ?></label>
                                            <br>
                                        <?php endforeach ?>
									</fieldset>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>                
                <div id="postbox-container-2" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="affiliates_edit_main" class="postbox  cmb2-postbox">
                            <h2 class="hndle ui-sortable-handle">
                            	<span><?php _e( 'Thông tin affiliates', 'affiliates' ); ?></span>
                            </h2>
                            <div class="inside">
                                <div class="cmb2-wrap form-table">
                                    <div id="cmb2-metabox-affiliates_edit_main" class="cmb2-metabox cmb-field-list">
                                        <div id="main-content">
                                            <table class="table widefat">
                                                <tbody>
                                                    <tr>
                                                        <td><?php _e( 'Mã giới thiệu', 'affiliates' ); ?>: </td>
                                                        <td>
                                                        	<input class="widefat" type="text" readonly value="<?php echo Affiliates_Utility::getAffCodeByUserId($user_data->ID); ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e( 'Link giới thiệu', 'affiliates' ); ?>: </td>
                                                        <td>
                                                        	<input class="widefat" type="text" readonly value="<?php echo affiliates_get_affiliate_url( get_bloginfo('url'), $user_data->ID ) ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e( 'Người giới thiệu (Referal)', 'affiliates' ); ?>: </td>
                                                        <td>
                                                            <?php 
                                                                $affiliates_referal = Affiliates_Utility::getAffiliatesReferalByUserId($user_data->ID);
                                                            ?>
                                                            <input class="widefat" type="text" readonly value="<?php echo $affiliates_referal->user_login; ?>">
                                                        </td>
                                                    </tr>                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- End CMB2 Fields -->
                            </div>
                        </div>
                        <div id="affiliates_edit_payment" class="postbox  cmb2-postbox">
                            <h2 class="hndle ui-sortable-handle">
                            	<span><?php _e( 'Thông tin thanh toán', 'affiliates' ); ?></span>
                            </h2>
                            <div class="inside">
                                <div class="cmb2-wrap form-table">
                                    <div id="cmb2-metabox-affiliates_edit_main" class="cmb2-metabox cmb-field-list">
                                        <div id="main-content">
                                            <table class="table widefat">
                                                <tbody>
													<?php foreach ($payment_info_fields as $id => $text): ?>
	                                                    <tr>
	                                                        <td><?php _e( $text, 'affiliates' ); ?>: </td>
	                                                        <td>
													            <input 
													            	type="text" 
													            	class="widefat" 
													            	name="payment[<?php echo $id ?>]" 
													            	id="<?php echo $id ?>" 
													            	placeholder="<?php echo $text ?>" 
													            	value="<?php echo get_user_meta( $user_data->ID, $id, true ); ?>"
												            	/>		                                                        	
	                                                        </td>
	                                                    </tr>
												    <?php endforeach ?>                                                	
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- End CMB2 Fields -->
                            </div>
                        </div>
                        <div id="affiliates_referal_tree" class="postbox  cmb2-postbox">
                            <h2 class="hndle ui-sortable-handle">
                                <span><?php _e( 'Cây hoa hồng', 'affiliates' ); ?></span>
                            </h2>
                            <div class="inside">
                                <div class="cmb2-wrap form-table">
                                    <div id="cmb2-metabox-affiliates_edit_main" class="cmb2-metabox cmb-field-list">
                                        <div id="main-content-referal-tree">
                                            <div id="cay-hoa-hong">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End CMB2 Fields -->
                            </div>
                            <script type="text/javascript">
                                // var ReferralTreeConfig = {};
                                var nodeStructure = <?php echo json_encode(Affiliates_Utility::getRefTreeNodeStruct($user_data->ID)); ?>;
                                var ReferralTreeConfig = {
                                    chart: {
                                        container: "#cay-hoa-hong",
                                        connectors: {
                                            type: 'step'
                                        },
                                        node: {
                                            HTMLclass: 'ref-node'
                                        }               
                                    },
                                    nodeStructure: nodeStructure
                                };


                            </script>
                        </div>                         
                    </div>
                </div>
               
            </div>
            <!-- /post-body -->
            <br class="clear">
        </div>
        <!-- /poststuff -->
        <?php wp_nonce_field( 'affiliates-edit', AFFILIATES_ADMIN_AFFILIATES_NONCE ); ?>
    </form>
</div>