<?php
namespace Piggly\WooPixGateway;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Manager;

/**
 * Plugin main core helper.
 * 
 * @package \Piggly\WooPixGateway
 * @subpackage \Piggly\WooPixGateway
 * @version 2.0.0
 * @since 2.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license GPLv3 or later
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class CoreConnector
{
	/**
	 * Plugin core instance.
	 * 
	 * @var Core
	 * @since 2.0.0
	 */
	static $_core;
	
	/**
	 * Set static core instance.
	 *
	 * @param Core $core
	 * @since 2.0.0
	 * @return void
	 */
	public static function setInstance ( Core $core )
	{ static::$_core = $core; }

	/**
	 * Get static core instance.
	 *
	 * @since 2.0.0
	 * @return Core|null
	 */
	public static function getInstance () : ?Core
	{ return static::$_core; }

	/**
	 * Get plugin data.
	 *
	 * @since 2.0.0
	 * @return Plugin
	 */
	public static function plugin () : Plugin
	{ return static::$_core->getPlugin(); }

	/**
	 * Get plugin domain.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public static function domain () : string
	{ return static::$_core->getPlugin()->getDomain(); }

	/**
	 * Get plugin runtime settings.
	 *
	 * @since 2.0.0
	 * @return KeyingBucket
	 */
	public static function runtimeSettings () : KeyingBucket
	{ return static::$_core->getPlugin()->bucket(); }

	/**
	 * Get debugger.
	 *
	 * @since 2.0.0
	 * @return Debugger
	 */
	public static function debugger () : Debugger
	{ return static::$_core->getPlugin()->debugger(); }

	/**
	 * Get bucket settings.
	 *
	 * @since 2.0.0
	 * @return Manager
	 */
	public static function settingsManager () : Manager
	{ return static::$_core->getPlugin()->settings(); }

	/**
	 * Get bucket settings.
	 *
	 * @since 2.0.0
	 * @return KeyingBucket
	 */
	public static function settings () : KeyingBucket
	{ return static::$_core->getPlugin()->settings()->bucket(); }

	/**
	 * Customize a string from settings.
	 * 
	 * @param string $key
	 * @param string $default
	 * @since 2.0.0
	 * @return string
	 */
	public static function __customize ( string $key, string $default = null ) : string
	{
		return static
					::$_core
					->settings()
					->bucket()
					->get('customizations', new KeyingBucket())
					->get($key, $default);
	}

	/**
	 * Translates $text and retrieves the singular or plural 
	 * form based on the supplied number.
	 *
	 * @param string $single
	 * @param string $plural
	 * @param integer $number
	 * @since 2.0.0
	 * @return string
	 */
	public static function _ntranslate ( string $single, string $plural, int $number ) : string
	{ return static::$_core->_ntranslate($single, $plural, $number); }

	/**
	 * Display the translation of $text.
	 *
	 * @param string $text
	 * @since 2.0.0
	 * @return void
	 */
	public static function _etranslate ( string $text )
	{ static::$_core->_etranslate($text); }

	/**
	 * Retrieve the translation of $text.
	 *
	 * @param string $text
	 * @since 2.0.0
	 * @return string
	 */
	public static function __translate ( string $text ) : string
	{ return static::$_core->__translate($text); }

	/**
	 * Enqueue styles and scripts to pgly-wps-admin.
	 *
	 * @internal When update the CSS/JS, update version.
	 * @param boolean $include_js
	 * @since 2.0.0
	 * @return void
	 */
	public static function enqueuePglyWpsAdmin ( bool $include_js = false )
	{
		if ( !wp_style_is('pgly-wps-settings-0-1-4-css') )
		{
			wp_enqueue_style(
				'pgly-wps-settings-css',
				static::plugin()->getUrl().'assets/vendor/css/pgly-wps-settings.v0.1.4.min.css',
				[],
				'0.1.4'
			);
		}

		if ( !$include_js ) return;

		if ( !wp_script_is('pgly-wps-settings-0-1-4-js') )
		{
			wp_enqueue_script(
				'pgly-wps-settings-0-1-4-js',
				static::plugin()->getUrl().'assets/vendor/js/pgly-wps-settings.bundle.v0.1.4.js',
				[],
				'0.1.4',
				true
			);
		}

		wp_localize_script(
			'pgly-wps-settings-0-1-4-js',
			'wcPigglyPix',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'x_security' => wp_create_nonce('pgly_wc_piggly_pix_admin'),
				'plugin_url' => admin_url('admin.php?page='.static::plugin()->getDomain())
			]
		);
	}
}