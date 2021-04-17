<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php $baseUrl = admin_url( 'admin.php?page=wc-settings&tab=checkout&section='.$this->id ); ?>
<h1 class="wpgly-title">Perguntas Frequentes</h1>

<div class="wpgly-wrapper">
	<h3 class="wpgly-title">1. Qual é a licença do plugin?</h3>

	<p>Este plugin esta licenciado como GPLv2. Ele é distrubuido de forma gratuita.</p>

	<h3 class="wpgly-title">2. O que eu preciso para utilizar este plugin?</h3>

	<ul class="wpgly-ul">
		<li>Ter instalado o Wordpress 4.0 ou superior;</li>
		<li>Ter instalado o plugin WooCommerce 3.0 ou superior;</li>
		<li>Utilizar a versão 7.2 do PHP;</li>
		<li>Ter a extensão `gd` para PHP habilitada, veja detalhes <a href="https://www.php.net/manual/pt_BR/book.image.php">aqui</a>;</li>
		<li>Possuir uma conta bancária com Chave Pix.</li>
	</ul>

	<h3 class="wpgly-title">3. Posso utilizar com outros gateways de pagamento?</h3>

	<p>Sim, esse plugin funciona apenas como um método de pagamento adicional, assim como acontece com o método de transferência eletrônica.</p>

	<h3 class="wpgly-title">4. Como aplicar desconto automático?</h3>

	<p>Acesse <a href="<?=$baseUrl.'&screen=orders'?>">Pedidos & E-mails</a> e insira um valor e um rótulo para o desconto Pix. O desconto será automaticamente aplicado quando o cliente escolher o método de pagamento Pix.</p>

	<h3 class="wpgly-title">5. Como conferir o pagamento Pix?</h3>

	<p>
		A conferência do Pix ainda é manual, assim como acontece em uma transferência 
		eletrônica. Para facilitar, o plugin gera os Pix com um código identificador. 
		Esse código possuí o número do pedido e você pode personalizá-lo na página 
		<a href="<?=$baseUrl.'&screen=pix'?>">Dados do Pix</a>
	</p>

	<p>
		Abra o pedido criado no Woocommerce e verifique o código identificador do Pix. 
		Ao abrir o aplicativo do seu banco, você poderá ver detalhes sobre o recebimento 
		Pix e, na maioria dos bancos, o pagamento estará identificado com o código 
		identificador do Pix.
	</p>

	<h3 class="wpgly-title">6. Não tem como atualizar o pagamento Pix automáticamente?</h3>

	<p>
		Para validar se um Pix foi pago a maioria dos bancos emissores irão cobrar taxas, 
		assim como os intermediadores de pagamento. Se você faz parte de um banco emissor 
		que já implementa a API Pix, pode entrar em contato com a gente em 
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a> para que possamos implementar a solução.
	</p>

	<h3 class="wpgly-title">7. Gerei o código Pix, mas não consigo efetuar o pagamento. E agora?</h3>

	<p>
		Acesse <a href="<?=$baseUrl.'&screen=support'?>">Suporte</a> e verifique a seção 
		"O plugin gera o QR Code, mas não consigo pagá-lo", lá estarão algumas dicas 
		automáticas que podem ajudar você. Se ainda sim precisar de algum suporte, 
		abra um chamado enviando um e-mail para <a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a>.
	</p>

	<h3 class="wpgly-title">8. Como customizar os templates?</h3>

	<p>
		Acesse <a href="<?=$baseUrl.'&screen=support'?>">Suporte</a> e verifique a seção 
		"Como substituir os templates de e-mail e da página de obrigado".
	</p>


	<p class="wpgly-notice-box wpgly-warning">
		<strong>AVISO</strong>: Ao customizar os templates você pode perder funcionalidades 
		importantes do plugin e comportamentos pré-existentes nos templates originais. 
		Tenha certeza sobre o que está fazendo para garantir que tudo funcione como deve ser. 
		<strong>Não prestaremos suporte para customizações</strong>.
	</p>
</div>