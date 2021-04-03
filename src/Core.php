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

		// Load translations
		i18n::init();

		// Upgrade plugin version
		Upgrade::upgrade();

		$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( isset($settings['debug']) )
		{ Debug::changeState((bool)$settings['debug']); }

		// Add gateways to woocommerce
		add_action('plugins_loaded', array($this, 'after_load') );
	}

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
}