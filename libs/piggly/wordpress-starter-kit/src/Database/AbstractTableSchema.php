<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Database;

/**
 * Abstract table schema.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Database
 * @version 2.0.0
 * @since 2.0.0
 * @category Database
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license piggly
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
abstract class AbstractTableSchema
{
    /**
     * Get SQL to create table.
     *
     * @since 2.0.0
     * @return string
     */
    public static abstract function createTable() : string;
    /**
     * Get table schema name.
     *
     * @since 2.0.0
     * @return string
     */
    public static abstract function getSchemaName() : string;
    /**
     * Get database version.
     *
     * @since 2.0.0
     * @return string
     */
    public static abstract function version() : string;
    /**
     * Get the option key that stores the database version.
     *
     * @since 2.0.0
     * @return string
     */
    public static abstract function versionOptionKey() : string;
    /**
     * Return if table exists.
     *
     * @since 2.0.0
     * @return boolean
     */
    public static function tableExists() : bool
    {
        $wpdb = $GLOBALS['wpdb'];
        $table = static::tableName();
        return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) === $table;
    }
    /**
     * Get SQL to table name.
     *
     * @since 2.0.0
     * @return string
     */
    public static function tableName() : string
    {
        $wpdb = $GLOBALS['wpdb'];
        return $wpdb->prefix . static::getSchemaName();
    }
    /**
     * Get installed database version.
     *
     * @since 2.0.0
     * @return string
     */
    public static function installedVersion() : string
    {
        return get_option(static::versionOptionKey(), '0');
    }
    /**
     * Update installed database version.
     *
     * @since 2.0.0
     * @return bool
     */
    public static function updateVersion() : bool
    {
        return \update_option(static::versionOptionKey(), static::version());
    }
}
