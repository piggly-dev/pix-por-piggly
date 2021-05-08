<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
use Piggly\Pix\Parser;
?>
<h1 class="wpgly-title">Configurações dos Comprovantes</h1>

<div class="wpgly-wrapper">

	<h3 class="wpgly-title">Formulário de Comprovantes</h3>

	<div class="wpgly-field">
		<span class="wpgly-label">Atualizar Pedido automaticamente para o status "Comprovante Pix Recebido"</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('auto_update_receipt')?>">
			<input type="checkbox" name="<?=$this->get_field_name('auto_update_receipt')?>" id="<?=$this->get_field_name('auto_update_receipt')?>" value="yes" <?=(($this->auto_update_receipt) ? 'checked="checked"' : '');?>> Sim, atualizar automaticamente.
		</label>
		<p class="description">Ao utilizar o shortcode <code>[pix-por-piggly-form]</code>, altera o pedido automaticamente para "Comprovante Pix Recebido" quando o comprovante for recebido.</p>
	</div>

	<div class="wpgly-field">
		<span class="wpgly-label">Ocultar status "Comprovante Pix Recebido" do Woocommerce</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('hide_receipt_status')?>">
			<input type="checkbox" name="<?=$this->get_field_name('hide_receipt_status')?>" id="<?=$this->get_field_name('hide_receipt_status')?>" value="yes" <?=(($this->hide_receipt_status) ? 'checked="checked"' : '');?>> Sim, ocultar do painel de pedidos.
		</label>
		<p class="description">Ao ocultar o status do painel de pedidos, pedidos marcado com o status precisarão ser atualizados para outro status.</p>
	</div>

	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('redirect_after_receipt')?>">Redirecionar para a página após o envio do comprovante:</label>
		<select required class="wc-enhanced-select" name="<?=$this->get_field_name('redirect_after_receipt')?>" id="<?=$this->get_field_name('redirect_after_receipt')?>">
			<option>Selecione uma página...</option>
			<?php
			$selected = $this->redirect_after_receipt ?? null;
			$options  = get_pages('post_status=publish');

			foreach ( $options as $page )
			{ 
				if ( $page->ID === (int)$selected )
				{ echo sprintf('<option value="%s" selected="selected">%s</option>', $page->ID, $page->post_title); }
				else
				{ echo sprintf('<option value="%s">%s</option>', $page->ID, $page->post_title); }
			}

			?>
		</select>
		<p class="description">O usuário será redirecionado para a página acima após sucesso no envio do comprovante.</p>
	</div>

	<a class="wpgly-button wpgly-action woocommerce-save-button" href="<?=admin_url('admin.php?page='.WC_PIGGLY_PIX_PLUGIN_NAME);?>">Visualizar Comprovantes</a>

	<h3 class="wpgly-title">Página para enviar o Comprovante</h3>

	<div class="wpgly-field">

		<label class="wpgly-label" for="<?=$this->get_field_name('receipt_page_value')?>">Link para a Página do Comprovante</label>
		<input value="<?=$this->receipt_page_value?>" type="text" name="<?=$this->get_field_name('receipt_page_value')?>" id="<?=$this->get_field_name('receipt_page_value')?>">
		<p class="description"><code class="wpgly-action">Deixe em branco para ocultar</code> Quando preenchido, adiciona um botão para ir até a página.</p>
		<?php if ( !empty($this->receipt_page_value) ) : ?>
		<p><strong>Pré-visualize</strong> <code><?=$this->parse_receipt_link($this->receipt_page_value, '12345', 'wc_order_Gv2NKD1esDi0o');?></code></p>
		<?php endif; ?>
		
		<div class="wpgly-spacing"></div>
		<div class="wpgly-notice-box wpgly-action">
			<h4 class="wpgly-title">Merge Tag</h4>
			<p><code>{{pedido}}</code> Faz referência ao número do pedido.</p>
		</div>

		<div class="wpgly-notice-box wpgly-warning">
			<h4 class="wpgly-title">Aviso</h4>
			<p>
				Você pode utilizar o shortcode <code>[pix-por-piggly-form]</code> na
				página acima para incluir automaticamente um formulário otimizado
				para receber os comprovantes Pix. Saiba mais em 
				<a href="<?=$baseUrl.'&screen=shortcode';?>">Shortcodes</a>.
				Nesse caso, você também pode ignorar as Merge Tag.
			</p>
		</div>

		<?php if ( !$helpText ) : ?>
		<div class="wpgly-notice-box">
			<h4 class="wpgly-title">Recomendações</h4>
			Crie um formulário com os plugins <strong>Gravity Forms</strong>, <strong>WP Forms</strong> ou similares.
			Coloque esse formulário em uma nova página, por exemplo <code>https://minhaloja.com.br/comprovante-pix</code>. Insira os
			campos: <em>Número do Pedido</em>, <em>E-mail</em>, <em>Comprovante Pix (permitindo envio de arquivos png, jpg ou pdf apenas)</em>.
			<br><br>
			No campo <em>Número do Pedido</em> preencha o valor dele dinâmicamente com a <strong>URL Query String</strong>, por exemplo,
			<code>order_id</code>. Assim, preencha a <strong>Página do Comprovante</strong> como <code>https://minhaloja.com.br/comprovante-pix/?order_id={{pedido}}</code>.
			Ao fazer isso, o formulário na página <code>/comprovante-pix</code> receberá o número do pedido automaticamente. Facilitando para o comprador.
			<br>
			<p><br>
				Você também pode utilizar o shortcode nativo <code>[pix-por-piggly-form]</code> 
				na	página acima para incluir automaticamente um formulário otimizado
				para receber os comprovantes Pix.
			</p>
		</div>
		<?php endif ?>
	</div>
	
	<h3 class="wpgly-title">Whatsapp enviar o Comprovante</h3>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('whatsapp')?>">Telefone:</label>
		<input value="<?=$this->whatsapp?>" type="text" name="<?=$this->get_field_name('whatsapp')?>" id="<?=$this->get_field_name('whatsapp')?>">
		<p class="description"><code class="wpgly-action">Deixe em branco para ocultar</code> Quando preenchido, adiciona um botão para enviar mensagem via Whatsapp.</p>
		<?php if ( !empty($this->whatsapp) ) : ?>
		<p><strong>Pré-visualize</strong> <code><?=$this->parse_phone($this->whatsapp);?></code></p>
		<?php endif; ?>
	</div>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('whatsapp_message')?>">Mensagem inicial para ser enviada no Whatsapp:</label>
		<input value="<?=$this->whatsapp_message?>" type="text" name="<?=$this->get_field_name('whatsapp_message')?>" id="<?=$this->get_field_name('whatsapp_message')?>">
		<?php if ( !empty($this->whatsapp_message) ) : ?>
			<p><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $this->whatsapp_message);?></code></p>
		<?php endif; ?>
		
		<div class="wpgly-spacing"></div>
		<div class="wpgly-notice-box wpgly-action">
			<h4 class="wpgly-title">Merge Tag</h4>
			<p><code>{{pedido}}</code> Faz referência ao número do pedido.</p>
		</div>
	</div>
	
	<h3 class="wpgly-title">Usuário do Telegram para enviar o Comprovante</h3>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('telegram')?>">Nome de Usuário:</label>
		<input value="<?=$this->telegram?>" type="text" name="<?=$this->get_field_name('telegram')?>" id="<?=$this->get_field_name('telegram')?>">
		<p class="description"><code class="wpgly-action">Deixe em branco para ocultar</code> Quando preenchido, adiciona um botão para enviar mensagem via Telegram.</p>
		<p class="description">Informe somente o seu nome de usuário com ou sem @.</p>
		<?php if ( !empty($this->telegram) ) : ?>
		<p><strong>Pré-visualize</strong> <code>@<?=str_replace('@', '', $this->telegram);?></code></p>
		<?php endif; ?>
	</div>
	
	<div class="wpgly-field">
		<label class="wpgly-label" for="<?=$this->get_field_name('telegram_message')?>">Mensagem inicial para ser enviada no Telegram:</label>
		<input value="<?=$this->telegram_message?>" type="text" name="<?=$this->get_field_name('telegram_message')?>" id="<?=$this->get_field_name('telegram_message')?>">
		<?php if ( !empty($this->telegram_message) ) : ?>
			<p><strong>Pré-visualize</strong> <code><?=str_replace('{{pedido}}', '123456', $this->telegram_message);?></code></p>
		<?php endif; ?>
		
		<div class="wpgly-spacing"></div>
		<div class="wpgly-notice-box wpgly-action">
			<h4 class="wpgly-title">Merge Tag</h4>
			<p><code>{{pedido}}</code> Faz referência ao número do pedido.</p>
		</div>
	</div>
</div>

<div class="wpgly-spacing"></div>

<p class="submit wpgly-submit">
	<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
</p>