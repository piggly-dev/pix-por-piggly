<?php
namespace Piggly\WooPixGateway\Core\Gateway;

use Exception;
use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

use WC_Order;
use WC_Payment_Gateway;
use WP_Error;

/**
 * The main gateway woocommerce behavior.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway\Core\Gateway
 * @version 2.0.0
 * @since 2.0.0
 * @category Gateway
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class PixGateway extends WC_Payment_Gateway
{
	/**
	 * Startup payment gateway method.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function __construct ()
	{
		// Gateway settings
		$this->id                 = CoreConnector::plugin()->getName();
		$this->method_title       = CoreConnector::__translate('Pix');
		$this->method_description = CoreConnector::__translate('Habilite o pagamento de pedidos via Pix. Este plugin automaticamente adiciona as instruções Pix na Página de Obrigado e na Página do Pedido.');
		$this->supports           = ['products', 'refunds'];
		$this->has_fields         = false;

		$this->init_settings();
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
		$gatewaySettings = CoreConnector::settings()->get('gateway', new KeyingBucket());
		/** @var KeyingBucket $gatewaySettings */
		$accountSettings = CoreConnector::settings()->get('account', new KeyingBucket());

		$this->enabled = empty($accountSettings->get('key_value')) ? 'no' : ($gatewaySettings->get('enabled', false) ? 'yes' : 'no');
		$this->title = $gatewaySettings->get('title');
		$this->description = $gatewaySettings->get('description');
		$this->icon = apply_filters('woocommerce_gateway_icon', CoreConnector::plugin()->getUrl().'assets/images/'.$gatewaySettings->get('icon').'.png');
	}

	/**
	 * Output the gateway settings screen.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function admin_options ()
	{ require_once(CoreConnector::plugin()->getTemplatePath().'/admin/redirection.php'); }

	/**
	 * Recreate pix to order.
	 *
	 * @param WC_Order|integer $order_id
	 * @since 2.0.0
	 * @return PixEntity|null
	 */
	public function recreate_pix ( $order_id ) : ?PixEntity
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		$order = $order_id instanceof WC_Order ? $order_id : new WC_Order($order_id);
		$pix   = PixEntity::mount($order);

		if ( $order->has_status(['cancelled']) )
		{ return null; }
		
		// Save as last order transaction
		$order->update_meta_data('_pgly_wc_piggly_pix_latest_pix', $pix->getTxid());
		$order->save();

		// Order was cancelled
		if ( static::order_not_waiting_payment($order) )
		{ $pix->setStatus(PixEntity::STATUS_PAID)->save(); }
		else if ( $order->has_status([$settings->get('receipt_status', 'on-hold')]) )
		{ $pix->updateStatus(PixEntity::STATUS_WAITING); }

		return $pix;
	}

	/**
	 * Process Payment.
	 *
	 * @param int $order_id Order ID.
	 * @since 2.0.0 Removed pgly_pix_after_process_payment action
	 * @return array
	 */
	public function process_payment ( $order_id )
	{
		global $woocommerce;
		
		WC()->mailer();

		$waiting_status = CoreConnector::settings()->get('orders', new KeyingBucket())->get('waiting_status', 'pending');
		$order = new WC_Order($order_id);
		$pix   = PixEntity::mount($order);

		// Save as last order transaction
		$order->update_meta_data('_pgly_wc_piggly_pix_latest_pix', $pix->getTxid());
		
		// Mark as pending we're awaiting the payment)
		$order->update_status( 
			apply_filters( 
				'pgly_wc_piggly_pix_pending_status',
				$waiting_status, 
				$order->get_id(), 
				$order 
			)
		);

		$order->add_order_note(
			\sprintf(
				CoreConnector::__translate('Processo de pagamento via Pix iniciado. ID da transação %s criado.'),
				$pix->getTxid()
			)
		);

		if ( CoreConnector::settings()->get('orders')->get('decrease_stock', false) )
		{ \wc_maybe_reduce_stock_levels($order_id); }

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Pix created
		\do_action('pgly_wc_piggly_pix_to_pay', $pix, $order, $order_id);

		// Return checkout payment url
		return array(
			'result' 	=> 'success',
			'redirect'	=> $waiting_status === 'pending' ? $order->get_checkout_payment_url(true) : $order->get_checkout_order_received_url(),
			'txid'      => $pix->getTxid(),
			'pix'       => $pix
		);
	}

	/**
	 * Process pending pix.
	 *
	 * @action pgly_wc_piggly_pix_close_to_expires
	 * @action woocommerce_payment_complete
	 * @filter woocommerce_payment_complete_order_status
	 * @param PixEntity $pix
	 * @since 2.0.0
	 * @return boolean
	 */
	public function process_pending ( PixEntity $pix ) : bool
	{
		CoreConnector::debugger()->debug(\sprintf('Pix `%s` iniciando o processamento...', $pix->getTxid()));

		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());
		$order = $pix->getOrder();

		// Order does not exists
		if ( empty($order) )
		{ return false; }

		if ( $pix->isStatus(PixEntity::STATUS_CREATED) || 
				($pix->isStatus(PixEntity::STATUS_WAITING) && static::order_waiting_payment($order)) )
		{
			// Run action when closest to expires
			if ( $pix->isClosestToExpires()
					&& empty($pix->getMetadata()['notify_close_to_expires']) )
			{ 
				CoreConnector::debugger()->debug(\sprintf('O Pix %s está próximo de ser expirado.', $pix->getTxid()));
				
				do_action('pgly_wc_piggly_pix_close_to_expires', $pix);
				
				try
				{ $pix->appendMetadata(['notify_close_to_expires'=>true])->save(); }
				catch ( Exception $e )
				{}

				return false;
			}
			
			// Check if is expired
			if ( $pix->isExpired() )
			{ 
				// Cancel order when needed
				if ( !static::order_not_waiting_payment($order)
						&& !$order->has_status(['cancelled'])
						&& $settings->get('cancel_when_expired', false) )
				{ $order->update_status('cancelled'); $order->save(); }

				return false; 
			}
		}
		else if ( $pix->isStatus(PixEntity::STATUS_EXPIRED) 
						|| $pix->isStatus(PixEntity::STATUS_CANCELLED) )
		{ return false; }

		// Order was cancelled
		if ( $order->has_status(['cancelled']) )
		{ 
			$pix->updateStatus(PixEntity::STATUS_CANCELLED); 
			return false;
		}

		// Pix is paid
		if ( $pix->isPaid() && static::order_waiting_payment($order) )
		{
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

		return false;
	}

	/**
	 * Process refund.
	 *
	 * @filter pgly_wc_piggly_pix_refund
	 * @param int $order_id Order ID.
	 * @param float|null $amount Refund amount.
	 * @param string $reason Refund reason.
	 * @since 2.0.19
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) 
	{		
		$order = new WC_Order($order_id);
		$pix   = (new PixRepo(CoreConnector::plugin()))->byId($order->get_meta('_pgly_wc_piggly_pix_latest_pix'));

		if ( empty($pix) )
		{ return new WP_Error(1, CoreConnector::__translate('Pix não localizado.')); }

		if ( empty($amount) )
		{ return new WP_Error(1, CoreConnector::__translate('O valor do reembolso não foi preenchido.')); }

		if ( $amount > $pix->getAmount() )
		{ return new WP_Error(1, CoreConnector::__translate('O valor do reembolso é maior que o valor do Pix.')); }

		$e2eid = $pix->getE2eid() ?? $order->get_transaction_id();
		
		if ( !$pix->isPaid() || empty($e2eid) )
		{ return new WP_Error(1, CoreConnector::__translate('Pix não processado.')); }

		return apply_filters( 
			'pgly_wc_piggly_pix_refund',
			false,
			$pix, 
			$amount,
			$reason,
			$order 
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
		$gatewaySettings = CoreConnector::settings()->get('gateway', new KeyingBucket());

		if ( !$gatewaySettings->get('advanced_description', false) )
		{
			$description = $this->get_description();

			if ( $description ) 
			{ echo esc_html( $description ); }

			return;
		}

		if ( !wp_style_is('pix-por-piggly-front-css') )
		{
			wp_enqueue_style(
				'pix-por-piggly-front-css',
				CoreConnector::plugin()->getUrl().'assets/css/pix-por-piggly.front.css',
				[],
				'2.0.0'
			);
		}
		
		$banner = CoreConnector::plugin()->getUrl().'assets/images/banner-'.$gatewaySettings->get('icon').'.png';
		
		\wc_get_template(
			'html-woocommerce-instructions.php',
			[	
				'pix_banner '=> $banner,
				'description' => $this->get_description(),
			],
			WC()->template_path().\dirname(CoreConnector::plugin()->getBasename()).'/',
			CoreConnector::plugin()->getTemplatePath().'woocommerce/'
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

	/**
	 * Update a single option.
	 *
	 * @since 2.0.0
	 * @param string $key Option key.
	 * @param mixed  $value Value to set.
	 * @return bool was anything saved?
	 */
	public function update_option( $key, $value = '' ) 
	{
		/** @var KeyingBucket $gatewaySettings */
		$settings = CoreConnector::settings()->get('gateway', new KeyingBucket());
		
		if ( $key === 'enabled' )
		{ $value = \filter_var($value, \FILTER_VALIDATE_BOOL); }

		$settings->set($key, $value);
		
		CoreConnector::settingsManager()->save();
		return true;
	}

	/**
	 * Get option from DB.
	 *
	 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key Option key.
	 * @param  mixed  $empty_value Value when empty.
	 * @return string The value specified for the option or a default value for the option.
	 */
	public function get_option( $key, $empty_value = null ) {
		/** @var KeyingBucket $gatewaySettings */
		$settings = CoreConnector::settings()->get('gateway', new KeyingBucket());
		$value = $settings->get($key, $empty_value);

		if ( \is_bool($value) )
		{ $value = $value ? 'yes' : 'no'; }

		return $value;
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_option_key() 
	{ return 'wc_piggly_pix_settings'; }

	/**
	 * Order is not cancelled or waiting payment anymore.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return boolean
	 */
	public static function order_not_waiting_payment ( WC_Order $order ) : bool
	{
		/** @var KeyingBucket $gatewaySettings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		return !$order->has_status([
			$settings->get('waiting_status', 'pending'), 
			$settings->get('receipt_status', 'on-hold'),
			'pix-receipt'
		]);
	}

	/**
	 * Order is not cancelled or waiting payment anymore.
	 *
	 * @param WC_Order $order
	 * @since 2.0.19
	 * @return boolean
	 */
	public static function order_waiting_payment ( WC_Order $order ) : bool
	{
		/** @var KeyingBucket $gatewaySettings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		return $order->has_status([
			$settings->get('waiting_status', 'pending'), 
			$settings->get('receipt_status', 'on-hold'),
			'pending',
			'on-hold',
			'pix-receipt'
		]);
	}
}