<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Tables;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Interfaces\PostTypeInterface;
use WP_List_Table;
/**
 * Record table manipulation.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Tables
 * @version 1.0.7
 * @since 1.0.7
 * @category Table
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
abstract class RecordTable extends WP_List_Table
{
    /**
     * Custom post type.
     *
     * @since 1.0.7
     * @var PostTypeInterface
     */
    protected PostTypeInterface $postType;
    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels,
     * as well as whether the class supports AJAX.
     *
     * @since 1.0.7
     * @return void
     */
    public function __construct(PostTypeInterface $postType)
    {
        $this->postType = $postType;
        $prefix = $postType::fieldPrefix();
        parent::__construct(['singular' => $prefix . '_table_link', 'plural' => $prefix . 's_table_links', 'ajax' => \false, 'screen' => $postType::getSlug() . '-table']);
    }
    /**
     * Get a list of columns.
     *
     * @since 1.0.7
     * @return array
     */
    public function get_columns()
    {
        throw new Exception('It must be implemented on child class...');
    }
    /**
     * Prepares the list of items for displaying.
     *
     * @since 1.0.7
     * @return void
     */
    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();
        $this->items = $this->fetch_table_data();
    }
    /**
     * Fetch data table.
     *
     * @since 1.0.7
     * @return void
     */
    public function fetch_table_data()
    {
        throw new Exception('It must be implemented on child class...');
    }
    /**
     * Return an array with WHERE expressions.
     *
     * @param array $columns
     * @since 1.0.7
     * @return array
     */
    protected function _applySearch(array $columns) : array
    {
        global $wpdb;
        $search = \filter_input(\INPUT_GET, 's', \FILTER_SANITIZE_STRING);
        if (empty($search)) {
            return [];
        }
        $where = [];
        foreach ($columns as $column) {
            $where[] = $wpdb->prepare("{$column} LIKE '%%%s%%'", $search);
        }
        return ['(' . \implode(' OR ', $where) . ')'];
    }
    /**
     * Apply where clausules.
     *
     * @param string $query
     * @param array $exp
     * @since 1.0.7
     * @return string
     */
    protected function _applyWhere(string $query, array $exp) : string
    {
        if (!empty($exp)) {
            $query .= \sprintf(' WHERE %s ', \implode(' AND ', $exp));
        }
        return $query;
    }
    /**
     * Apply ordenation to SQL query.
     *
     * @param string $query
     * @param array $columns
     * @since 1.0.7
     * @return string
     */
    protected function _applyOrdenation(string $query, array $columns) : string
    {
        if (empty($columns)) {
            return $query;
        }
        $columns = \array_map(function ($k, $v) {
            $v = empty($v) ? 'ASC' : $v;
            return "{$k} {$v}";
        }, \array_keys($columns), \array_values($columns));
        $query .= ' ORDER BY ' . \implode(', ', $columns);
        return $query;
    }
    /**
     * Applies pagination to SQL query.
     *
     * @param string $query
     * @since 1.0.7
     * @return string
     */
    protected function _applyPagination(string $query) : string
    {
        global $wpdb;
        //Number of elements in your table?
        $totalitems = $wpdb->query($query);
        //return the total number of affected rows
        //How many to display per page?
        $perpage = 10;
        //Which page is this?
        $paged = !empty($_GET['paged']) ? \filter_input(\INPUT_GET, 'paged', \FILTER_VALIDATE_INT) : '';
        //Page Number
        if (empty($paged) || !\is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = \ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }
        /* -- Register the pagination -- */
        $this->set_pagination_args(['total_items' => $totalitems, 'total_pages' => $totalpages, 'per_page' => $perpage]);
        return $query;
    }
    /**
     * Generates content for a single row of the table.
     *
     * @param object $item The current item.
     * @param string $column_name The current column name.
     * @since 1.0.7
     * @return string
     */
    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'priority':
                return \esc_html($item->priority);
            default:
                return 'Desconhecida';
        }
    }
    /**
     * Get sortable columns.
     *
     * @since 1.0.7
     * @return array
     */
    public function get_sortable_columns()
    {
        return [];
    }
    /**
     * Generates custom table navigation to prevent conflicting nonces.
     *
     * @param string $which The location of the bulk actions: 'top' or 'bottom'.
     * @since 1.0.7
     * @return void
     */
    protected function display_tablenav($which)
    {
        ?>
<div class="tablenav <?php 
        echo \esc_attr($which);
        ?>">

	<div class="alignleft actions bulkactions">
		<?php 
        $this->bulk_actions($which);
        ?>
	</div>
	<?php 
        $this->extra_tablenav($which);
        $this->pagination($which);
        ?>
	<br class="clear" />
</div>
<?php 
    }
    /**
     * Generates content for a single row of the table.
     *
     * @param object $item The current item.
     * @since 1.0.7
     * @return void
     */
    public function single_row($item)
    {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }
    /**
     * Add extra markup in the toolbars before or after the list.
     *
     * @since 1.0.7
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list.
     * @return void
     */
    public function extra_tablenav($which)
    {
        if ($which == 'top') {
            echo 'Abaixo, todos os ' . $this->postType::pluralName() . ' cadastrados.';
        }
    }
    /**
     * Returns an associative array containing the bulk action.
     *
     * @since 1.0.7
     * @return void
     */
    public function get_bulk_actions()
    {
        $actions = ['remover' => 'Remover ' . $this->postType::pluralName()];
        return $actions;
    }
    /**
     * Get links bulk actions.
     *
     * @param object $item A row's data.
     * @since 1.0.7
     * @return array [$edit, $remove]
     */
    public function get_links($item)
    {
        $url = admin_url('admin.php?page=' . $this->postType::getSlug());
        $edit_link = \esc_url(\add_query_arg(['id' => $item->id, 'action' => 'edit'], $url . '-content'));
        $remove_link = \esc_url(\add_query_arg(['id' => $item->id, 'action' => 'remove'], $url . '-content'));
        return [$edit_link, $remove_link];
    }
}
