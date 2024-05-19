<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Interfaces\Renderable;
/**
 * Base implementation to a html field.
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
abstract class HTMLField implements Renderable
{
    /**
     * Field options.
     *
     * @since 1.0.9
     * @var array
     */
    protected array $_options = [];
    /**
     * Class constructor.
     *
     * @since 1.0.9
     */
    public function __construct(array $options)
    {
        $this->_options = \array_merge(['name' => null, 'prefix' => null], $options);
    }
    /**
     * Get field name.
     *
     * @param bool $withPrefix
     * @since 1.0.9
     * @return string|null
     */
    public function name(bool $withPrefix = \false) : ?string
    {
        if (empty($this->_options['name'])) {
            return null;
        }
        if ($withPrefix && !empty($this->_options['prefix'])) {
            return $this->_options['prefix'] . $this->_options['name'];
        }
        return $this->_options['name'];
    }
    /**
     * Get field prefix.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function prefix() : ?string
    {
        return $this->_options['prefix'];
    }
    /**
     * Get field options.
     *
     * @since 1.0.9
     * @return array
     */
    public function options() : array
    {
        return $this->_options;
    }
    /**
     * Force field to be on group.
     *
     * @since 1.0.12
     * @return void
     */
    public function onGroup() : void
    {
        $this->_options['on_group'] = \true;
    }
}
