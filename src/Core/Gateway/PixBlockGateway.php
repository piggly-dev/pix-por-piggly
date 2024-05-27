<?php
namespace Piggly\WooPixGateway\Core\Gateway;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Piggly\WooPixGateway\Core\Gateway\PixGateway;
use Piggly\WooPixGateway\CoreConnector;

/**
 * Class for handling Pix Payment block checkout support.
 */
final class PixBlockGateway extends AbstractPaymentMethodType {

	/**
	 * The name of the payment method.
	 *
	 * @var string
	 */
	protected $name = 'wc_piggly_pix_gateway';

	/**
	 * Initializes the Pix Payment block.
	 *
	 * @since 2.2.3
	 */
	public function initialize() {
		$this->settings = get_option(
			'wc_piggly_pix_settings',
			array([
				'gateway' => [
					'enabled' => false,
					'title' => 'Faça um Pix',
					'description' => 'Você não precisa ter uma chave cadastrada. Pague os seus pedidos via Pix.',
					'icon' => 'pix-payment-icon'
				],
				'account' => [
					'key_value' => null,
				]
			])
		);
	}

	/**
	 * Checks if the payment method is active.
	 *
	 * @since 2.2.3
	 *
	 * @return bool True if the payment method is active, false otherwise.
	 */
	public function is_active() {
		return empty($this->settings['account']['key_value']) ? 'no' : ($this->settings['gateway']['enabled'] === true ? 'yes' : 'no');
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
			'wc-pix-por-piggly-blocks-integration',
			CoreConnector::plugin()->getUrl().'assets/js/pgly.pix.checkout.js',
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

		return array( 'wc-pix-por-piggly-blocks-integration' );
	}

	/**
	 * Retrieves the payment method data.
	 *
	 * @return array The payment method data, including title and description.
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => $this->settings['gateway']['title'],
			'description' => $this->settings['gateway']['description'],
			'icon'        => \apply_filters('woocommerce_gateway_icon', CoreConnector::plugin()->getUrl().'assets/images/'.$this->settings['icon'].'.png')
		);
	}
}