<?php
namespace Piggly\WC\Pix;

use Piggly\WC\Pix\Discount\ApplyDiscount;
use Piggly\WC\Pix\Gateway\BaseGateway;
use Piggly\WC\Pix\Order\Metabox;
use Piggly\WC\Pix\WP\Debug;
use Piggly\WC\Pix\WP\i18n;
use Piggly\WC\Pix\WP\Upgrade;
use Piggly\WC\Pix\WP\Helper as WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin startup core.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Core 
{
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.2.0
	 * @var string $pluginName
	 */
	public $pluginName;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.2.0
	 * @var string $pluginVersion
	 */
	public $pluginVersion;

	/**
	 * Path to plugin directory.
	 * 
	 * @since 1.2.0
	 * @var string $pluginPath Without trailing slash.
	 */
	public $pluginPath;

	/**
	 * URL to plugin directory.
	 * 
	 * @since 1.2.0
	 * @var string $pluginUrl Without trailing slash.
	 */
	public $pluginUrl;

	/**
	 * URL to plugin assets directory.
	 * 
	 * @since 1.2.0
	 * @var string $assetsUrl Without trailing slash.
	 */
	public $assetsUrl;

	/**
	 * Plugin settings.
	 * 
	 * @since 1.3.5
	 * @var array
	 */
	protected $settings;

	/**
	 * Startup plugin.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function __construct ()
	{
		$this->pluginUrl  = \WC_PIGGLY_PIX_PLUGIN_URL;
		$this->pluginPath = \WC_PIGGLY_PIX_PLUGIN_PATH;
		$this->assetsUrl  = $this->pluginUrl . '/assets';

		$this->pluginName    = \WC_PIGGLY_PIX_PLUGIN_NAME;
		$this->pluginVersion = \WC_PIGGLY_PIX_PLUGIN_VERSION;

		$this->settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( isset($this->settings['debug']) )
		{ Debug::changeState((bool)$this->settings['debug']); }

		// Load translations
		i18n::init();

		// Upgrade plugin version
		Upgrade::upgrade();

		// Add gateways to woocommerce
		WP::add_action('plugins_loaded', $this, 'after_load' );
		
		// Add admin menu
		WP::add_action( 'admin_menu', $this, 'create_menu' );
		// Action to change status behavior
		WP::add_action( 'init', $this, 'register_order_statuses' );
		// Action to change status behavior
		WP::add_filter( 'wc_order_statuses', $this, 'add_order_statuses' );
		// Action to change status behavior
		WP::add_action( 'admin_init', $this, 'startup_screen' );
	}

	/**
	 * Plugin loaded method.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public function after_load ()
	{
		if ( !class_exists('WC_Payment_Gateway') )
		{
			// Cannot start plugin
			add_action( 'admin_notices', 'wc_piggly_pix_missing_notice' );
			return;
		}

		// Startup gateway
		BaseGateway::init();

		// Discount system
		ApplyDiscount::init();

		// Metabox init
		Metabox::init();

		// Display all notices...
		WP::add_action('admin_notices', WP::class, 'display_notices' );
	}

	/**
	 * Register wc-pix-receipt order status.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public function register_order_statuses ()
	{
		register_post_status( 'wc-pix-receipt', array(
			'label' => 'Comprovante Pix Recebido',
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop( '<span class="count">(%s)</span> Comprovante Pix Recebido', '<span class="count">(%s)</span> Comprovantes Pix Recebidos' )
		) );
	}

	/**
	 * Add wc-pix-receipt to order status.
	 * 
	 * @since 1.3.0
	 * @param array $order_statuses
	 * @return array
	 */
	public function add_order_statuses ( $order_statuses )
	{
		if ( !isset($this->settings['hide_receipt_status']) || $this->settings['hide_receipt_status'] !== 'yes' )
		{ $order_statuses['wc-pix-receipt'] = 'Comprovante Pix Recebido'; }

	  	return $order_statuses;
	}

	/**
	 * Redirect to plugin news when active plugin or update.
	 * 
	 * @since 1.3.0
	 * @return void
	 */
	public function startup_screen ()
	{
		$welcome = get_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-welcome-screen' ) !== false;
		$upgraded = get_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-upgraded-screen' ) !== false;

		if ( !$welcome && !$upgraded )
		{ return; }

		delete_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-welcome-screen' );
		delete_transient( \WC_PIGGLY_PIX_PLUGIN_NAME.'-upgraded-screen' );

		// Return if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) return;

		if ( $welcome || $upgraded )
		{ 
			if ( wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=news' ) ) )
			{ exit; }
		}
	}

	/**
	 * Create submenu to pix receipts at Woocommerce menu.
	 * 
	 * @since 1.3.0
	 * @since 1.3.14 Capabilities to who managers woocommerce.
	 * @return string
	 */
	public function create_menu ()
	{
		add_submenu_page(
			'woocommerce',
			__('Comprovantes Pix - Pix por Piggly', \WC_PIGGLY_PIX_PLUGIN_NAME),
			__('Comprovantes Pix', \WC_PIGGLY_PIX_PLUGIN_NAME),
			'manage_woocommerce',
			\WC_PIGGLY_PIX_PLUGIN_NAME,
			[$this, 'page']
		);
	}

	/**
	 * Get plugin pages.
	 * 
	 * @since 1.3.0
	 * @return string
	 */
	public function page ()
	{
		// Get current screen
		$screen  = $this->get_screen();
		$baseUrl = admin_url( 'admin.php?page='.\WC_PIGGLY_PIX_PLUGIN_NAME );
		require_once(\WC_PIGGLY_PIX_PLUGIN_PATH.'templates/admin/pages/header.php');
		require_once(\WC_PIGGLY_PIX_PLUGIN_PATH.'templates/admin/pages/'.$screen.'.php');
	}

	/**
	 * Delete receipt from database and file path.
	 * 
	 * @param int $id
	 * @since 1.3.8
	 * @since 1.3.14 Actions.
	 * @return void
	 */
	protected function delete_receipt ( int $id )
	{
		global $wpdb;
		$table_name = $wpdb->prefix.'wpgly_pix_receipts';
		$item = $wpdb->get_row("SELECT * FROM {$table_name} WHERE id = {$id}");

		if ( $item )
		{
			$upload      = wp_upload_dir();
			$pix_receipt = str_replace( $upload['baseurl'], $upload['basedir'], $item->pix_receipt);
			$order       = wc_get_order($item->order_number);
			
			$wpdb->delete( $wpdb->prefix.'wpgly_pix_receipts', array( 'id' => $item->id ) );

			if ( file_exists($pix_receipt) )
			{ unlink($pix_receipt); }

			if ( $order !== false )
			{
				$order->delete_meta_data('_wc_piggly_pix_receipt'); 
				$order->save();

				// Do after save order
				do_action('wpgly_pix_after_delete_receipt_from_order', $order->get_id(), $order);
			}
		}
	}

	/**
	 * Get current plugin option screen.
	 * 
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_screen () : string 
	{
		$screen = filter_input( INPUT_GET, 'screen', FILTER_SANITIZE_STRING );
		
		// Fix screen always to main when not valid...
		if ( empty($screen) || !in_array( $screen, ['support']) )
		{ $screen = 'main'; }

		return $screen;
	}
}