<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Settings;

use RuntimeException;
/**
 * The Bucket class is a collection of
 * settings keys.
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
abstract class Bucket
{
    /**
     * Settings array.
     *
     * @var array
     * @since 1.0.0
     */
    protected $_settings = [];
    /**
     * Import an array to bucket.
     *
     * @param array<Bucket|mixed> $data
     * @param array $options
     * @since 1.0.0
     * @since 1.0.4 Optimizations
     * @return self
     */
    public function import(array $data, bool $overwrite = \true)
    {
        if ($this instanceof NonKeyingBucket) {
            if ($overwrite) {
                $this->set($data);
            } else {
                foreach ($data as $value) {
                    $this->push($value);
                }
            }
        } else {
            if ($this instanceof KeyingBucket) {
                foreach ($data as $key => $value) {
                    // New key, so add...
                    if (!$this->has($key)) {
                        $this->set($key, $value);
                        continue;
                    }
                    // Is a bucket, then import...
                    if ($this->_settings[$key] instanceof Bucket) {
                        if (!$value instanceof Bucket && !\is_array($value)) {
                            throw new RuntimeException(\sprintf('Setting value `%s` must be a Bucket object or an array.', $key));
                        }
                        $value = $value instanceof Bucket ? $value->export() : $value;
                        $this->_settings[$key]->import($value, $overwrite);
                        continue;
                    }
                    // Not a bucket, then overwrite when needed
                    if ($overwrite) {
                        $this->set($key, $value);
                        continue;
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Export current bucket to an array.
     * 
     * It will call export_{$key}($value) method when
     * it exists to export value key from bucket.
     * 
     * @since 1.0.3
     * @return array
     */
    public function export() : array
    {
        if ($this instanceof NonKeyingBucket) {
            $settings = [];
            foreach ($this->_settings as $value) {
                if ($value instanceof Bucket) {
                    $settings[] = $value->export();
                    continue;
                }
                $settings[] = $value;
            }
            return $settings;
        }
        if ($this instanceof KeyingBucket) {
            $settings = [];
            foreach ($this->_settings as $key => $value) {
                if ($value instanceof Bucket) {
                    $settings[$key] = $value->export();
                    continue;
                }
                if (\method_exists($this, 'export_' . $key)) {
                    $getter = 'export_' . $key;
                    $settings[$key] = $this->{$getter}($value);
                    continue;
                }
                $settings[$key] = $value;
            }
            return $settings;
        }
        return [];
    }
    /**
     * Return if $arr is associative or not.
     *
     * @param array $arr
     * @since 1.0.0
     * @since 1.0.4 Empty array is always non associative.
     * @return boolean
     */
    protected static function isAssociative(array $arr) : bool
    {
        $n = \count($arr);
        if (empty($n)) {
            return \false;
        }
        for ($i = 0; $i < $n; $i++) {
            if (\array_key_exists($i, $arr)) {
                return \false;
            }
        }
        return \true;
    }
}
