<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Bem-vindo(a) ao Pix por Piggly</h1>

<div class="wpgly-wrapper">	 
	<div class="wpgly-notice-box wpgly-warning">
		<strong>Enfrentando algum problema?</strong> Visite a seção de 
		<a href="<?=$baseUrl.'&screen=support'?>">Suporte</a> do plugin 
		e <em>mantenha-o sempre atualizado</em> ou 
		<a href="<?=$baseUrl.'&screen=faq';?>">clique aqui</a> 
		para ver as principais dúvidas sobre o plugin..
	</div>

	<div class="wpgly-notice-box wpgly-action">
		<h4 class="wpgly-title">Gostou o bastante? Ajude a continuar gratuito 👇</h4> 
		Se você apreciar a função deste plugin e quiser apoiar este trabalho 
		para que continuemos atualizando, sinta-se livre para 
		fazer qualquer doação para a chave aleatória Pix 
		<code>aae2196f-5f93-46e4-89e6-73bf4138427b</code> ❤.
	</div>

	<a href="https://wordpress.org/plugins/pix-por-piggly/#reviews" class="wpgly-button wpgly-accent">
		Avaliar o Plugin
	</a>

	<h3 class="wpgly-title">Guia de Abas</h3>

	<ul>
		<li><strong>Configurações Gerais</strong>: Habilite e configure o pagamento via Pix;</li>
		<li><strong>Dados do Pix</strong>: Preencha os dados da sua Conta Pix;</li>
		<li><strong>Automação & API Pix</strong>: Configure a API Pix para atualização automática dos pedidos;</li>
		<li><strong>Importar Pix</strong>: Importe um código Pix válido para preencher os dados da sua Conta Pix automaticamente;</li>
		<li><strong>Shortcodes</strong>: Utilize os templates Pix em qualquer página;</li>
		<li><strong>Pedidos, Desconto & E-mails</strong>: Configure o comportamento dos pedidos, e-mails e desconto Pix;</li>
		<li><strong>Comprovante Pix</strong>: Configuire o envio dos comprovantes de pagamento Pix;</li>
		<li><strong>Teste o seu Pix</strong>: Teste as configurações do Plugin e valide o pagamento do Pix;</li>
		<li><strong>Perguntas Frequentes</strong>: Resolva as suas principais dúvidas;</li>
		<li><strong>Suporte</strong>: Obtenha o suporte necessário;</li>
	</ul>

	<h3 class="wpgly-title">Configurações Gerais</h3>

	<?php $can_enable = $this->can_be_enabled(); ?>
	<div class="wpgly-field">
		<span class="wpgly-label">Habilitar/Desabilitar</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('enabled')?>">
			<input type="checkbox" name="<?=$this->get_field_name('enabled')?>" id="<?=$this->get_field_name('enabled')?>" value="yes" <?=(($this->enabled === 1 || $this->enabled === 'yes') ? 'checked="checked"' : '');?> <?=($can_enable ? '' : 'disabled="disabled"');?>> Habilitar o pagamento via Pix
		</label>
	</div>

	<?php if ( !$can_enable ) : ?>
	<p class="wpgly-notice-box wpgly-error">
		Antes de habilitar o método de pagamento, você precisa preencher a Chave Pix.
	</p>
	<?php endif; ?>

	<div class="wpgly-field">
		<span class="wpgly-label">Textos de Ajuda</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('help_text')?>">
			<input type="checkbox" name="<?=$this->get_field_name('help_text')?>" id="<?=$this->get_field_name('help_text')?>" value="1" <?=(($this->help_text == 1 || $this->help_text == 'yes') ? 'checked="checked"' : '');?>> Ocultar os textos de ajuda
		</label>
		<p class="description">Desabilite que você deseja ocultar os textos de ajuda do plugin.</p>
	</div>
	
	<div class="wpgly-field">
		<span class="wpgly-label">Ícone do Pix</span>
		<?php
		$selected = $this->select_icon;
		$select = [ 
			'pix-payment-icon' => 'Escuro',
			'pix-payment-icon-green' => 'Tradicional',
			'pix-payment-icon-white' => 'Claro'
		];

		foreach ( $select as $key => $label ) 
		{
			printf(
				'<label class="wpgly-label wpgly-radio" for="%s">', 
				$this->get_field_name('select_icon_'.$key)
			);

			printf(
				'<input style="vertical-align: middle;" type="radio" name="%s" id="%s" value="%s" %s/>  <code style="vertical-align: middle;">%s</code> <img style="vertical-align: middle;" src="%s"/>', 
				$this->get_field_name('select_icon'),
				$this->get_field_name('select_icon_'.$key),
				$key,
				$selected === $key ? 'checked="checked"' : '',
				$label,
				WC_PIGGLY_PIX_PLUGIN_URL.'assets/'.$key.'.png'
			);

			printf('</label>');
		}

		?>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('title')?>"><code class="wpgly-error">*</code> Título do Método de Pagamento</label>
		<input value="<?=$this->title?>" type="text" name="<?=$this->get_field_name('title')?>" id="<?=$this->get_field_name('title')?>">
		<p class="description">O título que o cliente visualizará ao selecionar o pagamento.</p>
	</div>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('description')?>">Descrição do Método de Pagamento</label>
		<input value="<?=$this->description?>" type="text" name="<?=$this->get_field_name('description')?>" id="<?=$this->get_field_name('description')?>">
		<p class="description">A descrição que o cliente visualizará para identificar o pagamento.</p>
	</div>

	<div class="wpgly-field">
		<span class="wpgly-label">Descrição Avançada</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('advanced_description')?>">
			<input type="checkbox" name="<?=$this->get_field_name('advanced_description')?>" id="<?=$this->get_field_name('advanced_description')?>" value="yes" <?=(($this->advanced_description) ? 'checked="checked"' : '');?>> Exibir descrição avançada do Pix.
		</label>
		<p class="description">A descrição avançada apresenta os três passos para pagamento via Pix.</p>
	</div>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('store_name')?>">Nome da Loja</label>
		<input value="<?=$this->store_name?>" type="text" name="<?=$this->get_field_name('store_name')?>" id="<?=$this->get_field_name('store_name')?>">
		<p class="description">Informe o nome da loja para acrescentar na descrição do Pix. São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> e <code>espaço</code>.</p>
		
		<?php if ( !empty($this->store_name) ) : ?>
			<p class="description"><strong>Pré-visualize</strong> <code><?=sprintf('Compra em %s', $this->store_name)?></code></p>
		<?php endif?> 
		
		<?php if ( strlen($this->store_name) >= 30 ) : ?>
		<div class="wpgly-notice-box wpgly-warning">
			O <strong>Nome da Loja</strong> possuí mais de <code>30</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</div>
		<?php endif; ?>
	</div>
	
	<p class="submit wpgly-submit">
		<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
	</p>

	<h3 class="wpgly-title">Configurações de Exibição</h3>

	<div class="wpgly-field">
		<span class="wpgly-label">QR Code</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('pix_qrcode')?>">
			<input type="checkbox" name="<?=$this->get_field_name('pix_qrcode')?>" id="<?=$this->get_field_name('pix_qrcode')?>" value="1" <?=(($this->pix_qrcode) ? 'checked="checked"' : '');?>> Exibe o QR Code para pagamento Pix.
		</label>
	</div>

	<div class="wpgly-field">
		<span class="wpgly-label">Pix Copia e Cola</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('pix_copypast')?>">
			<input type="checkbox" name="<?=$this->get_field_name('pix_copypast')?>" id="<?=$this->get_field_name('pix_copypast')?>" value="1" <?=(($this->pix_copypast) ? 'checked="checked"' : '');?>> Exibe o código Pix para copiar e colar.
		</label>
	</div>

	<div class="wpgly-field">
		<span class="wpgly-label">Pix Manual</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('pix_manual')?>">
			<input type="checkbox" name="<?=$this->get_field_name('pix_manual')?>" id="<?=$this->get_field_name('pix_manual')?>" value="1" <?=(($this->pix_manual) ? 'checked="checked"' : '');?>> Exibe a Chave Pix e o valor para realizar o Pix.
		</label>
	</div>
	
	<p class="submit wpgly-submit">
		<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
	</p>

</div>