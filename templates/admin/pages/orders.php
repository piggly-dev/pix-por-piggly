<?php
use Piggly\WooPixGateway\Data\OrdersTable;

if( !class_exists('WP_List_Table') ) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

$table = new OrdersTable();
$table->prepare_items();
?>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>


<script>
	document.addEventListener('DOMContentLoaded', () => {
		new PglyWpsAsync({
			container: '#pgly-wps-plugin',
			responseContainer: 'pgly-wps--response',
			url: wcPigglyPix.ajax_url,
			x_security: wcPigglyPix.x_security,
			messages: {
				request_error: 'Ocorreu um erro ao processar a requisição',
				invalid_fields: 'Campos inválidos'
			},
			debug: true
		});
	});
</script>

<button 
	class="pgly-wps--button pgly-async--behaviour pgly-wps-is-primary"
	data-action="pgly_wc_piggly_pix_admin_cron_process"
	>
	Processar Pix
	<svg 
		class="pgly-wps--spinner pgly-wps-is-white"
		viewBox="0 0 50 50">
		<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
	</svg>
</button>

<div class="pgly-wps--response" id="pgly-wps--response">
</div>

<form method="POST">
<?php
	$table->prepare_items();
	$table->display();
?>
</form>
