<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Preencha os dados da sua Conta Pix</h1>

<div class="wpgly-wrapper">
	<?php 
	$imported = filter_input( INPUT_GET, 'imported', FILTER_SANITIZE_STRING );
	if ( $imported ) : 
	?>
		<div class="wpgly-notice-box wpgly-success">
			Dados importados com sucesso do código Pix. Veja abaixo!
		</div>
	<?php endif; ?>

	<div class="wpgly-field">
		<span class="wpgly-label">Auto Correção</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('auto_fix')?>">
			<input type="checkbox" name="<?=$this->get_field_name('auto_fix')?>" id="<?=$this->get_field_name('auto_fix')?>" value="1" <?=(($this->auto_fix == 1) ? 'checked="checked"' : '');?>> Habilite para auto corrigir dados do Pix que contenham caracteres ou formatos inválidos...
		</label>
	</div>
	
	<div class="wpgly-notice-box wpgly-action">
		A partir da versão <strong>2.3.0</strong> do Pix, alguns bancos podem apresentar
		erros ao ler códigos Pix com caracteres inválidos. A auto correção busca resolver esse problema.
		Caso você prefira que a auto correção não aconteça, remova a seleção acima.
		Entretanto, sem a auto correção, é possível que existam problemas de incompatibilidades
		Pix. Você poderá resolver esses problemas manualmente na guia <a href="<?=$baseUrl.'&screen=support';?>">Suporte</a>.
	</div> 

	<h2 class="wpgly-title">Conta Pix</h2>

	<?php
		$banks = file_get_contents(WC_PIGGLY_PIX_PLUGIN_PATH.'templates/admin/settings/bancos.json');
		$banks = json_decode($banks, true);

		if ( is_array($banks) ) :
	?>
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('bank')?>">Banco</label>
		<select class="wc-enhanced-select" name="<?=$this->get_field_name('bank')?>" id="<?=$this->get_field_name('bank')?>">
			<option>Selecione o banco de origem...</option>
			<?php
			$selected = (int)$this->bank ?? 0;

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
	
		<?php if ( in_array($this->bank, [341,184,29,479,652]) ) : ?>
			<div class="wpgly-spacing"></div>
			<div class="wpgly-notice-box wpgly-warning">
				<strong>ATENÇÃO</strong>: para gerar pagamentos via Pix
				fora do aplicativo do Itaú, é necessário solicitar uma 
				autorização ao gerente para criação de QR Codes Simples
				(estáticos).
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('merchant_name')?>"><code class="wpgly-error">*</code> Nome do Titular</label>
		<input required value="<?=$this->merchant_name?>" type="text" name="<?=$this->get_field_name('merchant_name')?>" id="<?=$this->get_field_name('merchant_name')?>">
		<p class="description">Informe o nome do titular da conta que irá receber o PIX. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>espaço</code>.</p>
			
		<?php if ( strlen($this->merchant_name) >= 25 ) : ?>
			<div class="wpgly-spacing"></div>
			<div class="wpgly-notice-box wpgly-warning">
				O <strong>Nome do Titular</strong> possuí mais de <code>25</code> caracteres.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, reduzir/abreviar o nome.
			</div>
		<?php endif; ?>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('merchant_city')?>"><code class="wpgly-error">*</code> Cidade do Titular</label>
		<input required value="<?=$this->merchant_city?>" type="text" name="<?=$this->get_field_name('merchant_city')?>" id="<?=$this->get_field_name('merchant_city')?>">
		<p class="description">Informe a cidade do titular da conta que irá receber o PIX. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>espaço</code>.</p>
	
		<?php if ( strlen($this->merchant_city) >= 25 ) : ?>
			<div class="wpgly-spacing"></div>
			<div class="wpgly-notice-box wpgly-warning">
				A <strong>Cidade do Titular</strong> possuí mais de <code>25</code> caracteres.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, reduzir/abreviar o nome da cidade.
			</div>
		<?php endif; ?>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('key_type')?>"><code class="wpgly-error">*</code> Tipo da Chave Pix</label>
		<select required class="wc-enhanced-select" name="<?=$this->get_field_name('key_type')?>" id="<?=$this->get_field_name('key_type')?>">
			<option>Selecione o tipo da chave...</option>
			<?php
			$selected = $this->key_type;
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
		<p class="description">Informe o tipo da chave PIX a ser utilizada.</p>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('key_value')?>"><code class="wpgly-error">*</code> Chave Pix</label>
		<input required value="<?=$this->key_value?>" type="text" name="<?=$this->get_field_name('key_value')?>" id="<?=$this->get_field_name('key_value')?>">
		<p class="description">Digite sua Chave PIX da forma como ela foi cadastrada.</p>
	</div>

	<h2 class="wpgly-title">Configurações do Pix</h2>

	<div class="wpgly-field">
		<span class="wpgly-label">Renegeração</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('qr_regenerate')?>">
			<input type="checkbox" name="<?=$this->get_field_name('qr_regenerate')?>" id="<?=$this->get_field_name('qr_regenerate')?>" value="1" <?=(($this->qr_regenerate) ? 'checked="checked"' : '');?>> Auto renegerar Código Pix gerado ao atualizar dados Pix.
		</label>
		<p class="description">Se você marcar essa opção, todos os Pix não pagos serão regenerados automaticamente para conter os novos Dados Pix, caso você os altere.</p>
	</div>

	<div class="wpgly-notice-box wpgly-action">
		<h4 class="wpgly-title">Aviso</h4>
		Você pode regenerar manualmente os Pix, abrindo o pedido e indo até a
		meta box lateral "Pix".
	</div>

	<h2 class="wpgly-title">Configurações de Pagamento</h2>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('instructions')?>">Instruções de Pagamento</label>
		<textarea name="<?=$this->get_field_name('instructions')?>" id="<?=$this->get_field_name('instructions')?>" style="width:100%;" rows="3" cols="20" type="textarea"><?=$this->instructions?></textarea>
		<p class="description">Explique ao cliente o que deve ser feito após o pagamento do Pix.</p>
		<?php if ( !empty($this->instructions) ) : ?>
		<p><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $this->instructions);?></code></p>
		<?php endif; ?>
		
		<div class="wpgly-spacing"></div>
		<div class="wpgly-notice-box wpgly-action">
			<h4 class="wpgly-title">Merge Tag</h4>
			<p><code>{{pedido}}</code> Faz referência ao número do pedido.</p>
		</div>

		<?php if ( !$helpText ) : ?>
		<div class="wpgly-notice-box">
			<h4 class="wpgly-title">Recomendações</h4>
			Escreva uma chamada para ação como: <code>Faça o pagamento via PIX, 
			o pedido será liberado assim que a confirmação do pagamento for efetuada.</code>
			Você pode indicar também como o cliente pode enviar o comprovante de pagamento.
		</div>
		<?php endif ?>
	</div>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('identifier')?>">Identificador do Pix</label>
		<input maxlength="25" value="<?=$this->identifier?>" type="text" name="<?=$this->get_field_name('identifier')?>" id="<?=$this->get_field_name('identifier')?>">
		<p class="description">Crie o formato para o identificador Pix. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>0-9</code>. <code>***</code> é o valor padrão.</p>
		<p class="description"><strong>Pré-visualize</strong> <code><?=str_replace('{{id}}', '123456', $this->identifier);?></code></p>

		<div class="wpgly-spacing"></div>
		<div class="wpgly-notice-box wpgly-action">
			<h4 class="wpgly-title">Merge Tag</h4>
			<p><code>{{id}}</code> Faz referência ao número do pedido.</p>
		</div>

		<?php if ( !$helpText ) : ?>
		<div class="wpgly-notice-box">
			<h4 class="wpgly-title">Recomendações</h4>
			Todo Pix é marcado com um código identificador que deve ser único.
			Crie um modelo de identificação do Pix que seja fácil para você.
			Assim, será mais fácil localizar esse Pix no app do seu banco.
			Por exemplo, com a <strong>Loja Dummy</strong> o identificador pode 
			ser <code>LD{{id}}</code>.	Perceba que acima, utilizamos <code>{{id}}</code> 
			essa é uma merge tag. Ela será substituída pelo número do pedido, garantindo
			que o código seja sempre único.
		</div>
		<?php endif ?>
	</div>
</div>

<div class="wpgly-spacing"></div>

<p class="submit wpgly-submit">
	<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
</p>