<?php
/**
 * 
 * Copyright (c) 2018 Linh Ân https://zindo.info
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

global $post;
// Data for display
$order_id = get_post_meta( get_the_ID(), 'order_id', true );
$transaction_data = Affiliates_Utility::getTransactionDataByOrderID($order_id);

$conversion_data = array(
	'tour_id' => get_post_meta( get_the_ID(), 'tour_id', true ),
	'total_price' => Affiliates_Utility::price_format($transaction_data->total_price),
	'affiliate_id' => $post->post_author,
	'commision_value' => Affiliates_Utility::price_format(get_post_meta( get_the_ID(), 'commision_value', true )),
	'order_date' => $transaction_data->booking_date,
	'hit_date' => get_post_meta( get_the_ID(), 'hit_datetime', true )
);

?>

<div id="main-content">
	<h1 class="title">
		<?php the_title(); ?>
	</h1>
	<hr>
	<table class="table">
        <tbody>
            <tr>
                <td><?php _e( 'Sản phẩm: ', 'affiliates' ); ?></td>
                <td><a href="<?php echo get_permalink($conversion_data['tour_id']); ?>"><?php echo get_the_title( $conversion_data['tour_id'] ); ?></a></td>
            </tr>
            <tr>
                <td><?php _e( 'Cộng tác viên: ', 'affiliates' ); ?></td>
                <td><strong><?php echo the_author_meta('user_login', $conversion_data['affiliate_id']); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e( 'Ngày phát sinh click: ', 'affiliates' ); ?></td>
                <td><?php echo $conversion_data['hit_date']; ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Ngày đặt hàng: ', 'affiliates' ); ?></td>
                <td><?php echo $conversion_data['order_date']; ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Giá trị đơn hàng: ', 'affiliates' ); ?></td>
                <td><?php echo $conversion_data['total_price']; ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Hoa hồng: ', 'affiliates' ); ?></td>
                <td><?php echo $conversion_data['commision_value']; ?></td>
            </tr>
        </tbody>
    </table>	
</div>