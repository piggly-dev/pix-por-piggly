<?php
namespace Piggly\WooPixGateway;

use Piggly\Wordpress\Settings\KeyingBucket;

class CoreHelper
{
	/**
	 * Plugin core instance.
	 * 
	 * @var Core
	 * @since 1.0.0
	 */
	static $_core;
	
	/**
	 * Set static core instance.
	 *
	 * @param Core $core
	 * @since 1.0.0
	 * @return void
	 */
	public static function setInstance ( Core $core )
	{ static::$_core = $core; }

	/**
	 * Get static core instance.
	 *
	 * @since 1.0.0
	 * @return Core|null
	 */
	public static function getInstance () : ?Core
	{ return static::$_core; }

	/**
	 * Customize a string from settings.
	 * 
	 * @param string $key
	 * @param string $default
	 * @since 1.0.0
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
	 * @since 1.0.3
	 * @return string
	 */
	public static function _ntranslate ( string $single, string $plural, int $number ) : string
	{ return static::$_core->_ntranslate($single, $plural, $number); }

	/**
	 * Display the translation of $text.
	 *
	 * @param string $text
	 * @since 1.0.3
	 * @return void
	 */
	public static function _etranslate ( string $text )
	{ static::$_core->_etranslate($text); }

	/**
	 * Retrieve the translation of $text.
	 *
	 * @param string $text
	 * @since 1.0.3
	 * @return string
	 */
	public static function __translate ( string $text ) : string
	{ return static::$_core->__translate($text); }
}