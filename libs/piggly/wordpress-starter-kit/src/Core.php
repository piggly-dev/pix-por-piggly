<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Interfaces\Runnable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
/**
 * The Core class startup all plugin business
 * logic, it is the first startup point to plugin.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress
 * @version 1.0.0
 * @since 1.0.0
 * @category Core
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Core extends Initiable
{
    /**
     * Startup plugin core with an activator,
     * a deactivator and a upgrader.
     *
     * @param Plugin $plugin Master plugin settings.
     * @param Runnable $activator Run at register_activation_hook()
     * @param Runnable $deactivator Run at register_deactivation_hook()
     * @param Runnable $upgrader Manage updates logic.
     * @since 1.0.0
     * @since 1.0.3 Plugin as param.
     * @return void
     */
    public function __construct(Plugin $plugin, Runnable $activator = null, Runnable $deactivator = null, Runnable $upgrader = null)
    {
        $this->plugin($plugin);
        if ($activator) {
            $this->activator($activator);
        }
        if ($deactivator) {
            $this->deactivator($deactivator);
        }
        if ($upgrader) {
            $this->upgrader($upgrader);
        }
    }
    /**
     * Startup method with all actions and
     * filter to run.
     *
     * @since 1.0.12
     * @return void
     */
    public function startup()
    {
    }
    /**
     * Add a Runnable object as activator to
     * register_activation_hook().
     *
     * @param Runnable $activator
     * @since 1.0.0
     * @return void
     */
    public function activator(Runnable $activator)
    {
        // Plugin activation
        register_activation_hook($this->_plugin->bucket()->get('filename'), array($activator, 'run'));
    }
    /**
     * Add a Runnable object as desactivator to
     * register_deactivation_hook().
     *
     * @param Runnable $desactivator
     * @since 1.0.0
     * @return void
     */
    public function deactivator(Runnable $desactivator)
    {
        // Plugin desactivation
        register_deactivation_hook($this->_plugin->bucket()->get('filename'), array($desactivator, 'run'));
    }
    /**
     * Add a Runnable object as the upgrader
     * to manage updates and similar actions.
     *
     * @param Runnable $upgrader
     * @since 1.0.0
     * @return void
     */
    public function upgrader(Runnable $upgrader)
    {
        $upgrader->run();
    }
    /**
     * Init a initiable class.
     *
     * @param string $initiable
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
     * @param array<string> $initiables
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
     * Apply requirements and if all meet return true.
     *
     * @param string $response
     * @param array $requirements
     * @since 1.0.12
     * @return bool
     */
    public static function requirements(string $response, array $requirements) : bool
    {
        try {
            foreach ($requirements as $class => $params) {
                $class::run($params);
            }
            return \true;
        } catch (Exception $e) {
            \add_action('admin_notices', function () use($e, $response) {
                $html = '<div class="notice notice-error">';
                $html .= "<p>{$response}</p>";
                $html .= "<p>{$e->getMessage()}</p>";
                $html .= "</div>";
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
                \Piggly\WooPixGateway\Vendor\deactivate_plugins(\Piggly\WooPixGateway\Vendor\plugin_basename(__FILE__));
            });
            return \false;
        }
    }
    /**
     * Create a new application core and startup it.
     * All $options available are:
     *
     * plugin_file (*)
     * version (*)
     * db_version
     * min_php_version
     *
     * activator class name
     * deactivator class name
     * upgrader class name
     *
     * @param string $domain
     * @param string $name
     * @param KeyingBucket|null $settings
     * @param array $options
     * @return Core
     */
    public static function create(string $domain, string $name, KeyingBucket $settings = null, array $options = [])
    {
        if (empty($options['plugin_file']) || empty($options['plugin_version'])) {
            throw new Exception('Plugin __FILE__ and plugin version are both required...');
        }
        $plugin = (new Plugin($domain, $name . '_settings', $settings))->abspath($options['plugin_file'])->url($options['plugin_file'])->basename($options['plugin_file'])->name($name)->version($options['plugin_version'])->notices($name . '_notices');
        if (!empty($options['db_version'])) {
            $plugin->dbVersion($options['db_version']);
        }
        if (!empty($options['db_version'])) {
            $plugin->minPhpVersion($options['db_version']);
        }
        $core = new Core($plugin, $options['activator'] ? new $options['activator']($plugin) : null, $options['deactivator'] ? new $options['deactivator']($plugin) : null, $options['upgrader'] ? new $options['upgrader']($plugin) : null);
        Connector::setInstance($core);
        $core->startup();
        return $core;
    }
}
