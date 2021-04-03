<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Novidades da versão 1.2.0</h1>

<div style="max-width:720px; display: table">
	<p>
		A versão <strong>1.2.0</strong> mudou completamente o núcleo do plugin, 
		para torná-lo mais eficiente e poderoso. Se você fez mudanças na estrutura 
		do plugin esteja ciente que elas serão perdidas. Os templates de e-mail e 
		do pagamento Pix foram atualizados para atender as melhorias.
	</p>

	<h3>Principais Melhorias</h3>

	<ul>
		<li>✅ Reformulação das configurações;</li>
		<li>✅ Criação da metabox Pix nos pedidos pagos via Pix;</li>
		<li>✅ Otimização para geração dos QR Codes;</li>
		<li>✅ Desconto automático para pagamento via Pix.</li>
	</ul>

	<h3>Performance do QR Code</h3>

	<p>
		Antes, o plugin gerava o QR Code toda vez que o Pix era visto. 
		E apresentava um <em>"fix"</em> para um e-mail que salvada um arquivo <code>.png</code>
		toda vez que o e-mail era enviado.
	</p>

	<p>
		Para melhorar a performance do Pix e evitar processar desnecessariamente
		a imagem dos QR Codes. Agora, o plugin gerar o Pix pela primeira vez, salva o 
		QR Code na pasta <code>uploads > <?=\WC_PIGGLY_PIX_DIR_NAME?> > qr-codes</code> em um arquivo 
		<code>.png</code> e grava nos meta dados do Pedido.
	</p>

	<p>
		Dessa forma, se o pedido já foi pago, os meta dados serão mantidos e você 
		sempre poderá conferir por qual chave aquele Pix foi pago, mesmo que decida mudar a chave.
	</p>

	<p>
		Se o Pix ainda não foi pago, será gerado novamente somente se você mudar a chave 
		Pix por qualquer razão. Do contrário, os meta dados gravado no pedido serão utilizados.
	</p>

	<h3>Futuras Implementações</h3>

	<ul>
		<li>⭕ Personalização completa do template de pagamento;</li>
		<li>⭕ Implementação das API Pix para atualizar pedidos automaticamente.</li>
	</ul>
</div>