<?php

/**
 * Fired during plugin activation
 *
 * @link       http://frontsideapps.com
 * @since      1.0.0
 *
 * @package    Agendas
 * @subpackage Agendas/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Agendas
 * @subpackage Agendas/includes
 * @author     paul lindquist <paul.lindquist@gmail.com>
 */
class Agendas_Activator {

	/**
	 * Create agenda table
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'agendas';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			agenda_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			agenda smallint(5) NOT NULL,
			minutes smallint(5) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

}
