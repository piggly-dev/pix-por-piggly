<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Processors\PixProcessor;
use Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\Wordpress\Core\WP;

use WC_Order;
use WP_Post;

class Api extends Initiable
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
		WP::add_filter(
			'woocommerce_api_order_response',
			$this,
			'api_order_response',
			10,
			2
		);	
		
		WP::add_filter(
			'woocommerce_rest_prepare_shop_order', 
			$this, 
			'api_rest_prepare', 
			99, 
			2
		);

		WP::add_filter(
			'woocommerce_rest_prepare_shop_order_object', 
			$this, 
			'api_rest_prepare', 
			99, 
			2
		);	
	}

	/**
	 * Add pix data to Woocommerce legacy api response.
	 *
	 * @param array $order_data
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return array
	 */
	public function api_order_response ( $order_data, $order )
	{
		$order = $order instanceof WC_Order ? $order : new WC_Order($order);

		// Check payment id as pix
		if ( $order->get_payment_method() !== $this->_plugin->getName() )
		{ return $order_data; }

		// Get payload
		$payload = (new PixProcessor($this->_plugin))->get($order);

		if ( !$payload->hasData() )
		{ return $order_data; }

		if ( empty($order_data['payment_details']) )
		{ $order_data['payment_details'] = []; }

		$order_data['payment_details'] = array_merge(
			$order_data['payment_details'], [
				'pix' => $payload->export()
			]
		);

		return apply_filters( 'wpgly_woo_pix_gateway_after_create_legacy_api_response', $order_data, $order, $order->get_id() );
	}

	/**
	 * Add pix data to REST API response.
	 *
	 * @filter wpgly_woo_pix_gateway_after_create_api_response
	 * @param WP_REST_Response $response
	 * @param WP_Post|WC_Data $object
	 * @since 2.0.0
	 * @return WP_REST_Response
	 */
	public function api_rest_prepare ( $response, $object )
	{
		$id = $object instanceof WP_Post ? $object->ID : (\method_exists($object, 'get_id') ? $object->get_id() : $object);
		
		if ( \is_int($id) )
		{ return $response; }
		
		$order = new WC_Order($id);

		// Get payload
		$payload = (new PixProcessor($this->_plugin))->get($order);

		if ( !$payload->hasData() )
		{ return $response; }

		$response->data['pix'] = $payload->export();
		return apply_filters( 'wpgly_woo_pix_gateway_after_create_api_response', $response, $order, $order->get_id() );
	}
}