<?php
use Piggly\WooPixGateway\CoreConnector;
?>
<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<a
	href="<?=admin_url( 'admin.php?page='.CoreConnector::domain() )?>"
	class="button-primary">
	Ir para Configurações Avançadas
</a>

<script>
	(function () { window.location.href = "<?=admin_url( 'admin.php?page='.CoreConnector::domain() )?>"; })();
</script>

<style>p.submit { display: none !important; }</style> 