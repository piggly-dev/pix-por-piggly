<?php
namespace Piggly\WC\Pix\WP;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Upgrade plugin based in its version.
 *
 * @since      1.2.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\WP
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class Upgrade
{
	/**
	 * Do an upgrade to current plugin based in it's version...
	 * 
	 * @since 1.2.0
	 * @return void
	 */
	public static function upgrade ()
	{
		if ( !is_admin() )
		{ return; }

		$version = get_option('wc_piggly_pix_version', '0' );

		if ( \version_compare($version, WC_PIGGLY_PIX_PLUGIN_VERSION, '>=' ) )
		{ return; }

		$settings = get_option( 'woocommerce_wc_piggly_pix_gateway_settings', [] );

		if ( \version_compare($version, '1.2.0', '<' ) )
		{ 
			if ( !empty($settings['enabled']) )
			{
				if ( $settings['enabled'] === 1 )
				{ $settings['enabled'] = 'yes'; }
			}

			update_option('woocommerce_wc_piggly_pix_gateway_settings', $settings);
		}

		// New version
		update_option('wc_piggly_pix_version', WC_PIGGLY_PIX_PLUGIN_VERSION);
	}
}