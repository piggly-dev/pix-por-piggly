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
 * This code is released under the GPL licence version 3
 * or later, available here http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @wordpress-plugin
 * Plugin Name:       Pix por Piggly (para Woocommerce)
 * Plugin URI:        https://lab.piggly.com.br/pix-por-piggly
 * Description:       O melhor plugin para pagamentos via Pix no Woocommerce. Aplique desconto automático, personalize o comportamento e muito mais.
 * Requires at least: 4.0
 * Requires PHP:      7.2
 * Version:           2.0.11
 * Author:            Piggly Lab
 * Author URI:        https://lab.piggly.com.br
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wc-piggly-pix
 * Domain Path:       /languages 
 * Network:           false
 */

use Piggly\WooPixGateway\Core;
use Piggly\WooPixGateway\Core\Managers\SettingsManager;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\WP\Activator;
use Piggly\WooPixGateway\WP\Deactivator;
use Piggly\WooPixGateway\WP\Upgrader;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) exit;

/** @var string Currently plugin version. Start at version 1.0.0 and use SemVer - https://semver.org */
if (!defined('PGLY_PIX_GATEWAY_VERSION')) define( 'PGLY_PIX_GATEWAY_VERSION', '2.0.11' );

/** @var string Currently plugin version. Start at version 1.0.0 and use SemVer - https://semver.org */
if (!defined('PGLY_PIX_GATEWAY_DBVERSION')) define( 'PGLY_PIX_GATEWAY_DBVERSION', '2.0.11' );

/** @var string Minimum php version required. */
if (!defined('PGLY_PIX_GATEWAY_PHPVERSION')) define( 'PGLY_PIX_GATEWAY_PHPVERSION', '7.2' );

/**
 * Check if plugin has requirements.
 *
 * @since 1.0.0
 * @return void
 */
function pgly_pix_gateway_requirements () : bool
{
	try
	{
		if ( version_compare( phpversion(), \PGLY_PIX_GATEWAY_PHPVERSION, '<' ) )
		{ 
			throw new Exception(
				sprintf(
					__('A versão mínima requirida para o <strong>PHP</strong> é %s', 'wc-piggly-pix'), 
					\PGLY_PIX_GATEWAY_PHPVERSION
				)
			); 
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
					<p>Não é possível habilitar o plugin <strong>Pix por Piggly</strong> no momento, certifique-se de atender os seguintes requisitos:</p>
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

if ( pgly_pix_gateway_requirements() )
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
	function pgly_pix_gateway ()
	{
		// Prepare plugin runtime settings
		$plugin =
			(new Plugin('wc-piggly-pix', 'wc_piggly_pix_settings', SettingsManager::defaults()))
			->abspath(__FILE__)
			->url(__FILE__)
			->basename(__FILE__)
			->name('wc_piggly_pix_gateway')
			->version(PGLY_PIX_GATEWAY_VERSION)
			->dbVersion(PGLY_PIX_GATEWAY_DBVERSION)
			->minPhpVersion(PGLY_PIX_GATEWAY_PHPVERSION)
			->notices('wc-piggly-pix-gateway-notices');

		$core = new Core(
			$plugin,
			new Activator($plugin),
			new Deactivator($plugin),
			new Upgrader($plugin)
		);

		// Set global instance.
		CoreConnector::setInstance($core);

		// Return core
		return $core;
	}

	// Startup plugin core
	pgly_pix_gateway()->startup();
}