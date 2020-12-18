<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/core/pix
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix
{
	/**
	 * The admin class is responsible for retain all hooks and logic for admin side.
	 * @since 1.0.0
	 * @access protected
	 * @var WC_Piggly_Pix_Manager $admin All hooks and logic for admin side.
	 */
	protected $admin;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 * @since 1.0.0
	 * @var WC_Piggly_Pix_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 * @since 1.0.0
	 * @var string $pluginName The string used to uniquely identify this plugin.
	 */
	protected $pluginName;

	/**
	 * The current version of the plugin.
	 * @since 1.0.0
	 * @var string $pluginVersion The current version of the plugin.
	 */
	protected $pluginVersion;

	/**
	 * The current path of the plugin.
	 * @since 1.0.0
	 * @var string $pluginPath The current path of the plugin.
	 */
	protected $pluginPath;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Plugin version
		$this->pluginVersion = WC_PIGGLY_PIX_PLUGIN_VERSION;
		// Plugin name
		$this->pluginName = WC_PIGGLY_PIX_PLUGIN_NAME;
		// Plugin path
		$this->pluginPath = WC_PIGGLY_PIX_PLUGIN_PATH;


		// Dependencies
		$this->loadDependencies();
		// Locales
		$this->setLocale();

		// Construct and start admin-side core
		$this->startupManager();
	}

	/**
	 * Get the main core plugin admin manager.
	 * @since 1.0.0
	 * @return WC_Piggly_Pix_Manager
	 */
	public function getAdminManager () : WC_Piggly_Pix_Manager
	{ return $this->admin; }

	/**
	 * Get the main core plugin loader.
	 * @since 1.0.0
	 * @return WC_Piggly_Pix_Loader
	 */
	public function getLoader () : WC_Piggly_Pix_Loader
	{ return $this->loader; }

	/**
	 * Get the plugin name.
	 * @since 1.0.0
	 * @return string
	 */
	public function getPluginName () : string
	{ return $this->pluginName; }

	/**
	 * Get the plugin version.
	 * @since 1.0.0
	 * @return string
	 */
	public function getPluginVersion () : string
	{ return $this->pluginVersion; }

	/**
	 * Get the plugin path.
	 * @since 1.0.0
	 * @return string
	 */
	public function getPluginPath () : string
	{ return $this->pluginPath; }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 * @since 1.0.0
	 * @return void
	 */
	public function run() 
	{ $this->loader->run();	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WC_Piggly_Pix_Loader. Orchestrates the hooks of the plugin.
	 * - WC_Piggly_Pix_i18n. Defines internationalization functionality.
	 * - WC_Piggly_Pix_Manager. Defines all hooks for the admin area.
	 * - WC_Piggly_Pix_Public. Defines all hooks for the public side of the site.
	 * - WC_Piggly_Pix_Gateway. Defines the main core payment gateway.
	 * - WC_Piggly_Pix_Payload. Defines the pix payload.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function loadDependencies () {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->pluginPath . '/includes/class-wc-piggly-pix-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->pluginPath . '/includes/class-wc-piggly-pix-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->pluginPath . '/admin/class-wc-piggly-pix-manager.php';

		/**
		 * The class responsible for generating the pix payload.
		 */
		require_once $this->pluginPath . '/core/pix/class-wc-piggly-pix-payload.php';

		/**
		 * The class responsible for parse some pix data.
		 */
		require_once $this->pluginPath . '/core/pix/class-wc-piggly-pix-parser.php';

		$this->loader = new WC_Piggly_Pix_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WC_Piggly_Pix_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function setLocale () {
		$plugin_i18n = new WC_Piggly_Pix_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function startupManager () {
		$this->admin = new WC_Piggly_Pix_Manager( $this );
		$this->loader->add_action('plugins_loaded', $this->admin, 'init');
	}
}