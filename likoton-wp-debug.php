<?php
/**
 * Plugin Name: LiKoToN WP Debug
  * Plugin URI: https://likoton.pl
 * Description: Collects and displays WordPress and PHP debug logs with filters, live view and dark mode
 * Author: Likoton
 * Version: 1.0.1
 * Text Domain: likoton-wp-debug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LWD_VERSION', '1.0.0' );
define( 'LWD_TEXTDOMAIN', 'likoton-wp-debug' );
define( 'LWD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LWD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LWD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once LWD_PLUGIN_DIR . 'includes/class-lwd-installer.php';
require_once LWD_PLUGIN_DIR . 'includes/class-lwd-logger.php';
require_once LWD_PLUGIN_DIR . 'includes/class-lwd-admin.php';
require_once LWD_PLUGIN_DIR . 'includes/class-lwd-assets.php';

register_activation_hook( __FILE__, [ 'LWD_Installer', 'activate' ] );
register_uninstall_hook( __FILE__, [ 'LWD_Installer', 'uninstall' ] );

add_action( 'plugins_loaded', function () {
    load_plugin_textdomain(
        LWD_TEXTDOMAIN,
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );

    LWD_Logger::init();
    LWD_Admin::init();
    LWD_Assets::init();
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
    $logs_url     = admin_url( 'admin.php?page=lwd-logs' );
    $settings_url = admin_url( 'admin.php?page=lwd-settings' );

    $links[] = '<a href="' . esc_url( $logs_url ) . '">' . esc_html__( 'Logs', LWD_TEXTDOMAIN ) . '</a>';
    $links[] = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', LWD_TEXTDOMAIN ) . '</a>';

    return $links;
} );