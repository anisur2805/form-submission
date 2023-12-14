<?php

namespace AFS\Form_Submission;

/**
 * Installer class
 */
class Installer {
	public function run() {
		$this->add_version();
		$this->create_tables();
	}

	public function add_version() {
		$installed = get_option( 'afs_installed' );
		if ( ! $installed ) {
			update_option( 'afs_installed', time() );
		}

		update_option( 'afs_version', AFS_VERSION );
	}

	public function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}afs_form`(
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            amount int(10) NOT NULL,
            buyer varchar(255) NOT NULL,
            receipt_id varchar(20) NOT NULL,
            items varchar(255) NOT NULL,
            buyer_email varchar(50) NOT NULL,
            buyer_ip varchar(20) DEFAULT NULL,
            note text NOT NULL,
            city varchar(20) NOT NULL,
            phone varchar(20) NOT NULL,
            hash_key varchar(255) DEFAULT NULL,
            entry_at date DEFAULT NULL,
            entry_by int(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate";

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		dbDelta( $schema );
	}
}
