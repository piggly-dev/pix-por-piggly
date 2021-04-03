<?php
use Piggly\Pix\Parser;
use Piggly\Pix\Payload;

$helpText = $data->help_text;
?>

<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Bem-vindo(a) ao Pix por Piggly</h1>

<div style="max-width:720px; display: table">
	<p>
		Este plugin habilitará o método de pagamento via Pix. 
		O Pix, configurado abaixo, será automaticamente inserido nas 
		Páginas de Obrigado, na Página do Pedido e no E-mail. 
	</p>
	 
	<p class="notice notice-warning is-dismissible" style="padding: 10px"><em>
		<strong>Enfrentando algum problema?</strong> Visite a seção de 
		<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=support' );?>">Suporte</a> do plugin e <em>mantenha-o sempre atualizado</em>.
	</em></p>

	<?php if ( !$helpText ) : ?>
	<p>
		O Pix é um método novo e alguns bancos ainda estão implementando os
		padrões estabelecidos pelo Banco Central do Brasil. 
		Caso tenha dúvidas, visite a página do plugin, 
		<a href="https://wordpress.org/plugins/pix-por-piggly/">clicando aqui</a>. 
		Vamos atender ao seu chamado assim que pudermos.
	</p>
	<?php endif ?>

	<h3>Recursos que só o <strong>Pix por Piggly</strong> tem:</h3>

	<ul>
		<li>✅ Tratamento automático de dados, não se preocupe com o que você digita. O plugin automaticamente detecta melhorias;</li>
		<li>✅ Permita que o cliente envie o comprovante por uma página, pelo Whatsapp e/ou Telegram;</li>
		<li>✅ Teste o seu Pix a qualquer hora, antes mesmo de habilitar o plugin;</li>
		<li>✅ Aplique desconto automático, sem criação de cupons, ao realizar o pagamento via Pix;</li>
		<li>✅ Visualize os dados do Pix gerado na página do pedido;</li>
		<li>✅ Com problema na hora de preencher os dados do Pix? Importe os dados Pix de uma chave Pix válida e não tenha mais preocupações;</li>
		<li>✅ Utilize <strong>Merge Tags</strong>, em campos disponíveis, para substituir variáveis e customizar ainda mais as funções do plugin.</li>
		<li>✅ Use o shortcode <code>[pix-por-piggly order_id="$id"]</code> para importar o template do Pix em qualquer lugar;</li>
		<li>✅ Selecione o modelo de e-mail onde o Pix será enviado e o status do pedido enquanto aguarda a conferência do pagamento Pix.</li>
	</ul>

	<p><a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=faq' );?>">Clique aqui</a> para ver as principais Perguntas Frequentes sobre o plugin.</p>

	<p class="notice notice-info is-dismissible" style="padding: 10px">
		<em>
			Sua avaliação é muito importante para que continuemos a atualizar
			o plugin e prestar um suporte de qualidade! Clique em "Avaliar o Plugin"
			e nos avalie em Wordpress. <strong>Gostou o bastante?</strong> Se você apreciar a 
			função deste plugin e quiser apoiar este trabalho, sinta-se livre para 
			fazer qualquer doação para a chave aleatória Pix 
			<code>aae2196f-5f93-46e4-89e6-73bf4138427b</code> ❤.
		</em>
	</p>

	<a style="margin-left: 5px" href="https://wordpress.org/plugins/pix-por-piggly/#reviews" class="button button-primary">Avaliar o Plugin</a>


	<h3>Configurações Gerais</h3>
 
	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_enabled">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_enabled" id="woocommerce_wc_piggly_pix_gateway_enabled" value="yes" <?=(($data->enabled === 1 || $data->enabled === 'yes') ? 'checked="checked"' : '');?>> Habilitar o pagamento via Pix
	</label> 
	
	<?php if ( empty($data->key_value) ) : ?>
	<p class="notice notice-warning" style="padding: 10px"><em>
		Antes de habilitar o plugin, preencha a Chave Pix.
	</em></p>
	<?php endif; ?>
	
	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_debug">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_debug" id="woocommerce_wc_piggly_pix_gateway_debug" value="1" <?=(($data->debug == 1) ? 'checked="checked"' : '');?>> Modo Debug
	</label>
	<p class="description">O modo debug salvará operações, erros e outras informações no log de registro do plugin. Ideal para receber suporte.</p>
	
	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_help_text">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_help_text" id="woocommerce_wc_piggly_pix_gateway_help_text" value="1" <?=(($data->help_text == 1 || $data->help_text == 'yes') ? 'checked="checked"' : '');?>> Ocultar os textos de ajuda
	</label>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_title">Título do Método de Pagamento</label>
	<input value="<?=$data->title?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_title" id="woocommerce_wc_piggly_pix_gateway_title">
	<p class="description">Isso controla o que o usuário vê durante o checkout.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_description">Descrição do Método de Pagamento</label>
	<input value="<?=$data->description?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_description" id="woocommerce_wc_piggly_pix_gateway_description">
	<p class="description">Isso controla a curta descrição que o usuário vê durante o checkout.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_store_name">Nome da Loja</label>
	<input value="<?=$data->store_name?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_store_name" id="woocommerce_wc_piggly_pix_gateway_store_name">
	<p class="description">Informe o nome da loja para acrescentar na descrição do Pix.</p>
	<p class="description"><strong>Pré-visualize</strong> <code><?=sprintf('Compra em %s', $data->store_name)?></code></p>
	
	<?php if ( strlen($data->store_name) >= 30 ) : ?>
	<p class="notice notice-warning" style="padding: 10px"><em>
		O <strong>Nome da Loja</strong> possuí mais de <code>30</code> caracteres.
		Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
		por tanto, reduzir o nome.
	</em></p>
	<?php endif; ?>

	<p class="submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>

	<h3>Configurações de Exibição</h3>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_pix_qrcode">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_pix_qrcode" id="woocommerce_wc_piggly_pix_gateway_pix_qrcode" value="1" <?=(($data->pix_qrcode == 1) ? 'checked="checked"' : '');?>> Mostrar QR Code do Pix.
	</label>
	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_pix_copypast">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_pix_copypast" id="woocommerce_wc_piggly_pix_gateway_pix_copypast" value="1" <?=(($data->pix_copypast == 1) ? 'checked="checked"' : '');?>> Mostrar Pix Copie &amp; Cole.
	</label>
	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_pix_manual">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_pix_manual" id="woocommerce_wc_piggly_pix_gateway_pix_manual" value="1" <?=(($data->pix_manual == 1) ? 'checked="checked"' : '');?>> Mostrar dados manuais para realizar o Pix.
	</label>
	
	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>

</div>