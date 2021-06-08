<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="wpgly-wrapper">

	<h1 class="wpgly-title">Novidades da versão 1.3.14</h1>

	<p>
		O plugin agora suporta filtros e adota ações para personalizar
		ainda mais o comportamento do mesmo. Alguns filtros, entretanto,
		requerem conhecimento para ter certeza do que está sendo feito.
	</p>

	<h2>Filtros</h2>

	<ul class="wpgly-ul">
		<li><code>wpgly_pix_discount</code> Personaliza o valor calculado para o desconto antes de aplicar.</li>
		<li><code>wpgly_pix_before_create_pix_code</code> Personaliza ou altera o objeto Payload do Pix antes de gerar o código.</li>
		<li><code>wpgly_pix_before_save_pix_metadata</code> Personaliza os metadados do Pix que serão salvos ao pedido antes de salvar o pedido.</li>
		<li><code>wpgly_pix_after_create_api_response</code> Personaliza a <code>array</code> que é retornada para a API do Woocomerce.</li>
	</ul>

	<h2>Ações</h2>

	<ul class="wpgly-ul">
		<li><code>wpgly_pix_after_save_receipt_to_order</code> É executado após salvar um comprovante Pix nos metadados do pedido.</li>
		<li><code>wpgly_pix_after_delete_receipt_from_order</code> É executado após deletar um comprovante Pix de um pedido.</li>
		<li><code>wpgly_pix_after_process_payment</code> É executado durante o processamento do pagamento.</li>
	</ul>

	<h1 class="wpgly-title">Recursos que só o <strong>Pix por Piggly</strong> tem:</h1>

	<ul>
		<li>✅ Tratamento automático de dados, não se preocupe com o que você digita. O plugin automaticamente detecta melhorias;</li>
		<li>✅ Permita que o cliente envie o comprovante por uma página, pelo Whatsapp e/ou Telegram;</li>
		<li>✅ Teste o seu Pix a qualquer hora, antes mesmo de habilitar o plugin;</li>
		<li>✅ Aplique desconto automático, sem criação de cupons, ao realizar o pagamento via Pix;</li>
		<li>✅ Visualize os dados do Pix gerado na página do pedido;</li>
		<li>✅ Importe os dados Pix de uma chave Pix válida e preencha os dados da Conta Pix automaticamente;</li>
		<li>✅ Utilize <strong>Merge Tags</strong>, em campos disponíveis, para substituir variáveis e customizar ainda mais as funções do plugin.</li>
		<li>✅ Use o shortcode <code>[pix-por-piggly]</code> para importar o template do Pix em qualquer lugar. Veja mais em <a href="<?=$baseUrl.'&screen=shortcode';?>">Shortcodes</a>;</li>
		<li>✅ Use o shortcode <code>[pix-por-piggly-form]</code> para criar automaticamente o formulário para envio do comprovante Pix. Veja mais em <a href="<?=$baseUrl.'&screen=shortcode';?>">Shortcodes</a>;</li>
		<li>✅ Selecione o modelo de e-mail onde o Pix será enviado e o status do pedido enquanto aguarda a conferência do pagamento Pix.</li>
		<li>✅ Utilize o Pix na API do Woocommerce.</li>
	</ul>

	<div class="wpgly-notice-box wpgly-action">
		<h4 class="wpgly-title">Você Sabia?</h4>
		Você pode, opcionalmente, limpar as images na pasta com os QR Codes
		sempre que desejar. Se o plugin não encontrar um QR Code, ele criará
		novamente.
	</div>

	<h1 class="wpgly-title">Novidades da versão 1.3.0</h1>

	<p>
		A versão <strong>1.3.0</strong> vem com algumas melhorias e
		um novo recurso imperdível. Confira todos os detalhes logo
		abaixo:
	</p>

	<h2 class="wpgly-title">Shortcode do Template de Pagamento</h2>

	<p>
		Ao utilizar o shortcode <code>[pix-por-piggly]</code> não é mais
		necessário utilizar o atributo <code>order_id</code>. Quando esse
		atributo não for passado, o shortcode determinará qual é o pedido
		atual com base na página que foi aberta.
	</p>

	<p>
		Assim, você pode utilizar tranquilamente o shortcode <code>[pix-por-piggly]</code>
		para personalizar a Página de Obrigado da forma como você quiser.
	</p>

	<h2 class="wpgly-title">Shortcode do Comprovante Pix</h2>

	<p>
		Agora, você não precisa mais criar formulários personalizados.
		Basta criar uma página para receber o comprovante Pix, inserir
		a URL completa da página em <strong>Página do Comprovante</strong>
		nas configurações do plugin.
	</p>

	<p>
		Depois, basta utilizar o shortcode <code>[pix-por-piggly-form]</code>.
		Pronto! Nada mais precisa ser feito. Nosso formulário conta com alguns
		recursos otimizados para envio dos comprovantes Pix, confira:
	</p>

	<ul>
		<li>✅ O pedido e o e-mail do consumidor são capturados automaticamente;</li>
		<li>✅ Caso não seja possível identificar o pedido, será solicitado o e-mail e o número do pedido ao consumidor;</li>
		<li>✅ O consumidor poderá anexar imagens em JPG ou PNG, além de documento em PDF;</li>
		<li>✅ O arquivo enviado será analisado pelo plugin para determinar se é um arquivo seguro e válido;</li>
		<li>✅ Após enviar o comprovante, o comprovante será imediatamente anexado ao pedido;</li>
		<li>✅ Quando o pedido receber um comprovante Pix, o status será alterado para <strong>Comprovante Pix Recebido</strong>;</li>
	</ul>

	<h1 class="wpgly-title">Novidades da versão 1.2.0</h1>

	<p>
		A versão <strong>1.2.0</strong> mudou completamente o núcleo do plugin, 
		para torná-lo mais eficiente e poderoso. Se você fez mudanças na estrutura 
		do plugin esteja ciente que elas serão perdidas. Os templates de e-mail e 
		do pagamento Pix foram atualizados para atender as melhorias.
	</p>

	<h3 class="wpgly-title">Principais Melhorias</h3>

	<ul>
		<li>✅ Reformulação das configurações;</li>
		<li>✅ Criação da metabox Pix nos pedidos pagos via Pix;</li>
		<li>✅ Otimização para geração dos QR Codes;</li>
		<li>✅ Desconto automático para pagamento via Pix.</li>
	</ul>

	<h3 class="wpgly-title">API do Woocommerce</h3>

	<p>
		Os dados do Pix são incluídos na API do Woocomerce ao obter um
		pedido realizado com pagamento via Pix. Confira abaixo os dados
		de retorno conforme a API do Woocommerce utilizada:
	</p>

	<h4 class="wpgly-title">Retorno WC-API</h4>

	<p>
		Os dados Pix estarão dentro do conjunto <code>payment_details</code> ->
		<code>pix</code>.
	</p>
	
	<h4 class="wpgly-title">Retorno REST</h4>

	<p>
		Os dados Pix estarão dentro do conjunto <code>pix</code>.
	</p>

	<h4 class="wpgly-title">Dados retornados</h4>

	<p>
		Os dados retornados são:
	</p>

	<ul class="wpgly-ul">
		<li><code>code</code> Código Pix;</li>
		<li><code>qr</code> URL do QR Code ou imagem em <code>base64</code>;</li>
		<li><code>key_value</code> Valor da Chave;</li>
		<li><code>key_type</code> Tipo de Chave Pix;</li>
		<li><code>identifier</code> Identificador do Pix;</li>
		<li><code>store_name</code> Nome da Loja;</li>
		<li><code>merchant_name</code> Nome do Titular;</li>
		<li><code>merchant_city</code> Nome da Cidade do Titular.</li>
	</ul>


	<h3 class="wpgly-title">Performance do QR Code</h3>

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

	<h3 class="wpgly-title">Futuras Implementações</h3>

	<ul>
		<li>⭕ Personalização completa do template de pagamento;</li>
		<li>⭕ Implementação das API Pix para atualizar pedidos automaticamente.</li>
	</ul>
</div>