<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Preencha os dados da sua Conta Pix</h1>

<div style="max-width:720px; display: table">
	<?php 
	$imported = filter_input( INPUT_GET, 'imported', FILTER_SANITIZE_STRING );
	if ( $imported ) : 
	?>
		<section class="notice notice-success is-dismissible">
			<p>Dados importados com sucesso.</p> 
		</section>
	<?php endif; ?>

	<label class="piggly-label piggly-checkbox" for="woocommerce_wc_piggly_pix_gateway_auto_fix">
		<input type="checkbox" name="woocommerce_wc_piggly_pix_gateway_auto_fix" id="woocommerce_wc_piggly_pix_gateway_auto_fix" value="1" <?=(($data->auto_fix == 1) ? 'checked="checked"' : '');?>> Auto corrigir dados Pix que contenham caracteres inválidos.
	</label>

	<p class="description">
		A partir da versão <strong>2.3.0</strong> do Pix, alguns bancos podem apresentar
		erros ao ler códigos Pix com caracteres inválidos. A auto correção busca resolver esse problema.
		Caso você prefira que a auto correção não aconteça, remova a seleção acima.
		Entretanto, sem a auto correção, é possível que existam problemas de incompatibilidades
		Pix. Você poderá resolver esses problemas manualmente na guia <a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=support' );?>">Suporte</a>.
	</p> 

	<div class="piggly-space"></div>


	<?php
		$banks = file_get_contents(WC_PIGGLY_PIX_PLUGIN_PATH.'/templates/admin/bancos.json');
		$banks = json_decode($banks, true);

		if ( is_array($banks) ) :
	?>
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_bank">Banco</label>
	<select style="width:100%; max-width: 100%;" class="select wc-enhanced-select" name="woocommerce_wc_piggly_pix_gateway_bank" id="woocommerce_wc_piggly_pix_gateway_bank">
		<option>Selecione o banco de origem...</option>
		<?php
		$selected = (int)$data->bank ?? 0;

		foreach ( $banks as $bank )
		{ 
			if ( $bank['cod'] === $selected )
			{ echo sprintf('<option value="%s" selected="selected">%s</option>', $bank['cod'], $bank['name']); }
			else
			{ echo sprintf('<option value="%s">%s</option>', $bank['cod'], $bank['name']); }
		}

		?>
	</select>
	<p class="description">Ao selecionar o banco, utilizaremos algumas otimizações automáticas (se existirem) para evitar erros de leitura do Pix.</p>
	
	<div class="piggly-space"></div>
	<?php endif; ?>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_merchant_name"><code class="piggly-featured">Obrigatório</code> Nome do Titular</label>
	<input value="<?=$data->merchant_name?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_merchant_name" id="woocommerce_wc_piggly_pix_gateway_merchant_name">
	<p class="description">Informe o nome do titular da conta que irá receber o PIX. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>espaço</code>.</p>

	<?php if ( strlen($data->merchant_name) >= 25 ) : ?>
	<p class="notice notice-warning" style="padding: 10px"><em>
		O <strong>Nome do Titular</strong> possuí mais de <code>25</code> caracteres.
		Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
		por tanto, reduzir o nome.
	</em></p>
	<?php endif; ?>

	<div class="piggly-space"></div>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_merchant_city"><code class="piggly-featured">Obrigatório</code> Cidade do Titular</label>
	<input value="<?=$data->merchant_city?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_merchant_city" id="woocommerce_wc_piggly_pix_gateway_merchant_city">
	<p class="description">Informe a cidade do titular da conta que irá receber o PIX. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>espaço</code>.</p>

	<div class="piggly-space"></div>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_key_type"><code class="piggly-featured">Obrigatório</code> Tipo da Chave</label>
	<select style="width:100%; max-width: 100%;" class="select wc-enhanced-select" name="woocommerce_wc_piggly_pix_gateway_key_type" id="woocommerce_wc_piggly_pix_gateway_key_type">
		<option>Selecione o tipo da chave...</option>
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

	<div class="piggly-space"></div>

	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_key_value"><code class="piggly-featured">Obrigatório</code> Chave Pix</label>
	<input value="<?=$data->key_value?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_key_value" id="woocommerce_wc_piggly_pix_gateway_key_value">
	<p class="description">Digite sua Chave PIX da forma como ela foi cadastrada.</p>

	<div class="piggly-space"></div>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_instructions"><code class="piggly-featured">Obrigatório</code> Instruções</label>
	<textarea required style="width:100%;" rows="3" cols="20" class="input-text wide-input " type="textarea" name="woocommerce_wc_piggly_pix_gateway_instructions" id="woocommerce_wc_piggly_pix_gateway_instructions"><?=$data->instructions?></textarea>
	<p class="description">Explique ao cliente o que deve ser feito após o pagamento do Pix.</p>
	
	<?php if ( !empty($this->instructions) ) : ?>
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $data->instructions);?></code></p>
	<?php endif; ?>

	<div>
		<h4>Merge Tags</h4>
		<p><code>{{pedido}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
		
		<?php if ( !$helpText ) : ?>
		<h4>Recomendações</h4>
		<p>
			Escreva uma chamada para ação como: <code>Faça o pagamento via PIX, o pedido será liberado assim que a confirmação do pagamento for efetuada.</code>
			Você pode indicar também como o cliente pode enviar o comprovante de pagamento.
		</p>
		<?php endif ?>
	</div>

	<div class="piggly-space"></div>
	
	<label class="piggly-label" for="woocommerce_wc_piggly_pix_gateway_identifier">Identificador</label>
	<input maxlength="25" value="<?=$data->identifier?>" style="width:100%;" class="input-text regular-input " type="text" name="woocommerce_wc_piggly_pix_gateway_identifier" id="woocommerce_wc_piggly_pix_gateway_identifier">
	<p class="description">Crie o formato para o identificador Pix. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>0-9</code>. <code>***</code> é o valor padrão.</p>
	<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{id}}', '123456', $data->identifier);?></code></p>

	<div>
		<h4>Merge Tags</h4>
		<p><code>{{id}}</code> Insira para substituir para fazer referência ao número do pedido.</p>
	
		<?php if ( !$helpText ) : ?>
		<h4>Recomendações</h4>
		<p>
			Todo Pix tem um identificador, ao receber o seu Pix você verá este identificador. Crie um modelo que seja fácil para você.
			Por exemplo, com a <strong>Loja Dummy</strong> o identificador pode ser <code>LD{{id}}</code>.
			Perceba que acima, utilizamos <code>{{id}}</code> essa é uma merge tag. Ela será substituída pelo número do pedido.
		</p>
		<?php endif ?>
	</div>

	<p class="submit force-submit">
	<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Salvar">Salvar</button>
	</p>
</div>