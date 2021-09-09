<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings;

use RuntimeException;
/**
 * Manager read, save and delete wordpress
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
     * @param string $option Option name at Wordpress.
     * @param KeyingBucket $defaults Default settings.
     * @since 1.0.0
     * @return void
     */
    public function __construct(string $option, KeyingBucket $defaults = null)
    {
        $this->_bucket = \is_null($defaults) ? new KeyingBucket() : $defaults;
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
     * Reload setting from Wordpress options.
     * It will replace any settings loaded to
     * current bucket. Be careful.
     *
     * @since 1.0.0
     * @return self
     */
    public function reload()
    {
        $this->_bucket->import(get_option($this->_option, []));
        return $this;
    }
    /**
     * Delete all settings from Wordpress options.
     *
     * @since 1.0.0
     * @return self
     */
    public function delete()
    {
        if (delete_option($this->_option) === \false) {
            throw new RuntimeException(\sprintf('Cannot delete wordpress option `%s`.', $this->_option));
        }
        $this->_bucket = new KeyingBucket();
        return $this;
    }
    /**
     * Save current bucket to Wordpress options.
     *
     * @since 1.0.0
     * @return self
     */
    public function save()
    {
        update_option($this->_option, $this->_bucket->export());
        return $this;
    }
}
