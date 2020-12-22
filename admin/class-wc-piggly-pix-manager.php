<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/admin
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix_Manager
{
	/**
	 * The main class.
	 *
	 * @since 1.0.0
	 * @var WC_Piggly_Pix $parent The plugin main core.
	 */
	private $parent;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param WC_Piggly_Pix $parent The main class to access public methods.
	 */
	public function __construct( WC_Piggly_Pix $parent ) {
		$this->parent = $parent;

		// Add action link to setting
		$this->parent->getLoader()->add_filter( 'plugin_action_links_wc_piggly_pix_gateway', $this, 'actionLinks' );
		// Main filter
		$this->parent->getLoader()->add_filter( 'woocommerce_payment_gateways', $this, 'initGateway' ); 
	}

	/**
	 * Startup admin behavior and logic.
	 * 
	 * @return void
	 */
	public function init ()
	{
		// Check the woocommerce compatibility
		if ( !class_exists( 'WC_Payment_Gateway' ) )
		{ add_action( 'admin_notices', 'wc_piggly_pix_missing_notice' ); return; }
		
		// The class responsible for control all plugin settings and behavior.
		require_once WC_PIGGLY_PIX_PLUGIN_PATH . 'core/pix/class-wc-piggly-pix-gateway.php';
	}

	/**
	 * Add support to the pix gateway.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function initGateway ( $gateways )
	{
		$gateways[] = 'WC_Piggly_Pix_Gateway';
		return $gateways;
	}

	/**
	 * Action links.
	 *
	 * @since 1.0.0
	 * @param array $links Plugin links.
	 * @return array
	 */
	public function actionLinks ( $links ) {
		$plugin_links = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' ) ) . '">' . __( 'Configurações do Pix', $this->parent->getPluginName() ) . '</a>';
		return array_merge( $plugin_links, $links );
	}
}
