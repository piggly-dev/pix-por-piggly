<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Traits;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
/**
 * Abstract repository.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Traits
 * @version 2.0.0
 * @since 2.0.0
 * @category Traits
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license piggly
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
trait HasPluginTrait
{
    /**
     * Plugin data.
     *
     * @var Plugin
     * @since 2.0.0
     */
    protected $_plugin;
    /**
     * Set plugin data.
     *
     * @param Plugin $plugin Plugin data.
     * @since 2.0.0
     * @return Core
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }
    /**
     * Get plugin data.
     *
     * @since 2.0.0
     * @return Plugin
     */
    public function plugin() : Plugin
    {
        return $this->_plugin;
    }
}
