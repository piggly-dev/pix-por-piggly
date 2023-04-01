<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Collection;

use stdClass;
/**
 * Record collection manipulation.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Collection
 * @version 1.0.12
 * @since 1.0.12
 * @category Table
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
class RecordCollection
{
    /**
     * Mounted query.
     *
     * @var string
     * @since 1.0.12
     */
    protected $_query;
    /**
     * Where AND clauses.
     * It will be joined with
     * AND expression.
     *
     * @var array
     * @since 1.0.12
     */
    protected $_where = [];
    /**
     * Order by expression.
     *
     * @var array
     * @since 1.0.12
     */
    protected $_order_by = [];
    /**
     * Pagination metadata.
     *
     * @since 1.0.12
     * @var array
     */
    protected $_pagination = [];
    /**
     * Start with base SQL string with select and join.
     *
     * @param string $base_query Base query.
     * @since 1.0.12
     */
    public function __construct(string $base_query)
    {
        $this->_query = $base_query;
    }
    /**
     * Add a where expression.
     *
     * @param string $expression Expression to add.
     * @since 1.0.12
     * @return self
     */
    public function where(string $expression)
    {
        $this->_where[] = $expression;
        return $this;
    }
    /**
     * Order by column.
     *
     * @param string $column Column name.
     * @param string $order ASC or DESC.
     * @since 1.0.12
     * @return self
     */
    public function orderBy(string $column, string $order = 'ASC')
    {
        $this->_order_by[] = "{$column} {$order}";
        return $this;
    }
    /**
     * Paginate current query.
     *
     * @param integer $perpage Items per page.
     * @param integer $page Current page.
     * @since 1.0.12
     * @return self
     */
    public function paginate(int $perpage, int $page = 1)
    {
        $this->_pagination = [];
        /** @var \wpdb $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        if ($page <= 0) {
            $page = 1;
        }
        $totalitems = $wpdb->query($this->mount());
        $totalpages = \ceil($totalitems / $perpage);
        if (!empty($page) && !empty($perpage)) {
            $this->_pagination = ['page' => $page, 'perpage' => $perpage, 'offset' => ($page - 1) * $perpage, 'totalpages' => $totalpages, 'totalitems' => $totalitems];
        }
        return $this;
    }
    /**
     * Mount current query.
     *
     * @since 1.0.12
     * @return string
     */
    public function mount() : string
    {
        $query = $this->_query;
        if (!empty($this->_where)) {
            $query .= ' WHERE (' . \implode(') AND (', $this->_where) . ') ';
        }
        if (!empty($this->_order_by)) {
            $query .= ' ORDER BY ' . \implode(', ', $this->_order_by) . ' ';
        }
        if (!empty($this->_pagination)) {
            $query .= ' LIMIT ' . (int) $this->_pagination['offset'] . ', ' . (int) $this->_pagination['perpage'];
        }
        return $query;
    }
    /**
     * Get all records to current query.
     *
     * @since 1.0.12
     * @return array<stdClass>
     */
    public function get() : array
    {
        /** @var \wpdb $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $results = $wpdb->get_results($this->mount());
        if (empty($results)) {
            return [];
        }
        return $results;
    }
    /**
     * Get pagination metadata.
     *
     * @since 1.0.12
     * @return array
     */
    public function paginationMetadata() : array
    {
        return $this->_pagination;
    }
    /**
     * Get pagination html.
     *
     * @param string $base_url Base url to pagination.
     * @param int $maxpages Max pages to show.
     * @since 1.0.12
     * @return string
     */
    public function htmlPagination(string $base_url, int $maxpages = 5) : string
    {
        if (empty($this->_pagination)) {
            return '';
        }
        $totalpages = $this->_pagination['totalpages'];
        $page = $this->_pagination['page'];
        $pages = [];
        if ($totalpages <= $maxpages) {
            $pages = \range(1, $totalpages);
        } else {
            if ($page < $maxpages) {
                $pages = \range(1, $maxpages);
                $pages[] = '...';
                $pages[] = $totalpages - 1;
                $pages[] = $totalpages;
            } elseif ($page + $maxpages > $totalpages) {
                $pages = [1, '...'];
                for ($i = 5; $i >= 0; $i--) {
                    $pages[] = $totalpages - $i;
                }
            } else {
                $pages = [1, '...', $page - 2, $page - 1, $page, $page + 1, $page + 2, '...', $totalpages];
            }
        }
        $html = '<div class="pgly-wps--navigator pgly-wps-are-small">';
        foreach ($pages as $_page) {
            if ($_page === '...') {
                $html .= '<span class="pgly-wps--item">...</span>';
                continue;
            }
            $is_current = '';
            if ($_page === $page) {
                $is_current = 'pgly-wps-is-selected';
            }
            $base_url = \esc_url($base_url);
            $_page = \esc_attr($_page);
            $is_current = \esc_attr($is_current);
            $html .= "<a href=\"{$base_url}&paged={$_page}\" title=\"Ir para a página {$_page}\" class=\"pgly-wps--item {$is_current}\">";
            $html .= $_page;
            $html .= '</a>';
        }
        $html .= '</div>';
        return $html;
    }
}
