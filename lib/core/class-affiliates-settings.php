<?php
/**
 * class-affiliates-settings.php
 * 
 * Copyright (c) 2010 - 2015 Linh Ân https://zindo.info
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
 * @since affiliates 2.8.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var string options form nonce name
 */
define( 'AFFILIATES_ADMIN_SETTINGS_NONCE', 'affiliates-admin-nonce' );

/**
 * @var string generator nonce
*/
define( 'AFFILIATES_ADMIN_SETTINGS_GEN_NONCE', 'affiliates-admin-gen-nonce' );

/**
 * Settings admin section.
 */
class Affiliates_Settings {

	static $sections = null; 

	/**
	 * Settings initialization.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Settings sections.
	 * 
	 * @return array
	 */
	public static function init_sections() {
		self::$sections = apply_filters(
			'affiliates_settings_sections',
			array(
				'general'      => __( 'Cài đặt chung', 'affiliates' ),
				// 'registration' => __( 'Registration', 'affiliates' ),
				// 'pages'        => __( 'Pages', 'affiliates' ),
				'referrals'    => __( 'Cài đặt giới thiệu', 'affiliates' )
			)
		);
	}

	/**
	 * Registers an admin_notices action.
	 */
	public static function admin_init() {
		wp_register_style( 'affiliates-admin-settings', AFFILIATES_PLUGIN_URL . 'css/affiliates_admin_settings.css' );
		wp_register_script( 'affiliates-field-choice', AFFILIATES_PLUGIN_URL . 'js/affiliates-field-choice.js', array( 'jquery' ), AFFILIATES_CORE_VERSION, true );
	}

	/**
	 * Settings admin section.
	 */
	public static function admin_settings() {
		global $wp, $wpdb, $affiliates_options, $wp_roles;

		if ( !current_user_can( AFFILIATES_ADMINISTER_OPTIONS ) ) {
			wp_die( __( 'Access denied.', 'affiliates' ) );
		}

		wp_enqueue_style( 'affiliates-admin-settings' );
		wp_enqueue_script( 'affiliates-field-choice' );

		self::init_sections();

		$section = isset( $_REQUEST['section'] ) ? $_REQUEST['section'] : null;

		if ( !key_exists( $section, self::$sections ) ) {
			$section = 'general';
		}
		$section_title = self::$sections[$section];

		echo
			'<h1>' .
			__( 'Cấu hình', 'affiliates' ) .
			'</h1>';

		$section_links = '';
		foreach( self::$sections as $sec => $title ) {
			$section_links .= sprintf(
				'<a class="section-link nav-tab %s" href="%s">%s</a>',
				$section == $sec ? 'active nav-tab-active' : '',
				esc_url( add_query_arg( 'section', $sec, admin_url( 'admin.php?page=affiliates-admin-settings' ) ) ),
				$title
			);
		}
		echo '<div class="section-links nav-tab-wrapper">';
		echo $section_links;
		echo '</div>';

		echo '<br>';

		echo
			'<h2>' .
			$section_title .
			'</h2>';

		switch( $section ) {
			case 'pages' :
				require_once AFFILIATES_CORE_LIB . '/class-affiliates-settings-pages.php';
				Affiliates_Settings_Pages::section();
				break;
			case 'referrals' :
				require_once AFFILIATES_CORE_LIB . '/class-affiliates-settings-referrals.php';
				Affiliates_Settings_Referrals::section();
				break;
			case 'registration' :
				require_once AFFILIATES_CORE_LIB . '/class-affiliates-settings-registration.php';
				Affiliates_Settings_Registration::section();
				break;
			case 'general' :
				require_once AFFILIATES_CORE_LIB . '/class-affiliates-settings-general.php';
				Affiliates_Settings_General::section();
				break;
			default :
				do_action( 'affiliates_settings_section', $section );
		}

	}

	/**
	 * Outputs a note to confirm settings have been saved.
	 */
	public static function settings_saved_notice() {
		echo '<div class="updated">';
		echo __( 'Settings saved.', 'affiliates' );
		echo '</div>';
	}

}
Affiliates_Settings::init();
