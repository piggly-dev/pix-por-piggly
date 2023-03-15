<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields;

/**
 * Extract schema from an array of fields.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Fields
 * @version 1.0.10
 * @since 1.0.10
 * @category Fields
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class SchemaExtractor
{
    /**
     * Extract options from fields.
     *
     * @param array<HTMLField> $fields
     * @since 1.0.10
     * @return array
     */
    public static function extract(array $fields) : array
    {
        $schema = [];
        foreach ($fields as $field) {
            $schema[$field->name()] = !$field instanceof GroupInputForm ? $field->options() : ['schema' => static::extract($field->fields())];
        }
        return $schema;
    }
}
