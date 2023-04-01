<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets;

/**
 * The Bucket class is a collection of
 * settings keys.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Buckets
 * @version 2.0.0
 * @since 2.0.0
 * @category Buckets
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class KeyingBucket extends AbstractBucket
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
     * @param string $key Key name.
     * @param Bucket|mixed $value Value to be added.
     * @since 2.0.0
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
            $bucket = NonKeyingBucket::class;
            if (self::isAssociative($value)) {
                $bucket = self::class;
            }
            $_value = null;
            if (empty($value)) {
                $_value = new $bucket();
            } else {
                $_value = (new $bucket())->import($value);
            }
            $this->_data[$key] = $_value;
            return $this;
        }
        $this->_data[$key] = $value;
        return $this;
    }
    /**
     * Get value by key.
     *
     * Call get_{$key}($default) method if it
     * exists.
     *
     * @param string $key Key name.
     * @param mixed $default Default value.
     * @since 2.0.0
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (\method_exists($this, 'get_' . $key)) {
            $getter = 'get_' . $key;
            return $this->{$getter}($default);
        }
        return $this->_data[$key] ?? $default;
    }
    /**
     * Get value by key as Keying Bucket.
     *
     * @param string $key Key name.
     * @since 2.0.0
     * @return mixed
     */
    public function getAsKeyingBucket(string $key)
    {
        $bucket = $this->get($key, new KeyingBucket());
        if (!$bucket instanceof KeyingBucket) {
            $bucket = new KeyingBucket();
        }
        return $bucket;
    }
    /**
     * Get value by key as Non Keying Bucket.
     *
     * @param string $key Key name.
     * @since 2.0.0
     * @return mixed
     */
    public function getAsNonKeyingBucket(string $key)
    {
        $bucket = $this->get($key, new NonKeyingBucket());
        if (!$bucket instanceof NonKeyingBucket) {
            $bucket = new NonKeyingBucket();
        }
        return $bucket;
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
     * @param string $key Key name.
     * @param mixed $default Default value.
     * @since 2.0.0
     * @return mixed
     */
    public function getAndCreate(string $key, $default = null)
    {
        if (\method_exists($this, 'get_' . $key)) {
            $getter = 'get_' . $key;
            return $this->{$getter}($default);
        }
        if (!isset($this->_data[$key])) {
            $this->set($key, $default);
        }
        return $this->_data[$key];
    }
    /**
     * Check if has value by key.
     *
     * @param string $key Key name.
     * @since 2.0.0
     * @return boolean
     */
    public function has(string $key) : bool
    {
        return isset($this->_data[$key]);
    }
    /**
     * Remove value by key.
     *
     * @param string $key Key name.
     * @since 2.0.0
     * @return mixed
     */
    public function remove(string $key)
    {
        unset($this->_data[$key]);
        return $this;
    }
}
