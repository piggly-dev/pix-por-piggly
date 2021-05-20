<?php
namespace Piggly\WC\Pix\Order;

use WP_List_Table;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Receipts tables.
 *
 * @since      1.3.0 
 * @package    Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\Order
 * @author     Caique <caique@piggly.com.br>
 * @author     Piggly Lab <dev@piggly.com.br>
 */
class ReceiptTable extends WP_List_Table
{
	/**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, 
	 * as well as whether the class supports AJAX.
	 * 
	 * @since 1.3.0
	 * @return void
    */
   public function __construct() 
	{
		parent::__construct([
			'singular'=> 'wpgly_pix_table_link', //Singular label
			'plural' => 'wpgly_pix_table_links', //plural label, also this well be one of the table css class
			'ajax' => false, //We won't support Ajax for this table
			'screen' => 'wpgly_pix_receipt_screen'
	  	]);
	}

	/**
	 * Add extra markup in the toolbars before or after the list.
	 * 
	 * @since 1.3.0
	 * @since 1.3.8 Added search box
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list.
	 * @return void
	 */
	function extra_tablenav ( $which ) 
	{
		if ( $which == "top" )
		{ 
			echo 'Confira abaixo todos os comprovantes Pix enviados.'; 
			$this->search_box('Pesquisar Pedido', 'order-number');
		}

		if ( $which == 'bottom' )
		{ echo 'Os comprovantes também serão exibidos na metabox Pix na página do Pedido e o pedido será atualizado para "Comprovante Pix Recebido".'; }
	}

	/**
	 * Get table columns.
	 * 
	 * @since 1.3.0
	 * @since 1.3.8 Added checkboxes.
	 * @return array
	 */
	public function get_columns () 
	{
		return [
			'cb' => '<input type="checkbox" />',
			'order_number' => __('Pedido', \WC_PIGGLY_PIX_PLUGIN_NAME),
			'customer_email' => __('E-mail', \WC_PIGGLY_PIX_PLUGIN_NAME),
			'pix_receipt' => __('Comprovante Pix', \WC_PIGGLY_PIX_PLUGIN_NAME),
			'send_at' => __('Data do Envio', \WC_PIGGLY_PIX_PLUGIN_NAME)
		];
	}

	/**
	 * Get default table columns data.
	 * 
	 * @since 1.3.0
	 * @return mixed
	 */
	public function column_default ( $item, $column_name )
	{
		switch ( $column_name )
		{
			case 'order_number':
			case 'customer_email':
			case 'pix_receipt':
			case 'send_at':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	/**
	 * Get sortable columns.
	 * 
	 * @since 1.3.0
	 * @return array
	 */
	public function get_sortable_columns() 
	{
		return [
			'send_at' => ['send_at', false]
		];
	}

	/**
	 * Prepare items from database.
	 * 
	 * @since 1.3.0
	 * @since 1.3.8 Implemented search
	 * @return void
	 */
	public function prepare_items () 
	{
		global $wpdb, $_wp_column_headers;

		$screen     = get_current_screen();
		$table_name = $wpdb->prefix . 'wpgly_pix_receipts';
		$query      = "SELECT * FROM $table_name";
		$search     = filter_input( INPUT_POST, 's', \FILTER_SANITIZE_STRING );

		if ( !empty($search) )
		{ $query .= $wpdb->prepare(" WHERE order_number LIKE '%%%s%%' ", $search ); }

		//Parameters that are going to be used to order the result
		$orderby = filter_input ( INPUT_GET, 'orderby', \FILTER_SANITIZE_STRING );
		$order = filter_input ( INPUT_GET, 'order', \FILTER_SANITIZE_STRING );
		$order = !empty( $order ) ? $order : 'ASC';

		if ( !empty($orderby) && !empty($order) )
		{ $query .= ' ORDER BY '.$orderby.' '.$order; }
		else
		{ $query .= ' ORDER BY send_at DESC'; }

		//Number of elements in your table?
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		//How many to display per page?
		$perpage = 10;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? filter_input ( INPUT_GET, 'paged', \FILTER_VALIDATE_INT ) : '';

		//Page Number
		if( empty($paged) || !is_numeric($paged) || $paged<=0 )
		{ $paged=1; } 
		
		$totalpages = ceil($totalitems/$perpage); 

		//adjust the query to take pagination into account 
		if( !empty($paged) && !empty($perpage) )
		{ 
			$offset = ($paged-1)*$perpage; 
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage; 
		} 

		/* -- Register the pagination -- */ 
		$this->set_pagination_args(array(
			'total_items' => $totalitems,
			'total_pages' => $totalpages,
			'per_page' => $perpage,
		));

		$columns = $this->get_columns();
      $_wp_column_headers[$screen->id] = $columns;

		$this->items = $wpdb->get_results($query);
	}

	/**
	 * Display rows.
	 * 
	 * @since 1.3.0
	 * @since 1.3.8 Added checkboxes and actions.
	 * @return void
	 */
	public function display_rows ()
	{
		$records = $this->items;

		list ( $columns, $hidden ) = $this->get_column_info();

		if ( !empty( $records ) )
		{
			foreach ( $records as $rec )
			{
				printf('<tr id="item_%s">', $rec->id);
				$link  = null;
				$actions = [
					'view' => sprintf('<a href="%s" target="_blank">%s</a>', stripslashes($rec->pix_receipt), __('Ver Comprovante', \WC_PIGGLY_PIX_PLUGIN_NAME)),
					'delete' => sprintf('<a href="%s">%s</a>', admin_url( sprintf('admin.php?page=%s&action=delete&item_id=%s', \WC_PIGGLY_PIX_PLUGIN_NAME, $rec->id ) ), __('Deletar Comprovante', \WC_PIGGLY_PIX_PLUGIN_NAME)),
				];

				if ( $rec->auto_fill !== 0 )
				{ 
					$link = get_edit_post_link($rec->order_number); 
					$actions['edit'] = sprintf('<a href="%s">%s</a>', stripslashes($link), __('Editar Pedido', \WC_PIGGLY_PIX_PLUGIN_NAME));
				} 

				// Mark receipt as trusted
				$trusted = isset($rec->trusted) ? \boolval($rec->trusted) : null;

				foreach ( $columns as $column_name => $column_display )
				{
					$attrs = [];
					$attrs[] = sprintf('class="%s column-%s"', $column_name, $column_name);

					if ( in_array($column_name, $hidden) )
					{ $attrs[] = 'style="display:none;"'; }

					switch ( $column_name )
					{
						case 'cb': 
							printf('<th scope="row" class="check-column"><input type="checkbox" name="items[]" value="%s" id="cb-select-%s" /></th>', $rec->id, $rec->id);
							break;
						case 'order_number':
							if ( empty($link) ) :
								printf('<td %s>%s <code class="wpgly-action">Preenchimento Manual</code></td>', implode(' ', $attrs), stripslashes($rec->order_number));
							else :
								printf('<td %s><a href="%s">Pedido #%s</a> %s</td>', implode(' ', $attrs), stripslashes($link), stripslashes($rec->order_number), $this->row_actions($actions));
							endif;
							break;
						case 'customer_email':
							printf('<td %s>%s</td>', implode(' ', $attrs), $rec->customer_email);
							break;
						case 'pix_receipt':
							printf(
								'<td %s><a href="%s" target="_blank">%s</a>%s</td>', 
								implode(' ', $attrs), 
								stripslashes($rec->pix_receipt), 
								__('Ver Comprovante', \WC_PIGGLY_PIX_PLUGIN_NAME),
								$trusted === true ? ' - <small>(Arquivo seguro)</small>' : ($trusted === false ? ' - <small>(Arquivo aceito, mas sem validação de segurança)</small>' : '')
							);
							break;
						case 'send_at':
							$date = date_create($rec->send_at);
							printf('<td %s>%s</td>', implode(' ', $attrs), date_format($date,'d/m/Y H:i'));
							break;
					}
				}

				echo '</tr>';
			}
		}
	}

	/**
	 * Added bulk actions.
	 * 
	 * @since 1.3.8
	 * @return array
	 */
	public function get_bulk_actions () 
	{
		$actions = array('delete' => __('Deletar Comprovante', \WC_PIGGLY_PIX_PLUGIN_NAME));
		return $actions;
	}
}