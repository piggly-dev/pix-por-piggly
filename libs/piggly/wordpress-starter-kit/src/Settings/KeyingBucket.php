<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings;

/**
 * The Bucket class is a collection of
 * settings keys.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Settings
 * @version 1.0.3
 * @since 1.0.3
 * @category Settings
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class KeyingBucket extends Bucket
{
    /**
     * Add new value to bucket.
     * 
     * Call set_{$key}($default) method if it
     * exists.
     * 
     * When $value is an array, import it 
     * as a new Bucket class.
     *
     * @param string $key
     * @param Bucket|mixed $value
     * @param integer $bucket_flag
     * @since 1.0.3
     * @return self
     */
    public function set(string $key, $value)
    {
        if (\method_exists($this, 'set_' . $key)) {
            $setter = 'set_' . $key;
            $this->{$setter}($value);
            return $this;
        }
        if (\is_array($value)) {
            $bucket = self::isAssociative($value) ? KeyingBucket::class : NonKeyingBucket::class;
            $this->_settings[$key] = empty($value) ? new $bucket() : (new $bucket())->import($value);
            return $this;
        }
        $this->_settings[$key] = $value;
        return $this;
    }
    /**
     * Get value by key.
     * 
     * Call get_{$key}($default) method if it
     * exists.
     *
     * @param string $key
     * @param mixed $default
     * @since 1.0.3
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (\method_exists($this, 'get_' . $key)) {
            $getter = 'get_' . $key;
            return $this->{$getter}($default);
        }
        return $this->_settings[$key] ?? $default;
    }
    /**
     * Get value by key.
     * 
     * Call get_{$key}($default) method if it
     * exists.
     * 
     * If it does not exists then set it
     * with the default value.
     *
     * @param string $key
     * @param mixed $default
     * @since 1.0.3
     * @return mixed
     */
    public function getAndCreate(string $key, $default = null)
    {
        if (\method_exists($this, 'get_' . $key)) {
            $getter = 'get_' . $key;
            return $this->{$getter}($default);
        }
        if (!isset($this->_settings[$key])) {
            $this->set($key, $default);
        }
        return $this->_settings[$key];
    }
    /**
     * Check if has value by key.
     *
     * @param string $key
     * @since 1.0.3
     * @return boolean
     */
    public function has(string $key) : bool
    {
        return isset($this->_settings[$key]);
    }
    /**
     * Remove value by key.
     *
     * @param string $key
     * @param mixed $default
     * @since 1.0.3
     * @return mixed
     */
    public function remove(string $key)
    {
        unset($this->_settings[$key]);
        return $this;
    }
}
