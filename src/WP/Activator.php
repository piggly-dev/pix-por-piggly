<?php
namespace Piggly\WooPixGateway\WP;

use Exception;
use Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\Wordpress\Core\WP;
use Piggly\Wordpress\Core\Scaffold\Internationalizable;

class Activator extends Internationalizable implements Runnable
{
	/**
	 * Requirements string.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $_requirements;

	/**
	 * Method to run all business logic.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run ()
	{
		if ( !WP::is_pure_admin() )
		{ return; }

		// Prepare and create database
		$this->create_database();
		// Prepare and create paths
		$this->create_paths();

		// Create cronjobs
		Cron::create($this->_plugin);

		// First setup
		$this->setup();
	}

	/**
	 * First setup to plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function setup ()
	{}

	/**
	 * Create the main database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function create_database () 
	{
		global $wpdb;

		// It is not initial database version
		if ( get_option('wc_piggly_pix_dbversion', '0' ) !== '0' )
		{ return; }
		
		if ( !function_exists('dbDelta') )
		{ require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); }

		try
		{
			$prefix          = $wpdb->prefix;
			$charset_collate = '';

			/** Setting the default charset collation **/
			if ( !empty ( $wpdb->charset ) )
			{ $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }

			if ( !empty ( $wpdb->collate ) ) 
			{ $charset_collate .= ' COLLATE '.$wpdb->collate; }

			$table_name = $prefix . 'wpgly_pix_receipts';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name )
			{
				$SQL = 
					"CREATE TABLE $table_name 
					(
						`id` INT(11) NOT NULL AUTO_INCREMENT,
						`order_number` VARCHAR(255) NOT NULL DEFAULT 'BRL',
						`customer_email` VARCHAR(255) NOT NULL,
						`pix_receipt` VARCHAR(255) NOT NULL,
						`trusted` TINYINT(1) NOT NULL DEFAULT 1,
						`send_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
						PRIMARY KEY `id` (`id`),
						INDEX `currency` (`currency`)
					) $charset_collate;";

				@dbDelta( $SQL );
			}

			update_option('wc_piggly_pix_dbversion', $this->_plugin->getDbVersion());
		}
		catch ( Exception $e )
		{ $this->debug()->force()->error(\sprintf($this->__translate('Não foi possível criar o banco de dados: %s'), $e->getMessage())); }
	}

	/**
	 * Create plugin paths.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function create_paths ()
	{
		/** @var string $base_dir Upload base dir */
		$base_dir = wp_upload_dir()['basedir'];
		/** @var string $pix_dir Folder name */
		$pix_dir  = \dirname($this->_plugin->getBasename());

		// INDEX
		// Check for .htaccess file
		$PATH = \sprintf('%s/%s/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = \sprintf('%s/%s/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// QR CODES

		// QR CODE PATH
		$PATH = sprintf('%s/%s/qr-codes/', $base_dir, $pix_dir);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		// Check for .htaccess file
		$PATH = \sprintf('%s/%s/qr-codes/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/qr-codes/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// RECEIPTS

		// RECEIPTS PATH
		$PATH = \sprintf('%s/%s/receipts/', $base_dir, $pix_dir);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		// Check for .htaccess file
		$PATH = sprintf('%s/%s/receipts/.htaccess', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/receipts/index.php', $base_dir, $pix_dir);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }
	}
}