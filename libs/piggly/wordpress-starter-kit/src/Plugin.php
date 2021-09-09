<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Debugger;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\i18n;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\Bucket;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings\KeyingBucket;
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
 * @version 1.0.3
 * @since 1.0.0
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
     * @since 1.0.0
     */
    protected $_bucket;
    /**
     * Debugger.
     *
     * @var Debugger
     * @since 1.0.2
     */
    protected $_debugger;
    /**
     * Settings.
     *
     * @var Manager
     * @since 1.0.2
     */
    protected $_settings;
    /**
     * Startup runtime settings.
     * 
     * @param string $domain Plugin domain.
     * @param string $option Option setting name at Wordpress.
     * @param KeyingBucket $defaults Default plugin settings.
     * @param boolean $debug Debug status.
     * @since 1.0.0
     * @since 1.0.2 Added setting and debugger.
     * @return void
     */
    public function __construct(string $domain, string $option, KeyingBucket $defaults = null)
    {
        $this->_bucket = new KeyingBucket();
        $this->_settings = new Manager($option, $defaults);
        $this->_debugger = new Debugger($domain);
        $this->domain($domain);
        $this->_debugger->changeState($this->_settings->bucket()->get('debug', \false));
        i18n::init($this);
    }
    /**
     * Get runtime settings bucket to manage.
     *
     * @since 1.0.0
     * @return KeyingBucket
     */
    public function bucket() : KeyingBucket
    {
        return $this->_bucket;
    }
    /**
     * Get settings manager.
     *
     * @since 1.0.2
     * @return Manager
     */
    public function settings() : Manager
    {
        return $this->_settings;
    }
    /**
     * Get debugger.
     *
     * @since 1.0.2
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
     * @param string $abspath
     * @since 1.0.0
     * @since 1.0.3 Filename to plugin.
     * @return self
     */
    public function abspath(string $abspath)
    {
        $this->_bucket->set('filename', $abspath);
        $this->_bucket->set('abspath', plugin_dir_path($abspath));
        $this->_bucket->set('templatePath', plugin_dir_path($abspath) . 'templates/');
        return $this;
    }
    /**
     * Get plugin absolute path.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getAbspath() : ?string
    {
        return $this->_bucket->get('abspath');
    }
    /**
     * Set basename to plugin. It applies plugin_basename()
     * function to $basename string.
     *
     * @param string $basename
     * @since 1.0.0
     * @return self
     */
    public function basename(string $basename)
    {
        $this->_bucket->set('basename', plugin_basename($basename));
        $this->_bucket->set('dirname', \dirname($basename));
        return $this;
    }
    /**
     * Get plugin basename.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getBasename() : ?string
    {
        return $this->_bucket->get('basename');
    }
    /**
     * Set version to plugin database.
     *
     * @param string $dbVersion
     * @since 1.0.0
     * @return self
     */
    public function dbVersion(string $dbVersion)
    {
        $this->_bucket->set('dbVersion', $dbVersion);
        return $this;
    }
    /**
     * Get plugin database version.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getDbVersion() : ?string
    {
        return $this->_bucket->get('dbVersion');
    }
    /**
     * Set text domain to plugin.
     *
     * @param string $domain
     * @since 1.0.0
     * @return self
     */
    public function domain(string $domain)
    {
        $this->_bucket->set('domain', $domain);
        return $this;
    }
    /**
     * Get plugin text domain.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getDomain() : ?string
    {
        return $this->_bucket->get('domain');
    }
    /**
     * Set a name to plugin.
     *
     * @param string $domain
     * @since 1.0.0
     * @return self
     */
    public function name(string $name)
    {
        $this->_bucket->set('name', $name);
        return $this;
    }
    /**
     * Get plugin name.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->_bucket->get('name');
    }
    /**
     * Set the minimum php version supported by plugin.
     *
     * @param string $phpVersion
     * @since 1.0.0
     * @return self
     */
    public function minPhpVersion(string $phpVersion)
    {
        $this->_bucket->set('minPhpVersion', $phpVersion);
        return $this;
    }
    /**
     * Get plugin minimum php version.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getMinPhpVersion() : ?string
    {
        return $this->_bucket->get('minPhpVersion');
    }
    /**
     * Get plugin absolute template path.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getTemplatePath() : ?string
    {
        return $this->_bucket->get('templatePath');
    }
    /**
     * Set url to plugin. It applies plugin_dir_url()
     * function to $url string.
     *
     * @param string $url
     * @since 1.0.0
     * @return self
     */
    public function url(string $url)
    {
        $this->_bucket->set('url', plugin_dir_url($url));
        return $this;
    }
    /**
     * Get plugin url.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getUrl() : ?string
    {
        return $this->_bucket->get('url');
    }
    /**
     * Set the plugin version.
     *
     * @param string $version
     * @since 1.0.0
     * @return self
     */
    public function version(string $version)
    {
        $this->_bucket->set('version', $version);
        return $this;
    }
    /**
     * Get plugin version.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getVersion() : ?string
    {
        return $this->_bucket->get('version');
    }
    /**
     * Set the plugin notices transient name.
     *
     * @param string $transient
     * @since 1.0.0
     * @return self
     */
    public function notices(string $transient)
    {
        $this->_bucket->set('noticesTransient', $transient);
        return $this;
    }
    /**
     * Get plugin notices transient name.
     * 
     * @since 1.0.0
     * @return string|null
     */
    public function getNotices() : ?string
    {
        return $this->_bucket->get('noticesTransient');
    }
}
