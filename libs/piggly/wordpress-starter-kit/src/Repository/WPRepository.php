<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository;

use Exception;
use stdClass;
/**
 * Base implementation to WPDB.
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
abstract class WPRepository implements RepositoryInterface
{
    /**
     * Get by id.
     *
     * @param mixed $id
     * @since 1.0.7
     * @since 1.0.10 controls output
     * @return mixed
     */
    public static function byId($id, string $output = 'ARRAY_A')
    {
        global $wpdb;
        $table = static::tableName();
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), $output);
    }
    /**
     * Get by query.
     *
     * @param string $sql
     * @since 1.0.7
     * @return array<stdClass>
     */
    public static function byQuery(string $sql) : array
    {
        global $wpdb;
        return $wpdb->get_results($sql);
    }
    /**
     * Insert record.
     *
     * @param array $data
     * @param string $primary_key
     * @since 1.0.7
     * @return array
     * @throws Exception if fail
     */
    public static function insert(array $data, string $primary_key) : array
    {
        global $wpdb;
        $table = static::tableName();
        if ($wpdb->insert($table, $data) === \false) {
            $error = empty($wpdb->last_error) ? $wpdb->last_query : $wpdb->last_error;
            throw new Exception("Erro ao executar a query: {$error}.");
        }
        $data[$primary_key] = $wpdb->insert_id;
        return $data;
    }
    /**
     * Update record.
     *
     * @param array $data
     * @param array $where
     * @since 1.0.7
     * @return array
     * @throws Exception if fail
     */
    public static function update(array $data, array $where = []) : array
    {
        global $wpdb;
        $table = static::tableName();
        if ($wpdb->update($table, $data, $where) === \false) {
            $error = empty($wpdb->last_error) ? $wpdb->last_query : $wpdb->last_error;
            throw new Exception("Erro ao executar a query: {$error}.");
        }
        return $data;
    }
    /**
     * Delete record.
     *
     * @param array $where
     * @since 1.0.7
     * @return bool
     */
    public static function delete(array $where) : bool
    {
        global $wpdb;
        $table = static::tableName();
        if ($wpdb->delete($table, $where) === \false) {
            $error = empty($wpdb->last_error) ? $wpdb->last_query : $wpdb->last_error;
            throw new Exception("Erro ao executar a query: {$error}.");
        }
        return \true;
    }
}
