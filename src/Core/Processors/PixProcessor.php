<?php
namespace Piggly\WooPixGateway\Core\Processors;

use Exception;
use Piggly\Pix\AbstractPayload;
use Piggly\Pix\Enums\QrCode;
use Piggly\Pix\Reader;
use Piggly\Pix\StaticPayload;
use Piggly\WooPixGateway\Core\Entities\PixPayload;
use Piggly\WooPixGateway\CoreHelper;
use Piggly\Wordpress\Plugin;
use Piggly\Wordpress\Settings\KeyingBucket;

use WC_Order;

class PixProcessor
{
	/**
	 * Plugin data.
	 * 
	 * @var Plugin
	 * @since 2.0.0
	 */
	protected $_plugin;

	/**
	 * Settings.
	 * 
	 * @var Manager
	 * @since 2.0.0
	 */
	protected $_settings;

	/**
	 * Prepare processor with plugin and settings.
	 * 
	 * @param Plugin $plugin
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct ( Plugin $plugin )
	{
		$this->_plugin   = $plugin;
		$this->_settings = $this->_plugin->settings();
	}

	/**
	 * Get the pix payload attached to $order.
	 * It will create when none payload is set
	 * and refresh data only if needed.
	 *
	 * @filter wpgly_woo_pix_gateway_before_create_pix_code
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return PixPayload
	 * @throws Exception
	 */
	public function get ( WC_Order $order ) : PixPayload
	{
		$payload = PixPayload::fill($order);

		// Payload is fresh and new
		if ( !$payload->hasData() )
		{ return $this->new($payload, $order); }

		// Order is paid or something else
		// so does not need to update any
		// pix payload data...
		if ( !$this->isPaymentWaiting($order) )
		{
			$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('O pedido %s já foi marcado como pago. Preparando os dados Pix em cache...'), $order->get_id()));
		
			// Recreate QR Code if needed
			if ( !empty($payload->getQrCodePath()) )
			{
				if ( !$this->hasQRCode($payload->getQrCodePath()) )
				{ $payload = $this->recreateQrCode($order, $payload); }
			}

			return $payload;
		}

		/** @var KeyingBucket $settings */
		$settings = $this->_settings->bucket()->get('account', new KeyingBucket());

		// Dont regenerate any pix data
		if ( !$settings->get('regenerate', false) )
		{
			// Recreate QR Code if needed
			if ( !empty($payload->getQrCodePath()) )
			{
				if ( !$this->hasQRCode($payload->getQrCodePath()) )
				{ $payload = $this->recreateQrCode($order, $payload); }
			}

			$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Pix não regenerado para o pedido %s. Preparando os dados Pix em cache...'), $order->get_id()));
			return $payload;
		}

		// Pix payload data exists
		// but need to be regenerated
		try
		{
			/** @var AbstractPayload $pixPayload */
			$pixPayload = apply_filters('wpgly_woo_pix_gateway_before_create_pix_code', $this->createPayload($payload), $order, $order->get_id());
		}
		catch ( Exception $e )
		{
			$this->_plugin->debugger()->force()->error(
				\sprintf(
					CoreHelper::__translate('Ocorreu um erro ao gerar o Pix: %s'), 
					$e->getMessage()
				)
			);

			throw $e;
		}

		// Current pix code
		$old_pix_code = $payload->getPixCode();
		$new_pix_code = $pixPayload->getPixCode();

		// When equal, does not refresh pix code
		if ( $old_pix_code === $new_pix_code )
		{
			// Recreate QR Code if needed
			if ( !empty($payload->getQrCodePath()) )
			{
				if ( !$this->hasQRCode($payload->getQrCodePath()) )
				{ $payload = $this->recreateQrCode($order, $payload); }
			}

			$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Pix não regenerado para o pedido %s. Preparando os dados Pix em cache...'), $order->get_id()));
			return $payload;
		}

		$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Pix do pedido %s regenerado'), $order->get_id()));
		return $this->fresh($payload, $order, $pixPayload);
	}

	/**
	 * Create a new $payload attached to $order.
	 *
	 * @filter wpgly_woo_pix_gateway_before_create_pix_code
	 * @param PixPayload $payload
	 * @param WC_Order $order
	 * @since 1.0.0
	 * @return PixPayload
	 * @throws Exception
	 */
	protected function new ( PixPayload $payload, WC_Order $order ) : PixPayload
	{
		try
		{
			$order_number = \method_exists($order, 'get_order_number') ? $order->get_order_number() : $order->get_id();
			$order_amount = $order->get_total();

			/** @var KeyingBucket $settings */
			$settings = $this->_settings->bucket()->get('account', new KeyingBucket());
			$identifier = $settings->get('identifier', '{{order_number}}');
			
			if ( !empty($identifier) || $identifier !== '***' )
			{ $payload->setIdentifier(\str_replace('{{order_number}}', $order_number, $identifier)); }

			$payload
				->setAmount($order_amount)
				->setPixKey(
					$settings->get('key_type'),
					$settings->get('key_value')
				)
				->setMerchantData(
					$settings->get('store_name', ''),
					$settings->get('merchant_name', ''),
					$settings->get('merchant_city', '')
				);
			

			/** @var AbstractPayload $pixPayload */
			$pixPayload = apply_filters('wpgly_woo_pix_gateway_before_create_pix_code', $this->createPayload($payload), $order, $order->get_id());
			$qrcode = $this->createQrCode($pixPayload, $order, $payload->getQrCodePath());

			$payload
				->setPixCode($pixPayload->getPixCode());

			if ( !empty($qrcode) )
			{
				$payload->setQrCode(
					$qrcode['url'],
					$qrcode['path']
				);
			}

			$payload->save();
			return $payload;
		}
		catch ( Exception $e )
		{
			$this->_plugin->debugger()->force()->error(
				\sprintf(
					CoreHelper::__translate('Ocorreu um erro ao gerar o Pix: %s'), 
					$e->getMessage()
				)
			);

			throw $e;
		}
	}

	/**
	 * Just update the $payload attached to $order
	 * with the new data.
	 *
	 * @filter wpgly_woo_pix_gateway_before_create_pix_code
	 * @param PixPayload $payload
	 * @param WC_Order $order
	 * @param AbstractPayload $pixPayload
	 * @since 1.0.0
	 * @return PixPayload
	 * @throws Exception
	 */
	protected function fresh ( 
		PixPayload $payload, 
		WC_Order $order,
		AbstractPayload $pixPayload
	) : PixPayload
	{
		try
		{
			$order_number = \method_exists($order, 'get_order_number') ? $order->get_order_number() : $order->get_id();
			$order_amount = $order->get_total();

			/** @var KeyingBucket $settings */
			$settings = $this->_settings->bucket()->get('account', new KeyingBucket());
			$identifier = $settings->get('identifier', '{{order_number}}');
			
			if ( !empty($identifier) || $identifier !== '***' )
			{ $payload->setIdentifier(\str_replace('{{order_number}}', $order_number, $identifier)); }

			$payload
				->setPixCode($pixPayload->getPixCode())
				->setAmount($order_amount)
				->setPixKey(
					$settings->get('key_type'),
					$settings->get('key_value')
				)
				->setMerchantData(
					$settings->get('store_name', ''),
					$settings->get('merchant_name', ''),
					$settings->get('merchant_city', '')
				);
			
			$qrcode = $this->createQrCode($pixPayload, $order, $payload->getQrCodePath());

			if ( !empty($qrcode) )
			{
				$payload->setQrCode(
					$qrcode['url'],
					$qrcode['path']
				);
			}

			$payload->save();
			return $payload;
		}
		catch ( Exception $e )
		{
			$this->_plugin->debugger()->force()->error(
				\sprintf(
					CoreHelper::__translate('Ocorreu um erro ao gerar o Pix: %s'), 
					$e->getMessage()
				)
			);

			throw $e;
		}
	}

	/**
	 * Create the pix payload as StaticPayload
	 * or DynamicPayload.
	 *
	 * @filter wpgly_woo_pix_gateway_payload
	 * @param PixPayload $payload
	 * @since 2.0.0
	 * @return AbstractPayload
	 */
	protected function createPayload ( 
		PixPayload $payload 
	) : AbstractPayload
	{
		if ( empty($payload->getAmount()) )
		{ throw new Exception(CoreHelper::__translate('O valor do Pix é inválido')); }

		if ( empty($payload->getPixKeyType()) || empty($payload->getPixKeyValue()) )
		{ throw new Exception(CoreHelper::__translate('É necessário preencher a Chave Pix')); }

		/** @var StaticPayload $default */
		$default = 
			(new StaticPayload())
				->setAmount($payload->getAmount() ?? 0)
				->setTid($payload->getIdentifier())
				->setPixKey($payload->getPixKeyType(), $payload->getPixKeyValue());

		if ( !empty($payload->getMerchantName()))
		{ $default->setMerchantName($payload->getMerchantName()); }

		if ( !empty($payload->getMerchantCity()))
		{ $default->setMerchantName($payload->getMerchantCity()); }

		if ( !empty($payload->getStoreName()) )
		{ $default->setDescription(\sprintf(CoreHelper::__translate('Compra em %s'), $payload->getStoreName)); }
	
		$pix = intval(get_option('wc_piggly_pix_counter', 0));
		update_option('wc_piggly_pix_counter', $pix++);

		return \apply_filters('wpgly_woo_pix_gateway_payload', $default, $payload);
	}

	/**
	 * Recreate QR Code when available
	 * saving the PixPayload to order.
	 *
	 * @param WC_Order $order
	 * @param PixPayload $payload
	 * @since 2.0.0
	 * @return PixPayload
	 */
	protected function recreateQrCode ( 
		WC_Order $order, 
		PixPayload $payload 
	) : PixPayload
	{
		$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Criando QR Code para o pedido %s'), $order->get_id()));

		/** @var AbstractPayload $_payload */
		$_payload = (new Reader($payload->getPixCode()))->export();

		$qrcode = $this->createQrCode(
			$_payload, 
			$order, 
			$payload->getQrCodePath()
		);
		
		if ( empty($qrcode) )
		{
			$payload
				->set('pix_qr', null)
				->set('qr_path', null);
		}
		else
		{
			$payload
				->setQrCode($qrcode['url'], $qrcode['path']);
		}

		$payload->save();
		return $payload;
	}

	/**
	 * Create the QR Code image with URL and PATH.
	 * Returns an array with url and path keys.
	 *
	 * @param AbstractPayload $payload
	 * @param WC_Order $order
	 * @param string $curr_path
	 * @since 2.0.0
	 * @return array|null
	 */
	protected function createQrCode ( 
		AbstractPayload $payload, 
		WC_Order $order,
		$curr_path = null
	) : ?array
	{
		if ( !$this->isQrCodeAvailable() )
		{ 	
			$this->_plugin->debugger()->alert(CoreHelper::__translate('A criação do QR Code não está disponível, verifique as configurações do plugin e a extensão `gd` no PHP'));
			return null;
		}

		$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Criando QR Code para o pedido %s'), $order->get_id()));
	
		try
		{
			if ( !empty($curr_path) )
			{ $this->delQRCode($curr_path); }

			$upload     = wp_upload_dir();
			$dirname    = dirname($this->_plugin->getBasename());
			$uploadPath = $upload['basedir'].'/'.$dirname.'/qr-codes/';
			$uploadUrl  = $upload['baseurl'].'/'.$dirname.'/qr-codes/';
			$fileName   = md5('order-'.$order->get_id().time()).'.png';
			$file       = $uploadPath.$fileName;

			if ( !file_exists( $uploadPath ) ) 
			{ wp_mkdir_p($uploadPath); }

			if ( file_exists($file) )
			{ unlink($file); }

			$img     = str_replace('data:image/png;base64,', '', $payload->getQRCode(QrCode::OUTPUT_PNG, QrCode::ECC_L) );
			$img     = str_replace(' ', '+', $img);
			$data_   = base64_decode($img);
			$success = file_put_contents($file, $data_);

			if ( !$success )
			{ throw new Exception(\sprintf(CoreHelper::__translate('Arquivo indisponível %s'), $file)); }

			return [
				'url' => $uploadUrl.$fileName, 
				'path' => $file
			];
		}
		catch ( Exception $e )
		{
			$this->_plugin->debugger()->error(\sprintf(CoreHelper::__translate('Erro ao salvar o QR Code: %s'), $e->getMessage()));
		}

		return null; 
	}

	protected function hasQRCode ( string $path ) : bool
	{ return \file_exists($path); }

	/**
	 * Delete QR Code image at $path.
	 *
	 * @param string $path
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function delQRCode ( string $path ) : bool
	{
		if ( $this->hasQRCode($path) )
		{
			$this->_plugin->debugger()->debug(\sprintf(CoreHelper::__translate('Removendo QR Code em %s'), $path));
			return (bool)\unlink($path);
		}

		return true;
	}

	/**
	 * Return if QR Code is available to generate.
	 *
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function isQrCodeAvailable () : bool
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_settings->bucket()->get('gateway', new KeyingBucket());

		return (
			$settings->get('shows', new KeyingBucket())->get('qrcode', false)
			&& AbstractPayload::supportQrCode()
		);
	}

	/**
	 * Verify if $order is waiting for payment.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function isPaymentWaiting ( 
		WC_Order $order 
	) : bool
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_settings->bucket()->get('orders', new KeyingBucket());
		
		$expected = [
			'new',
			'on-hold',
			$settings->get('waiting_status', 'on-hold'),
			$settings->get('receipt_status', 'on-hold')
		];

		return (
			$this->_plugin->getName() === $order->get_payment_method()
			&& $order->has_status($expected)
		);
	}
}