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
 * @since             1.1.0
 * @package           WC_Piggly_Pix
 *
 * @wordpress-plugin
 * Plugin Name:       Pix por Piggly
 * Plugin URI:        https://github.com/piggly-dev/wc-piggly-pix
 * Description:       Possibilite o pagamento com Pix de uma forma simples, rápida e direta. Mantenha atualizado para garantir todas as correções e recursos.
 * Version:           1.1.10
 * Author:            Caique 
 * Author URI:        https://github.com/caiquearaujo
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
define( 'WC_PIGGLY_PIX_PLUGIN_VERSION', '1.1.10' );

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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WC_PIGGLY_PIX_PLUGIN_PATH . 'includes/class-wc-piggly-pix.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_wc_piggly_pix () {
	$plugin = new WC_Piggly_Pix();
	$plugin->run();
}

/**
 * Includes missing notice in Wordpress panel.
 * 
 * @since 1.0.0
 */
function wc_piggly_pix_missing_notice ()
{ include WC_PIGGLY_PIX_PLUGIN_PATH . 'admin/partials/html-woocommerce-missing-notice.php'; }

require WC_PIGGLY_PIX_PLUGIN_PATH . 'vendor/autoload.php';
run_wc_piggly_pix();