<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LWD_Installer {

    const TABLE_NAME = 'lwd_logs';

    public static function activate() {
        global $wpdb;

        $table   = $wpdb->prefix . self::TABLE_NAME;
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            level VARCHAR(50) NOT NULL,
            source VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            context LONGTEXT NULL,
            ip VARCHAR(45) NULL,
            user_id BIGINT UNSIGNED NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY level (level),
            KEY source (source),
            KEY created_at (created_at)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function uninstall() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_NAME;
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
    }
	
	public static function get_unique_sources() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_col( "SELECT DISTINCT source FROM $table ORDER BY source ASC" );
	}

    public static function get_logs( $args = [] ) {
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE_NAME;

        $defaults = [
            'search' => '',
            'last'   => 50,
            'level'  => '',
        ];
        $args = wp_parse_args( $args, $defaults );

        $where   = '1=1';
        $params  = [];

        if ( $args['search'] !== '' ) {
            $where   .= ' AND message LIKE %s';
            $params[] = '%' . $wpdb->esc_like( $args['search'] ) . '%';
        }

        if ( $args['level'] !== '' ) {
            $where   .= ' AND level = %s';
            $params[] = $args['level'];
        }
		
		if ( $args['source'] !== '' ) {
			$where   .= ' AND source = %s';
			$params[] = $args['source'];
		}


		if ( $args['last'] === 'all' ) {
			$limit = null;
		} else {
			$limit = (int) $args['last'];
			if ( $limit <= 0 ) {
				$limit = 50;
			}
		}

		if ( $limit === null ) {
			// Wszystkie logi
			$sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC";
			$prepared = $wpdb->prepare( $sql, $params );
		} else {
			// Ograniczona liczba logów
			$sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d";
			$params[] = $limit;
			$prepared = $wpdb->prepare( $sql, $params );
		}

        $prepared = $wpdb->prepare( $sql, $params );

        return $wpdb->get_results( $prepared );
    }
}
