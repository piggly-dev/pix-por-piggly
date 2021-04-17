<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Configurações de Pedidos e E-mails</h1>

<div class="wpgly-wrapper">
	<h2 class="wpgly-title">E-mails</h2>
	<?php if ( class_exists('WC_Emails') ) : ?>
		<div class="wpgly-field">
			<label class="wpgly-label" for="<?=$this->get_field_name('email_status')?>">Anexar Pix ao Modelo de E-mail:</label>
			<select class="wc-enhanced-select" name="<?=$this->get_field_name('email_status')?>" id="<?=$this->get_field_name('email_status')?>">
				<option>Selecione o modelo de e-mail...</option>
				<?php
				$wc_emails = WC_Emails::instance();
				$emails    = $wc_emails->get_emails();
				$selected = $this->email_status;
	
				foreach ( $emails as $index => $email )
				{ 
					if ( $index === $selected )
					{ echo sprintf('<option value="%s" selected="selected">%s</option>', $index, $email->title); }
					else
					{ echo sprintf('<option value="%s">%s</option>', $index, $email->title); }
				}

				?>
			</select>
			<p class="description">Selecione em qual modelo de e-mail o pix deve ser enviado.</p>
		</div>
		
		<div class="wpgly-field">
			<span class="wpgly-label">Posição do Pix no Modelo de E-mail:</span>
			<?php
			$selected = $this->email_position;
			$options  = [
				'before' => __('Acima da tabela do pedido', WC_PIGGLY_PIX_PLUGIN_NAME),
				'after' => __('Abaixo da tabela do pedido', WC_PIGGLY_PIX_PLUGIN_NAME),
			];

			foreach ( $options as $value => $label ) :
				$checked = $value === $selected ? 'checked="checked"' : '';
				?>
				<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('email_position_').$value?>">
					<input type="radio" value="<?=$value;?>"  name="<?=$this->get_field_name('email_position')?>" id="<?=$this->get_field_name('email_position_').$value?>" <?=$checked?>/> <?=$label?>
				</label>
				<?php
			endforeach;
			?>
			<p class="description">Selecione em qual posição do e-mail o pix deve ser anexado.</p>
		</div>
	<?php endif; ?>
 
	<h2 class="wpgly-title">Pedidos</h2>
	
	<?php if ( function_exists('wc_get_order_statuses') ) : ?>
		<div class="wpgly-field">
			<label class="wpgly-label" for="<?=$this->get_field_name('order_status')?>">Status "Aguardando Pagamento" via Pix:</label>
			<select class="wc-enhanced-select" name="<?=$this->get_field_name('order_status')?>" id="<?=$this->get_field_name('order_status')?>">
				<option>Selecione o status...</option>
				<?php
				$status   = wc_get_order_statuses();
				$selected = $this->order_status;

				foreach ( $status as $key => $alias )
				{ 
					if ( $key === $selected )
					{ echo sprintf('<option value="%s" selected="selected">%s</option>', $key, $alias); }
					else
					{ echo sprintf('<option value="%s">%s</option>', $key, $alias); }
				}

				?>
			</select>
			<p class="description">Ao ser criado, informe qual deve ser o novo status do pedido enquanto aguarda o pagamento Pix. Por padrão será <strong>Aguardando</strong> (<code>on-hold</code>)</p>
	
			<?php if ( !$helpText ) : ?>
				<div class="wpgly-spacing"></div>
				<div class="wpgly-notice-box">
					<h4 class="wpgly-title">Ajuda</h4>
					Utilizamos o status Aguardando, pois na nossa concepção a loja aguarda
					o cliente pagar o Pix. Se você criar status personalizados com plugins
					de terceiros, você pode alterar o status para aguardando o pagamento Pix
					de acordo com a sua grade de status.
				</div>
			<?php endif ?>
	<?php endif; ?>

	<div class="wpgly-field">
		<span class="wpgly-label">Pix em Minha Conta</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('hide_in_order')?>">
			<input type="checkbox" name="<?=$this->get_field_name('hide_in_order')?>" id="<?=$this->get_field_name('hide_in_order')?>" value="1" <?=(($this->hide_in_order) ? 'checked="checked"' : '');?>> Ocultar pagamento Pix na página de Visualização do Pedido em Minha Conta.
		</label>
		<p class="description">Ao não marcar, o pagamento Pix será exibido quando o cliente visualizar o pedido em Minha Conta, mas ainda não tiver pago.</p>
	</div>
		
	<h2 class="wpgly-title">Desconto para pagamento via Pix</h2>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('discount')?>">Desconto Aplicado</label>
		<input value="<?=$this->discount?>" type="text" name="<?=$this->get_field_name('discount')?>" id="<?=$this->get_field_name('discount')?>">
		<p class="description"><code class="wpgly-action">Deixe em branco para remover o desconto</code> Utilize o simbolo % para cupom de desconto em %.</p>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('discount_label')?>">Rótulo do Desconto Aplicado</label>
		<input value="<?=$this->discount_label?>" type="text" name="<?=$this->get_field_name('discount_label')?>" id="<?=$this->get_field_name('discount_label')?>">
		<p class="description">Descrição do desconto visualizada pelo cliente.</p>
	</div>
</div>

<div class="wpgly-spacing"></div>

<p class="submit wpgly-submit">
	<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
</p>