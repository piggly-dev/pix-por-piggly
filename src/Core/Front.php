<?php
namespace Piggly\WooPixGateway\Core;

use Piggly\WooPixGateway\Core\Processors\PixProcessor;
use Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\Wordpress\Core\WP;
use Piggly\Wordpress\Settings\KeyingBucket;

use WC_Order;

class Front extends Initiable
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
		// This action hook loads the thank you page
		WP::add_action( 
			'woocommerce_thankyou_'.$this->_plugin->getName(), 
			$this, 
			'thankyou_page', 
			5, 
			1 
		);
		
		// Add method instructions in order details page 
		WP::add_action( 
			'woocommerce_order_details_before_order_table', 
			$this, 
			'page_instructions', 
			5, 
			1
		);

		// Customer Emails
		WP::add_action( 
			'woocommerce_email_'.$this->email_position.'_order_table', 
			$this, 
			'email_instructions', 
			10, 
			4 
		);
	}

	public function thankyou_page ( $order_id )
	{
		// Getting order object
		$order = $order_id instanceof WC_Order ? $order_id : wc_get_order($order_id);

		// Return if $order not found.
		if ( !$order )
		{ return; }

		// Load payload
		$payload = (new PixProcessor($this->_plugin))->get($order);

		wc_get_template(
			'html-woocommerce-thank-you-page.php',
			[
				'payload' => $payload,
				'order' => $order
			],
			WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
			$this->_plugin->getTemplatePath()
		);
	}

	/**
	 * Add content to the woocommerce email.
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 * @param WC_Email $email
	 * @since 1.2.0
	 * @return void
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false, $email ) 
	{
		/** @var KeyingBucket $gatewaySettings */
		$settings = $this->_settings->bucket()->get('emails', new KeyingBucket());

		if ( get_class($email) === $settings->get('model', 'WC_Email_Customer_On_Hold_Order') 
				&& $order->get_payment_method() === $this->id ) 
		{
			// Load payload
			$payload = (new PixProcessor($this->_plugin))->get($order);

			wc_get_template(
				'email-woocommerce-pix.php',
				[
					'payload' => $payload,
					'order' => $order
				],
				WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
				$this->_plugin->getTemplatePath()
			);
		}
	}

	/**
	 * Show pix instructions when viewing the order, only
	 * when payment is waiting...
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function page_instructions ( $order )
	{
		if( $this->mustShow($order) )
		{
			// Load payload
			$payload = (new PixProcessor($this->_plugin))->get($order);

			wc_get_template(
				'html-woocommerce-thank-you-page.php',
				[
					'payload' => $payload,
					'order' => $order
				],
				WC()->template_path().\dirname($this->_plugin->getBasename()).'/',
				$this->_plugin->getTemplatePath()
			);
		}
	}
	
	/**
	 * Verify if must show pix data in $order.
	 *
	 * @param WC_Order $order
	 * @since 2.0.0
	 * @return boolean
	 */
	protected function mustShow ( 
		WC_Order $order 
	) : bool
	{
		/** @var KeyingBucket $settings */
		$settings = $this->_settings->bucket()->get('orders', new KeyingBucket());
		
		$expected = [
			'new',
			'on-hold',
			$settings->get('waiting_status', 'on-hold'),
			$settings->get('receipt_status', 'on-hold')
		];

		return (
			($this->_plugin->getName() === $order->get_payment_method()
			&& $order->has_status($expected))
			&& is_wc_endpoint_url( 'view-order' )
			&& !$settings->get('hide_in_order', false)
		);
	}
}