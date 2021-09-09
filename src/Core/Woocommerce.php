<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use WC_Order;

/**
 * Manages all woocommerce actions and filters.
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
class Woocommerce extends Initiable
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
		// Action to change status behavior
		WP::add_filter( 
			'wc_order_statuses', 
			$this, 
			'add_order_statuses' 
		);

		WP::add_filter(
			'woocommerce_payment_gateways', 
			$this, 
			'add_gateway'
		);

		WP::add_filter(
			'woocommerce_cancel_unpaid_order',
			$this,
			'unpaid_orders',
			99,
			2
		);

		/** @var KeyingBucket $settings */
		$settings = CoreConnector::settings()->get('orders', new KeyingBucket());

		$processing_actions = [
			'woocommerce_order_status_'.$settings->get('paid_status', 'processing'),
			'woocommerce_order_status_completed',
			'woocommerce_payment_complete'
		];

		foreach ( $processing_actions as $actions )
		{
			WP::add_action(
				$actions,
				$this,
				'payment_complete'
			);
		}

		WP::add_action(
			'woocommerce_order_status_cancelled',
			$this,
			'payment_cancelled'
		);
	}

	/**
	 * Add gateway to Woocommerce.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function add_gateway ( array $gateways )
	{
		array_push( $gateways, PixGateway::class );
		return $gateways;
	}

	/**
	 * Update pix to paid status when order is complete.
	 *
	 * @param integer $order_id
	 * @since 2.0.0
	 * @return void
	 */
	public function payment_complete ( $order_id )
	{
		$order = new WC_Order($order_id);

		if ( empty($order) )
		{ return; }
		
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return; }

		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( empty($pix) )
		{ return; }

		$pix = (new PixRepo($this->_plugin))->byId($pix);

		if ( !$pix->isStatus(PixEntity::STATUS_PAID) )
		{ $pix->updateStatus(PixEntity::STATUS_PAID); }
	}
	
	/**
	 * Update pix to cancelled status when order is cancelled.
	 *
	 * @param integer $order_id
	 * @since 2.0.0
	 * @return void
	 */
	public function payment_cancelled ( $order_id )
	{
		$order = new WC_Order($order_id);

		if ( empty($order) )
		{ return; }
		
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return; }

		$pix = $order->get_meta('_pgly_wc_piggly_pix_latest_pix');

		if ( empty($pix) )
		{ return; }

		$pix = (new PixRepo($this->_plugin))->byId($pix);

		if ( !$pix->isStatus(PixEntity::STATUS_CANCELLED) )
		{ $pix->updateStatus(PixEntity::STATUS_CANCELLED); }
	}

	/**
	 * Return if must cancel order when unpaid.
	 * Order will always have the pending status.
	 *
	 * @param boolean $must_cancel
	 * @param WC_Order $order
	 * @return boolean
	 */
	public function unpaid_orders ( $must_cancel, $order ) : bool
	{
		if ( $order->get_payment_method() !== CoreConnector::plugin()->getName() )
		{ return $must_cancel; }
		
		$pix = (new PixRepo($this->_plugin))->latestStatus($order, ['created']);

		// No transaction was created
		if ( empty($pix) )
		{ return true; }

		if ( !$pix->isExpired() )
		{ return false; }

		$pix->updateStatus(PixEntity::STATUS_CANCELLED);
		return true;
	}

	/**
	 * Add wc-pix-receipt to order status.
	 * 
	 * @since 2.0.0
	 * @param array $order_statuses
	 * @return array
	 */
	public function add_order_statuses ( $order_statuses )
	{
		$order_statuses['wc-pix-receipt'] = 'Comprovante Pix Recebido';
	  	return $order_statuses;
	}

	/**
	 * Get all available woocommerce status.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public static function getAvailableStatuses () : array
	{ return \wc_get_order_statuses(); }
}