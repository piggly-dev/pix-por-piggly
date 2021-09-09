<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;

use WC_Order;

/**
 * Manages all shortcodes.
 * 
 * @package \Piggly\WooBdmGateway
 * @subpackage \Piggly\WooBdmGateway\Core
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Shortcode extends Initiable
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
		// Create shortcode to pix data
		add_shortcode( 'pix-por-piggly', array($this, 'pix_shortcode') );
		// Create shortcode to pix form
		add_shortcode( 'pix-por-piggly-form', array($this, 'pix_form_shortcode') );
	}

	/**
	 * Add pix template when call the shortcode.
	 * 
	 * @deprecated 2.0.0
	 * @param array $attrs
	 * @since 2.0.0
	 * @return void
	 */
	public function pix_shortcode ( $attrs )
	{
		global $wp;

		$attrs = shortcode_atts( array('order_id' => null), $attrs );
		$order = $attrs['order_id'];

		if ( empty($order) )
		{
			$order_id  = $wp->query_vars['order'] ?? $wp->query_vars['order-received'] ?? null;
			$order_key = \wc_clean( \wp_unslash( $_GET['key'] ) );

			if ( !empty($order_id) )
			{ $order = \wc_get_order($order_id); }
			else if ( !empty($order_key) )
			{ $order = \wc_get_order((int)\wc_get_order_id_by_order_key($order_key)); }

			if ( $order === false || empty($order) )
			{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }
		}
		else
		{
			// Get order
			$order = \wc_get_order((int)$order);

			if ( $order === false || empty($order) )
			{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }
		}

		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		$allowed = $order === false ? false : \get_current_user_id() === $order->get_customer_id();
		$allowed = $allowed && $order->get_payment_method() === CoreConnector::plugin()->getName();
		$allowed = $allowed && $order->has_status(['pending', $settings->get('receipt_status', 'on-hold')]);

		// If order is not payment waiting, return...
		if ( !$allowed )
		{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }

		// Load payload
		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( empty($pix) )
		{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }

		$pix = (new PixRepo($this->_plugin))->byId($pix);

		return \wc_get_template_html(
			'html-woocommerce-payment-instructions.php',
			[
				'pix' => $pix,
				'order' => $order
			],
			WC()->template_path().\dirname(CoreConnector::plugin()->getBasename()).'/',
			CoreConnector::plugin()->getTemplatePath().'woocommerce/'
		);
	}
	
	/**
	 * Add pix form template when call the shortcode.
	 * Requires "key" query string parameter with order_key.
	 * 
	 * @deprecated 2.0.0
	 * @param array $attrs
	 * @since 2.0.0
	 * @return void
	 */
	public function pix_form_shortcode ( $attrs )
	{
		$order_key = filter_input( INPUT_GET, 'key', \FILTER_SANITIZE_STRING );

		if ( empty($order_key) )
		{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }

		// Get order
		$order = \wc_get_order((int)\wc_get_order_id_by_order_key($order_key));
		
		if ( !$order )
		{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }

		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		$allowed = $order === false ? false : \get_current_user_id() === $order->get_customer_id();
		$allowed = $allowed && $order->get_payment_method() === CoreConnector::plugin()->getName();
		$allowed = $allowed && $order->has_status(['pending', $settings->get('receipt_status', 'on-hold')]);

		if ( !$allowed )
		{ return '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div><ul class="woocommerce-error" role="alert">'.CoreConnector::__translate('Pedido indisponível. Contate o suporte.').'</ul></div>'; }

		return (new Front(CoreConnector::plugin()))->receipt_page($order, false);
	}
}