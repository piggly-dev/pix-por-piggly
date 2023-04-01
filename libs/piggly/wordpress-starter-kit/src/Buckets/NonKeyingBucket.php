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
class NonKeyingBucket extends AbstractBucket
{
    /**
     * Reset bucket with array data.
     *
     * @param array $bucket Bucket data.
     * @since 2.0.0
     * @since 2.0.0 Fixed eternal loop
     * @return NonKeyingBucket
     */
    public function set(array $bucket)
    {
        $this->_data = $bucket;
        return $this;
    }
    /**
     * Add new value to bucket.
     *
     * When $value is an array, import it
     * as a new Bucket class.
     *
     * @param AbstractBucket|mixed $value Value to be added.
     * @since 2.0.0
     * @return NonKeyingBucket
     */
    public function push($value)
    {
        if (\is_array($value)) {
            $bucket = self::class;
            if (self::isAssociative($value)) {
                $bucket = KeyingBucket::class;
            }
            $this->_data[] = (new $bucket())->import($value);
            return $this;
        }
        $this->_data[] = $value;
        return $this;
    }
    /**
     * Remove the last element from bucket.
     *
     * @since 2.0.0
     * @return NonKeyingBucket
     */
    public function pop()
    {
        \array_pop($this->_data);
        return $this;
    }
    /**
     * Remove the first element from bucket.
     *
     * @since 2.0.0
     * @return NonKeyingBucket
     */
    public function shift()
    {
        \array_shift($this->_data);
        return $this;
    }
    /**
     * Count settings array.
     *
     * @since 2.0.0
     * @return integer
     */
    public function count() : int
    {
        return \count($this->_data);
    }
    /**
     * Get all bucket data.
     *
     * @since 2.0.0
     * @return array
     */
    public function get() : array
    {
        return $this->_data;
    }
}
