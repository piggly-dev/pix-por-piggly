<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<style>
	.wpgly-description
	{
		font-style: italic;
		font-size: 12px;
	}

	.wpgly-label
	{
		display: block;
		font-size: 14px;
		text-transform: uppercase;
		font-weight: bold;
	}

	.wpgly-field input[type=text], .wpgly-field input[type=file]
	{
		width: 100%;
		margin-bottom: 16px;
	}
</style>
<?php if ( $sent && $error === false ) : ?>
	<div class="woocommerce-message" role="alert">
		<p>Recebemos o seu comprovante, em breve você receberá a confirmação do seu pagamento.</p>
	</div>

	<?php if ( $permalink !== false ) : ?>
		<script>location.href="<?=$permalink?>";</script>
	<?php endif; ?>
<?php else : ?>
	<?php if ( $error !== false ) : ?>
	<div class="woocommerce-error" role="alert">
		<p><?=$error;?></p>
	</div>
	<?php endif ?>

	<form class="wpgly-form" method="POST" action="<?=$link?>" enctype="multipart/form-data">
		<?php if ( $auto_fill ) : ?>
			<input type="hidden" name="wpgly_pix_email" value="<?=$email?>" />
			<input type="hidden" name="wpgly_pix_order_key" value="<?=$order_key?>" />
			<input type="hidden" name="wpgly_pix_auto_fill" value="true" />
		<?php else : ?>
			<div class="wpgly-field">
				<label class="wpgly-label" for="wpgly_pix_email">E-mail:</label>
				<input required type="text" name="wpgly_pix_email" id="wpgly_pix_email">
			</div>
			<div class="wpgly-field">
				<label class="wpgly-label" for="wpgly_pix_receipt">Número do Pedido:</label>
				<input required type="text" name="wpgly_pix_order_key" id="wpgly_pix_order_key">
			</div>
			<input type="hidden" name="wpgly_pix_auto_fill" value="false" />
		<?php endif; ?>
			
		<div class="wpgly-field">
			<label class="wpgly-label" for="wpgly_pix_receipt">Anexe o Comprovante:</label>
			<input required type="file" name="wpgly_pix_receipt" id="wpgly_pix_receipt">
			<p class="wpgly-description">Anexe uma imagem JPG, PNG ou um arquivo PDF.</p>
		</div>

		<input type="hidden" name="wpgly_pix_nonce" value="<?=$_nonce?>" />
		<input type="submit" value="Enviar Comprovante"/>
	</form>
<?php endif; ?>