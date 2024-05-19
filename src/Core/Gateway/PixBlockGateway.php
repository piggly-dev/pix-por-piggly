<?php
namespace Piggly\WooPixGateway\Core\Gateway;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\CoreConnector;

/**
 * Class for handling HBL Payment block checkout support.
 */
final class PixBlockGateway extends AbstractPaymentMethodType {

	/**
	 * The HBL Payment gateway instance.
	 *
	 * @var PixGateway
	 */
	private $gateway;

	/**
	 * The name of the payment method.
	 *
	 * @var string
	 */
	protected $name = 'wc_piggly_pix_gateway';

	/**
	 * Initializes the HBL Payment block.
	 *
	 * @since 2.2.3
	 */
	public function initialize() {
		$this->settings = get_option( 'wc_piggly_pix_settings-payment_settings', array() );
		$this->gateway  = new PixGateway();
	}

	/**
	 * Checks if the payment method is active.
	 *
	 * @since 2.2.3
	 *
	 * @return bool True if the payment method is active, false otherwise.
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Retrieves the script handles for the payment method.
	 *
	 * @since 2.2.3
	 *
	 * @return array The script handles for the payment method.
	 */
	public function get_payment_method_script_handles() {
		wp_register_script(
			'wc-hbl-payment-blocks-integration',
			CoreConnector::plugin()->getUrl().'assets/pgly.pix.checkout.js',
			array(
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
			),
			null,
			true
		);

		return array( 'wc-piggly-pix-block-integration' );
	}

	/**
	 * Retrieves the payment method data.
	 *
	 * @return array The payment method data, including title and description.
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => $this->gateway->title,
			'description' => $this->gateway->description,
		);
	}
}