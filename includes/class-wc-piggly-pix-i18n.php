<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/includes
 * @author     Caique <caique@piggly.com.br>
 */
class WC_Piggly_Pix_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			WC_PIGGLY_PIX_PLUGIN_NAME,
			false,
			WC_PIGGLY_PIX_PLUGIN_PATH . '/languages/'
		);
	}
}