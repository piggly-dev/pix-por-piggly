<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
if ( $description ) 
{ printf('<p class="wpgly-pix-description">%s</p>', wptexturize( $description )); }
?>
<img class="wpgly-pix-img" src="<?=$banner?>" title="Pague via Pix" />
<div class="wpgly-pix-wrapper">
	<div class="wpgly-pix-step" data-step="1">
		<p>
			Finalize a sua compra e abra o app do banco
			na opção Pix.
		</p>
	</div>
	<div class="wpgly-pix-step" data-step="2">
		<p>
			Aponte a câmera do celular para o QR Code
			ou copie e cole o código Pix.
		</p>
	</div>
	<div class="wpgly-pix-step" data-step="3">
		<p>
			Confira os dados e confirme o seu pagamento
			pelo app do Banco.
		</p>
	</div>
</div>