<?php
/**
 * @link https://lab.piggly.com.br
 * @since 2.0.0
 * @version 2.0.0
 * @package \Piggly\WooPixGateway
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 * 
 * This code is released under the GPL licence version 2
 * or later, available here http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @wordpress-plugin
 * Plugin Name:       Pix por Piggly (para Woocommerce)
 * Plugin URI:        https://lab.piggly.com.br/pix-por-piggly
 * Description:       O melhor plugin para pagamentos via Pix no Woocommerce. Aplique desconto automático, personalize o comportamento e muito mais.
 * Requires at least: 4.0
 * Requires PHP:      7.2
 * Version:           2.0.0
 * Author:            Piggly Lab
 * Author URI:        https://lab.piggly.com.br
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpgly-woo-piggly-pix
 * Domain Path:       /languages 
 * Network:           false
 */

use Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Core;
use Piggly\WooPixGateway\Core\Managers\SettingsManager;
use Piggly\WooPixGateway\CoreHelper;
use Piggly\WooPixGateway\WP\Activator;
use Piggly\WooPixGateway\WP\Deactivator;
use Piggly\WooPixGateway\WP\Upgrader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) exit;

/** @var string Currently plugin version. Start at version 1.0.0 and use SemVer - https://semver.org */
if (!defined('WPGLY_PIX_GATEWAY_VERSION')) define( 'WPGLY_PIX_GATEWAY_VERSION', '2.0.0' );

/** @var string Currently plugin version. Start at version 1.0.0 and use SemVer - https://semver.org */
if (!defined('WPGLY_PIX_GATEWAY_DBVERSION')) define( 'WPGLY_PIX_GATEWAY_DBVERSION', '1.0.1' );

/** @var string Minimum php version required. */
if (!defined('WPGLY_PIX_GATEWAY_PHPVERSION')) define( 'WPGLY_PIX_GATEWAY_PHPVERSION', '7.2' );


/**
 * Check if plugin has requirements.
 *
 * @since 1.0.0
 * @return void
 */
function wpgly_pix_gateway_requirements () : bool
{
	try
	{
		if ( version_compare( phpversion(), \WPGLY_PIX_GATEWAY_PHPVERSION, '<' ) )
		{ 
			throw new Exception(
				sprintf(
					__('A versão mínima requirida para o <strong>PHP</strong> é %s', 'wc-piggly-pix'), 
					\WPGLY_PIX_GATEWAY_PHPVERSION
				)
			); 
		}
		
		$exts = [
			'bcmath',
			'curl',
			'gmp',
			'json',
			'pcre'
		];

		foreach ( $exts as $ext )
		{
			if ( !extension_loaded($ext) )
			{
				throw new Exception(
					sprintf(
						__('Habilite as seguintes <strong>extensões</strong> para o PHP: %s', 'wc-piggly-pix'), 
						implode(', ', $exts)
					)
				); 
			}
		}

		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		if ( !is_plugin_active ('woocommerce/woocommerce.php') ) 
		{
			throw new Exception(
				__('Verifique se o <strong>Woocommerce</strong> está ativado', 'wc-piggly-pix')
			); 
		}

		return true;
	}
	catch ( Exception $e )
	{
		add_action(
			'admin_notices',
			function () use ($e) {
				?>
				<div class="notice notice-error">
					<p>Não é possível habilitar o plugin <strong>BDM Commerce</strong> no momento, certifique-se de atender os seguintes requisitos:</p>
					<p><?=$e->getMessage();?>.</p>
				</div>
				<?php

				// In case this is on plugin activation.
				if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
				// Desactivate plugin
				deactivate_plugins( plugin_basename( __FILE__ ) );
			}
		);

		return false;
	}
}

if ( wpgly_pix_gateway_requirements() )
{
	// Include composer autoloader
	require __DIR__ . '/vendor/autoload.php';

	/**
	 * Global function holder. 
	 * Works similar to a singleton's instance().
	 * 
	 * @since 1.0.0
	 * @return Core
	 */
	function wpgly_pix_gateway ()
	{
		// Prepare plugin runtime settings
		$plugin =
			(new Plugin('wpgly-woo-pix-gateway', 'wpgly_woo_pix_gateway', SettingsManager::defaults()))
			->abspath(__FILE__)
			->url(__FILE__)
			->basename(__FILE__)
			->name('wpgly_woo_pix_gateway')
			->version(WPGLY_PIX_GATEWAY_VERSION)
			->dbVersion(WPGLY_PIX_GATEWAY_DBVERSION)
			->minPhpVersion(WPGLY_PIX_GATEWAY_PHPVERSION)
			->notices('wpgly-woo-pix-gateway-notices');

		$core = new Core(
			$plugin,
			new Activator($plugin),
			new Deactivator(),
			new Upgrader()
		);

		// Set global instance.
		CoreHelper::setInstance($core);

		// Return core
		return $core;
	}

	// Startup plugin core
	wpgly_pix_gateway()->startup();
}

// use Piggly\WC\Pix\Core;
// use Piggly\WC\Pix\WP\Activator;
// use Piggly\WC\Pix\WP\Desactivator;

// // Include composer autoloader
// require __DIR__ . '/vendor/autoload.php';

// /** DEFINE GLOBALS */

// /** @var string The plugin absolute directory. */
// if (!defined('WPGLY_PIX_ABSPATH')) define( 'WPGLY_PIX_ABSPATH', plugin_dir_path( __FILE__ ) );

// /** @var string The plugin url. */
// if (!defined('WPGLY_PIX_URL')) define( 'WPGLY_PIX_URL', plugin_dir_url( __FILE__ ) );

// /** @var string The plugin base name. */
// if (!defined('WPGLY_PIX_BASENAME')) define( 'WPGLY_PIX_BASENAME', plugin_basename( __FILE__ ) );

// /** @var string The plugin DIR name. */
// if (!defined('WPGLY_PIX_DIRNAME')) define( 'WPGLY_PIX_DIRNAME', dirname(WPGLY_PIX_BASENAME) );

// /** @var string The plugin name. */
// if (!defined('WPGLY_PIX_NAME')) define( 'WPGLY_PIX_NAME', 'wpgly_pix' );

// /** @var string The plugin domain. */
// if (!defined('WPGLY_PIX_DOMAIN')) define( 'WPGLY_PIX_DOMAIN', 'pix-por-piggly' );

// /** @var string Currently plugin version. */
// if (!defined('WPGLY_PIX_VERSION')) define( 'WPGLY_PIX_VERSION', '2.0.0' );

// /** @var string Currently database version. */
// if (!defined('WPGLY_PIX_DB_VERSION')) define( 'WPGLY_PIX_DB_VERSION', '1.0.1' );

// /** @var string Minimum php version required. */
// if (!defined('WPGLY_PIX_PHPVERSION')) define( 'WPGLY_PIX_PHPVERSION', '7.2' );

// /** @var string Order meta prefix. */
// if (!defined('WPGLY_PIX_ORDER_META')) define( 'WPGLY_PIX_ORDER_META', '_wpgly_pix' );

// /** @var string Template path. */
// if (!defined('WPGLY_PIX_TEMPLATE_ABSPATH')) define( 'WPGLY_PIX_TEMPLATE_ABSPATH', WPGLY_PIX_ABSPATH . '/templates/' );

// /** @var string Template path to woocommerce. */
// if (!defined('WPGLY_PIX_TEMPLATE_WOOPATH')) define( 'WPGLY_PIX_TEMPLATE_WOOPATH', WPGLY_PIX_TEMPLATE_ABSPATH . 'woocommerce/' );

// /**
//  * Display an admin notice and prevent code execution if server
//  * is using old/insecure PHP version.
//  * 
//  * @since 1.0.0
//  * @return void
//  */
// if ( version_compare( phpversion(), WPGLY_PIX_PHPVERSION, '<' ) )
// {
// 	add_action( 'admin_notices', [Core::class, 'insecure_php'] );
// 	return;
// }

// /**
//  * Global function holder. Works similar to a singleton's instance().
//  * 
//  * @since 1.0.0
//  * @return Core
//  */
// function wpgly_pix () 
// {
// 	/** @var Core $core */
// 	static $core;

// 	// Create core if not created...
// 	if ( !isset($core) ) $core = new Core();
	
// 	// Return core
// 	return $core;
// }

// // Activate plugin
// register_activation_hook( __FILE__, array(Activator::class, 'activate'));
// // Desactivate plugin
// register_deactivation_hook( __FILE__, array(Desactivator::class, 'desactivate'));

// // Startup Plugin
// $core = wpgly_pix();

