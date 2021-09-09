<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

use WC_Cart;

/**
 * Apply discount to pix.
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
class Discount extends Initiable
{
	/**
	 * Startup method with all actions and
	 * filter to run.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function startup ()
	{
		WP::add_action(
			'wp_enqueue_scripts', 
			$this, 
			'enqueue_scripts'
		);

		WP::add_action(
			'woocommerce_cart_calculate_fees',
			$this,
			'add_discount',
			99
		);

		WP::add_action(
			'woocommerce_checkout_order_processed',
			$this,
			'update_order_data'
		);

		WP::add_filter(
			'woocommerce_gateway_title',
			$this,
			'payment_method_title',
			10,
			2
		);
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function enqueue_scripts() 
	{
		if ( \is_checkout() ) 
		{
			wp_enqueue_script(
				'pix-por-piggly-update-checkout',
				CoreConnector::plugin()->getUrl().'assets/js/update-checkout.min.js',
				['wc-checkout'],
				'2.0.0',
				true
			); 
		}
	}

	/**
	 * Display the discount in payment method title.
	 *
	 * @param string $title Gateway title.
	 * @param string $id Gateway ID.
	 * @since 1.2.0
	 * @return string
	 */
	public function payment_method_title ( $title, $id ) 
	{
		if ( !\is_checkout() && !WP::is_doing_ajax() ) 
		{ return $title; }

		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('discount', new KeyingBucket());

		$amount = $settings->get('value') ?? null;

		// Should not apply
		if ( !(!empty($amount) && $id === $this->_plugin->getName()) ) 
		{ return $title; }

		$apply = \wc_price($amount);

		switch ( $settings->get('type', 'PERCENT') )
		{
			case 'PERCENT':
				$apply = \wc_format_decimal($amount).'%';
				break;
		}

		$title .= ' <small class="pix-por-piggly--featured">(' . sprintf( $this->__translate('%s de desconto'), $apply ) . ')</small>';
		return $title;
	}

	/**
	 * Add discount.
	 *
	 * @filter pgly_wc_piggly_pix_discount_applied
	 * @param WC_Cart $cart Cart object.
	 * @since 2.0.0
	 * @return void
	 */
	public function add_discount( $cart ) 
	{
		if ( WP::is_pure_admin() || \is_cart() )
		{ return; }

		/** @var KeyingBucket $gatewaySettings */
		$settings = CoreConnector::settings()->get('discount', new KeyingBucket());

		$amount         = $settings->get('value') ?? null;
		$payment_method = WC()->session->get('chosen_payment_method');

		// Should not apply
		if ( !(!empty($amount) && $payment_method === $this->_plugin->getName()) ) 
		{ return; }

		$amount = \floatval($amount);
		$apply  = $amount;

		switch ( $settings->get('type', 'PERCENT') )
		{
			case 'PERCENT':
				$apply = \floatval( \wc_format_decimal(($cart->subtotal_ex_tax - $cart->discount_cart) * ($amount / 100)) );
				break;
		}

		// Apply filter to pix discount
		$final = \apply_filters( 'pgly_wc_piggly_pix_discount_applied', $apply, $cart );

		if ( $final <= 0 )
		{ return; }
  
		$cart->add_fee( 
			$settings->get('label', $this->__translate('Desconto Pix Aplicado')), 
			$final*-1, 
			false 
		);
	}

	/**
	 * Remove the discount in the payment method title.
	 *
	 * @param int $order_id Order ID.
	 * @since 2.0.0
	 * @return void
	 */
	public function update_order_data( $order_id ) 
	{
		$payment_method_title     = \get_post_meta( $order_id, '_payment_method_title', true );
		$new_payment_method_title = \preg_replace( '/<small class=\"pix-por-piggly--featured\">.*<\/small>/', '', $payment_method_title );

		// Save the new payment method title.
		\update_post_meta( $order_id, '_payment_method_title', $new_payment_method_title );
	}
}