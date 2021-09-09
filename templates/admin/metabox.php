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
	<?php if ( $pix->isType(PixEntity::TYPE_STATIC) ) : ?>
		<h3 style="text-align: center" class="pgly-wps--title pgly-wps-is-7">Pix Estático</h3>
	<?php else: ?>
		<h3 style="text-align: center" class="pgly-wps--title pgly-wps-is-7">Pix Dinâmico</h3>
	<?php endif; ?>
	<?php if ( !empty($pix->getQrCode()['url']) ) : ?>
	<div>
		<img style="max-width:100%; height: auto;" src="<?=$pix->getQrCode()['url'];?>" alt="QR Code de Pagamento"/>
	</div>
	<?php endif; ?>

	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Identificador</strong>
		<span><?=$pix->getTxid();?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Nome do Titular do Pix</strong>
		<span><?=$pix->getMerchantName();?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Cidade do Titular do Pix</strong>
		<span><?=$pix->getMerchantCity();?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Banco</strong>
		<span>CÓDIGO <?=$pix->getBank();?></span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Status</strong>
		<div style="margin-top: 4px" class="pgly-wps--badge pgly-wps-is-<?=$pix->getStatusColor()?>"><?=$pix->getStatusLabel();?></div>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Chave Pix</strong>
		<span><?=$pix->getPixKeyValue();?> (<?=Parser::getAlias($pix->getPixKeyType());?>)</span>
	</div>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Valor do Pix</strong>
		<span><?=\wc_price($pix->getAmount());?></span>
	</div>
	<?php if ( !empty($pix->getDiscount()) ) : ?>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Desconto</strong>
		<span><?=\wc_price($pix->getDiscount());?></span>
	</div>
	<?php endif; ?>
	<div class="pgly-wps--explorer pgly-wps-is-compact">
		<strong>Pix Copia & Cola</strong>
		<span><?=$pix->getBrCode();?></span>
	</div>

	<?php if ( $pix->isStatus(PixEntity::STATUS_CREATED) ) : ?>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Data da Expiração</strong>
			<span><?=$pix->getExpiresAt()->format('d/m/Y H:i:s');?></span>
		</div>
	<?php endif; ?>

	<?php if ( !$pix->isType(PixEntity::TYPE_STATIC) ) : ?>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>ID do Pagamento</strong>
			<span><?=$pix->getE2eid() ?? 'Não Processado';?></span>
		</div>
	<?php endif; ?>

	<?php if ( !empty($pix->getReceipt()['url']) ) : ?>
	<div>
		<h4 style="text-align: center" class="pgly-wps--title pgly-wps-is-8">Comprovante Pix</h4>
		<div class="pgly-wps--explorer pgly-wps-is-compact">
			<strong>Verificação do Arquivo</strong>
			<span><?=$pix->getReceipt()['trusted'] ? 'Arquivo Verificado' : 'O arquivo não pode ser verificado';?></span>
		</div>
		<a 
			class="pgly-wps--button pgly-wps-is-success pgly-wps-is-expanded"
			href="<?=$pix->getReceipt()['url'];?>"
			target="_blank">
			Visualizar Comprovante
		</a>
	</div>
	<?php endif; ?>
</div>