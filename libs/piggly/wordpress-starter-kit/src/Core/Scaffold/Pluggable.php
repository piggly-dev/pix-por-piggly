<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Manager;
/**
 * Make plugin part of this.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Scaffold
 * @version 1.0.3
 * @since 1.0.3
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class Pluggable
{
    /**
     * Plugin data.
     *
     * @var Plugin
     * @since 1.0.3
     */
    protected $_plugin;
    /**
     * Construct with optional plugin data.
     *
     * @param Plugin $plugin
     * @since 1.0.3
     * @return void
     */
    public function __construct(Plugin $plugin = null)
    {
        if (!\is_null($plugin)) {
            $this->_plugin = $plugin;
        }
    }
    /**
     * Set plugin data.
     *
     * @param Plugin $plugin
     * @since 1.0.3
     * @return Core
     */
    public function plugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }
    /**
     * Get plugin data.
     *
     * @since 1.0.3
     * @return Plugin
     */
    public function getPlugin() : Plugin
    {
        return $this->_plugin;
    }
    /**
     * Get plugin debugger.
     *
     * @since 1.0.3
     * @return Debugger
     */
    public function debug() : Debugger
    {
        return $this->_plugin->debugger();
    }
    /**
     * Get plugin settings.
     *
     * @since 1.0.3
     * @return Manager
     */
    public function settings() : Manager
    {
        return $this->_plugin->settings();
    }
}
