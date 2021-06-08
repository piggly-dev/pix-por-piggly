<?php
namespace Piggly\WC\Pix\Discount;

use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Manipulate a WooCommerce coupon to apply
 * to pix payment gateway.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\Discount
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class ApplyDiscount
{
	/**
	 * Add all actions and filters to configure woocommerce
	 * gateways.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function init ()
	{
		$base = new self();

		WP::add_action('wp_enqueue_scripts', $base, 'enqueue_scripts');
		WP::add_action('woocommerce_cart_calculate_fees', $base, 'add_discount', 99);
		WP::add_action('woocommerce_checkout_order_processed', $base, 'update_order_data');
		WP::add_filter('woocommerce_gateway_title', $base, 'payment_method_title', 10, 2);
	}
	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function enqueue_scripts() 
	{
		if ( is_checkout() ) 
		{
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( \WC_PIGGLY_PIX_PLUGIN_NAME, \WC_PIGGLY_PIX_PLUGIN_URL.'assets/js/public/update-checkout' . $suffix . '.js', array( 'wc-checkout' ), \WC_PIGGLY_PIX_PLUGIN_VERSION );
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
		if ( !is_checkout() && !WP::is_doing_ajax() ) 
		{ return $title; }

		$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );
		$amount   = $settings['discount'] ?? null;

		if ( empty( $amount ) || $id !== 'wc_piggly_pix_gateway' )
		{ return $title; }

		if ( strstr( $amount, '%' ) ) 
		{ $value = str_replace( '%', '', floatval( wc_format_decimal( $amount ) ) ) . '%'; } 
		else 
		{ $value = wc_price( floatval( wc_format_decimal( $amount ) ) ); }

		$title .= ' <small class="wpgly-pix-featured">(' . sprintf( __( '%s de desconto', \WC_PIGGLY_PIX_PLUGIN_NAME ), $value ) . ')</small>';

		return $title;
	}

	/**
	 * Add discount.
	 *
	 * @param WC_Cart $cart Cart object.
	 * @since 1.2.0
	 * @since 1.3.14 Fix discount value
	 * @return void
	 */
	public function add_discount( $cart ) 
	{
		if ( WP::is_pure_admin() || is_cart() )
		{ return; }

		$settings       = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );
		$amount         = $settings['discount'] ?? null;
		$payment_method = WC()->session->get('chosen_payment_method');
		$applies        = !empty($amount) && $payment_method === 'wc_piggly_pix_gateway';

		if ( !$applies ) return;

		if ( strstr( $amount, '%' ) ) 
		{ 
			$discount = \str_replace('%', '', $amount);
			$discount = \floatval( wc_format_decimal( $discount ) );
			$discount = \floatval( wc_format_decimal(($cart->subtotal_ex_tax - $cart->discount_cart) * ($discount / 100)) ); 
		} 
		else 
		{ $discount = \floatval( wc_format_decimal( $amount ) ); }

		// Apply filter to pix discount
		$discount = apply_filters( 'wpgly_pix_discount', $discount );
  
		$cart->add_fee( 
			empty( $settings['discount_label'] ) ? __('Desconto Pix Aplicado', \WC_PIGGLY_PIX_PLUGIN_NAME) : $settings['discount_label'], 
			-$discount, 
			false 
		);
	}

	/**
	 * Remove the discount in the payment method title.
	 *
	 * @param int $order_id Order ID.
	 * @since 1.3.9
	 * @return void
	 */
	public function update_order_data( $order_id ) 
	{
		$payment_method_title     = get_post_meta( $order_id, '_payment_method_title', true );
		$new_payment_method_title = \preg_replace( '/<small class=\"wpgly-pix-featured\">.*<\/small>/', '', $payment_method_title );

		// Save the new payment method title.
		update_post_meta( $order_id, '_payment_method_title', $new_payment_method_title );
	}
}