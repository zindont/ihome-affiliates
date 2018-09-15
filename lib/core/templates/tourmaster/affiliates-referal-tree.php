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
* Start block cay hoa hong
*/
tourmaster_user_content_block_start(array(
	'title' => esc_html__('Cây hoa hồng', 'affiliates'),
	'title-link-text' => '',
	'title-link' => ''
));


?>

	<div id="cay-hoa-hong" class="overflow-hidden">

	</div>

	<script type="text/javascript">
		// var ReferralTreeConfig = {};
		var nodeStructure = <?php echo json_encode(Affiliates_Utility::getRefTreeNodeStruct($current_user->ID)); ?>;
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

<?php 
tourmaster_user_content_block_end();
/**
* End cay hoa hong
*/