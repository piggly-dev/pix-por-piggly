<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The core plugin class.
 *
 * This is used to define internationalization, manager-specific hooks, and
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
	 * The manager class is responsible for retain all hooks and logic for admin side.
	 * @since 1.0.0
	 * @access protected
	 * @var WC_Piggly_Pix_Manager $manager All hooks and logic for admin side.
	 */
	protected $manager;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 * @since 1.0.0
	 * @var WC_Piggly_Pix_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the manager area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Dependencies
		$this->loadDependencies();
		// Locales
		$this->setLocale();

		// Construct and start manager-side core
		$this->startupManager();
	}

	/**
	 * Get the main core plugin admin manager.
	 * @since 1.0.0
	 * @return WC_Piggly_Pix_Manager
	 */
	public function getAdminManager () : WC_Piggly_Pix_Manager
	{ return $this->manager; }

	/**
	 * Get the main core plugin loader.
	 * @since 1.0.0
	 * @return WC_Piggly_Pix_Loader
	 */
	public function getLoader () : WC_Piggly_Pix_Loader
	{ return $this->loader; }

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
	 * - WC_Piggly_Pix_Manager. Defines all hooks for the manager area.
	 * - WC_Piggly_Pix_Public. Defines all hooks for the public side of the site.
	 * - WC_Piggly_Pix_Gateway. Defines the main core payment gateway.
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
		require_once WC_PIGGLY_PIX_PLUGIN_PATH . 'includes/class-wc-piggly-pix-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WC_PIGGLY_PIX_PLUGIN_PATH . 'includes/class-wc-piggly-pix-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the manager area.
		 */
		require_once WC_PIGGLY_PIX_PLUGIN_PATH . 'admin/class-wc-piggly-pix-manager.php';

		/**
		 * All vendor classes.
		 */
		require_once WC_PIGGLY_PIX_PLUGIN_PATH . 'vendor/autoload.php';

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
	 * Register all of the hooks related to the manager area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function startupManager () {
		$this->manager = new WC_Piggly_Pix_Manager( $this );
		$this->loader->add_action('plugins_loaded', $this->manager, 'init');
	}
}