<?php

use Piggly\WooPixGateway\CoreConnector;

if ( ! defined( 'ABSPATH' ) ) { exit; } 

$banner = CoreConnector::plugin()->getUrl().'assets/images/banner-'.CoreConnector::settings()->get('gateway')->get('icon').'.png';
?>
<div id="pix-por-piggly">
	<?php
	if ( $description ) 
	{ printf('<p class="pix-por-piggly--description">%s</p>', wptexturize( $description )); }
	?>
	
	<img class="pix-por-piggly--img" src="<?=$banner?>" title="Pague via Pix" />
	<div class="pix-por-piggly--wrapper">
		<div class="pix-por-piggly--step" data-step="1">
			<p>
				Finalize a sua compra e abra o app do banco
				na opção Pix.
			</p>
		</div>
		<div class="pix-por-piggly--step" data-step="2">
			<p>
				Aponte a câmera do celular para o QR Code
				ou copie e cole o código Pix.
			</p>
		</div>
		<div class="pix-por-piggly--step" data-step="3">
			<p>
				Confira os dados e confirme o seu pagamento
				pelo app do Banco.
			</p>
		</div>
	</div>
</div>