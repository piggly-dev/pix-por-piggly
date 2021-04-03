<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Shortcode de Pagamento Pix</h1>

<div style="max-width:720px; display: table">
	<p>
		Você pode utilizar o shortcode <code>[pix-por-piggly order_id="$id"]</code>
		para carregar o template de pagamento Pix em qualquer página que você
		deseje.
	</p>

	<h3>O shortcode não carregará o template, quando:</h3>

	<ul>
		<li>❌ Não for enviado o parâmetro <code>order_id</code>;</li>
		<li>❌ O módulo de pagamento Pix estiver desabilitado;</li>
		<li>❌ O <code>order_id</code> não existir;</li>
		<li>❌ O status do pedido for atualizado para <strong>Pago</strong>;</li>
		<li>❌ Nenhuma chave Pix estiver configurada.</li>
	</ul>
</div>