<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Upgrades;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\WP;
use stdClass;
/**
 * The ExternalApiUpgrades class is a scaffold
 * to create a external api upgrades.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Upgrades
 * @version 2.0.0
 * @since 2.0.0
 * @category Scaffold
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
class ExternalApiUpgrades
{
    /**
     * Cache key.
     *
     * @var string
     * @since 2.0.0
     */
    protected $_cache_key;
    /**
     * Is cache allowed.
     *
     * @var bool
     * @since 2.0.0
     */
    protected $_cache_allowed;
    /**
     * API Url.
     *
     * @var string
     * @since 2.0.0
     */
    protected $_api_url;
    /**
     * API Key.
     *
     * @var array
     * @since 2.0.0
     */
    protected $_headers;
    /**
     * Constructor.
     *
     * @param string $api_url API url. Will replace [slug] with plugin slug. Eg.: https://example.com/api/[slug].
     * @param array $headers API headers. You may include Authorization header.
     * @since 2.0.0
     * @return void
     */
    public function __construct(string $api_url, array $headers = [])
    {
        $this->_api_url = $api_url;
        $this->_headers = $headers;
        $this->_cache_key = Connector::plugin()->name() . '_cache_update';
        $this->_cache_allowed = \false;
    }
    /**
     * Allow cache.
     *
     * @since 2.0.0
     * @return self
     */
    public function allowCache()
    {
        $this->_cache_allowed = \true;
        return $this;
    }
    /**
     * Startup method with all actions and
     * filter to run.
     *
     * @since 2.0.0
     * @return void
     */
    public function hooks()
    {
        WP::add_filter('site_transient_update_plugins', $this, 'update', 10, 1);
        WP::add_filter('plugins_api', $this, 'info', 20, 3);
        WP::add_filter('plugin_row_meta', $this, 'details_link', 25, 4);
        WP::add_action('upgrader_process_complete', $this, 'purge', 10, 2);
    }
    /**
     * Get plugin information.
     *
     * Your API must return a JSON object with the following format:
     *
     * {
     *   "name": "Plugin Name", // Plugin name.
     *   "slug": "plugin-slug", // Plugin slug.
     *   "homepage": "https://wordpress.org/plugins/plugin-slug/", // Plugin homepage.
     *   "author": "Author Name", // Plugin author.
     *   "author_profile": "https://profiles.wordpress.org/user", // Author profile URL on wordpress.org
     *   "contributors": { // The associative array of contributors
     *      "user": "https://profiles.wordpress.org/user"
     *   },
     *   "version": "1.0.0", // Current plugin version available to install.
     *   "versions": { // The associative array of versions.
     *       "1.0.0": "https://example.com/download/plugin-slug.zip",
     *   }
     *   "tested": "5.3.2", // The latest WordPress version plugin tested with.
     *   "requires": "4.0", // The minimum WordPress version required.
     *   "requires_php": "5.6", // The minimum PHP version required.
     *   "rating": 100, // The average rating of the plugin. From 1 to 100.
     *   "ratings": { // The associative array of ratings.
     *       1: 2,
     *       2: 45,
     *       3: 555,
     *       4: 45,
     *       5: 245
     *   },
     *   "num_ratings": 1000, // The number of ratings.
     *   "active_installs": 1000, // The number of active installations.
     *   "added": "2020-03-20 12:00:00", // The date the plugin was added to the repository.
     *   "last_updated": "2020-03-20 12:00:00", // The date the plugin was last updated.
     *   "download_url": "https://example.com/download/plugin-slug.zip",
     *   "trunk": "https://example.com/download/plugin-slug.zip",
     *   "sections": {
     *      "description": "Description of the plugin.",
     *      "installation": "Installation instructions.",
     *      "changelog": "List of changes in the plugin."
     *   },
     *   "banners": {
     *      "low": "https://example.com/banner-772x250.jpg",
     *      "high": "https://example.com/banner-1544x500.jpg"
     *   },
     * }
     *
     * in case you want the screenshots tab, use the following HTML format for its content:.
     * <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>.
     *
     * @param bool $res Response.
     * @param string $action Action.
     * @param object $args Arguments.
     * @see plugins_api() in wp-admin/includes/plugin-install.php
     * @since 2.0.0
     * @return object
     */
    public function info($res, $action, $args)
    {
        // Do nothing if this is not about getting plugin information.
        if ($action !== 'plugin_information') {
            return $res;
        }
        // Do nothing if it is not our plugin.
        if (\strpos($args->slug, Connector::plugin()->dirname()) === \false) {
            return $res;
        }
        // Get all plugin information.
        $remote = $this->request();
        if (empty($remote) || $remote === \false) {
            return $res;
        }
        // Fix all arrays.
        if (!empty($remote->contributors)) {
            $remote->contributors = \get_object_vars($remote->contributors);
        }
        if (!empty($remote->versions)) {
            $remote->versions = \get_object_vars($remote->versions);
        }
        if (!empty($remote->ratings)) {
            $remote->ratings = \get_object_vars($remote->ratings);
        }
        if (!empty($remote->sections)) {
            $remote->sections = \get_object_vars($remote->sections);
        }
        if (!empty($remote->banners)) {
            $remote->banners = \get_object_vars($remote->banners);
        }
        return \apply_filters(Connector::plugin()->name() . '_api_info', $res, $remote);
    }
    /**
     * Check for updates.
     *
     * @param object $transient Transient object.
     * @see wp_update_plugins() in wp-admin/includes/update.php
     * @since 2.0.0
     * @return object
     */
    public function update($transient)
    {
        // $transient->checked is an array of all installed plugins.
        // $transient->response is an array of all plugins that have an update available.
        // No need to query the API if nothing is installed.
        if (empty($transient->checked)) {
            return $transient;
        }
        // Get all plugin information.
        $remote = $this->request();
        // Do nothing if we don't get the correct response from the server.
        if (empty($remote) || $remote === \false) {
            return $transient;
        }
        $remote_version = $remote->version ?? Connector::plugin()->version();
        $remote_requires = $remote->requires ?? \get_bloginfo('version');
        $remote_requires_php = $remote->requires_php ?? \PHP_VERSION;
        if (\version_compare(Connector::plugin()->version(), $remote_version, '<') && \version_compare($remote_requires, \get_bloginfo('version'), '<=') && \version_compare($remote_requires_php, \PHP_VERSION, '<=')) {
            $res = new stdClass();
            $res->slug = Connector::plugin()->dirname();
            $res->plugin = Connector::plugin()->basename();
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $transient->response[$res->plugin] = $res;
        }
        return $transient;
    }
    /**
     * Add plugin details link.
     *
     * @param array $links_array Links array.
     * @param string $plugin_file_name Plugin file name.
     * @see plugin_row_meta() in wp-admin/includes/plugin.php
     * @since 2.0.0
     * @return array
     */
    public function details_link($links_array, $plugin_file_name)
    {
        if (\strpos($plugin_file_name, Connector::plugin()->basename())) {
            $links_array[] = \sprintf('<a href="%s" class="thickbox open-plugin-details-modal">%s</a>', \add_query_arg(['tab' => 'plugin-information', 'plugin' => Connector::plugin()->dirname(), 'TB_iframe' => \true, 'width' => 772, 'height' => 788], \admin_url('plugin-install.php')), \__('View details'));
        }
        return $links_array;
    }
    /**
     * Purge cache.
     *
     * @param object $upgrader Upgrader object.
     * @param array $options Options.
     * @see upgrade_plugin_complete() in wp-admin/includes/class-wp-upgrader.php
     * @since 2.0.0
     * @return void
     */
    public function purge($upgrader, $options)
    {
        if ($this->_cache_allowed && $options['action'] === 'update' && $options['type'] === 'plugin') {
            // Just clean the cache when new plugin version is installed.
            \delete_transient($this->_cache_key);
        }
    }
    /**
     * Prepare and run request.
     *
     * @see plugins_api() in wp-admin/includes/plugin-install.php
     * @since 2.0.0
     * @return object
     */
    protected function request()
    {
        $remote = \get_transient($this->_cache_key);
        if ($remote === \false || $this->_cache_allowed === \false) {
            // Get all plugin information.
            $remote = \wp_remote_get(\str_replace('[slug]', Connector::plugin()->dirname(), $this->_api_url), ['timeout' => 10, 'headers' => \array_merge(['Accept' => 'application/json', 'Content-Type' => 'application/json'], $this->_headers)]);
            // Do nothing if we don't get the correct response from the server.
            if (\is_wp_error($remote) || \wp_remote_retrieve_response_code($remote) !== 200 || empty(\wp_remote_retrieve_body($remote))) {
                return null;
            }
            \set_transient($this->_cache_key, $remote, \DAY_IN_SECONDS);
        }
        //end if
        return \json_decode(\wp_remote_retrieve_body($remote));
    }
}
