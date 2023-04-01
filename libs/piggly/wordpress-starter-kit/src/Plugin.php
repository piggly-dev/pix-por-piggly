<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\WP;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets\KeyingBucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Manager;
/**
 * This class is used to manage all runtime
 * settings required to plugin, such as current
 * version, abspath, basename, url, domain name...
 *
 * The purpose is replace the use of constants
 * created with define() function. This will be
 * the master plugin runtime settings and it will
 * be accessible by WP class.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress
 * @version 2.0.0
 * @since 2.0.0
 * @category Settings
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Plugin
{
    /**
     * Bucket.
     *
     * @var KeyingBucket
     * @since 2.0.0
     */
    protected $_bucket;
    /**
     * Debugger.
     *
     * @var Debugger
     * @since 2.0.0
     */
    protected $_debugger;
    /**
     * Settings.
     *
     * @var Manager
     * @since 2.0.0
     */
    protected $_settings;
    /**
     * Startup runtime settings.
     *
     * @param string $domain Plugin domain.
     * @param string $option Option setting name at WordPress.
     * @param KeyingBucket $option_defaults Default plugin settings.
     * @since 2.0.0
     * @return void
     */
    public function __construct(string $domain, string $option, KeyingBucket $option_defaults = null)
    {
        $this->_bucket = new KeyingBucket();
        $this->_settings = new Manager($option, $option_defaults);
        $this->_debugger = new Debugger($domain);
        $this->changeDomain($domain);
        $this->_debugger->changeState($this->_settings->bucket()->get('debug', \false));
        WP::add_action('plugins_loaded', $this, 'load_plugin_textdomain');
    }
    /**
     * Get runtime settings bucket to manage.
     *
     * @since 2.0.0
     * @return KeyingBucket
     */
    public function bucket() : KeyingBucket
    {
        return $this->_bucket;
    }
    /**
     * Get settings manager.
     *
     * @since 2.0.0
     * @return Manager
     */
    public function settings() : Manager
    {
        return $this->_settings;
    }
    /**
     * Get debugger.
     *
     * @since 2.0.0
     * @return Debugger
     */
    public function debugger() : Debugger
    {
        return $this->_debugger;
    }
    /**
     * Set absolute path to plugin. It applies plugin_dir_path()
     * function to $abspath string.
     *
     * @param string $abspath Plugin absolute path.
     * @since 2.0.0
     * @return self
     */
    public function changeAbsPath(string $abspath)
    {
        $this->_bucket->set('filename', $abspath);
        $this->_bucket->set('abspath', \plugin_dir_path($abspath));
        $this->_bucket->set('templatePath', \plugin_dir_path($abspath) . 'templates/');
        return $this;
    }
    /**
     * Get plugin absolute path.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function absPath() : ?string
    {
        return $this->_bucket->get('abspath');
    }
    /**
     * Set basename to plugin. It applies plugin_basename()
     * function to $basename string. And solve dirname.
     *
     * @param string $basename Plugin basename.
     * @since 2.0.0
     * @return self
     */
    public function changeBasename(string $basename)
    {
        $this->_bucket->set('basename', \plugin_basename($basename));
        $this->_bucket->set('dirname', \dirname($basename));
        return $this;
    }
    /**
     * Get plugin basename.
     * Eg.: my-plugin/my-plugin.php
     *
     * @since 2.0.0
     * @return string
     */
    public function basename() : string
    {
        return $this->_bucket->get('basename', '');
    }
    /**
     * Get plugin dirname.
     * Eg.: my-plugin
     *
     * @since 2.0.0
     * @return string
     */
    public function dirname() : string
    {
        return $this->_bucket->get('dirname', '');
    }
    /**
     * Set version to plugin database.
     *
     * @param string $dbVersion Plugin database version.
     * @since 2.0.0
     * @return self
     */
    public function changeDbVersion(string $dbVersion)
    {
        $this->_bucket->set('dbVersion', $dbVersion);
        return $this;
    }
    /**
     * Get plugin database version.
     *
     * @since 2.0.0
     * @return string
     */
    public function dbVersion() : string
    {
        return $this->_bucket->get('dbVersion', '');
    }
    /**
     * Set text domain to plugin.
     *
     * @param string $domain Plugin text domain.
     * @since 2.0.0
     * @return self
     */
    public function changeDomain(string $domain)
    {
        $this->_bucket->set('domain', $domain);
        return $this;
    }
    /**
     * Get plugin text domain.
     *
     * @since 2.0.0
     * @return string
     */
    public function domain() : string
    {
        return $this->_bucket->get('domain', '');
    }
    /**
     * Set a name to plugin.
     *
     * @param string $name Plugin name.
     * @since 2.0.0
     * @return self
     */
    public function changeName(string $name)
    {
        $this->_bucket->set('name', $name);
        return $this;
    }
    /**
     * Get plugin name.
     *
     * @since 2.0.0
     * @return string
     */
    public function name() : string
    {
        return $this->_bucket->get('name', '');
    }
    /**
     * Set the minimum php version supported by plugin.
     *
     * @param string $phpVersion Minimum php version.
     * @since 2.0.0
     * @return self
     */
    public function changeMinPhpVersion(string $phpVersion)
    {
        $this->_bucket->set('minPhpVersion', $phpVersion);
        return $this;
    }
    /**
     * Get plugin minimum php version.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function minPhpVersion() : string
    {
        return $this->_bucket->get('minPhpVersion', '0');
    }
    /**
     * Get plugin absolute template path.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function templatePath() : string
    {
        return $this->_bucket->get('templatePath', '');
    }
    /**
     * Set url to plugin. It applies plugin_dir_url()
     * function to $url string.
     *
     * @param string $url Plugin url.
     * @since 2.0.0
     * @return self
     */
    public function changeUrl(string $url)
    {
        $this->_bucket->set('url', \plugin_dir_url($url));
        return $this;
    }
    /**
     * Get plugin url.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function url() : string
    {
        return $this->_bucket->get('url', '');
    }
    /**
     * Set the plugin version.
     *
     * @param string $version Plugin version.
     * @since 2.0.0
     * @return self
     */
    public function changeVersion(string $version)
    {
        $this->_bucket->set('version', $version);
        return $this;
    }
    /**
     * Get plugin version.
     *
     * @since 2.0.0
     * @return string
     */
    public function version() : string
    {
        return $this->_bucket->get('version', '');
    }
    /**
     * Load the plugin text domain for translation
     * at /path/to/plugin/languages.
     *
     * @since 2.0.0
     * @since 2.0.0 Non static.
     * @return void
     */
    public function load_plugin_textdomain()
    {
        \load_plugin_textdomain($this->domain(), \false, $this->absPath() . '/languages');
    }
}
