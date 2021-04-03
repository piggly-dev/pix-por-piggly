<?php
namespace Piggly\WC\Pix\Gateway;

use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load gateway if woocommerce is available.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class BaseGateway 
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

		WP::add_filter('woocommerce_payment_gateways', $base, 'add_gateway');
		WP::add_filter('plugin_action_links_'.\WC_PIGGLY_PIX_BASE_NAME, $base, 'plugin_action_links');
	}

	/**
	 * Add gateways to Woocommerce.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function add_gateway ( array $gateways )
	{
		array_push( $gateways, PixGateway::class );
		return $gateways;
	}

	/**
	 * Add links to plugin settings page.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function plugin_action_links ( $links )
	{
		$pluginLinks = array();

		$baseUrl = esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' ) );

		$pluginLinks[] = sprintf('<a href="%s">%s</a>', $baseUrl, __('Configurações', \WC_PIGGLY_PIX_PLUGIN_NAME));
		$pluginLinks[] = sprintf('<a href="%s&screen=support">%s</a>', $baseUrl, __('Suporte', \WC_PIGGLY_PIX_PLUGIN_NAME));
		$pluginLinks[] = sprintf('<a href="%s">%s</a>', 'https://wordpress.org/plugins/pix-por-piggly/#reviews', __('Avalie o Plugin!', \WC_PIGGLY_PIX_PLUGIN_NAME));

		return array_merge( $pluginLinks, $links );
	}
}