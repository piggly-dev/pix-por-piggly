<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets\KeyingBucket;
use RuntimeException;
/**
 * Manager read, save and delete WordPress
 * option usign Bucket as settings data interface.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Settings
 * @version 1.0.0
 * @since 1.0.0
 * @category Settings
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Manager
{
    /**
     * Bucket.
     *
     * @var KeyingBucket
     * @since 1.0.0
     */
    protected $_bucket;
    /**
     * Option name.
     *
     * @var string
     * @since 1.0.0
     */
    protected $_option;
    /**
     * Startup settings manager.
     *
     * @param string $option Option name at WordPress.
     * @param KeyingBucket $defaults Default settings.
     * @since 1.0.0
     * @return void
     */
    public function __construct(string $option, KeyingBucket $defaults = null)
    {
        if ($defaults === null) {
            $defaults = new KeyingBucket();
        }
        $this->_bucket = $defaults;
        $this->_option = $option;
        $this->reload();
    }
    /**
     * Get settings bucket to manages.
     *
     * @since 1.0.0
     * @return KeyingBucket
     */
    public function bucket() : KeyingBucket
    {
        return $this->_bucket;
    }
    /**
     * Reload setting from WordPress options.
     * It will replace any settings loaded to
     * current bucket. Be careful.
     *
     * @since 1.0.0
     * @return self
     */
    public function reload()
    {
        $this->_bucket->import(\get_option($this->_option, []));
        return $this;
    }
    /**
     * Delete all settings from WordPress options.
     *
     * @since 1.0.0
     * @throws RuntimeException When cannot delete option.
     * @return self
     */
    public function delete()
    {
        if (\delete_option($this->_option) === \false) {
            throw new RuntimeException('Cannot delete plugin options. Unexpected error found.');
        }
        $this->_bucket = new KeyingBucket();
        return $this;
    }
    /**
     * Save current bucket to WordPress options.
     *
     * @since 1.0.0
     * @return self
     */
    public function save()
    {
        \update_option($this->_option, $this->_bucket->export());
        return $this;
    }
}
