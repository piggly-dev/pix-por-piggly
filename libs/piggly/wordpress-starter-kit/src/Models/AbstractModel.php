<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Models;

use DateTime;
use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository\WPRepository;
use stdClass;
/**
 * Abstraction of models.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Models
 * @version 1.0.12
 * @since 1.0.12
 * @category Model
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
abstract class AbstractModel
{
    /**
     * Fields for entity.
     *
     * @since 1.0.12
     * @var array
     */
    protected array $_fields = [];
    /**
     * Hide following fields when
     * updating or insertin.
     *
     * @since 1.0.12
     * @var array
     */
    protected array $_hidden = [];
    /**
     * Get a field.
     *
     * @param string $field
     * @param mixed $default
     * @since 1.0.12
     * @return mixed
     */
    public function get(string $field, $default = null)
    {
        return $this->_fields[$field] ?? $default;
    }
    /**
     * Set a field.
     *
     * @param string $field
     * @param mixed $value
     * @since 1.0.12
     * @return self
     */
    public function set(string $field, $value)
    {
        $func = 'mutate_' . $field;
        if (\method_exists($this, $func)) {
            $value = $this->{$func}($value);
        }
        $this->_fields[$field] = $value;
        return $this;
    }
    /**
     * Has a field. Same as isset().
     *
     * @param string $field
     * @param mixed $value
     * @since 1.0.12
     * @return bool
     */
    public function has(string $field) : bool
    {
        return isset($this->_fields[$field]);
    }
    /**
     * Is a field empty? Same as empty()
     *
     * @param string $field
     * @param mixed $value
     * @since 1.0.12
     * @return bool
     */
    public function empty(string $field) : bool
    {
        return empty($this->_fields[$field]);
    }
    /**
     * Save entity on database.
     *
     * @since 1.0.12
     * @return void
     */
    public function save()
    {
        $fields = $this->_prepare();
        try {
            if ($this->isCreated()) {
                static::getRepo()::update($this->_removeFromArray($fields, $this->_hidden), [static::primaryKey() => $this->id()]);
                return \true;
            }
            $this->_fields[static::primaryKey()] = static::getRepo()::insert($this->_removeFromArray($fields, $this->_hidden), static::primaryKey())[static::primaryKey()];
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Remove entity from database.
     *
     * @since 1.0.12
     * @return boolean
     */
    public function remove() : bool
    {
        if (!$this->isCreated()) {
            return \false;
        }
        return static::getRepo()::delete([static::primaryKey() => $this->_fields[static::primaryKey()]]);
    }
    /**
     * Get the id of entity. If empty, entity
     * must be saved to database before.
     *
     * @since 1.0.12
     * @return mixed
     */
    public function id()
    {
        return $this->_fields[static::primaryKey()] ?? null;
    }
    /**
     * Return if entity is created on database.
     *
     * @since 1.0.12
     * @return boolean
     */
    public function isCreated() : bool
    {
        return isset($this->_fields[static::primaryKey()]);
    }
    /**
     * Applies fields to current fields.
     * Will only apply valid values...
     *
     * @param array $fields
     * @since 1.0.12
     * @return void
     */
    public function apply(array $fields = [])
    {
        foreach ($this->_fields as $key => $value) {
            if (isset($fields[$key])) {
                $this->_fields[$key] = $fields[$key];
            }
        }
    }
    /**
     * Convert entity to an array.
     *
     * @param array $base
     * @since 1.0.12
     * @return array
     */
    public abstract function toArray(array $base = []) : array;
    /**
     * Prepare fields before save.
     *
     * @since 1.0.12
     * @return array
     */
    protected function _prepare() : array
    {
        $fields = $this->_fields;
        $fields['updated_at'] = (new DateTime('now', \wp_timezone()))->format('Y-m-d\\TH:i:s');
        return $fields;
    }
    /**
     * Remove fields from array.
     *
     * @param array $arr
     * @param array $remove
     * @since 1.0.12
     * @return array
     */
    protected function _removeFromArray(array $arr, array $remove) : array
    {
        return \array_filter($arr, function ($k) use($remove) {
            return !\in_array($k, $remove);
        }, \ARRAY_FILTER_USE_KEY);
    }
    /**
     * Create entity object from parsed body.
     *
     * @param array $parsed Parsed body.
     * @param array $raw Raw body.
     * @since 1.0.12
     * @return self
     */
    public static abstract function fromBody(array $parsed, array $raw = []);
    /**
     * Create entity object from record object.
     *
     * @param stdClass $record
     * @since 1.0.12
     * @return self
     */
    public static abstract function fromRecord(stdClass $record);
    /**
     * Create entity object with defaults.
     *
     * @param mixed $options
     * @since 1.0.12
     * @return self
     */
    public static abstract function create(array $options = []);
    /**
     * Get the primary key column name.
     *
     * @since 1.0.12
     * @return string
     */
    public static abstract function primaryKey() : string;
    /**
     * Get repository.
     *
     * @since 1.0.12
     * @return WPRepository
     */
    public static abstract function getRepo() : WPRepository;
}
