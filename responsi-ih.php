<?php
/*
Plugin Name: Responsi Inline Header
Description: This Responsi Theme Framework plugin adds an Inline Header to the framework. That header includes Logo, Nav Bar plus 2 optional widget areas in a single row.
Version: 1.1.5
Author: a3rev Software
Author URI: http://a3rev.com/
Text Domain: responsi-ih
Domain Path: /languages
License: This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

	Responsi Inline Header. Plugin for the Responsi Framework.
	Copyright © 2011 a3THEMES

	a3THEMES
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RESPONSI_IH_PATH', dirname(__FILE__));
define( 'RESPONSI_IH_FOLDER', dirname(plugin_basename(__FILE__)) );
define( 'RESPONSI_IH_NAME', plugin_basename(__FILE__) );
define( 'RESPONSI_IH_URL', str_replace( array( 'http:','https:' ), '', untrailingslashit( plugins_url( '/', __FILE__ ) ) ) );
define( 'RESPONSI_IH_IMAGES_URL', RESPONSI_IH_URL . '/assets/images' );
define( 'RESPONSI_IH_JS_URL', RESPONSI_IH_URL . '/assets/js' );
define( 'RESPONSI_IH_CSS_URL', RESPONSI_IH_URL . '/assets/css' );

define( 'RESPONSI_IH_KEY', 'responsi_ih' );
define( 'RESPONSI_IH_VERSION', '1.1.5' );

function responsi_ih_activate_validate() {
    if ( 'responsi' !== get_template() ) {
        echo sprintf( __( 'This is a plugin for Responsi Framework, you need to install <a href="%s" target="_blank" rel="noopener">Responsi Framework</a> theme from WordPress first before can activate this.', 'responsi-ih' ), 'https://wordpress.org/themes/responsi-framework/' );
        die();
    }
    update_option('a3rev_responsi_ih_version', RESPONSI_IH_VERSION );
    update_option('responsi_ih_installed', true);
}

register_activation_hook(__FILE__,'responsi_ih_activate_validate');

if( !defined( 'RESPONSI_IH_TRAVIS' ) ){
	if ( !file_exists( get_theme_root().'/responsi/functions.php' ) ) return;
	if ( !isset( $_POST['wp_customize'] ) && get_option('template') != 'responsi' ) return;
	if ( isset( $_POST['wp_customize'] ) && $_POST['wp_customize'] == 'on' && isset( $_POST['theme'] ) && stristr( $_POST['theme'], 'responsi' ) === FALSE ) return;
	if ( version_compare(get_option('responsi_framework_version'), '6.9.5', '<') ) return;
}

if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
	require __DIR__ . '/vendor/autoload.php';
	global $responsi_ih_admin, $responsi_ih;
	$responsi_ih_admin = new \A3Rev\RIH\Admin();
	$responsi_ih = new \A3Rev\RIH\Main();
	new \A3Rev\RIH\Customizer();
} else {
	return;
}

add_action( 'after_setup_theme', 'responsi_ih_upgrade_version' );
function responsi_ih_upgrade_version() {

	if ( version_compare(get_option('a3rev_responsi_ih_version'), '1.1.5') === -1 ) {
		global $responsi_ih;
        $responsi_ih->build_css_after_updated();
	}

	update_option('a3rev_responsi_ih_version', RESPONSI_IH_VERSION );
}

include ( 'upgrade/plugin_upgrade.php' );
include ( 'admin/responsi-ih-init.php' );
include ( 'classes/responsi-ih-frontend.php' );
?>
