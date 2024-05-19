<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Manager;
/**
 * Plugin main core helper.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress
 * @version 1.0.7
 * @since 1.0.7
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class Connector
{
    /**
     * Plugin core instance.
     *
     * @var Initiable
     * @since 1.0.7
     */
    public static $_core;
    /**
     * Set static core instance.
     *
     * @param Initiable $core
     * @since 1.0.7
     * @return void
     */
    public static function setInstance(Initiable $core)
    {
        static::$_core = $core;
    }
    /**
     * Get static core instance.
     *
     * @since 1.0.7
     * @return Initiable|null
     */
    public static function getInstance() : ?Initiable
    {
        return static::$_core;
    }
    /**
     * Get plugin data.
     *
     * @since 1.0.7
     * @return Plugin
     */
    public static function plugin() : Plugin
    {
        return static::$_core->getPlugin();
    }
    /**
     * Get plugin domain.
     *
     * @since 1.0.7
     * @return string
     */
    public static function domain() : string
    {
        return static::$_core->getPlugin()->getDomain();
    }
    /**
     * Get plugin runtime settings.
     *
     * @since 1.0.7
     * @return KeyingBucket
     */
    public static function runtimeSettings() : KeyingBucket
    {
        return static::$_core->getPlugin()->bucket();
    }
    /**
     * Get debugger.
     *
     * @since 1.0.7
     * @return Debugger
     */
    public static function debugger() : Debugger
    {
        return static::$_core->getPlugin()->debugger();
    }
    /**
     * Get bucket settings.
     *
     * @since 1.0.7
     * @return Manager
     */
    public static function settingsManager() : Manager
    {
        return static::$_core->getPlugin()->settings();
    }
    /**
     * Get bucket settings.
     *
     * @since 1.0.7
     * @return KeyingBucket
     */
    public static function settings() : KeyingBucket
    {
        return static::$_core->getPlugin()->settings()->bucket();
    }
    /**
     * Customize a string from settings.
     *
     * @param string $key
     * @param string $default
     * @since 1.0.7
     * @return string
     */
    public static function __customize(string $key, string $default = null) : string
    {
        return static::$_core->settings()->bucket()->get('customizations', new KeyingBucket())->get($key, $default);
    }
    /**
     * Translates $text and retrieves the singular or plural
     * form based on the supplied number.
     *
     * @param string $single
     * @param string $plural
     * @param integer $number
     * @since 1.0.7
     * @return string
     */
    public static function _ntranslate(string $single, string $plural, int $number) : string
    {
        return static::$_core->_ntranslate($single, $plural, $number);
    }
    /**
     * Display the translation of $text.
     *
     * @param string $text
     * @since 1.0.7
     * @return void
     */
    public static function _etranslate(string $text)
    {
        static::$_core->_etranslate($text);
    }
    /**
     * Retrieve the translation of $text.
     *
     * @param string $text
     * @since 1.0.7
     * @return string
     */
    public static function __translate(string $text) : string
    {
        return static::$_core->__translate($text);
    }
}
