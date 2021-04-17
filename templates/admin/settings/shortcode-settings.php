<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="wpgly-wrapper">
	<h1 class="wpgly-title">Shortcode de Pagamento Pix</h1>

	<p>
		Você pode utilizar o shortcode <code>[pix-por-piggly]</code>
		para carregar o template de pagamento Pix em qualquer página que você
		deseje. Veja a seguir os atributos disponíveis:
	</p>

	<p>
		O shortcode utiliza o atributo <code>order_id</code> com ID do pedido 
		a ser carregado. Esse atributo é opcional e quando não enviado, o shortcode
		irá buscar pelo pedido carregado na página onde ele foi inserido.
	</p>

	<div class="wpgly-notice-box wpgly-action">
		<h4 class="wpgly-title">Ajuda</h4>
		Na página de obrigado, por exemplo, utilizar o shortcode <code>[pix-por-piggly]</code>
		carregará os dados de pagamento Pix para o pedido atual. Você ainda pode definir qual pedido
		deve ser carregado pelo shortcode com o atributo <code>order_id</code>. O shortcode
		<code>[pix-por-piggly order_id="1"]</code> carregará o pedido com ID 1, por exemplo.
	</div>

	<h3 class="wpgly-title">O shortcode não carregará o template, quando:</h3>

	<ul>
		<li>❌ O módulo de pagamento Pix estiver desabilitado;</li>
		<li>❌ O pedido não tiver o método de pagamento Pix selecionado;</li>
		<li>❌ O pedido selecionado não existir;</li>
		<li>❌ O status do pedido for atualizado para <strong>Pago</strong>;</li>
		<li>❌ Nenhuma chave Pix estiver configurada.</li>
	</ul>

	<h1 class="wpgly-title">Shortcode para Comprovante Pix</h1>

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

	<h2 class="wpgly-title">Tutorial Básico</h2>

	<ul class="wpgly-ul">
		<li>Crie uma nova página para receber os comprovantes Pix;</li>
		<li>Insira na página o shortcode <code>[pix-por-piggly-form]</code>;</li>
		<li>Em <a href="<?=$baseUrl.'&screen=receipt';?>">Comprovante Pix</a>, insira o link permanente da página criada em <strong>Link para a Página do Comprovante</strong>;</li>
		<li>Agora, os comprovantes Pix já podem ser recebidos na página.</li>
	</ul>
</div>