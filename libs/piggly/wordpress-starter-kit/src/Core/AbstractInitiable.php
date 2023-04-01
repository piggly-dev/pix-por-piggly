<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Traits\HasPluginTrait;
/**
 * Every business login must be at a different
 * class, to easy manager these classes, Initiable
 * class will give them a shortcut to create a
 * new instance and run the startup method.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core
 * @version 2.0.0
 * @since 2.0.0
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
abstract class AbstractInitiable
{
    use HasPluginTrait;
    /**
     * Run startup method to class create
     * it own instance.
     *
     * @param Plugin $plugin Plugin data.
     * @since 2.0.0
     * @return void
     */
    public static function init(Plugin $plugin = null)
    {
        $obj = new static();
        if ($plugin !== null) {
            $obj->setPlugin($plugin);
        }
        $obj->startup();
    }
    /**
     * Init a initiable class.
     *
     * @param string $initiable AbstractInitiable class name.
     * @since 1.0.0
     * @return void
     */
    public function initiable(string $initiable)
    {
        $initiable::init($this->_plugin);
    }
    /**
     * Init a bunch of initiable classes.
     *
     * @param array<string> $initiables AbstractInitiable class names.
     * @since 1.0.12
     * @return void
     */
    public function initiables(array $initiables)
    {
        foreach ($initiables as $initiable) {
            $initiable::init($this->_plugin);
        }
    }
    /**
     * Startup method with all actions and
     * filter to run.
     *
     * @since 1.0.0
     * @return void
     */
    public abstract function startup();
}
