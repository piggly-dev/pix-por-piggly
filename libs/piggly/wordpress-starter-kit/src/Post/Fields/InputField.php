<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Interfaces\Renderable;
/**
 * Base implementation to an input field.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.7
 * @since 1.0.7
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
abstract class InputField extends HTMLField
{
    /**
     * Field value.
     *
     * @since 1.0.9
     * @var mixed
     */
    protected $_value;
    /**
     * Class constructor.
     *
     * @since 1.0.9
     */
    public function __construct(array $options)
    {
        $this->_options = \array_merge([
            'name' => null,
            'label' => null,
            'description' => null,
            'prefix' => null,
            'placeholder' => null,
            'required' => \false,
            'default' => null,
            'allowed_values' => null,
            'parse' => null,
            // parse function fn ($v) => $v
            'transform' => null,
            // transformer function fn ($v) => $v
            'validation' => null,
            // array of functions fn ($v) => throw Exception
            'column_size' => 12,
            'on_group' => \false,
        ], $options);
        $this->_value = null;
    }
    /**
     * Get field column size.
     *
     * @since 1.0.9
     * @return int
     */
    public function columnSize() : int
    {
        return $this->_options['column_size'];
    }
    /**
     * Get field label.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function label() : string
    {
        return $this->_options['label'] ?? '';
    }
    /**
     * Get field description.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function description() : string
    {
        return $this->_options['description'] ?? '';
    }
    /**
     * Get field placeholder.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function placeholder() : string
    {
        return $this->_options['placeholder'] ?? '';
    }
    /**
     * Get field default value.
     *
     * @since 1.0.9
     * @return mixed
     */
    public function default()
    {
        return $this->_options['default'];
    }
    /**
     * Get if field is required.
     *
     * @since 1.0.9
     * @return boolean
     */
    public function isRequired() : bool
    {
        return $this->_options['required'] ?? \false;
    }
    /**
     * Get allowed value for field.
     *
     * @since 1.0.9
     * @return array|null
     */
    public function allowedValues() : ?array
    {
        return $this->_options['allowed_values'];
    }
    /**
     * Parse $value with parse function.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return mixed
     */
    public function parse($value)
    {
        if (empty($this->_options['parse'])) {
            return $value;
        }
        return $this->_options['parse']($value);
    }
    /**
     * Transform $value with transform function.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return mixed
     */
    public function transform($value)
    {
        if (empty($this->_options['transform'])) {
            return $value;
        }
        return $this->_options['transform']($value);
    }
    /**
     * Validate $value with validation functions.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return mixed
     */
    public function validation($value)
    {
        if (empty($this->_options['validation'])) {
            return $value;
        }
        foreach ($this->_options['validation'] as $validate) {
            $validate($value);
        }
        return ${$value};
    }
    /**
     * Get value or default.
     *
     * @since 1.0.9
     * @return mixed
     */
    public function value()
    {
        return $this->_value ?? $this->_options['default'] ?? null;
    }
    /**
     * Change value.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return self
     */
    public function changeValue($value)
    {
        $this->_value = $this->parse($value);
        return $this;
    }
    /**
     * Get css form class.
     *
     * @since 1.0.12
     * @return self
     */
    protected function getCssForm()
    {
        return $this->_options['on_group'] ? 'pgly-gform' : 'pgly-form';
    }
}
