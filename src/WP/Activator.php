<?php
namespace Piggly\WC\Pix\WP;

use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin behavior when activated.
 * 
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\WP
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Activator
{
	/**
	 * All function to run when activate plugin.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function activate ()
	{
		if ( !WP::is_pure_admin() )
		{ return; }

		self::create_database();
		self::setup_welcome();
	}

	/**
	 * Create new database when activating plugin.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public static function create_database ()
	{
		global $wpdb;

		$ivl_db_version = \WC_PIGGLY_PIX_DB_VERSION;
		$ins_db_version = get_option('wc_piggly_pix_dbversion', '0' );
		$prefix         = $wpdb->prefix;
		$table_name     = $prefix . 'wpgly_pix_receipts';

		/** Setting the default charset collation **/
		$charset_collate = '';

		if ( $ins_db_version === '0' || $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )
		{
			if ( !empty ( $wpdb->charset ) )
			{ $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }

			if ( !empty ( $wpdb->collate ) ) 
			{ $charset_collate .= ' COLLATE '.$wpdb->collate; }

			$SQL = 
				"CREATE TABLE $table_name 
				(
					id int(11) NOT NULL AUTO_INCREMENT,
					order_number varchar(255) NOT NULL,
					customer_email varchar(255) NOT NULL,
					pix_receipt varchar(255) NOT NULL,
					auto_fill tinyint(1) NOT NULL DEFAULT 0,
					send_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
					PRIMARY KEY id (id)
				) $charset_collate;";

			if ( !function_exists('dbDelta') )
			{ require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); }

			@dbDelta( $SQL );
			update_option('wc_piggly_pix_dbversion', $ivl_db_version);
		}
	}

	/**
	 * Set welcome transient to redirect after activation.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public static function setup_welcome ()
	{ set_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-welcome-screen', true, 10 ); }
}