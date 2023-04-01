<?php

use Piggly\WooPixGateway\Core\Entities\PixEntity;

if ( ! defined( 'ABSPATH' ) ) { exit; } 

/** @var PixEntity $pix */

if ( $sent && $error === false ) : 
?>
	<div class="woocommerce">
	<div class="woocommerce-notices-wrapper"></div>
		<ul class="woocommerce-message" role="alert">
			Recebemos o seu comprovante, em breve você receberá a confirmação do seu pagamento.
		</ul>
	</div>

	<?php if ( $permalink !== false ) : ?>
		<script>setTimeout(function () { location.href="<?php echo esc_url($permalink)?>"; }, 1500);</script>
	<?php endif; ?>
<?php else : ?>
	<?php if ( $error !== false ) : ?>
	<div class="woocommerce">
	<div class="woocommerce-notices-wrapper"></div>
		<ul class="woocommerce-error" role="alert">
			<?php echo esc_html($error);?>	
		</ul>
	</div>
	<?php endif ?>
	<div id="pix-por-piggly">
		<form class="pix-por-piggly-form" method="POST" action="<?php echo esc_html($link)?>" enctype="multipart/form-data">
			<div class="pix-por-piggly--item">
				<span class="pix-por-piggly--label">Número do Pedido</span>
				<span class="pix-por-piggly--data">
					<span><?php echo esc_html($pix->getOrder()->get_order_number());?></span>
				</span>
			</div>
			<div class="pix-por-piggly--item">
				<span class="pix-por-piggly--label">E-mail Associado</span>
				<span class="pix-por-piggly--data">
					<span><?php echo esc_html($pix->getOrder()->get_billing_email());?></span>
				</span>
			</div>
			<div class="pix-por-piggly--item">
				<span class="pix-por-piggly--label">Identificador Pix</span>
				<span class="pix-por-piggly--data">
					<span><?php echo esc_html($pix->getTxid());?></span>
				</span>
			</div>
				
			<div class="pix-por-piggly--field">
				<label class="pix-por-piggly--label" for="pgly_pix_receipt">Anexe o Comprovante:</label>
				<input required type="file" name="pgly_pix_receipt" id="pgly_pix_receipt">
				<p class="pix-por-piggly--description">Anexe uma imagem JPG, PNG ou um arquivo PDF.</p>
			</div>

			<input type="hidden" name="pgly_pix_nonce" value="<?php echo esc_attr($_nonce)?>" />
			<input type="submit" value="Enviar Comprovante"/>
		</form>
	</div>
<?php endif; ?>