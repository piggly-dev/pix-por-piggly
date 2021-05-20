<?php
namespace Piggly\WC\Pix\WP;

use Exception;
use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Upgrade plugin based in its version.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\WP
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Upgrade
{
	/**
	 * Do an upgrade to current plugin based in it's version...
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function upgrade ()
	{
		if ( !is_admin() )
		{ return; }

		// Manage database
		Activator::create_database();

		$version = get_option('wc_piggly_pix_version', '0' );

		if ( \version_compare($version, WC_PIGGLY_PIX_PLUGIN_VERSION, '>=' ) )
		{ return; }

		$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( \version_compare($version, '1.2.0', '<' ) )
		{ 
			if ( !empty($settings['enabled']) )
			{
				if ( $settings['enabled'] === 1 )
				{ $settings['enabled'] = 'yes'; }
			}

			update_option('woocommerce_wc_piggly_pix_gateway_settings', $settings);
		}

		if ( \version_compare($version, '1.3.0', '<') )
		{ WP::add_admin_notice(self::upgrade_notice()); }

		// Fix to .htaccess created as a folder...
		if ( \version_compare($version, '1.3.7', '<') )
		{ 
			// Prevent users which don't protected upload dir
			$upload = wp_upload_dir();

			// Check for .htaccess file
			$PATH = sprintf('%s/%s/receipts/.htaccess', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

			if ( is_dir( $PATH ) )
			{ 
				rmdir($PATH);
				file_put_contents( $PATH, 'Options -Indexes' ); 
			}
		}

		// Revalidate .htaccess files
		self::update_database();
		self::protect_access();
		self::setup_upgraded();
		// New version
		update_option('wc_piggly_pix_version', WC_PIGGLY_PIX_PLUGIN_VERSION);
	}

	/**
	 * Protect access to upload dir.
	 * 
	 * @since 1.2.4
	 * @return void
	 */
	public static function protect_access ()
	{
		// Prevent users which don't protected upload dir
		$upload = wp_upload_dir();

		$PATH = sprintf('%s/%s/qr-codes/', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		$PATH = sprintf('%s/%s/receipts/', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		// Create folder if not exists...
		if ( !\file_exists( $PATH ) ) 
		{ wp_mkdir_p($PATH); }

		// Check for .htaccess file
		$PATH = sprintf('%s/%s/.htaccess', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for .htaccess file
		$PATH = sprintf('%s/%s/qr-codes/.htaccess', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for .htaccess file
		$PATH = sprintf('%s/%s/receipts/.htaccess', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, 'Options -Indexes' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/index.php', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/qr-codes/index.php', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }

		// Check for index.php file
		$PATH = sprintf('%s/%s/receipts/index.php', $upload['basedir'], \WC_PIGGLY_PIX_DIR_NAME);

		if ( !\file_exists( $PATH ) )
		{ file_put_contents( $PATH, '<?php // Silence is golden' ); }
	}

	/**
	 * Upgrade database when needed.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public static function update_database ()
	{
		global $wpdb;

		$ivl_db_version = \WC_PIGGLY_PIX_DB_VERSION;
		$ins_db_version = get_option('wc_piggly_pix_dbversion', '0' );

		if ( \version_compare($ins_db_version, $ivl_db_version, '>=' ) )
		{ return; }

		$prefix = $wpdb->prefix;
		$table_name = $prefix . 'wpgly_pix_receipts';

		if ( \version_compare($ins_db_version, '1.0.1', '<' ) )
		{
			if ( empty($wpdb->get_var("SHOW COLUMNS FROM `$table_name` LIKE 'trusted'")) )
			{ 
				try
				{ $wpdb->query("ALTER TABLE $table_name ADD trusted tinyint(1) NOT NULL DEFAULT 1"); }
				catch ( Exception $e )
				{ print_r($e); die(); }
			}
		}

		update_option('wc_piggly_pix_dbversion', $ivl_db_version);
	}

	/**
	 * Last upgrade notice message.
	 * 
	 * @since 1.3.0
	 * @return string
	 */
	public static function upgrade_notice () : string
	{
		$link = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' ).'&screen=shortcode';
		return 'Agora, o <strong>Pix por Piggly</strong> tem um formulário nativo para receber os comprovantes Pix. Esses comprovantes atualizarão o pedido e poderão ser vistos na metabox Pix. <a href="'.$link.'">Clique aqui</a> para configurar.';
	}

	/**
	 * Setup upgraded screen transient.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public static function setup_upgraded ()
	{ set_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-upgraded-screen', true, 30 ); }
}