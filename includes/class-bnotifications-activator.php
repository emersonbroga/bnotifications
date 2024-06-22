<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BNotifications
 * @subpackage BNotifications/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    BNotifications
 * @subpackage BNotifications/includes
 * @author     Emerson Broga <emerson@emersonbroga.com>
 */
class BNotifications_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::createDatabaseTable();

	}

	public static function createDatabaseTable() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . BNotifications::TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(9) NOT NULL AUTO_INCREMENT,
			endpoint VARCHAR(256) NOT NULL DEFAULT '',
			p256dh VARCHAR(256) NOT NULL DEFAULT '',
			auth VARCHAR(256) NOT NULL DEFAULT '',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
  		updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			CONSTRAINT unique_auth UNIQUE (auth)
		) $charset_collate;";

		
		dbDelta( $sql );
	}

}
