<?php
namespace Piggly\WooPixGateway\Core\Gateway;

use Piggly\WooPixGateway\Core\Entities\PixPayload;
use Piggly\WooPixGateway\Core\Processors\PixProcessor;
use Piggly\WooPixGateway\CoreHelper;
use Piggly\Wordpress\Core\WP;
use Piggly\Wordpress\Plugin;
use Piggly\Wordpress\Settings\KeyingBucket;
use Piggly\Wordpress\Settings\Manager;

use WC_Order;

class PixGateway extends WC_Payment_Gateway
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

	public function __construct ()
	{
		$this->_plugin   = CoreHelper::getInstance()->getPlugin();
		$this->_settings = $this->_plugin->settings();

		$this->id = $this->_plugin->getName();
		$this->method_title = CoreHelper::__translate('Pix');
		$this->method_description = CoreHelper::__translate('Habilite o pagamento de pedidos via Pix. Este plugin automaticamente adiciona as instruções Pix na Página de Obrigado e na Página do Pedido.');
		$this->supports = ['products'];

		$this->init_settings();
		$this->init_form_fields();
	}

	/**
	 * Initialise settings form fields.
	 * It ignores the WC_Settings_API behavior.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function init_form_fields () 
	{ return; }

	/**
	 * Initialise settings for gateways.
	 * It ignores the WC_Settings_API behavior.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function init_settings() 
	{
		/** @var KeyingBucket $gatewaySettings */
		$gatewaySettings = $this->_settings->bucket()->get('gateway', new KeyingBucket());
		/** @var KeyingBucket $gatewaySettings */
		$accountSettings = $this->_settings->bucket()->get('account', new KeyingBucket());

		$this->enabled = empty($accountSettings->get('key_value')) ? 'no' : ($gatewaySettings->get('enabled', false) ? 'yes' : 'no');
		$this->title = $gatewaySettings->get('title');
		$this->description = $gatewaySettings->get('description');
		$this->icon = apply_filters('woocommerce_gateway_icon', $this->_plugin->getUrl().'assets/icons/'.$gatewaySettings->get('icon').'.png');
	}

	/**
	 * Output the gateway settings screen.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function admin_options ()
	{ 
		wp_enqueue_script(
			'wpgly-woo-pix-gateway-admin-app-js',
			$this->_plugin->getUrl().'assets/dist/js/wpgly-woo-pix-gateway-admin.app.js',
			[],
			'0.0.1',
			true
		);

		wp_localize_script(
			'wpgly-woo-pix-gateway-admin-app-js',
			'bdmCommerce',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'x_security' => wp_create_nonce('wpgly_bdm_commerce_admin'),
				'plugin_url' => admin_url('admin.php?page='.$this->_plugin->getDomain()),
				'debug' => true
			]
		);

		if ( !wp_style_is('wpgly-wps-admin') )
		{
			wp_enqueue_style(
				'wpgly-wps-admin',
				$this->_plugin->getUrl().'assets/css/wpgly-wps-admin.css'
			);
		}

		require_once($this->_plugin->getTemplatePath().'/admin/settings.php'); 
	}

	/**
	 * Process Payment.
	 *
	 * @param int $order_id Order ID.
	 * @since 2.0.0 Removed wpgly_pix_after_process_payment action
	 * @return array
	 */
	public function process_payment ( $order_id )
	{
		global $woocommerce;

		$order = new WC_Order($order_id);

		// Create pix data
		(new PixProcessor($this->_plugin))->get($order);
		
		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 
			str_replace('wc-', '', $this->order_status), 
			__( 'Aguardando pagamento via Pix', \WC_PIGGLY_PIX_PLUGIN_NAME ) 
		);
 
		// Remove cart
		$woocommerce->cart->empty_cart();
		
		// Return thank-you redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_return_url($order)
		);
	}

	/**
	 * If There are no payment fields 
	 * show the description if set.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function payment_fields ()
	{
		/** @var KeyingBucket $gatewaySettings */
		$gatewaySettings = $this->_settings->bucket()->get('gateway', new KeyingBucket());

		if ( !$gatewaySettings->get('advanced_description', false) )
		{
			$description = $this->get_description();

			if ( $description ) 
			{ echo wpautop( wptexturize( $description ) ); }

			return;
		}

		if ( !wp_style_is('wpgly-woo-pix-gateway') )
		{
			wp_enqueue_style(
				'wpgly-woo-pix-gateway',
				$this->_plugin->getUrl().'assets/css/wpgly-woo-pix-gateway.css'
			);
		}

		wc_get_template(
			'html-woocommerce-instructions.php',
			[
				'description' => $this->get_description(),
				'banner '=> $this->_plugin->getUrl().'assets/banners/banner-'.$gatewaySettings->get('icon').'.png'
			],
			WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
			$this->_plugin->getTemplatePath()
		);
	}
	
	/**
	 * Processes and saves options.
	 * If there is an error thrown, will continue to save 
	 * and validate fields, but will leave the erroring field out.
	 * It ignores the WC_Settings_API behavior.
	 *
	 * @since 2.0.0
	 * @return bool was anything saved?
	 */
	public function process_admin_options ()
	{ return false; }
}