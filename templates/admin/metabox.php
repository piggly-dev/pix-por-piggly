<?php

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\Core\Repo\PixRepo;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;

if ( ! defined( 'ABSPATH' ) ) { exit; }

global $post;

$order = new WC_Order( $post->ID );
$pix   = (new PixRepo(CoreConnector::plugin()))->byId($order->get_meta('_pgly_wc_piggly_pix_latest_pix'));
?>

<div id="pgly-pix-por-piggly" class="pgly-wps--settings" style="padding: 10px;">
<?php if ( empty($pix) ) : ?>
	<h3 style="text-align: center" class="pgly-wps--title pgly-wps-is-7">Pix Indisponível</h3>
	<div class="pgly-wps--notification pgly-wps-is-warning">
		Nenhum Pix está associado ao pedido, ele deverá ser recriado
		caso queira continuar com este pedido.
	</div>
	<div class="pgly-wps--notification">
		Não se preocupe, se o pedido estiver ativo e o cliente
		tentar efetuar o pagamento, o Pix será recriado.
	</div>
<?php else: ?>
	<?php if ( $pix->isType(PixEntity::TYPE_STATIC) ) : ?>
		<h3 style="text-align: center" class="pgly-wps--title pgly-wps-is-7">Pix de Verificação Manual (Estático)</h3>
	<?php else: ?>
		<h3 style="text-align: center" class="pgly-wps--title pgly-wps-is-7">Pix de Verificação Automática (Dinâmico)</h3>
		<p style="margin: 16px auto; text-align: center; font-style: italic">Este Pix é verificado de forma automática a partir da API do Banco.</p>
		<?php if (!$pix->isStatus(PixEntity::STATUS_PAID)) : ?>
			<p style="margin: 16px auto; text-align: center;">Se o cliente já realizou o pagamento, clique no botão abaixo para validar:</p>
			<button 
				class="pgly-wps--button pgly-async--behaviour pgly-wps-is-primary"
				data-action="pgly_wc_piggly_pix_admin_cron_process"
				data-response-container="woo-bdm-gateway-tx-<?php echo esc_attr($index);?>"
				data-refresh="true"
				data-tx="<?php echo esc_attr($pix->getTxid());?>">
				Verificar Pagamento
				<svg 
					class="pgly-wps--spinner pgly-wps-is-white"
					viewBox="0 0 50 50">
					<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
				</svg>
			</button>
			
			<div class="pgly-wps--response" id="pgly-pix-por-piggly-tx-<?php echo esc_attr($index);?>"></div>
			<div class="pgly-wps--space"></div>

			<script>
				document.addEventListener('DOMContentLoaded', () => {
					new PglyWpsAsync({
						container: '#pgly-pix-por-piggly',
						responseContainer: 'pgly-pix-por-piggly--response',
						url: wcPigglyPix .ajax_url,
						x_security: wcPigglyPix .x_security,
						messages: {
							request_error: 'Ocorreu um erro ao processar a requisição',
							invalid_fields: 'Campos inválidos'
						},
						debug: false
					});
				});
			</script>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( !empty($pix->getQrCode()['url']) ) : ?>
	<div>
		<img style="max-width:100%; height: auto;" src="<?php echo esc_url($pix->getQrCode()['url']);?>" alt="QR Code de Pagamento"/>
	</div>
	<?php endif; ?>
	
	<?php if ( $pix->isType(PixEntity::TYPE_STATIC) ) : ?>
	<div class="pgly-wps--notification pgly-wps-is-danger">
		O pagamento do Pix Estático não é verificado automaticamente.
		Para atualizar o Pix automaticamente, é necessário
		ter uma API do Pix conectada ao plugin.
		Saiba mais em <strong>API do Pix</strong> no menu 
		lateral "Pix por Piggly".
	</div>
	<?php endif; ?>

	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Identificador</strong>
		<span><?php echo esc_html($pix->getTxid());?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Nome do Titular do Pix</strong>
		<span><?php echo esc_html($pix->getMerchantName());?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Cidade do Titular do Pix</strong>
		<span><?php echo esc_html($pix->getMerchantCity());?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Banco</strong>
		<span>CÓDIGO <?php echo esc_html($pix->getBank());?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Status</strong>
		<div style="margin-top: 4px" class="pgly-wps--badge pgly-wps-is-<?php echo esc_attr($pix->getStatusColor())?>"><?php echo esc_html($pix->getStatusLabel());?></div>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Chave Pix</strong>
		<span><?php echo esc_html($pix->getPixKeyValue());?> (<?php echo esc_html(Parser::getAlias($pix->getPixKeyType()));?>)</span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Valor do Pix</strong>
		<span><?php echo \wc_price($pix->getAmount());?></span>
	</div>
	<?php if ( !empty($pix->getDiscount()) ) : ?>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Desconto</strong>
		<span><?php echo \wc_price($pix->getDiscount());?></span>
	</div>
	<?php endif; ?>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Pix Copia & Cola</strong>
		<span><?php echo esc_html($pix->getBrCode());?></span>
	</div>

	<?php if ( $pix->isStatus(PixEntity::STATUS_CREATED) && !empty($pix->getExpiresAt()) ) : ?>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Data da Expiração</strong>
			<span><?php echo $pix->getExpiresAt()->format('d/m/Y H:i:s');?></span>
		</div>
	<?php endif; ?>

	<?php if ( !$pix->isType(PixEntity::TYPE_STATIC) ) : ?>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>ID do Pagamento</strong>
			<span><?php echo esc_html($pix->getE2eid() ?? $order->get_transaction_id() ?? 'Não Processado');?></span>
		</div>
	<?php endif; ?>

	<?php if ( !empty($pix->getReceipt()['url']) ) : ?>
	<div>
		<h4 style="text-align: center" class="pgly-wps--title pgly-wps-is-8">Comprovante Pix</h4>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Verificação do Arquivo</strong>
			<span><?php echo esc_html($pix->getReceipt()['trusted'] ? 'Arquivo Verificado' : 'O arquivo não pode ser verificado');?></span>
		</div>
		<a 
			class="pgly-wps--button pgly-wps-is-success pgly-wps-is-expanded"
			href="<?php echo esc_url($pix->getReceipt()['url']);?>"
			target="_blank">
			Visualizar Comprovante
		</a>
	</div>
	<?php endif; ?>
<?php endif; ?>
</div>