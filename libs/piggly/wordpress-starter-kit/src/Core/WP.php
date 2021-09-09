<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core;

/**
 * This class contains shortcuts to Wordpress
 * functions, making it easy and prettier to
 * call standard functions with some improvements.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core
 * @version 1.0.3
 * @since 1.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class WP
{
    /**
     * Add an action to the "queue of actions".
     *
     * @since 1.0.0
     * @param string $hook The name of the WordPress action that is being registered.
     * @param object|string $component A reference to the instance of the object on which the action is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @return void
     */
    public static function add_action(string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1)
    {
        add_action($hook, [$component, $callback], $priority, $accepted_args);
    }
    /**
     * Add a filter to the "queue of filters".
     *
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object|string $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @since 1.0.0
     * @return void
     */
    public static function add_filter(string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1)
    {
        add_filter($hook, [$component, $callback], $priority, $accepted_args);
    }
    /**
     * Check if WordPress is processing an AJAX call.
     *
     * @since 1.0.0
     * @return bool
     */
    public static function is_doing_ajax() : bool
    {
        // Return WordPress native function if exists
        if (\function_exists('wp_doing_ajax')) {
            return wp_doing_ajax();
        }
        // Check for ajax constant variable
        return \defined('DOING_AJAX') && DOING_AJAX;
    }
    /**
     * Check if "I am" in the Admin Panel.
     * 
     * @since 1.0.3
     * @return bool
     */
    public static function is_admin() : bool
    {
        return is_admin();
    }
    /**
     * Check if "I am" in the Admin Panel, not doing AJAX call.
     * 
     * @since 1.0.0
     * @return bool
     */
    public static function is_pure_admin() : bool
    {
        return is_admin() && !self::is_doing_ajax();
    }
    /**
     * Check if is user logged in.
     * 
     * @since 1.0.3
     * @return bool
     */
    public static function is_user_logger_in() : bool
    {
        return \is_user_logged_in();
    }
    /**
     * Check if WP_DEBUG is active.
     * 
     * @since 1.0.0
     * @return bool
     */
    public static function is_debugging() : bool
    {
        return \defined('WP_DEBUG') && WP_DEBUG;
    }
    /**
     * Get the postfix for assets files - ".min" or empty
     * ".min" if in production mode.
     * 
     * @since 1.0.0
     * @return string
     */
    public static function minify() : string
    {
        return \defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    }
    /**
     * Check whether the string is a JSON or not.
     *
     * @param string $string String to test if it's json.
     * @since 1.0.0
     * @return string
     */
    public static function is_json(string $string) : bool
    {
        return \is_string($string) && \is_array(\json_decode($string, \true)) && \json_last_error() === \JSON_ERROR_NONE ? \true : \false;
    }
}
