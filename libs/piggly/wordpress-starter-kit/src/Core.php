<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces\RunnableInterface;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\AbstractInitiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets\KeyingBucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Traits\HasPluginTrait;
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
class Core extends AbstractInitiable
{
    use HasPluginTrait;
    /**
     * Startup plugin core with an activator,
     * a deactivator and a upgrader.
     *
     * @param Plugin $plugin Master plugin settings.
     * @param RunnableInterface $activator Run at register_activation_hook().
     * @param RunnableInterface $deactivator Run at register_deactivation_hook().
     * @param RunnableInterface $upgrader Manage updates logic.
     * @since 1.0.0
     * @since 1.0.3 Plugin as param.
     * @return void
     */
    public function __construct(Plugin $plugin, RunnableInterface $activator = null, RunnableInterface $deactivator = null, RunnableInterface $upgrader = null)
    {
        $this->setPlugin($plugin);
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
     * Add a RunnableInterface object as activator to
     * register_activation_hook().
     *
     * @param RunnableInterface $activator Activator to run.
     * @since 1.0.0
     * @return void
     */
    public function activator(RunnableInterface $activator)
    {
        // Plugin activation.
        \register_activation_hook($this->_plugin->bucket()->get('filename'), array($activator, 'run'));
    }
    /**
     * Add a RunnableInterface object as deactivator to
     * register_deactivation_hook().
     *
     * @param RunnableInterface $deactivator Deactivator to run.
     * @since 1.0.0
     * @return void
     */
    public function deactivator(RunnableInterface $deactivator)
    {
        // Plugin deactivation.
        \register_deactivation_hook($this->_plugin->bucket()->get('filename'), array($deactivator, 'run'));
    }
    /**
     * Add a RunnableInterface object as the upgrader
     * to manage updates and similar actions.
     *
     * @param RunnableInterface $upgrader Upgrader to run.
     * @since 1.0.0
     * @return void
     */
    public function upgrader(RunnableInterface $upgrader)
    {
        $upgrader->run();
    }
    /**
     * Apply requirements and if all meet return true.
     *
     * @param string $response Message to show if requirements not meet.
     * @param array $requirements Requirements to meet.
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
                $response = \esc_html($response);
                echo '<div class="notice notice-error">';
                echo '<p>' . \esc_html($response) . '</p>';
                echo '<p>' . \esc_html($e->getMessage()) . '</p>';
                echo '</div>';
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
                \deactivate_plugins(\plugin_basename(__FILE__));
            });
            return \false;
        }
        //end try
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
     * Plugin settings name will me $name . '_settings'.
     * E.g.: 'my_plugin_settings'.
     *
     * @param string $domain Plugin domain for translations.
     * @param string $name Plugin name for actions and filters.
     * @param KeyingBucket|null $settings Plugin bucket of settings.
     * @param array $options Plugin options.
     * @since 1.0.0
     * @throws Exception If plugin file or version are not set.
     * @return Core
     */
    public static function create(string $domain, string $name, KeyingBucket $settings = null, array $options = []) : Core
    {
        if (empty($options['plugin_file']) || empty($options['plugin_version'])) {
            throw new Exception('Plugin __FILE__ and plugin version are both required...');
        }
        $plugin = (new Plugin($domain, $name . '_settings', $settings))->changeAbsPath($options['plugin_file'])->changeUrl($options['plugin_file'])->changeBasename($options['plugin_file'])->changeName($name)->changeVersion($options['plugin_version']);
        if (!empty($options['db_version'])) {
            $plugin->dbVersion($options['db_version']);
        }
        if (!empty($options['php_version'])) {
            $plugin->changeMinPhpVersion($options['php_version']);
        }
        $activator = null;
        $deactivator = null;
        $upgrader = null;
        if ($options['activator'] instanceof RunnableInterface === \true) {
            $activator = new $options['activator']($plugin);
        }
        if ($options['deactivator'] instanceof RunnableInterface === \true) {
            $deactivator = new $options['deactivator']($plugin);
        }
        if ($options['upgrader'] instanceof RunnableInterface === \true) {
            $upgrader = new $options['upgrader']($plugin);
        }
        $core = new Core($plugin, $activator, $deactivator, $upgrader);
        Connector::setInstance($core);
        $core->startup();
        return $core;
    }
}
