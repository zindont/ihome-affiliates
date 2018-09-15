<?php
/**
 * affiliates-admin.php
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
 * @since affiliates 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiliates overview and summarized statistics.
 */
function affiliates_admin() {
	// exit('DASHBOARD - TONG QUAN');
	global $wpdb, $affiliates_options;

	if ( !current_user_can( AFFILIATES_ACCESS_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', 'affiliates' ) );
	}

	echo "<div class='wrap'>";
	include( AFFILIATES_CORE_LIB . '/templates/affiliates/affiliates_admin_dashboard.php' );

	affiliates_footer();
	echo "</div>";
}

function affiliates_admin_dashboard_styles() {
	global $affiliates_version;
	wp_register_style( 'affiliates-admin-bootstrap', AFFILIATES_PLUGIN_URL . 'css/affiliates-admin-bootstrap.css', array(), $affiliates_version );
	wp_enqueue_style( 'affiliates-admin-bootstrap' );

	// sb-admin
	wp_register_style( 'affiliates-sb-admin-2', AFFILIATES_PLUGIN_URL . 'css/sb-admin-2.min.css', array(), $affiliates_version );
	wp_enqueue_style( 'affiliates-sb-admin-2' );

	// Font Awesome
	wp_register_style( 'affiliates-fa-font', AFFILIATES_PLUGIN_URL . 'css/font-awesome.min.css', array(), $affiliates_version );
	wp_enqueue_style( 'affiliates-fa-font' );

	// Morris Chart
	wp_register_style( 'affiliates-morris', AFFILIATES_PLUGIN_URL . 'css/morris.css', array(), $affiliates_version );
	wp_enqueue_style( 'affiliates-morris' );

}
add_action( 'admin_print_styles-toplevel_page_affiliates-admin', 'affiliates_admin_dashboard_styles', 10, 1 );

function affiliates_admin_dashboard_scripts() {
	global $affiliates_version;
	// Raphael (morris lib)
	wp_register_script( 'affiliates-raphael', AFFILIATES_PLUGIN_URL . 'js/raphael.min.js', array('jquery') );
	wp_enqueue_script( 'affiliates-raphael' );

	// Moris
	wp_register_script( 'affiliates-morris', AFFILIATES_PLUGIN_URL . 'js/morris.min.js', array('jquery', 'affiliates-raphael') );
	wp_enqueue_script( 'affiliates-morris' );

	// Admin dashboard
	wp_register_script( 'affiliates-admin-dashboard', AFFILIATES_PLUGIN_URL . 'js/affiliates-admin-dashboard.js', array('jquery', 'affiliates-morris'), $affiliates_version );
	wp_enqueue_script( 'affiliates-admin-dashboard' );
}
add_action( 'admin_print_scripts-toplevel_page_affiliates-admin', 'affiliates_admin_dashboard_scripts', 10, 1 );
