<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository;

use stdClass;
/**
 * Interface for database.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Repository
 * @version 1.0.7
 * @since 1.0.7
 * @category Repository
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
interface RepositoryInterface
{
    /**
     * Return if table exists.
     *
     * @since 1.0.7
     * @return boolean
     */
    public static function tableExists() : bool;
    /**
     * Get SQL to create table.
     *
     * @since 1.0.7
     * @return string
     */
    public static function createTable() : string;
    /**
     * Get by id.
     *
     * @param mixed $id
     * @since 1.0.7
     * @since 1.0.10 controls output
     * @return mixed
     */
    public static function byId($id, string $output);
    /**
     * Get by query.
     *
     * @param string $sql
     * @since 1.0.7
     * @return array<stdClass>
     */
    public static function byQuery(string $sql) : array;
    /**
     * Insert record.
     *
     * @param array $data
     * @param string $primary_key
     * @since 1.0.7
     * @return array
     * @throws Exception if fail
     */
    public static function insert(array $data, string $primary_key) : array;
    /**
     * Update record.
     *
     * @param array $data
     * @param array $where
     * @since 1.0.7
     * @return array
     * @throws Exception if fail
     */
    public static function update(array $data, array $where = []) : array;
    /**
     * Delete record.
     *
     * @param array $where
     * @since 1.0.7
     * @return bool
     */
    public static function delete(array $where) : bool;
    /**
     * Get SQL to table name.
     *
     * @since 1.0.7
     * @return string
     */
    public static function tableName() : string;
    /**
     * Get database version.
     *
     * @since 1.0.7
     * @return string
     */
    public static function version() : string;
    /**
     * Get installed database version.
     *
     * @since 1.0.7
     * @return string
     */
    public static function installedVersion() : string;
    /**
     * Update installed database version.
     *
     * @since 1.0.7
     * @return bool
     */
    public static function updateVersion() : bool;
}
