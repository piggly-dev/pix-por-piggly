<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Interfaces\Renderable;
/**
 * Base implementation to a form.
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
class Form extends HTMLField
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
        $this->_options = \array_merge(['name' => null, 'id' => null, 'action' => null, 'record_id' => null, 'attrs' => [], 'method' => 'POST', 'submit' => 'Submit', 'remove' => 'Remove'], $options);
        $this->_rows = $rows;
    }
    /**
     * Add a row of fields.
     *
     * @param array<Renderable> $columns
     * @since 1.0.9
     * @return void
     */
    public function row(array $columns)
    {
        $this->_rows[] = $columns;
    }
    /**
     * Get form name.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function name(bool $withPrefix = \false) : ?string
    {
        return $this->_options['name'];
    }
    /**
     * Get form current record id.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function recordId() : ?string
    {
        return $this->_options['record_id'];
    }
    /**
     * Get form id.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function id() : ?string
    {
        return $this->_options['id'];
    }
    /**
     * Get form attrs.
     *
     * @since 1.0.12
     * @return array
     */
    public function attrs() : array
    {
        return $this->_options['attrs'] ?? [];
    }
    /**
     * Get form action.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function action() : ?string
    {
        return $this->_options['action'];
    }
    /**
     * Get form method.
     *
     * @since 1.0.9
     * @return string|null
     */
    public function method() : ?string
    {
        return $this->_options['method'];
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
     * Unset field.
     *
     * @param string $name
     * @since 0.1.0
     * @return self
     */
    public function unset(string $name)
    {
        foreach ($this->_rows as $row_idx => $row) {
            foreach ($row as $field_idx => $field) {
                if ($field->name() === $name) {
                    unset($this->_rows[$row_idx][$field_idx]);
                    return $this;
                }
            }
        }
        return $this;
    }
    /**
     * Convert form to a group.
     *
     * @param array $options
     * @since 1.0.12
     * @return GroupInputForm
     */
    public function toGroup(array $options) : GroupInputForm
    {
        return new GroupInputForm($options, $this->_rows);
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
        $id = $this->id() ?? '';
        $name = $this->name() ?? '';
        $action = $this->action() ?? '';
        $method = $this->method() ?? '';
        $recordId = $this->recordId() ?? '';
        $attrs = \implode(' ', $this->attrs());
        $html = "<form id=\"{$id}\" name=\"{$name}\" action=\"{$action}\" method=\"{$method}\" data-record-id=\"{$recordId}\" {$attrs}>";
        $html .= '<div class="pgly-wps--row"><div class="pgly-wps--column">';
        $html .= "<button class=\"pgly-wps--button pgly-wps-is-primary pgly-async--behaviour pgly-form--submit\">{$this->_options['submit']}<svg class=\"pgly-wps--spinner pgly-wps-is-white\" viewBox=\"0 0 50 50\"><circle class=\"path\" cx=\"25\" cy=\"25\" r=\"20\" fill=\"none\" stroke-width=\"5\"></circle></svg></button>";
        if (!empty($recordId) || $recordId === 0) {
            $html .= "<button class=\"pgly-wps--button pgly-wps-is-danger pgly-async--behaviour pgly-form--remove\">{$this->_options['remove']}<svg class=\"pgly-wps--spinner pgly-wps-is-white\" viewBox=\"0 0 50 50\"><circle class=\"path\" cx=\"25\" cy=\"25\" r=\"20\" fill=\"none\" stroke-width=\"5\"></circle></svg></button>";
        }
        $html .= '</div></div>';
        foreach ($this->_rows as $row) {
            $html .= '<div class="pgly-wps--row">';
            foreach ($row as $column) {
                if (\is_string($column)) {
                    $html .= $column;
                    continue;
                }
                $_values = $values[$column->name()] ?? [];
                $html .= $column->render(...$_values);
            }
            $html .= '</div>';
        }
        $html .= '<div class="pgly-wps--row"><div class="pgly-wps--column">';
        $html .= "<button class=\"pgly-wps--button pgly-wps-is-primary pgly-async--behaviour pgly-form--submit\">{$this->_options['submit']}<svg class=\"pgly-wps--spinner pgly-wps-is-white\" viewBox=\"0 0 50 50\"><circle class=\"path\" cx=\"25\" cy=\"25\" r=\"20\" fill=\"none\" stroke-width=\"5\"></circle></svg></button>";
        if (!empty($recordId) || $recordId === 0) {
            $html .= "<button class=\"pgly-wps--button pgly-wps-is-danger pgly-async--behaviour pgly-form--remove\">{$this->_options['remove']}<svg class=\"pgly-wps--spinner pgly-wps-is-white\" viewBox=\"0 0 50 50\"><circle class=\"path\" cx=\"25\" cy=\"25\" r=\"20\" fill=\"none\" stroke-width=\"5\"></circle></svg></button>";
        }
        $html .= '</div></div>';
        $html .= '</form>';
        return $html;
    }
}
