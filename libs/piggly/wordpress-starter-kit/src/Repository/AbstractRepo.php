<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Database\AbstractTableSchema;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Interfaces\RecordableModelInterface;
use wpdb;
/**
 * Abstract repository.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Repository
 * @version 2.0.0
 * @since 2.0.0
 * @category Interfaces
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license piggly
 * @copyright 2023 Piggly Lab <dev@piggly.com.br>
 */
abstract class AbstractRepo
{
    /**
     * Save model.
     *
     * @param RecordableModelInterface $model Model to save.
     * @since 2.0.0
     * @throws Exception If cannot insert or update.
     * @return bool
     */
    public static function save(RecordableModelInterface $model) : bool
    {
        $data = \json_decode(\wp_json_encode($model->toRecord()), \true);
        try {
            if ($model->isPreloaded()) {
                static::update($data, [$model->primaryKeyName() => $model->primaryKey()]);
            } else {
                $data = static::insert($data, $model->primaryKeyName());
                $model->preload($data[$model->primaryKeyName()]);
            }
            return \true;
        } catch (Exception $e) {
            Connector::debugger()->error($e->getMessage());
            throw new Exception('Cannot insert/update on database. See logs.', 500, $e);
        }
    }
    /**
     * Insert record.
     *
     * @param array $data Data to insert.
     * @param string $primary_key Primary key name.
     * @since 2.0.0
     * @throws Exception If cannot insert.
     * @return array
     */
    protected static function insert(array $data, string $primary_key) : array
    {
        /** @var wpdb $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $table = static::schema()::tableName();
        if ($wpdb->insert($table, $data) === \false) {
            static::throwError($wpdb);
        }
        $data[$primary_key] = $wpdb->insert_id;
        return $data;
    }
    /**
     * Update record.
     *
     * @param array $data Data to update.
     * @param array $where Where to update.
     * @since 2.0.0
     * @return array
     * @throws Exception If cannot update.
     */
    protected static function update(array $data, array $where = []) : array
    {
        /** @var wpdb $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $table = static::schema()::tableName();
        if ($wpdb->update($table, $data, $where) === \false) {
            static::throwError($wpdb);
        }
        return $data;
    }
    /**
     * Delete record.
     *
     * @param array $where Where to delete.
     * @since 2.0.0
     * @return bool
     */
    public static function delete(array $where) : bool
    {
        /** @var wpdb $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $table = static::schema()::tableName();
        if ($wpdb->delete($table, $where) === \false) {
            static::throwError($wpdb);
        }
        return \true;
    }
    /**
     * Throw an error from wpdb.
     *
     * @param wpdb $wpdb WPDB instance.
     * @throws Exception Query error.
     * @return void
     * @since 2.0.0
     */
    protected function throwError(wpdb $wpdb) : void
    {
        $error = $wpdb->last_query ?? 'Unknown error.';
        if (!empty($wpdb->last_error)) {
            $error = $wpdb->last_error;
        }
        throw new Exception($error);
    }
    /**
     * Get table schema.
     *
     * @since 2.0.0
     * @return AbstractTableSchema
     */
    public static abstract function schema() : AbstractTableSchema;
}
