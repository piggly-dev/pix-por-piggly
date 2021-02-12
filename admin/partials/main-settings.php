<?php

?>
<style>
.piggly-label { font-size: 12px; font-weight: bold; margin-bottom: 4px; display: block; }
.piggly-checkbox { display: table; margin: 12px 0; }
</style>
<h2>
	<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout' );?>"><?=__('Métodos de Pagamento', WC_PIGGLY_PIX_PLUGIN_NAME);?></a> > <?=$data->method_title?> por Piggly
</h2>
<h1>Bem-vindo(a) ao Pix por Piggly</h1>

<div style="max-width:640px; display: table">
	<p>
		Este plugin habilitará o método de pagamento via Pix. 
		O Pix, configurado abaixo, será automaticamente inserido nas 
		Páginas de Obrigado, na Página do Pedido e no E-mail.
	</p>

	<a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=testing' );?>" class="button">Teste o seu Pix</a>

	<h3>Importação Pix</h3>

	<p>
		Para facilitar o processo, você pode inserir abaixo um
		<strong>código Pix</strong> válido gerado pelo seu banco. O plugin extrairá automaticamente
		os dados do seu Pix.
	</p>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_pix_code">Código Pix Válido</label>
	<input style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_pix_code" id="woocommerce_wc_piggly_pix_gateway_pix_code">
	<p class="description">Tem um código Pix válido? Coloque-o aqui, clique em salvar e os dados principais serão extraídos dele.</p>
	
	<p class="submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Importar Configurações">Importar Configurações</button>
	</p> 

	<p>
		O Pix é um método novo e alguns bancos ainda estão implementando os
		padrões estabelecidos pelo Banco Central do Brasil. 
		Caso tenha dúvidas, visite a página do plugin, 
		<a href="https://wordpress.org/plugins/pix-por-piggly/">clicando aqui</a>. 
		Vamos atender ao seu chamado assim que pudermos.
	</p>

	<h3>Configurações Gerais</h3>

	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_enabled">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_enabled" id="woocommerce_wc_piggly_pix_gateway_enabled" value="1" <?=(($data->enabled == 1 || $data->enabled == 'yes') ? 'checked="checked"' : '');?>> Habilitar o pagamento via Pix
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
	
	<p class="submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>

	<h3>Dados do Pix</h3>

	<p>
		Para facilitar o processo, você pode inserir abaixo um
		<strong>código Pix</strong> válido gerado pelo seu banco. O plugin extrairá automaticamente
		os dados do seu Pix.
	</p>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_merchant_name">Nome do Titular</label>
	<input value="<?=$data->merchant_name?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_merchant_name" id="woocommerce_wc_piggly_pix_gateway_merchant_name">
	<p class="description">Informe o nome do titular da conta que irá receber o PIX. Como consta no Banco.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_merchant_city">Cidade do Titular</label>
	<input value="<?=$data->merchant_city?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_merchant_city" id="woocommerce_wc_piggly_pix_gateway_merchant_city">
	<p class="description">Informe a cidade do titular da conta que irá receber o PIX. Como consta no Banco.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_key_type">Tipo da Chave</label>
	<select style="width:100%; max-width: 100%;" class="select " name="woocommerce_wc_piggly_pix_gateway_key_type" id="woocommerce_wc_piggly_pix_gateway_key_type">
		<?php
		$selected = $data->key_type;
		$options  = [
			'random' => __('Chave Aleatória', WC_PIGGLY_PIX_PLUGIN_NAME),
			'document' => __('CPF/CNPJ', WC_PIGGLY_PIX_PLUGIN_NAME),
			'phone' => __('Telefone', WC_PIGGLY_PIX_PLUGIN_NAME),
			'email' => __('E-mail', WC_PIGGLY_PIX_PLUGIN_NAME)
		];

		foreach ( $options as $key => $value )
		{ 
			if ( $key === $selected )
			{ echo sprintf('<option value="%s" selected="selected">%s</option>', $key, $value); }
			else
			{ echo sprintf('<option value="%s">%s</option>', $key, $value); }
		}

		?>
	</select>
	<p class="description">Informe o tipo da chave PIX a ser compartilhada.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_key_value">Chave Pix</label>
	<input value="<?=$data->key_value?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_key_value" id="woocommerce_wc_piggly_pix_gateway_key_value">
	<p class="description">Digite sua Chave PIX da forma como ela foi cadastrada.</p>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_instructions">Instruções</label>
	<textarea style="width:100%;" rows="3" cols="20" class="input-text wide-input " type="textarea" name="woocommerce_wc_piggly_pix_gateway_instructions" id="woocommerce_wc_piggly_pix_gateway_instructions"><?=$data->instructions?></textarea>
	<p class="description">Explique ao cliente o que deve ser feito após o pagamento do Pix.</p>

	<div>
		<h4>Recomendações</h4>
		<p>
			Escreva uma chamada para ação como: <code>Faça o pagamento via PIX, o pedido será liberado assim que a confirmação do pagamento for efetuada.</code>
			Se você utilizar a <strong>Página do Comprovante</strong> abaixo, continue com algo como <code>Após realizar o pagamento, clique no botão abaixo para enviar o comprovante.</code>
		</p>
		<h4>Merge Tags</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	</div>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_identifier">Identificador</label>
	<input value="<?=$data->identifier?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_identifier" id="woocommerce_wc_piggly_pix_gateway_identifier">
	<p class="description">Crie o formato para o identificador Pix.</p>
	
	<div>
		<h4>Recomendações</h4>
		<p>
			Todo Pix tem um identificador, ao receber o seu Pix você verá este identificador. Crie um modelo que seja fácil para você.
			Por exemplo, com a <strong>Loja Dummy</strong> o identificador pode ser <code>LD-{{id}}</code>.
			Perceba que acima, utilizamos <code>{{id}}</code> essa é uma merge tag. Ela será substituída pelo número do pedido.
		</p>
		<h4>Merge Tags</h4>
		<p><code>{{id}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	</div>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_receipt_page_value">Página do Comprovante</label>
	<input value="<?=$data->receipt_page_value?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_receipt_page_value" id="woocommerce_wc_piggly_pix_gateway_receipt_page_value">
	<p class="description">Quando preenchido, adiciona um botão para ir até a página.</p>
	
	<div>
		<h4>Recomendações</h4>
		<p>
			Crie um formulário com os plugins <strong>Gravity Forms</strong>, <strong>WP Forms</strong> ou similares.
			Coloque esse formulário em uma nova página, por exemplo <code>https://minhaloja.com.br/comprovante-pix</code>. Insira os
			campos: <em>Número do Pedido</em>, <em>E-mail</em>, <em>Comprovante Pix (permitindo envio de arquivos png, jpg ou pdf apenas)</em>.
		</p>
		<p>
			No campo <em>Número do Pedido</em> preencha o valor dele dinâmicamente com a <strong>URL Query String</strong>, por exemplo,
			<code>order_id</code>. Assim, preencha a <strong>Página do Comprovante</strong> como <code>https://minhaloja.com.br/comprovante-pix/?order_id={{pedido}}</code>.
			Ao fazer isso, o formulário na página <code>/comprovante-pix</code> receberá o número do pedido automaticamente. Facilitando para o comprador.
		</p>
		<h4>Merge Tags</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	</div>

	<p class="submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>

	<h3>Problemas com o Pix?</h3>

	<p>
		O Pix ainda é muito recente e, apenas das padronizações do Banco Central do Brasil, 
		muitos bancos criaram algumas variações e definiram como aceitam determinadas chaves. 
		A nossa recomendação principal é: <em>utilize as chaves aleatórias</em>. Assim,
		você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.
	</p>

	<h4>Divergências entre Pix Copia & Cola e QR Codes</h4>

	<p>
		Há alguns relatos que alguns bancos leem o QR Code, mas não leem o Pix Copia & Cola. 
		Este não é um problema do plugin, o código Pix de ambos são o mesmo! Caso esteja curioso, 
		abra um leitor de QR Code e leia o código é examente o mesmo que o Pix Copia & Cola.
	</p>

	<p>
		Neste caso, precisamos verificar cada caso. E você pode contribuir com isso enviando um e-mail
		para <a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a>. Ao enviar um e-mail, certifique-se de informar:
	</p>

	<ul>
		<li>Versão do Wordpress;</li>
		<li>Versão do WooCommerce;</li>
		<li>Banco Emitente (Conta Pix);</li>
		<li>Banco Pagador (que está utilizando o Código Pix);</li>
		<li>Tipo de Erro;</li>
		<li>Chave Pix gerada;</li>
	</ul>

</div>