<?php
namespace Piggly\WooPixGateway\Core;

use Exception;
use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\Core\Managers\SettingsManager;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Upgrade\VersionUpgrader;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Exceptions\InvalidPixCodeException;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Exceptions\InvalidPixKeyException;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Exceptions\InvalidPixKeyTypeException;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Reader;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Ajaxable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use Piggly\WooPixGateway\WP\Cron;
use WC_Order;

/**
 * Manages all AJAX endpoints.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Ajax extends Ajaxable
{
	/**
	 * Handle all admin endpoints to ajax.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function handlers ()
	{
		WC()->mailer();

		$priv = [
			'pgly_wc_piggly_pix_get_plugin_settings',
			'pgly_wc_piggly_pix_set_plugin_settings',
			'pgly_wc_piggly_pix_admin_update',
			'pgly_wc_piggly_pix_upgrader',
			'pgly_wc_piggly_pix_admin_import',
			'pgly_wc_piggly_pix_admin_cron_process',
			'pgly_wc_piggly_pix_admin_cron_recreate',
			'pgly_wc_piggly_pix_admin_cron_cleaning',
			'pgly_wc_piggly_pix_admin_clean_logs'
		];

		foreach ( $priv as $action )
		{
			WP::add_action(
				'wp_ajax_'.$action,
				$this,
				$action
			); 
		} 

		$public = [
			'pgly_wc_piggly_pix_webhook'
		];
		
		foreach ( $public as $action )
		{
			WP::add_action(
				'wp_ajax_'.$action,
				$this,
				$action
			); 

			WP::add_action(
				'wp_ajax_nopriv_'.$action,
				$this,
				$action
			); 
		}
	}

	/**
	 * Get all plugin settings.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_get_plugin_settings () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		$this->success((new SettingsManager())->getSettings());
		exit;
	}

	/**
	 * Set plugin settings by section.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_set_plugin_settings ()
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		// Plugin setting section
		$section = \filter_input(\INPUT_POST, 'section', \FILTER_SANITIZE_STRING);
		// Plugin setting data
		$data = \filter_input(\INPUT_POST, 'data', \FILTER_SANITIZE_ENCODED);

		$sManager = new SettingsManager();

		try
		{ $sManager->saveSettings($section, json_decode(urldecode($data), true)); }
		catch ( Exception $e )
		{
			$this->status(422)->error([
				'code' => 3,
				'message' => $e->getMessage()
			]);
		}

		$this->success([
			'message' => CoreConnector::__translate('Configurações salvas')
		]);
	}

	/**
	 * Update old pix to order.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_update ()
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		// Order id
		$order = \filter_input(\INPUT_POST, 'order', \FILTER_SANITIZE_STRING);
		$order = new WC_Order($order);

		try
		{
			(new PixGateway())->recreate_pix($order);

			$this->success([
				'message' => CoreConnector::__translate('Pix Atualizado')
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Upgrade plugin.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_upgrader () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		$upgrader = new VersionUpgrader($this->_plugin);

		try
		{ 
			$upgrader->upgrade(); 

			$this->success([
				'message' => 'Plugin atualizado, atualize a página para continuar...'
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Import data from pix.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_import () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		try
		{ 
			// Get pix-code
			$pixCode = \filter_input(\INPUT_POST, 'pix', \FILTER_SANITIZE_STRING);

			if ( empty($pixCode) )
			{ throw new Exception(CoreConnector::__translate('O Código Pix Copia & Cola deve ser enviado...')); }

			try
			{
				// Read pix data and save it...
				$reader = new Reader($pixCode);

				$account = CoreConnector::settings()->get('account', new KeyingBucket());

				$account->set('key_value', $reader->getPixKey());
				$account->set('key_type', Parser::getKeyType($reader->getPixKey()));

				$account->set('merchant_name', $reader->getMerchantName());
				$account->set('merchant_city', $reader->getMerchantCity());

				CoreConnector::settingsManager()->save();
			}
			catch ( InvalidPixCodeException $e )
			{ throw new Exception(CoreConnector::__translate('O código Pix importado é inválido. Certifique-se que é um código "Pix Copia & Cola" válido.')); }
			catch ( InvalidPixKeyTypeException $e )
			{ throw new Exception(CoreConnector::__translate('O tipo da chave do código Pix importado é inválido.')); }
			catch ( InvalidPixKeyException $e )
			{ throw new Exception(CoreConnector::__translate('A chave do código Pix importado é inválida.')); }
			
			$this->success([
				'message' => 'Pix importado com sucesso. Veja as Configurações do Plugin.'
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Process pix.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_cron_process () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		try
		{ 
			(new Cron($this->_plugin))->processing(); 
			$this->success([
				'message'=>CoreConnector::__translate('Pix processados')
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Process pix.
	 * 
	 *	@since 2.0.25
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_cron_recreate () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		try
		{ 
			(new Cron($this->_plugin))->create($this->_plugin); 
			$this->success([
				'message'=>CoreConnector::__translate('Cronjob recriada')
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Clean pix.
	 * 
	 *	@since 2.0.0
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_cron_cleaning () 
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		try
		{ 
			(new Cron($this->_plugin))->cleaning(); 
			$this->success([
				'message'=>CoreConnector::__translate('Banco de dados dos Pix otimizado')
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}

	/**
	 * Clean pix.
	 * 
	 *	@since 2.0.22
	 * @return void
	 */
	public function pgly_wc_piggly_pix_admin_clean_logs ()
	{
		$this
			->prepare('pgly_wc_piggly_pix_admin', 'xSecurity')
			->need_capability('manage_woocommerce');

		$path  = ABSPATH.'wp-content/pix-por-piggly/';
		$files = [];
		$files = glob($path.'*.log');

		foreach ( $files as $file )
		{ \unlink($file); }

		$this->success([
			'message'=>CoreConnector::__translate('Logs limpos com sucesso, atualize para continuar')
		]);
	}

	/**
	 * Validate if pix was paid.
	 * 
	 *	@since 2.0.19
	 * @return void
	 */
	public function pgly_wc_piggly_pix_webhook () {
		$this
			->prepare('pgly_wc_piggly_pix', 'xSecurity');

		try
		{ 
			// Get pix-code
			$txid = \filter_input(\INPUT_POST, 'txid', \FILTER_SANITIZE_STRING);

			if ( empty($txid) )
			{ throw new Exception(CoreConnector::__translate('Pagamento Pix não identificado')); }

			// Read pix data and save it...
			$pix = (new PixRepo($this->_plugin))->byId($txid);

			if ( empty($pix) )
			{ throw new Exception(CoreConnector::__translate('Pagamento Pix não identificado')); }

			if ( $pix->isType(PixEntity::TYPE_STATIC) )
			{ throw new Exception(CoreConnector::__translate('Pix estático identificado')); }

			if ( !$pix->isPaid() )
			{ 
				// Try to process pendind pix
				$pix = \apply_filters('pgly_wc_piggly_pix_process', $pix);

				if ( $pix->isPaid() && !empty($pix->getOrder()))
				{
					$order = $pix->getOrder();
					$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

					// Flush session
					if ( WC()->session ) 
					{ WC()->session->set( 'order_awaiting_payment', false ); }

					$order->set_transaction_id($pix->getE2eid());

					// Update status
					$order->set_status( 
						apply_filters( 
							'woocommerce_payment_complete_order_status',
							$order->needs_processing() ? $settings->get('paid_status', 'processing') : 'completed', 
							$order->get_id(), 
							$order 
						),
						\sprintf(
							CoreConnector::__translate('Pix `%s` identificado e confirmado.'), 
							$pix->getE2eid()
						)
					);

					// Set paid date
					if ( ! $order->get_date_paid( 'edit' ) ) 
					{ $order->set_date_paid( time() ); }

					// Save order
					$order->save();
					
					// Do action
					do_action( 'woocommerce_payment_complete', $order->get_id() );
					return true;
				}
				else
				{ throw new Exception(CoreConnector::__translate('Pagamento Pix não identificado')); }
			}

			$this->success([
				'message' => 'Pagamento Pix identificado'
			]);
		}
		catch ( Exception $e )
		{ $this->exceptionError($e); }
	}
}