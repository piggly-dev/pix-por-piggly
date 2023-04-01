<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Buckets;

use RuntimeException;
/**
 * The AbstractBucket class is a collection of
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
abstract class AbstractBucket
{
    /**
     * Buckets array.
     *
     * @var array
     * @since 2.0.0
     */
    protected $_data = [];
    /**
     * Import an array to bucket.
     *
     * @param array<AbstractBucket|mixed> $data Data to import.
     * @param bool $overwrite Overwrite current settings.
     * @since 2.0.0
     * @since 2.0.0 Optimizations
     * @return self
     */
    public function import(array $data, bool $overwrite = \true)
    {
        if ($this instanceof NonKeyingBucket) {
            return $this->importAsNonKeyingBucket($data, $overwrite);
        }
        if ($this instanceof KeyingBucket) {
            return $this->importAsKeyingBucket($data, $overwrite);
        }
        return $this;
    }
    /**
     * Import an array to bucket as a non-keying bucket.
     *
     * @param array<AbstractBucket|mixed> $data Data to import.
     * @param bool $overwrite Overwrite current settings.
     * @since 2.0.0
     * @throws RuntimeException When bucket is not a NonKeyingBucket.
     * @return self
     */
    protected function importAsNonKeyingBucket(array $data, bool $overwrite = \true)
    {
        /** @var NonKeyingBucket $this */
        if ($overwrite) {
            $this->set($data);
        } else {
            foreach ($data as $value) {
                $this->push($value);
            }
        }
        return $this;
    }
    /**
     * Import an array to bucket as a keying bucket.
     *
     * @param array<AbstractBucket|mixed> $data Data to import.
     * @param bool $overwrite Overwrite current settings.
     * @since 2.0.0
     * @throws RuntimeException When bucket is not a KeyingBucket.
     * @return self
     */
    protected function importAsKeyingBucket(array $data, bool $overwrite = \true)
    {
        /** @var KeyingBucket $this */
        foreach ($data as $key => $value) {
            // New key, so add...
            if (!$this->has($key)) {
                $this->set($key, $value);
                continue;
            }
            // Is a bucket, then import...
            if ($this->_data[$key] instanceof AbstractBucket) {
                if (!$value instanceof AbstractBucket && !\is_array($value)) {
                    throw new RuntimeException(\sprintf('Setting value `%s` must be a AbstractBucket object or an array.', $key));
                }
                if ($value instanceof AbstractBucket === \true) {
                    $value = $value->export();
                }
                $this->_data[$key]->import($value, $overwrite);
                continue;
            }
            // Not a bucket, then overwrite when needed.
            if ($overwrite) {
                $this->set($key, $value);
                continue;
            }
        }
        //end foreach
        return $this;
    }
    /**
     * Export current bucket to an array.
     *
     * It will call export_{$key}($value) method when
     * it exists to export value key from bucket.
     *
     * @since 2.0.0
     * @return array
     */
    public function export() : array
    {
        if ($this instanceof NonKeyingBucket) {
            $settings = [];
            foreach ($this->_data as $value) {
                if ($value instanceof AbstractBucket) {
                    $settings[] = $value->export();
                    continue;
                }
                $settings[] = $value;
            }
            return $settings;
        }
        if ($this instanceof KeyingBucket) {
            $settings = [];
            foreach ($this->_data as $key => $value) {
                if ($value instanceof AbstractBucket) {
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
     * @param array $arr Array to check.
     * @since 2.0.0
     * @since 2.0.0 Empty array is always non associative.
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
