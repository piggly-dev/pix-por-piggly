<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

use WC_Order;

/**
 * Manages all plugin endpoints actions and filters.
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
class Endpoints extends Initiable
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
			'init', 
			$this, 
			'add_endpoints'
		);
		
		WP::add_filter(
			'woocommerce_my_account_my_orders_actions', 
			$this, 
			'order_actions',
			10,
			2
		);
	}

	/**
	 * Add endpoints to wordpress.
	 * Must update the Wordpress Permalinks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function add_endpoints ()
	{ 
		add_rewrite_endpoint('pgly-pix-payment', EP_PAGES); 
		add_rewrite_endpoint('pgly-pix-receipt', EP_PAGES); 
	}

	/**
	 * Add order actions to Orders page.
	 *
	 * @param array $actions
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return void
	 */
	public function order_actions ( $actions, $order )
	{
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return $actions; }

		// Only when payment is not confirmed yet
		if ( $order->has_status(['pending']) )
		{
			$actions['view-pix-payment'] = [
				'url' => static::getPaymentUrl($order),
				'name' => CoreConnector::__translate('Pagar o Pix')
			];

			$actions['send-pix-receipt'] = [
				'url' => static::getReceiptUrl($order),
				'name' => CoreConnector::__translate('Enviar Comprovante')
			];
		}

		return $actions;
	}

	/**
	 * Get the endpoint to pay order.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @since 2.0.2 Fixed endpoint to my-account
	 * @return string
	 */
	public static function getPaymentUrl ( WC_Order $order ) : string
	{ return \wc_get_endpoint_url('pgly-pix-payment', $order->get_id(), \wc_get_page_permalink('myaccount')); }

	/**
	 * Get the endpoint to send receipt.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @since 2.0.2 Fixed endpoint to my-account
	 * @return string
	 */
	public static function getReceiptUrl ( WC_Order $order ) : string
	{ return \wc_get_endpoint_url('pgly-pix-receipt', $order->get_id(), \wc_get_page_permalink('myaccount')); }
}