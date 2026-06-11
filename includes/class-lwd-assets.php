<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LWD_Assets {

    public static function init() {
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
    }

    public static function enqueue( $hook ) {
        if ( strpos( $hook, 'lwd-' ) === false ) {
            return;
        }

		// CSS (light)
		wp_enqueue_style(
			'lwd-admin',
			LWD_PLUGIN_URL . 'assets/admin.css',
			[],
			filemtime( LWD_PLUGIN_PATH . 'assets/admin.css' )
		);

		// CSS (dark)
		if ( get_option( 'lwd_dark_mode', 0 ) ) {
			wp_enqueue_style(
				'lwd-admin-dark',
				LWD_PLUGIN_URL . 'assets/admin-dark.css',
				[ 'lwd-admin' ],
				filemtime( LWD_PLUGIN_PATH . 'assets/admin-dark.css' )
			);
		}

		// JS
		wp_enqueue_script(
			'lwd-admin',
			LWD_PLUGIN_URL . 'assets/admin.js',
			[ 'jquery' ],
			filemtime( LWD_PLUGIN_PATH . 'assets/admin.js' ),
			true
		);
    }
}
