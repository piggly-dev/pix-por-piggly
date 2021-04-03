<?php
namespace Piggly\WC\Pix\WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load text domain translations to plugin.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\WP
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class i18n
{
	/**
	 * Startup i18n actions and filters hooks.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function init ()
	{
		$i18n = new self();

		Helper::add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Load the plugin text domain for translation.
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function load_plugin_textdomain ()
	{
		load_plugin_textdomain(
			WC_PIGGLY_PIX_PLUGIN_NAME,
			false,
			WC_PIGGLY_PIX_PLUGIN_PATH . '/languages'
		);
	}
}