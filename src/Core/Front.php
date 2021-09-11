<?php
namespace Piggly\WooPixGateway\Core;

use Exception;
use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\Core\Processors\ReceiptProcessor;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use WC_Order;

class Front extends Initiable
{
	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function startup ()
	{
		WP::add_action( 
			'woocommerce_receipt_'.CoreConnector::plugin()->getName(), 
			$this, 
			'payment_page', 
			5, 
			1 
		);

		WP::add_action( 
			'woocommerce_account_pgly-pix-payment_endpoint', 
			$this, 
			'pay'
		);

		WP::add_action( 
			'woocommerce_account_pgly-pix-receipt_endpoint', 
			$this, 
			'receipt'
		);

		if ( \is_wc_endpoint_url( 'order-pay' ) 
				|| !empty(get_query_var('pgly-pix-payment'))
				|| !empty(get_query_var('pgly-pix-receipt'))
				|| \is_checkout() )
		{ $this->wp_enqueue(); }
	}

	/**
	 * Open template to edit payment.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function pay () 
	{
		if ( empty(\get_query_var('pgly-pix-payment')) )
		{
			?>
			<div class="woocommerce">
			<div class="woocommerce-notices-wrapper"></div>
				<ul class="woocommerce-error" role="alert">
					<?=CoreConnector::__translate('Nenhum pedido solicitado.')?>
				</ul>
			</div>
			<?php
			return;
		}

		$order = \wc_get_order(\get_query_var('pgly-pix-payment'));
		$allowed = $order === false ? false : \get_current_user_id() === $order->get_customer_id();
		$allowed = $allowed && $order->get_payment_method() === CoreConnector::plugin()->getName();
		
		if ( $order )
		{ $allowed = $allowed && !PixGateway::order_not_waiting_payment($order); }

		if ( !$allowed )
		{
			?>
			<div class="woocommerce">
			<div class="woocommerce-notices-wrapper"></div>
				<ul class="woocommerce-error" role="alert">
					<?=CoreConnector::__translate('Pedido indisponível.')?>
				</ul>
			</div>
			<?php
			return;
		}
		
		$this->payment_page($order);
	} 
	
	/**
	 * Open the payment page.
	 *
	 * @param WC_Order|integer $order_id
	 * @param boolean $echo
	 * @since 2.0.0
	 * @return void
	 */
	public function payment_page ( $order_id, bool $echo = true )
	{
		try
		{
			$order = $order_id instanceof WC_Order ? $order_id : \wc_get_order($order_id);
			$pix   = null;

			// Return if $order not found.
			if ( !$order )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('O pedido solicitado não foi encontrado. Entre em contato com o suporte.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('O pedido solicitado não foi encontrado. Entre em contato com o suporte.').'</ul></div>'; }	
			}

			$txid  = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

			if ( !empty($txid) )
			{ $pix = (new PixRepo($this->_plugin))->byId($txid); }

			if ( empty($pix) )
			{ $pix = (new PixGateway())->recreate_pix($order); }

			if ( $pix->isExpired() || $pix->isStatus(PixEntity::STATUS_CANCELLED) || $order->has_status('cancelled') )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix indisponível para pagamento, ele foi cancelado ou expirado.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix indisponível para pagamento, ele foi cancelado ou expirado.').'</ul></div>'; }	
			}
			else if ( $pix->isPaid() || PixGateway::order_not_waiting_payment($order) )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-message" role="alert">'.CoreConnector::__translate('O pedido associado ao Pix já foi pago. Não há mais nada a ser feito.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-message" role="alert">'.CoreConnector::__translate('O pedido associado ao Pix já foi pago. Não há mais nada a ser feito.').'</ul></div>'; }	
			}

			$settings = CoreConnector::settings();

			if ( empty($order->get_user_id()) )
			{ $settings->get('receipts')->set('receipt_page', false); }

			\wc_get_template(
				'html-woocommerce-payment-instructions.php',
				array(
					'pix' => $pix,
					'order' => $order,
					'instructions' => str_replace('{{order_number}}', $order->get_order_number(), $settings->get('gateway')->get('instructions')),
					'receipt_page' => $settings->get('receipts')->get('receipt_page', true),
					'whatsapp_number' => $settings->get('receipts')->get('whatsapp_number', true),
					'whatsapp_message' => str_replace('{{order_number}}', $order->get_order_number(), $settings->get('receipts')->get('whatsapp_message', true)),
					'telegram_number' => str_replace('{{order_number}}', $order->get_order_number(), $settings->get('receipts')->get('telegram_number', true)),
					'telegram_message' => str_replace('{{order_number}}', $order->get_order_number(), $settings->get('receipts')->get('telegram_message', true)),
					'shows_qrcode' => $settings->get('gateway')->get('shows_qrcode', false),
					'shows_copypast' => $settings->get('gateway')->get('shows_copypast', false),
					'shows_manual' => $settings->get('gateway')->get('shows_manual', false),
					'shows_amount' => $settings->get('gateway')->get('shows_amount', false),
					'shows_receipt' => $settings->get('receipts')->get('shows_receipt', 'up')
				),
				WC()->template_path().\dirname(CoreConnector::plugin()->getBasename()).'/',
				CoreConnector::plugin()->getTemplatePath().'woocommerce/'
			);
		}
		catch ( Exception $e )
		{
			$this->debug()->force()->error(
				\sprintf(
					CoreConnector::__translate('Não foi possível carregar o pagamento do pedido: %s'), 
					$e->getMessage()
				)
			); 

			if ( $echo )
			{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Ocorreu um erro indeterminado. Entre em contato com o suporte.').'</ul></div>'; return null; }
			else 
			{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Ocorreu um erro indeterminado. Entre em contato com o suporte.').'</ul></div>'; }
		}
	}

	/**
	 * Open template to edit payment.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function receipt () 
	{
		if ( empty(\get_query_var('pgly-pix-receipt')) )
		{
			?>
			<div class="woocommerce">
			<div class="woocommerce-notices-wrapper"></div>
				<ul class="woocommerce-error" role="alert">
					<?=CoreConnector::__translate('Nenhum pedido solicitado.')?>
				</ul>
			</div>
			<?php
			return;
		}

		$order = \wc_get_order(\get_query_var('pgly-pix-receipt'));
		$allowed = $order === false ? false : \get_current_user_id() === $order->get_customer_id();
		$allowed = $allowed && $order->get_payment_method() === CoreConnector::plugin()->getName();
		
		if ( $order )
		{ $allowed = $allowed && !PixGateway::order_not_waiting_payment($order); }

		if ( !$allowed )
		{
			?>
			<div class="woocommerce">
			<div class="woocommerce-notices-wrapper"></div>
				<ul class="woocommerce-error" role="alert">
					<?=CoreConnector::__translate('Pedido indisponível.')?>
				</ul>
			</div>
			<?php
			return;
		}
		
		$this->receipt_page($order);
	} 

	/**
	 * Add pix form template when call the shortcode.
	 * Requires "key" query string parameter with order_key.
	 * 
	 * @param WC_Order|integer $order_id
	 * @param boolean $echo
	 * @since 1.3.0
	 * @since 1.3.11 Melhorias e redirecionamento
	 * @return string|null
	 */
	public function receipt_page ($order_id, bool $echo = true) : ?string
	{
		try
		{
			$order = $order_id instanceof WC_Order ? $order_id : \wc_get_order($order_id);
			$pix   = null;

			// Return if $order not found.
			if ( !$order )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('O pedido solicitado não foi encontrado. Entre em contato com o suporte.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('O pedido solicitado não foi encontrado. Entre em contato com o suporte.').'</ul></div>'; }	
			}

			$txid  = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

			if ( !empty($txid) )
			{ $pix = (new PixRepo($this->_plugin))->byId($txid); }

			if ( empty($pix) )
			{ 
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix não encontrado. Entre em contato com o suporte.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix não encontrado. Entre em contato com o suporte.').'</ul></div>'; }
			}

			if ( $pix->isExpired() || $pix->isStatus(PixEntity::STATUS_CANCELLED) )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix indisponível para pagamento, ele foi cancelado ou expirado.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pix indisponível para pagamento, ele foi cancelado ou expirado.').'</ul></div>'; }	
			}
			else if ( $pix->isPaid() )
			{
				if ( $echo )
				{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-message" role="alert">'.CoreConnector::__translate('O pagamento para o Pix já foi identificado, não é necessário o comprovante.').'</ul></div>'; return null; }
				else 
				{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-message" role="alert">'.CoreConnector::__translate('O pagamento para o Pix já foi identificado, não é necessário o comprovante.').'</ul></div>'; }	
			}

			$data = [];

			$data['sent']      = $_SERVER['REQUEST_METHOD'] === 'POST';
			$data['link']      = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";		
			$data['error']     = false;
			$data['permalink'] = false;
			$data['pix']       = $pix;

			if ( $data['sent'] )
			{ 
				try
				{ $this->validateReceiptForm($pix); }
				catch ( Exception $e )
				{ 
					$this->debug()->error($e->getMessage());
					$data['error'] = $e->getMessage(); 
				}

				/** @var KeyingBucket $settings */
				$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

				if ( !empty($settings->get('after_receipt')) )
				{ $data['permalink'] = \get_permalink($settings->get('after_receipt')); }
			} 
						
			$data['_nonce'] = wp_create_nonce('pgly-pix-receipt');

			if ( $echo ) 
			{
				\wc_get_template(
					'html-woocommerce-receipt-form.php',
					$data,
					WC()->template_path().\dirname(CoreConnector::plugin()->getBasename()).'/',
					CoreConnector::plugin()->getTemplatePath().'woocommerce/'
				);

				return null;
			}
			else
			{
				return \wc_get_template_html(
					'html-woocommerce-receipt-form.php',
					$data,
					WC()->template_path().\dirname(CoreConnector::plugin()->getBasename()).'/',
					CoreConnector::plugin()->getTemplatePath().'woocommerce/'
				);
			}
		}
		catch ( Exception $e )
		{
			$this->debug()->force()->error(
				\sprintf(
					CoreConnector::__translate('Não foi possível carregar o pagamento do pedido: %s'), 
					$e->getMessage()
				)
			); 

			if ( $echo )
			{ echo '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Ocorreu um erro indeterminado. Entre em contato com o suporte.').'</ul></div>'; return null; }
			else 
			{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Ocorreu um erro indeterminado. Entre em contato com o suporte.').'</ul></div>'; }
		}
	}

	/**
	 * Validate and process receipt form data.
	 *
	 * @param PixEntity $pix
	 * @since 2.0.0
	 * @return void
	 * @throws Exception
	 */
	protected function validateReceiptForm ( PixEntity $pix )
	{
		// processing data
		$nonce = filter_input( INPUT_POST, 'pgly_pix_nonce', \FILTER_SANITIZE_STRING );
		
		if ( !wp_verify_nonce( $nonce, 'pgly-pix-receipt' ) )
		{ throw new Exception($this->__translate('Não foi possível validar o formulário no momento.')); }

		if ( empty($_FILES['pgly_pix_receipt']))
		{ throw new Exception($this->__translate('O comprovante não foi enviado.')); }

		if ( empty($pix))
		{ throw new Exception($this->__translate('O pix não pode ser identificado.')); }

		(new ReceiptProcessor())->run($pix);
	}

	/**
	 * Enqueue JS and CSS scripts.
	 *
	 * @internal When update the CSS/JS, update version.
	 * @since 2.0.0
	 * @return void
	 */
	protected function wp_enqueue ()
	{
		wp_enqueue_style(
			'pix-por-piggly-front-css',
			CoreConnector::plugin()->getUrl().'assets/css/pix-por-piggly.front.css',
			[],
			'2.0.0'
		);

		wp_enqueue_script(
			'pix-por-piggly-front-js',
			CoreConnector::plugin()->getUrl().'assets/js/pgly-pix-por-piggly.front.js',
			[],
			'2.0.0',
			true
		); 
	}

	/**
	 * Check if can edit order.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return boolean
	 */
	public static function canEditOrder ( WC_Order $order )
	{
		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		$allowed = $order === false ? false : \get_current_user_id() === $order->get_customer_id();
		$allowed = $allowed && $order->get_payment_method() === CoreConnector::plugin()->getName();
		$allowed = $allowed && $order->has_status(['pending', $settings->get('receipt_status', 'on-hold')]);
		return $allowed;
	}
}