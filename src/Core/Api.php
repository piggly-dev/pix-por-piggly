<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;

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

		// Pix webhook
		WP::add_action(
			'woocommerce_api_pgly-pix-webhook', 
			$this, 
			'api_callback'
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
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return; }

		// Get payload
		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( !empty($pix) )
		{
			$pix = (new PixRepo(CoreConnector::plugin()))->byId($pix);
			$export = $pix->getPayload();
		}
		else
		{
			$pix = $order->get_meta('_wc_piggly_pix');

			if ( !empty($pix) )
			{
				$data = $order->get_meta('_wc_piggly_pix');

				$export = [
					'code' => $data['pix_code'],
					'qr' => $data['pix_qr'],
					'key_value' => $data['key_value'],
					'key_type' => Parser::getAlias($data['key_type']),
					'identifier' => $data['identifier'],
					'store_name' => $data['store_name'],
					'merchant_name' => $data['merchant_name'],
					'merchant_city' => $data['merchant_city'],
					'_version' => 'v1'
				];
			}
		}

		if ( empty($order_data['payment_details']) )
		{ $order_data['payment_details'] = []; }

		$order_data['payment_details'] = array_merge(
			$order_data['payment_details'], [
				'pix' => $export
			]
		);

		return apply_filters( 'pgly_wc_piggly_pix_after_create_legacy_api_response', $order_data, $order, $order->get_id() );
	}

	/**
	 * Add pix data to REST API response.
	 *
	 * @filter pgly_wc_piggly_pix_after_create_api_response
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

		// Check payment id as pix
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return; }

		// Get payload
		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( !empty($pix) )
		{
			$pix = (new PixRepo(CoreConnector::plugin()))->byId($pix);
			$export = $pix->getPayload();
		}
		else
		{
			$pix = $order->get_meta('_wc_piggly_pix');

			if ( !empty($pix) )
			{
				$data = $order->get_meta('_wc_piggly_pix');

				$export = [
					'code' => $data['pix_code'],
					'qr' => $data['pix_qr'],
					'key_value' => $data['key_value'],
					'key_type' => Parser::getAlias($data['key_type']),
					'identifier' => $data['identifier'],
					'store_name' => $data['store_name'],
					'merchant_name' => $data['merchant_name'],
					'merchant_city' => $data['merchant_city'],
					'_version' => 'v1'
				];
			}
		}

		$response->data['pix'] = $export;
		return apply_filters( 'pgly_wc_piggly_pix_after_create_api_response', $response, $order, $order->get_id() );
	}

	/**
	 * Api callback to pix webhook.
	 * 
	 * @action pgly_wc_piggly_pix_webhook
	 * @since 2.0.0
	 * @return void
	 */
	public function api_callback ()
	{ do_action('pgly_wc_piggly_pix_webhook', CoreConnector::settings()->get('account')->get('bank', 0)); }
}