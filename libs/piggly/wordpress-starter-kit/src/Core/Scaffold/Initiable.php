<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Plugin;
/**
 * Every business login must be at a different
 * class, to easy manager these classes, Initiable
 * class will give them a shortcut to create a
 * new instance and run the startup method.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Core\Scaffold
 * @version 1.0.3
 * @since 1.0.0
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
abstract class Initiable extends Internationalizable
{
    /**
     * Run startup method to class create
     * it own instance.
     *
     * @param Plugin $plugin
     * @since 1.0.0
     * @since 1.0.3 Static instead self
     * @return void
     */
    public static function init(Plugin $plugin = null)
    {
        $obj = new static($plugin);
        $obj->startup();
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
