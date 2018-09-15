<?php
/**
 * 
 * Copyright (c) 2010-2018 Linh Ân https://zindo.info
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
 * @author Linh Ân
 * @package ihometour-affiliates
 * @since ihometour-affiliates 1.0.0
 *
 * Plugin Name: iHomeTour Affiliates
 * Plugin URI: https://ihometour.vn/
 * Description: Affiliate plugin cho iHomeTour
 * Version: 1.0.4
 * Author: Linh Ân
 * Author URI: https://zindo.info
 * Text Domain: affiliates
 * Domain Path: /lib/core/languages
 * License: GPLv3
 */
if ( !defined( 'AFFILIATES_CORE_VERSION' ) ) {
	define( 'AFFILIATES_CORE_VERSION', '1.0.4.4' );
	define( 'AFFILIATES_PLUGIN_NAME', 'ihometour-affiliates' );
	define( 'AFFILIATES_FILE', __FILE__ );
	define( 'AFFILIATES_PLUGIN_BASENAME', plugin_basename( AFFILIATES_FILE ) );
	if ( !defined( 'AFFILIATES_CORE_DIR' ) ) {
		define( 'AFFILIATES_CORE_DIR', WP_PLUGIN_DIR . '/ihometour-affiliates' );
	}
	if ( !defined( 'AFFILIATES_CORE_LIB' ) ) {
		define( 'AFFILIATES_CORE_LIB', AFFILIATES_CORE_DIR . '/lib/core' );
	}
	if ( !defined( 'AFFILIATES_EXTERNAL_LIB' ) ) {
		define( 'AFFILIATES_EXTERNAL_LIB', AFFILIATES_CORE_DIR . '/lib/external' );
	}	
	if ( !defined( 'AFFILIATES_CORE_URL' ) ) {
		define( 'AFFILIATES_CORE_URL', WP_PLUGIN_URL . '/ihometour-affiliates' );
	}

	/**
	* Include CMB2
	*/
	if ( file_exists( AFFILIATES_EXTERNAL_LIB . '/cmb2/init.php' ) ) {
		require_once AFFILIATES_EXTERNAL_LIB . '/cmb2/init.php';
	} elseif ( file_exists( AFFILIATES_EXTERNAL_LIB . '/CMB2/init.php' ) ) {
		require_once AFFILIATES_EXTERNAL_LIB . '/CMB2/init.php';
	}

	/**
	 * Plugin Update Checker
	 * @author info@zindo.info
	 * 
	 */
	require_once AFFILIATES_EXTERNAL_LIB . '/plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/zindont/ihometour-affiliates',
		__FILE__,
		'ihometour-affiliates'
	);


	//Optional: Set the branch that contains the stable release.
	$myUpdateChecker->setBranch('master');

	require_once( AFFILIATES_CORE_LIB . '/constants.php' );
	require_once( AFFILIATES_CORE_LIB . '/wp-init.php');
}

/**
 * Load this plugin first
 * @author info@zindo.info
 * 
 */
function affiliates_load_first() {
    $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
    if ( $plugins = get_option( 'active_plugins' ) ) {
        if ( $key = array_search( $path, $plugins ) ) {
            array_splice( $plugins, $key, 1 );
            array_unshift( $plugins, $path );
            update_option( 'active_plugins', $plugins );
        }
    }
}
add_action("activated_plugin", "affiliates_load_first");