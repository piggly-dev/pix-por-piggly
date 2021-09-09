<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
/**
 * i18n manages the plugin trannslation
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
class i18n extends Initiable
{
    /**
     * Startup method with all actions and
     * filter to run.
     *
     * @since 1.0.0
     * @return void
     */
    public function startup()
    {
        WP::add_action('plugins_loaded', $this, 'load_plugin_textdomain');
    }
    /**
     * Load the plugin text domain for translation
     * at /path/to/plugin/languages.
     * 
     * @since 1.0.0
     * @since 1.0.2 Non static.
     * @return void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain($this->_plugin->getDomain(), \false, $this->_plugin->getAbspath() . '/languages');
    }
}
