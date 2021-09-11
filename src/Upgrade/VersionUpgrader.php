<?php
namespace Piggly\WooPixGateway\Upgrade;

use Exception;
use Piggly\WooPixGateway\Core\Ajax;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Internationalizable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

/**
 * Upgrade plugin from v1.x to v2.x.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\WP
 * @version 2.0.0
 * @since 2.0.0
 * @category WP
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class VersionUpgrader extends Internationalizable implements Runnable
{
	/**
	 * Method to run all business logic.
	 * It will run only when plugin is already
	 * installed and must to update from v1.x
	 * to v2.x.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function run ()
	{ 
		if ( !WP::is_admin() )
		{ return; }

		// Init ajax
		Ajax::init(CoreConnector::plugin());

		WP::add_action(
			'admin_menu', 
			$this, 
			'add_menu', 
			99
		);

		WP::add_action( 
			'admin_notices', 
			$this,
			'upgrader_notice' 
		);
	}

	/**
	 * Create a new menu at Wordpress admin menu bar.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function add_menu ()
	{
		add_menu_page(
			__('Configurações Pix', 'wc-piggly-pix'),
			__('Pix por Piggly', 'wc-piggly-pix'),
			'manage_woocommerce',
			'wc-piggly-pix',
			[$this, 'upgrader_page'],
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNi45MiAyNi45MiI+PHBhdGggZD0iTTIzLjM1LDIzLjM5YTMuOTMsMy45MywwLDAsMS0yLjgtMS4xNmwtNC00YS43NS43NSwwLDAsMC0xLjA2LDBMMTEuNCwyMi4yNWEzLjk0LDMuOTQsMCwwLDEtMi43OSwxLjE2aC0uOGw1LjEyLDUuMTFhNC4wOCw0LjA4LDAsMCwwLDUuNzgsMGw1LjEzLTUuMTNaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMi4zNiAtMi44KSIgc3R5bGU9ImZpbGw6bm9uZSIvPjxwYXRoIGQ9Ik04LjYxLDkuMTFhMy45LDMuOSwwLDAsMSwyLjc5LDEuMTZsNC4wNiw0LjA1YS43NS43NSwwLDAsMCwxLjA2LDBsNC00YTQsNCwwLDAsMSwyLjgtMS4xNWguNDlMMTguNzEsNGE0LjA4LDQuMDgsMCwwLDAtNS43OCwwTDcuODEsOS4xMVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0yLjM2IC0yLjgpIiBzdHlsZT0iZmlsbDpub25lIi8+PHBhdGggZD0iTTI4LjA4LDEzLjM3LDI1LDEwLjI3YS41NC41NCwwLDAsMS0uMjIsMEgyMy4zNWEyLjgyLDIuODIsMCwwLDAtMiwuODFsLTQsNGExLjk0LDEuOTQsMCwwLDEtMS4zNy41NywxLjkxLDEuOTEsMCwwLDEtMS4zNy0uNTdsLTQuMDYtNC4wNWEyLjc0LDIuNzQsMCwwLDAtMi0uODFINi44OGEuNjUuNjUsMCwwLDEtLjIxLDBMMy41NiwxMy4zN2E0LjA4LDQuMDgsMCwwLDAsMCw1Ljc4bDMuMTEsMy4xMWEuNjUuNjUsMCwwLDEsLjIxLDBIOC42MWEyLjc4LDIuNzgsMCwwLDAsMi0uODFsNC4wNi00LjA1YTIsMiwwLDAsMSwyLjc0LDBsNCw0YTIuNzgsMi43OCwwLDAsMCwyLC44MWgxLjQxYS41NC41NCwwLDAsMSwuMjIuMDVsMy4xLTMuMWE0LjEsNC4xLDAsMCwwLDAtNS43OCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTIuMzYgLTIuOCkiIHN0eWxlPSJmaWxsOm5vbmUiLz48L3N2Zz4='
		);
	}

	/**
	 * Shows about the update.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrader_notice ()
	{
		?>
		<div class="notice notice-error">
			<p>
				O plugin <strong>Pix por Piggly</strong> precisa
				da sua atenção.
			</p>
		</div>
		<?php
		?>
		<div class="notice notice-warning">
			<p>
				O plugin <strong>Pix por Piggly</strong> sofreu
				uma atualização muito importante e requer a sua
				atenção para continuar funcionando. <a href="<?=admin_url('admin.php?page='.CoreConnector::domain())?>">
				Clique aqui</a> para verificar.
			</p>
		</div>
		<?php
	}

	/**
	 * Load plugin page settings.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrader_page ()
	{
		// CSS and JS
		CoreConnector::enqueuePglyWpsAdmin(true);

		echo '<div id="pgly-wps-plugin" class="pgly-wps--settings">';
		require_once(CoreConnector::plugin()->getTemplatePath().'admin/version-upgrader.php');
		echo '</div>';
	}

	/**
	 * Do all upgrade operations.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrade ()
	{
		$upgrader = \get_option('wc_piggly_pix_upgrader', []);

		if ( !\in_array('database', $upgrader, true) )
		{
			if ( $this->upgrade_database() )
			{ 
				$upgrader = \array_merge($upgrader, ['database']);
				\update_option('wc_piggly_pix_dbversion', \PGLY_PIX_GATEWAY_DBVERSION);
				\update_option('wc_piggly_pix_upgrader', $upgrader);
			}
			else
			{ throw new Exception(CoreConnector::__translate('Não foi possível atualizar o banco de dados. Você deve estar utilizando uma versão antiga do MySQL. Tente novamente.')); }
		}

		if ( !\in_array('settings', $upgrader, true) )
		{
			if ( $this->upgrade_settings() )
			{ 
				$upgrader = \array_merge($upgrader, ['settings']);
				\delete_option('woocommerce_wc_piggly_pix_gateway_settings');
				\update_option('wc_piggly_pix_upgrader', $upgrader);
			}
			else
			{ throw new Exception(CoreConnector::__translate('Não foi possível atualizar as configurações. Tente novamente.')); }
		}

		\update_option('wc_piggly_pix_version', \PGLY_PIX_GATEWAY_VERSION);
	}

	/**
	 * Upgrade settings.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function upgrade_settings () : bool
	{
		// Current plugin settings
		$settings = \get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( empty($settings) )
		{ return true; }

		$newest = [
			'upgraded' => true,
			'upgraded_endpoints' => false,
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
				
				'shows_qrcode' => true,
				'shows_copypast' => true,
				'shows_manual' => true,
				'shows_amount' => true
			],
			'orders' => [
				'receipt_status' => ($settings['auto_update_receipt'] ?? 'no') === 'yes' ? 'pix-receipt' : 'on-hold',
				'paid_status' => $settings['paid_status'] ?? 'processing',
				'after_receipt' => $settings['redirect_after_receipt'] ?? '',
				'expires_after' => 24,
				'closest_lifetime' => 60,
				'cron_frequency' => 'daily',
				'decrease_stock' => true
			],
			'account' => [
				'store_name' => $settings['store_name'] ?? '',
				'bank' => \intval($settings['bank'] ?? 0),
				'key_type' => $settings['key_type'] ?? '',
				'key_value' => $settings['key_value'] ?? '',
				'merchant_name' => $settings['merchant_name'] ?? '',
				'merchant_city' => $settings['merchant_city'] ?? '',
				'description' => 'Compra em {{store_name}}',
				'fix' => ($settings['fix'] ?? 'no') === 'yes',
			],
			'discount' => [
				'value' => \str_replace('%', '', $settings['discount']) ?? '',
				'type' => \strpos($settings['discount'] ?? '', '%') ? 'PERCENT' : 'FIXED',
				'label' => $settings['discount_label'] ?? $this->__translate('Desconto Pix Aplicado')
			],
			'receipts' => [
				'shows_receipt' => 'up',
				'receipt_page' => true,
				'whatsapp_number' => $settings['whatsapp'] ?? '',
				'whatsapp_message' => \str_replace('{{pedido}}', '{{order_number}}', $settings['whatsapp_message'] ?? $this->__translate('Segue o comprovante para o pedido {{order_number}}:')),
				'telegram_number' => $settings['telegram'] ?? '',
				'telegram_message' => \str_replace('{{pedido}}', '{{order_number}}', $settings['telegram_message'] ?? $this->__translate('Segue o comprovante para o pedido {{order_number}}:'))
			]
		];

		return \update_option('wc_piggly_pix_settings', $newest);
	}

	/**
	 * Upgrade database.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function upgrade_database () : bool
	{
		global $wpdb;

		if ( !function_exists('dbDelta') )
		{ require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); }

		try
		{
			$prefix = $wpdb->prefix;

			$table_name = $prefix . 'pgly_pix';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name )
			{
				$SQL = 
					"CREATE TABLE ".$table_name." (
						`id` INT NOT NULL AUTO_INCREMENT,
						`oid` INT NULL COMMENT 'Order ID',
						`txid` VARCHAR(255) NOT NULL,
						`e2eid` VARCHAR(255) NULL,
						`store_name` VARCHAR(255) NULL,
						`merchant_name` VARCHAR(255) NULL,
						`merchant_city` VARCHAR(255) NULL,
						`key` VARCHAR(255) NOT NULL,
						`key_type` VARCHAR(255) NOT NULL,
						`description` VARCHAR(255) NULL,
						`amount` DECIMAL(8,2) NOT NULL,
						`discount` DECIMAL(8,2) NULL DEFAULT 0,
						`bank` INT NULL,
						`brcode` TEXT NULL,
						`qrcode` TEXT NULL,
						`receipt` TEXT NULL,
						`metadata` TEXT NULL,
						`type` VARCHAR(10) NOT NULL DEFAULT 'static',
						`status` VARCHAR(10) NOT NULL DEFAULT 'created',
						`expires_at` TIMESTAMP NULL,
						`updated_at` TIMESTAMP NULL,
						`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
						PRIMARY KEY (`id`),
						INDEX `oid` (`oid`),
						INDEX `expires_at` (`expires_at`)
					);";

				@dbDelta( $SQL );
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '".$table_name."'" ) != $table_name )
			{ @\trigger_error(CoreConnector::__translate('Não foi possível criar o banco de dados')); return false; }
			
			return true;
		}
		catch ( Exception $e )
		{ 
			$this->debug()->force()->error(\sprintf(CoreConnector::__translate('Não foi possível criar o banco de dados: %s'), $e->getMessage())); 
			return false;
		}
	}
}