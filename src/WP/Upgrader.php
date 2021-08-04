<?php
namespace Piggly\WooPixGateway\WP;

use Exception;
use Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\Wordpress\Core\Scaffold\Internationalizable;
use Piggly\Wordpress\Core\WP;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Upgrader extends Internationalizable implements Runnable
{
	/**
	 * Method to run all business logic.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run ()
	{ 
		if ( !WP::is_admin() )
		{ return; }

		// Current version
		$version = get_option('wc_piggly_pix_version', '0' );

		// If version greater than plugin version, ignore
		if ( \version_compare($version, $this->_plugin->getVersion(), '>=') )
		{ return; }

		// Rebuild when lower than 2.0.0
		if ( \version_compare($version, '2.0.0', '<') )
		{ 
			$this->rebuild_settings();
			$this->rebuild_paths(); 
		}

		// Check if need to upgrade database
		$this->upgrader_database();
		
		// New version
		update_option('wc_piggly_pix_version', $this->_plugin->getVersion());
	}

	/**
	 * Rebuild settings to the new format.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function rebuild_settings ()
	{
		// Current plugin settings
		$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( empty($settings) )
		{ return; }

		$newest = [
			'global' => [
				'debug' => ($settings['debug'] ?? 'no') === 'yes'
			],
			'gateway' => [
				'enabled' => ($settings['enabled'] ?? 'no') === 'yes',
				'icon' => $settings['select_icon'] ?? 'pix-payment-icon',
				'title' => $settings['title'] ?? $this->__translate('Faça um Pix'),
				'description' => $settings['description'] ?? $this->__translate('Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.'),
				'advanced_description' => ($settings['advanced_description'] ?? 'no') === 'yes',
				'instructions' => \str_replace('{{pedido}}', '{{order_number}}', $settings['instructions'] ?? $this->__translate('Faça o pagamento via PIX. O pedido número {{order_number}} será liberado assim que a confirmação do pagamento for efetuada.')),
				'shows' => [
					'qrcode' => ($settings['qrcode'] ?? 'no') === 'yes',
					'copypast' => ($settings['copypast'] ?? 'no') === 'yes',
					'manual' => ($settings['manual'] ?? 'no') === 'yes',
				]
			],
			'emails' => [
				'model' => $settings['email_status'] ?? 'WC_Email_Customer_On_Hold_Order',
				'position' => $settings['email_position'] ?? 'before'
			],
			'orders' => [
				'waiting_status' => $settings['order_status'] ?? 'on-hold',
				'receipt_status' => ($settings['auto_update_receipt'] ?? 'no') === 'yes' ? 'pix-receipt' : 'on-hold',
				'paid_status' => $settings['paid_status'] ?? 'processing',
				'after_receipt' => $settings['redirect_after_receipt'] ?? '',
				'hide_in_order' => ($settings['fix'] ?? 'no') === 'yes',
				'expires_after' => '',
				'cron_frequency' => 'daily'
			],
			'account' => [
				'store_name' => $settings['store_name'] ?? '',
				'bank' => $settings['bank'] ?? '',
				'key_type' => $settings['key_type'] ?? '',
				'key_value' => $settings['key_value'] ?? '',
				'merchant_name' => $settings['merchant_name'] ?? '',
				'merchant_city' => $settings['merchant_city'] ?? '',
				'identifier' => \str_replace('{{id}}', '{{order_number}}', $settings['identifier'] ?? '{{order_number}}'),
				'fix' => ($settings['fix'] ?? 'no') === 'yes',
				'regenerate' => ($settings['qr_regenerate'] ?? 'no') === 'yes',
			],
			'discount' => [
				'value' => \str_replace('%', '', $settings['discount']) ?? '',
				'type' => \strpos($settings['discount'] ?? '', '%') ? 'PERCENT' : 'FIXED',
				'label' => $settings['discount_label'] ?? $this->__translate('Desconto Pix Aplicado')
			],
			'receipts' => [
				'whatsapp' => [
					'number' => $settings['whatsapp'] ?? '',
					'message' => \str_replace('{{pedido}}', '{{order_number}}', $settings['whatsapp_message'] ?? $this->__translate('Segue o comprovante para o pedido {{order_number}}:')),
				],
				'telegram' => [
					'number' => $settings['telegram'] ?? '',
					'message' => \str_replace('{{pedido}}', '{{order_number}}', $settings['telegram_message'] ?? $this->__translate('Segue o comprovante para o pedido {{order_number}}:')),
				]
			]
		];

		update_option('wpgly_woo_pix_gateway', $newest);
		$this->_plugin->settings()->reload();
	}

	/**
	 * Check if paths are created and validated them.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function rebuild_paths ()
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

	/**
	 * Upgrade database according to its version.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function upgrader_database ()
	{
		global $wpdb;
		
		// Current database version
		$ivl_db_version = \WC_PIGGLY_PIX_DB_VERSION;
		// Installed database version
		$ins_db_version = get_option('wc_piggly_pix_dbversion', '0' );

		// If installed is greater or equal to current, return
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
				{ $this->debug()->force()->error(\sprintf($this->__translate('Não foi possível atualizar o banco de dados: %s'), $e->getMessage())); }
			}
		}

		update_option('wc_piggly_pix_dbversion', $ivl_db_version);
	}
}