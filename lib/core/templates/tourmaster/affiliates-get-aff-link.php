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
* Start block payment info
*/
tourmaster_user_content_block_start(array(
	'title' => esc_html__('Lấy link', 'affiliates'),
	'title-link-text' => '',
	'title-link' => ''
));

$tour_query_args = array(
	'post_type' => 'tour',
	'post_status' => 'publish',
	'posts_per_page' => -1,
);
$tour_query = new WP_Query( $tour_query_args );
?>

	<div id="get-aff-link" class="overflow-hidden">
		<table class="table">
		    <thead>
		        <tr>
		            <th scope="col">Ảnh</th>
		            <th scope="col">Tên tour</th>
		            <th scope="col">Giá bán</th>
		        </tr>
		    </thead>
		    <tbody>
		    	<?php if ( $tour_query->have_posts() ): ?>
					<?php while ( $tour_query->have_posts() ) : $tour_query->the_post(); ?>
				        <tr>
				            <td rowspan="2">
				            	<?php 
				            		if ( has_post_thumbnail() ) {
				            			the_post_thumbnail('thumbnail');
				            		}
				            	?>
			            	</td>
				            <td class="text-left">
				            	<strong><a target="_blank" href="<?php echo get_the_permalink(); ?>"><?php the_title() ?></a></strong>
				            </td>
				            <td><?php echo Affiliates_Utility::price_format( get_post_meta( get_the_ID(), 'tourmaster-tour-price', true ) ) ?></td>
				        </tr>
				        <tr>
				        	<?php 
				        		$tour_aff_link = get_the_permalink();
				        		$tour_aff_link = affiliates_get_affiliate_url($tour_aff_link, $current_user->ID);
				        		$input_id = 'aff-link-' . get_the_ID();
				        	?>
				        	<td colspan="2" class="align-middle">
							    <div class="form-group row mb-0">
							        <label class="col-sm-2 col-form-label"><?php _e( 'Link giới thiệu', 'affiliate' ); ?></label>
							        <div class="col-sm-10">
										<div class="input-group">
											<input 
												id="<?php echo $input_id ?>"
												readonly
												type="text" 
												class="form-control" 
												placeholder="<?php _e( 'Link giới thiệu', 'affiliate' ); ?>" 
												aria-label="<?php _e( 'Link giới thiệu', 'affiliate' ); ?>"
												value="<?php echo $tour_aff_link ?>"
											/>

											<div class="input-group-append">
												<button 
													data-clipboard-target="<?php echo '#' . $input_id; ?>"
													class="btn btn-primary" 
													type="button" >
												<?php _e( 'Copy', 'affiliate' ); ?>
												</button>
											</div>
										</div>							            
							        </div>
							    </div>
				        	</td>
				        </tr>
			        <?php endwhile; ?>
			        <?php wp_reset_postdata() ?>
		        <?php endif ?>
		    </tbody>
		</table>		
	</div>

<?php 
tourmaster_user_content_block_end();
/**
* End content payment info block
*/