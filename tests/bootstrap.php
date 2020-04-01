<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Sample_Plugin
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Adds WooCommerce testing framework classes.
 */
function _test_includes() {
	require_once ( responsi_dir().'/functions/admin-functions.php' );
}

function responsi_dir() {
	static $dir = '';
	if ( $dir === '' ) {
		if ( file_exists( WP_CONTENT_DIR . '/responsi/functions.php' ) ) {
			$dir = WP_CONTENT_DIR . '/responsi';
			echo "Found Responsi theme in content dir." . PHP_EOL;
		} elseif ( file_exists( dirname( dirname( __DIR__ ) ) . '/responsi/functions.php' ) ) {
			$dir = dirname( dirname( __DIR__ ) ) . '/responsi';
			echo "Found Responsi theme in relative dir " . dirname( dirname( __DIR__ ) ) . '/responsi' . PHP_EOL;
		} elseif ( file_exists( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/responsi/functions.php' ) ) {
			$dir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/responsi';
			echo "Found Responsi theme in relative dir " . dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/responsi' . PHP_EOL;
		} elseif ( file_exists( '/tmp/wordpress/wp-content/themes/responsi/functions.php' ) ) {
			$dir = '/tmp/wordpress/wp-content/themes/responsi';
			echo "Found Responsi theme in tmp dir /tmp/wordpress/wp-content/themes/responsi" . PHP_EOL;
		} elseif ( file_exists( '/home/travis/build/themes/responsi/functions.php' ) ) {
			$dir = '/home/travis/build/responsi/responsi';
			echo "Found Responsi theme in home dir /home/travis/build/responsi/responsi" . PHP_EOL;
		} elseif ( file_exists( '/tmp/wordpress/wp-content/themes/home/travis/build/responsi/responsi/functions.php' ) ) {
			$dir = '/tmp/wordpress/wp-content/themes/home/travis/build/responsi/responsi';
			echo "Found Responsi theme in tmp dir /tmp/wordpress/wp-content/themes/home/travis/build/responsi/responsi" . PHP_EOL;
		} else {
			echo "Could not find Responsi theme." . PHP_EOL;
			exit( 1 );
		}
	}
	return $dir;
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {

	if( !defined( 'RESPONSI_IH_TRAVIS' ) ){
		define( 'RESPONSI_IH_TRAVIS', true );
	}

	echo esc_html( 'Loading Responsi theme' . PHP_EOL );
	require_once responsi_dir() . '/functions.php';
	require_once ( responsi_dir().'/functions/admin-functions.php' );

	echo esc_html( 'Loading addons' . PHP_EOL );
	require dirname( dirname( __FILE__ ) ) . '/responsi-ih.php';
	update_option('a3rev_responsi_header_extender_version', RESPONSI_IH_VERSION );
    update_option('responsi_header_extender_installed', true);
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function _manual_install_data() {

	if( !defined( 'RESPONSI_IH_TRAVIS' ) ){
		define( 'RESPONSI_IH_TRAVIS', true );
	}

	echo esc_html( 'Installing My Plugin Data ...' . PHP_EOL );
	responsi_header_extender_upgrade_version();

	define( 'WP_UNINSTALL_PLUGIN', true );

	// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
	$GLOBALS['wp_roles'] = null; // WPCS: override ok.
	wp_roles();
}
tests_add_filter( 'setup_theme', '_manual_install_data' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

_test_includes();
