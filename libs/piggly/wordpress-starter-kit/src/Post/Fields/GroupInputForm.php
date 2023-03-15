<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Interfaces\Renderable;
/**
 * Base implementation to a group of inputs inside a form.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.7
 * @since 1.0.7s
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class GroupInputForm extends HTMLField
{
    /**
     * Field options.
     *
     * @since 1.0.9
     * @var array
     */
    protected array $_options = [];
    /**
     * Rows with fields.
     *
     * @since 1.0.9
     * @var array<array<HTMLField>>
     */
    protected array $_rows = [];
    /**
     * Class constructor.
     *
     * @param array $options
     * @param array<array<HTMLField>> $rows
     * @since 1.0.9
     */
    public function __construct(array $options, array $rows = [])
    {
        $this->_options = \array_merge(['name' => null, 'column_size' => 12, 'submit' => 'Add data', 'cancel' => 'Cancel'], $options);
        $this->_rows = $rows;
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
     * Add a row of fields.
     *
     * @param array<HTMLField> $columns
     * @since 1.0.9
     * @return void
     */
    public function row(array $columns)
    {
        $this->_rows[] = $columns;
    }
    /**
     * Get fields.
     *
     * @since 1.0.10
     * @return array
     */
    public function fields() : array
    {
        $fields = [];
        foreach ($this->_rows as $row) {
            foreach ($row as $column) {
                $fields[] = $column;
            }
        }
        return $fields;
    }
    /**
     * Render to HTML.
     *
     * @param mixed $value
     * @since 1.0.9
     * @return string
     */
    public function render($values = []) : string
    {
        $html = "<div class=\"pgly-wps--column pgly-wps-col--{$this->columnSize()}\">";
        $html .= "<div class=\"pgly-wps--group pgly-form--input pgly-form--group\" data-name=\"{$this->name()}\">";
        $html .= '<span class="pgly-wps--message"></span>';
        $html .= '<svg class="pgly-wps--spinner pgly-wps-is-primary" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg>';
        $html .= '<div class="container">';
        foreach ($this->_rows as $row) {
            $html .= '<div class="pgly-wps--row">';
            foreach ($row as $column) {
                $column->onGroup();
                $_values = $values[$column->name()] ?? [];
                $html .= $column->render(...$_values);
            }
            $html .= '</div>';
        }
        $html .= '<div class="pgly-wps--row"><div class="pgly-wps--column">';
        $html .= "<button class=\"pgly-wps--button pgly-wps-is-primary pgly-gform--submit\">{$this->_options['submit']}</button>";
        $html .= "<button class=\"pgly-wps--button pgly-wps-is-secondary pgly-gform--cancel\">{$this->_options['cancel']}</button>";
        $html .= '</div></div>';
        $html .= '<div class="pgly-wps--row"><div class="pgly-wps--column pgly-wps--items"></div></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
