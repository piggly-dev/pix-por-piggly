<?php
/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/piggly-dev/wc-piggly-pix
 * @since             1.2.0
 * @package           \Piggly\WC\Pix
 *
 * @wordpress-plugin
 * Plugin Name:       Pix por Piggly (para Woocommerce)
 * Plugin URI:        https://github.com/piggly-dev/wc-piggly-pix
 * Description:       O melhor plugin para pagamentos via Pix no Woocommerce. Aplique desconto automático, personalize o comportamento e muito mais.
 * Version:           1.3.14
 * Author:            Piggly Lab 
 * Author URI:        https://github.com/piggly-dev
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-piggly-pix
 * Domain Path:       /languages 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The plugin current name.
 * @var string
 */
define( 'WC_PIGGLY_PIX_PLUGIN_NAME', 'wc-piggly-pix' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * @var string  
 */
define( 'WC_PIGGLY_PIX_PLUGIN_VERSION', '1.3.14' );

/**
 * Currently plugin database version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * @var string 
 */
define( 'WC_PIGGLY_PIX_DB_VERSION', '1.0.1' ); 

/**
 * The plugin absolute directory.
 * @var string
 */
define( 'WC_PIGGLY_PIX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The plugin absolute directory.
 * @var string
 */
define( 'WC_PIGGLY_PIX_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The plugin base name.
 * @var string
 */ 
define( 'WC_PIGGLY_PIX_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The plugin dir name.
 * @var string
 */ 
define( 'WC_PIGGLY_PIX_DIR_NAME', dirname(plugin_basename( __FILE__ )) );
 
// Include composer autoloader
require __DIR__ . '/vendor/autoload.php';

/**
 * Global function holder. Works similar to a singleton's instance().
 * 
 * @since 1.0.0
 * @return \Piggly\WC\Pix\Core
 */
function wc_piggly_pix () 
{
	/** @var \Piggly\WC\Pix\Core $core */
	static $core;

	// Create core if not created...
	if ( !isset($core) ) $core = new \Piggly\WC\Pix\Core();
	
	// Return core
	return $core;
}

/**
 * Includes missing notice in Wordpress panel.
 * 
 * @since 1.0.0
 */
function wc_piggly_pix_missing_notice ()
{ include WC_PIGGLY_PIX_PLUGIN_PATH . 'templates/admin/woocommerce-missing.php'; }

// Activate plugin
register_activation_hook( __FILE__, array(\Piggly\WC\Pix\WP\Activator::class, 'activate'));
// Desactivate plugin
register_deactivation_hook( __FILE__, array(\Piggly\WC\Pix\WP\Desactivator::class, 'desactivate'));

// Startup Plugin
wc_piggly_pix();