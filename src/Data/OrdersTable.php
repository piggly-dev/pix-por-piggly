<?php
namespace Piggly\WooPixGateway\Data;

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Entities\TransactionEntity;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use WP_List_Table;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Receipts tables.
 *
 * @since 2.0.0
 * @package Piggly\WC\Pix
 * @subpackage Piggly\WC\Pix\Order
 * @author Caique <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 */
class OrdersTable extends WP_List_Table
{
	/**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, 
	 * as well as whether the class supports AJAX.
	 * 
	 * @since 2.0.0
	 * @return void
    */
   public function __construct() 
	{
		parent::__construct([
			'singular'=> 'pgly_wc_piggly_pix_orders_table_link', //Singular label
			'plural' => 'pgly_wc_piggly_pix_orders_table_links', //plural label, also this well be one of the table css class
			'ajax' => false, //We won't support Ajax for this table
			'screen' => 'pgly_wc_piggly_pix_orders_screen'
	  	]);
	}

	/**
	 * Add extra markup in the toolbars before or after the list.
	 * 
	 * @since 2.0.0
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list.
	 * @return void
	 */
	function extra_tablenav ( $which ) 
	{
		if ( $which == "top" )
		{ echo 'Abaixo, todas as transações Pix.'; }

		if ( $which == 'bottom' )
		{ echo 'Os Pix associados a cada pedido também são exibidas em uma metabox ao acessar o pedido.'; }
	}

	/**
	 * Get table columns.
	 * 
	 * @since 2.0.0
	 * @return array
	 */
	public function get_columns () 
	{
		return [
			'pix' => 'Pix'
		];
	}

	/**
	 * Get default table columns data.
	 * 
	 * @since 2.0.0
	 * @return mixed
	 */
	public function column_default ( $item, $column_name )
	{
		switch ( $column_name )
		{
			case 'pix':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	/**
	 * Get sortable columns.
	 * 
	 * @since 2.0.0
	 * @return array
	 */
	public function get_sortable_columns() 
	{
		return [
			'updated_at' => ['updated_at', false],
			'created_at' => ['created_at', false]
		];
	}

	/**
	 * Prepare items from database.
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function prepare_items () 
	{
		global $wpdb, $_wp_column_headers;

		$screen     = get_current_screen();
		$table_name = $wpdb->prefix . 'pgly_pix';
		$query      = "SELECT * FROM $table_name";
		$where      = [];

		if ( !empty($search = filter_input( INPUT_POST, 's', \FILTER_SANITIZE_STRING )) ) 
		{ $where[] = $wpdb->prepare("(txid LIKE '%%%s%%' OR e2eid LIKE '%%%s%%')", $search ); }

		$status = filter_input( INPUT_POST, 'status', \FILTER_SANITIZE_STRING );

		if ( !empty($status = filter_input( INPUT_POST, 'status', \FILTER_SANITIZE_STRING )) ) 
		{ $where[] = $wpdb->prepare("(status = %s)", $status ); }

		$query .= \sprintf(' %s ', \implode(' AND ', $where));

		//Parameters that are going to be used to order the result
		$orderby = filter_input ( INPUT_GET, 'orderby', \FILTER_SANITIZE_STRING );
		$order   = filter_input ( INPUT_GET, 'order', \FILTER_SANITIZE_STRING );
		$order   = !empty( $order ) ? $order : 'ASC';

		if ( !empty($orderby) && !empty($order) )
		{ $query .= \sprintf(' ORDER BY %s %s ', $orderby,	$order); }
		else
		{ $query .= ' ORDER BY created_at DESC'; }

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
	 * @since 2.0.0
	 * @return void
	 */
	public function display_rows ()
	{
		$records = $this->items;

		list ( $columns, $hidden ) = $this->get_column_info();

		if ( !empty( $records ) )
		{
			$i = 1;

			foreach ( $records as $_pix )
			{
				$pix = PixEntity::create($_pix, false);

				printf('<tr id="item_%s">', $pix->getTxid());
				
				foreach ( $columns as $column_name => $column_display )
				{
					if ( $i % 2 !== 0 )
					{ $style = 'pgly-wps--explorer pgly-wps-is-compact pgly-wps-is-white'; }
					else
					{ $style = 'pgly-wps--explorer pgly-wps-is-compact'; }

					$i++;

					switch ( $column_name )
					{
						case 'pix':
							?>
							<td class="column column-pix">
								<div class="pgly-wps--row">
									<div class="pgly-wps--column pgly-wps-col--6">
										<div class="<?=$style?>">
											<strong>Tipo</strong>
											<span><?=$pix->getTypeLabel();?></span>
										</div>
										<div class="<?=$style?>">
											<strong>Status</strong>
											<div style="margin-top: 4px" class="pgly-wps--badge pgly-wps-is-<?=$pix->getStatusColor()?>"><?=$pix->getStatusLabel();?></div>
										</div>
										<div class="<?=$style?>">
											<strong>Chave Pix</strong>
											<span><?=$pix->getPixKeyValue();?> (<?=Parser::getAlias($pix->getPixKeyType());?>)</span>
										</div>
										<div class="<?=$style?>">
											<strong>Valor do Pix</strong>
											<span><?=\wc_price($pix->getAmount());?></span>
										</div>
										
										<?php if ( !empty($_pix->oid) ) : ?>
										<a 
											class="pgly-wps--button pgly-wps-is-success pgly-wps-is-primary"
											href="<?=get_edit_post_link($_pix->oid);?>"
											target="_blank">
											Ver Pedido
										</a>
										<?php endif; ?>
										
										<?php if ( !empty($pix->getReceipt()['url']) ) : ?>
										<a 
											class="pgly-wps--button pgly-wps-is-success pgly-wps-is-regular"
											href="<?=$pix->getReceipt()['url'];?>"
											target="_blank">
											Visualizar Comprovante
										</a>
										<?php endif; ?>
									</div>
									<div class="pgly-wps--column pgly-wps-col--3">
										<div class="<?=$style?>">
											<strong>Criado em</strong>
											<span><?=$pix->getCreatedAt()->format('d/m/Y H:i:s');?></span>
										</div>
									</div>
									<div class="pgly-wps--column pgly-wps-col--3">
										<div class="<?=$style?>">
											<strong>Atualizado em</strong>
											<span><?=$pix->getUpdatedAt()->format('d/m/Y H:i:s');?></span>
										</div>
									</div>
								</div>
							</td>
							<?php
							break;
					}
				}

				echo '</tr>';
			}
		}
	}
}