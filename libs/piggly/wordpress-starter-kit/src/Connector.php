<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets\KeyingBucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Manager;
use RuntimeException;
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
     * @var Core
     * @since 1.0.7
     */
    public static $_core;
    /**
     * Set static core instance.
     *
     * @param Core $core Plugin core instance.
     * @since 1.0.7
     * @return void
     */
    public static function setInstance(Core $core)
    {
        static::$_core = $core;
    }
    /**
     * Get static core instance.
     *
     * @since 1.0.7
     * @throws RuntimeException If core is not set.
     * @return Core
     */
    public static function getInstance() : Core
    {
        if (!static::$_core) {
            throw new RuntimeException('Plugin core is not set.');
        }
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
        return static::$_core->plugin();
    }
    /**
     * Get plugin domain.
     *
     * @since 1.0.7
     * @return string
     */
    public static function domain() : string
    {
        return static::$_core->plugin()->domain();
    }
    /**
     * Get plugin runtime settings.
     *
     * @since 1.0.7
     * @return KeyingBucket
     */
    public static function runtimeSettings() : KeyingBucket
    {
        return static::$_core->plugin()->bucket();
    }
    /**
     * Get debugger.
     *
     * @since 1.0.7
     * @return Debugger
     */
    public static function debugger() : Debugger
    {
        return static::$_core->plugin()->debugger();
    }
    /**
     * Get bucket settings.
     *
     * @since 1.0.7
     * @return Manager
     */
    public static function settingsManager() : Manager
    {
        return static::$_core->plugin()->settings();
    }
    /**
     * Get bucket settings.
     *
     * @since 1.0.7
     * @return KeyingBucket
     */
    public static function settings() : KeyingBucket
    {
        return static::$_core->plugin()->settings()->bucket();
    }
    /**
     * Customize a string from settings.
     *
     * @param string $key Key to get.
     * @param string $default Default value.
     * @since 1.0.7
     * @return string
     */
    public static function __customize(string $key, string $default = null) : string
    {
        return static::$_core->plugin()->settings()->bucket()->get('customizations', new KeyingBucket())->get($key, $default);
    }
}
