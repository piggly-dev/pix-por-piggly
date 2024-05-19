<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Base implementation to a section form.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.12
 * @since 1.0.12
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class SectionForm extends Form
{
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
        $html = "<section id=\"{$id}\" data-name=\"{$name}\" data-action=\"{$action}\" data-method=\"{$method}\" data-record-id=\"{$recordId}\" {$attrs}>";
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
        $html .= '</section>';
        return $html;
    }
}
