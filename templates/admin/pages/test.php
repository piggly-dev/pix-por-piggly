<?php

use Piggly\WooPixGateway\Core\Entities\PixEntity;
use Piggly\WooPixGateway\CoreConnector;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\AbstractPayload;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\StaticPayload;

if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings    = CoreConnector::settings();
$plugin_page = admin_url('admin.php?page='.CoreConnector::domain());
?>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		<h2 class="pgly-wps--title pgly-wps-is-5">Teste seu Pix!</h2>

		<?php if ( empty($settings->get('account')->get('key_value')) ) : ?>
		<div class="pgly-wps--notification pgly-wps-is-warning">
			⚠ Antes de testar o Pix, lembre-se de preencher todos
			os dados associados a sua Conta Pix nas <a href="<?=$plugin_page?>">Configurações
			do Plugin</a>.
		</div>
		<?php else: ?>
		<p>
			Neste playground, você pode testar o Pix que é gerado durante os pedidos.
			Para isso, preencha os dados abaixo e clique em <strong>Gerar Pix</strong>.
		</p>

		<div class="pgly-wps--notification pgly-wps-is-warning">
			⚠ Caso tenha algum problema com o processamento do Pix, acesse o menu
			<a href="<?=$plugin_page.'-support'?>">Suporte</a> para saber como proceder
			antes de enviar uma notificação aos desenvolvedores.
		</div>

		<form method="POST">
		<div class="pgly-wps--row">
			<div class="pgly-wps--column pgly-wps-col--6">
				<div class="pgly-wps--field">
					<label class="pgly-wps--label" for="amount">Valor do Pix</label>
					<div class="pgly-wps--content">
						<input 
							tabindex="0" 
							type="number" 
							id="amount" 
							name="amount" 
							placeholder="Preencha o valor em reais..." 
							value="1.00" 
							type="text">
					</div>
					<p class="pgly-wps--description">
						Simule o valor para pagamento do Pix.
					</p>
				</div>
			</div>
			<div class="pgly-wps--column pgly-wps-col--6">
				<div class="pgly-wps--field">
					<label class="pgly-wps--label" for="order_id">Selecione um pedido</label>
					<div class="pgly-wps--content">
						<select name="order_id" id="order_id">
							<option value="000001">Nenhum pedido selecionado</option>
							<?php 
							$orders = \wc_get_orders( array('limit' => 10) );
							
							foreach ( $orders as $order )
							{ 
								$id = $order->get_id();
								echo sprintf('<option value="%s">Pedido #%s</option>', $id, $id); 
							}
							?>
						</select>
					</div>
					<p class="pgly-wps--description">
						O pedido será utilizado apenas para fins de teste, o cliente
						não receberá nenhuma notificação sobre o Pix gerado.
					</p>
				</div>
			</div>
		</div>
		<div class="pgly-wps--row">
			<div class="pgly-wps--column">
				<button type="submit" class="pgly-wps--button pgly-wps-is-regular pgly-wps-is-accent">
					Gerar Pix
				</button>
			</div>
		</div>
		</form>
		<?php endif; ?>

		<div class="pgly-wps--row">
			<div class="pgly-wps--column">
				<?php 
				if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) :
					$amount    = str_replace(',', '.', filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING ) );
					$order_id  = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_STRING );
			
					if ( !empty($amount) ) :
						try
						{
							$instructions     = str_replace('{{order_number}}', $order_id, $settings->get('gateway')->get('instructions'));
							$whatsapp_number  = $settings->get('receipts')->get('whatsapp_number');
							$telegram_number  = $settings->get('receipts')->get('telegram_number'); 
							$whatsapp_message = str_replace('{{order_number}}', $order_id, $settings->get('receipts')->get('whatsapp_message'));
							$telegram_message = str_replace('{{order_number}}', $order_id, $settings->get('receipts')->get('telegram_message')); 
							$identifier       = str_replace('{{order_number}}', $order_id, $settings->get('account')->get('identifier'));
							$description      = str_replace('{{order_number}}', $order_id, $settings->get('account')->get('description'));
							$description      = str_replace('{{store_name}}', $settings->get('account')->get('store_name'), $description);
							$bank             = $settings->get('account')->get('bank');
							
							$pix = 
								(new StaticPayload())
									->setAmount((float)$amount)
									->setTid($identifier)
									->setPixKey($settings->get('account')->get('key_type'), $settings->get('account')->get('key_value'))
									->setDescription($description)
									->setMerchantName($settings->get('account')->get('merchant_name'))
									->setMerchantCity($settings->get('account')->get('merchant_city'));

							// Payload test must be changed by apis
							/** @var AbstractPayload $pix */
							$pix = apply_filters('pgly_wc_piggly_pix_test_payload', $pix, $amount);
							
							?>
							<div class="pgly-wps--row">
								<div class="pgly-wps--column pgly-wps-col--6">
									<h3 class="pgly-wps--title pgly-wps-is-6">Dados de Depuração</h3>
									
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Banco Origem</strong> 
										<span><?=empty($bank) ? 'Nenhum' : 'CÓDIGO '.$bank;?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Tipo da Chave</strong> 
										<span><?=Parser::getAlias($settings->get('account')->get('key_type'))?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Chave</strong> 
										<span><?=$settings->get('account')->get('key_value')?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Titular</strong> 
										<span><?=$settings->get('account')->get('merchant_name')?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Cidade</strong> 
										<span><?=$settings->get('account')->get('merchant_city')?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Identificador</strong> 
										<span><?=$identifier?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Valor</strong> 
										<span><?=$amount?></span>
									</div>
									<div class="pgly-wps--explorer pgly-wps-is-compact">
										<strong>Código Pix</strong> 
										<span><?=$pix->getPixCode()?></span>
									</div>
								</div>
								<div class="pgly-wps--column pgly-wps-col--6">
									<h3 class="pgly-wps--title pgly-wps-is-6">Links Gerados</h3>

									<?php if ( empty($whatsapp_number) || empty($telegram_number) ) : ?>
										<p>Os links de compartilhamento do comprovante estão desabilitados.</p>
									<?php else: ?>
										<?php if ( !empty($whatsapp_number) ) : ?>
											<?php $link = sprintf('https://wa.me/%s?text=%s',str_replace('+', '', PixEntity::parse_phone($whatsapp_number)),urlencode($whatsapp_message)); ?>
											<h4 class="pgly-wps--title pgly-wps-is-7">Whatsapp</h4>
											<div class="pgly-wps--explorer pgly-wps-is-compact">
												<strong>Link</strong> 
												<span><?=$link?> (<a href="<?=$link?>" target="_blank">Abrir Link</a>)</span>
											</div>
											<div class="pgly-wps--explorer pgly-wps-is-compact">
												<strong>Mensagem</strong> 
												<span><?=$whatsapp_message?></span>
											</div>
										<?php endif; ?>
						
										<?php if ( !empty($telegram_number) ) : ?>
											<?php $link = sprintf('https://t.me/%s?text=%s',str_replace('@', '', $telegram_number),urlencode($telegram_message)); ?>
											<h4 class="pgly-wps--title pgly-wps-is-7">Telegram</h4>
											<div class="pgly-wps--explorer pgly-wps-is-compact">
												<strong>Link</strong> 
												<span><?=$link?> (<a href="<?=$link?>" target="_blank">Abrir Link</a>)</span>
											</div>
											<div class="pgly-wps--explorer pgly-wps-is-compact">
												<strong>Mensagem</strong> 
												<span><?=$telegram_message?></span>
											</div>
										<?php endif; ?>	
									<?php endif; ?>
								</div>
							</div>

							<div class="pgly-wps--space"></div>
							<p style="text-align: center">Confira abaixo o template que está sendo carregado 👇</p>
							
							<div id="pix-por-piggly">
							<svg class="pix-por-piggly-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 165.27 165.27"><path d="M137.25,145.29a24.11,24.11,0,0,1-17.16-7.1L95.3,113.4a4.71,4.71,0,0,0-6.52,0L63.9,138.28a24.12,24.12,0,0,1-17.16,7.11H41.85l31.4,31.39a25.11,25.11,0,0,0,35.5,0l31.49-31.49Z" transform="translate(-8.37 -18.87)"/><path d="M46.74,57.61A24.12,24.12,0,0,1,63.9,64.72L88.78,89.61a4.62,4.62,0,0,0,6.52,0l24.78-24.79a24.16,24.16,0,0,1,17.17-7.11h3L108.75,26.22a25.11,25.11,0,0,0-35.5,0L41.85,57.61Z" transform="translate(-8.37 -18.87)"/><path d="M166.28,83.75l-19-19a3.57,3.57,0,0,1-1.35.27h-8.65a17.11,17.11,0,0,0-12,5L100.45,94.76a11.9,11.9,0,0,1-16.82,0L58.75,69.88a17.11,17.11,0,0,0-12-5H36.1a3.44,3.44,0,0,1-1.28-.26L15.72,83.75a25.09,25.09,0,0,0,0,35.5l19.1,19.11a3.44,3.44,0,0,1,1.28-.26H46.74a17.11,17.11,0,0,0,12-5l24.88-24.88a12.19,12.19,0,0,1,16.82,0L125.24,133a17.11,17.11,0,0,0,12,5h8.65a3.57,3.57,0,0,1,1.35.27l19-19a25.09,25.09,0,0,0,0-35.5" transform="translate(-8.37 -18.87)"/></svg>
							<h2 class="pix-por-piggly-title">Pague agora com o <strong>Pix</strong></h2>
	
								<div class="pix-por-piggly--row pix-por-piggly--review">
									<div class="pix-por-piggly--column">
										<div class="pix-por-piggly--item">
											<p class="pix-por-piggly--centered pix-por-piggly--space">
												<?php echo wptexturize( $instructions ); ?>
											</p>
											
											<span class="pix-por-piggly--label">
												Valor do Pedido
											</span>
											<span class="pix-por-piggly--data">
												R$ <?=\wc_format_decimal($amount);?>
											</span>
										</div>
									</div>
								</div>

								<div style="padding: 28px 0; text-align: center">
									<?php if ( $settings->get('receipts')->get('receipt_page', true) ) : ?>
									<a href="<?=$data->receipt_page_value?>" class="pix-por-piggly--button ">
										<svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9" fill="#000"/><line x1="10" x2="21" y1="14" y2="3" fill="#000"/></svg>
										Enviar Comprovante
									</a>
									<?php endif; ?>

									<?php if ( !empty($whatsapp_number) ) : ?>
										<a href="<?=sprintf('https://wa.me/%s?text=%s',str_replace('+', '', PixEntity::parse_phone($whatsapp_number)),urlencode($whatsapp_message));?>" class="pix-por-piggly--button" style="background-color: #25D366; border-color: #25D366">
											<svg height="100%" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" version="1.1" viewBox="0 0 512 512" width="100%" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M373.295,307.064c-6.37,-3.188 -37.687,-18.596 -43.526,-20.724c-5.838,-2.126 -10.084,-3.187 -14.331,3.188c-4.246,6.376 -16.454,20.725 -20.17,24.976c-3.715,4.251 -7.431,4.785 -13.8,1.594c-6.37,-3.187 -26.895,-9.913 -51.225,-31.616c-18.935,-16.89 -31.72,-37.749 -35.435,-44.126c-3.716,-6.377 -0.397,-9.824 2.792,-13c2.867,-2.854 6.371,-7.44 9.555,-11.16c3.186,-3.718 4.247,-6.377 6.37,-10.626c2.123,-4.252 1.062,-7.971 -0.532,-11.159c-1.591,-3.188 -14.33,-34.542 -19.638,-47.298c-5.171,-12.419 -10.422,-10.737 -14.332,-10.934c-3.711,-0.184 -7.963,-0.223 -12.208,-0.223c-4.246,0 -11.148,1.594 -16.987,7.969c-5.838,6.377 -22.293,21.789 -22.293,53.14c0,31.355 22.824,61.642 26.009,65.894c3.185,4.252 44.916,68.59 108.816,96.181c15.196,6.564 27.062,10.483 36.312,13.418c15.259,4.849 29.145,4.165 40.121,2.524c12.238,-1.827 37.686,-15.408 42.995,-30.286c5.307,-14.882 5.307,-27.635 3.715,-30.292c-1.592,-2.657 -5.838,-4.251 -12.208,-7.44m-116.224,158.693l-0.086,0c-38.022,-0.015 -75.313,-10.23 -107.845,-29.535l-7.738,-4.592l-80.194,21.037l21.405,-78.19l-5.037,-8.017c-21.211,-33.735 -32.414,-72.726 -32.397,-112.763c0.047,-116.825 95.1,-211.87 211.976,-211.87c56.595,0.019 109.795,22.088 149.801,62.139c40.005,40.05 62.023,93.286 62.001,149.902c-0.048,116.834 -95.1,211.889 -211.886,211.889m180.332,-392.224c-48.131,-48.186 -112.138,-74.735 -180.335,-74.763c-140.514,0 -254.875,114.354 -254.932,254.911c-0.018,44.932 11.72,88.786 34.03,127.448l-36.166,132.102l135.141,-35.45c37.236,20.31 79.159,31.015 121.826,31.029l0.105,0c140.499,0 254.87,-114.366 254.928,-254.925c0.026,-68.117 -26.467,-132.166 -74.597,-180.352" id="WhatsApp-Logo"/></svg>
											<span>Enviar Comprovante via Whatsapp</span>
										</a>
									<?php endif; ?>

									<?php if ( !empty($telegram_number) ) : ?>
										<a href="<?=sprintf('https://t.me/%s?text=%s',str_replace('@', '', $telegram_number),urlencode($telegram_message))?>" class="pix-por-piggly--button" style="background-color: #6CC1E3; border-color: #6CC1E3">
											<svg height="512px" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="comp_x5F_335-telegram"><g><path d="M484.689,98.231l-69.417,327.37c-5.237,23.105-18.895,28.854-38.304,17.972L271.2,365.631l-51.034,49.086    c-5.647,5.647-10.372,10.372-21.256,10.372l7.598-107.722L402.539,140.23c8.523-7.598-1.848-11.809-13.247-4.21L146.95,288.614    L42.619,255.96c-22.694-7.086-23.104-22.695,4.723-33.579L455.423,65.166C474.316,58.081,490.85,69.375,484.689,98.231z"/></g></g><g id="Layer_1"/></svg>
											<span>Enviar Comprovante via Telegram</span>
										</a>
									<?php endif; ?>
								</div>

								<?php if ( $settings->get('gateway')->get('shows_qrcode', false) ) : ?>
								<div class="pix-por-piggly--row pix-por-piggly--qrcode">
									<div class="pix-por-piggly--column">
										<p>
											Leia o <strong>QRCode</strong> abaixo com o aplicativo
											<strong>do seu banco</strong> e realize o pagamento do Pix:
										</p>
										<img src="<?=$pix->getQRCode();?>"/>
									</div>
								</div>
								<?php endif; ?>

								<?php if ( $settings->get('gateway')->get('shows_qrcode', false) && $settings->get('gateway')->get('shows_copypast', false) ) : ?>
								<div class="pix-por-piggly--row">
									<div class="pix-por-piggly--column pix-por-piggly--or">
										<span>OU</span>
									</div>
								</div>
								<?php endif; ?>

								<?php if ( $settings->get('gateway')->get('shows_copypast', false) ) : ?>
								<div class="pix-por-piggly--row pix-por-piggly--manual">
									<div class="pix-por-piggly--column">
										<p>
											Copie o <strong>Código Pix</strong> abaixo e insira na opção
											<strong>Pix Copia e Cola</strong> no aplicativo <strong>do seu banco</strong> 
											para realizar o pagamento do Pix:
										</p>
										<div class="pix-por-piggly--item">
											<span class="pix-por-piggly--data">
												<span><?=$pix->getPixCode()?></span>
											</span>
											<button
												id="pix-copy-pix"
												class="pix-por-piggly--copy"
												onclick="pixCopyText('<?=$pix->getPixCode()?>', 'pix-copy-pix');">
												Copiar Pix
											</button>
										</div>
									</div>
								</div>
								<?php endif; ?>
								
								<?php if ( $settings->get('gateway')->get('shows_manual', false) && ($settings->get('gateway')->get('shows_copypast', false) || $settings->get('gateway')->get('shows_qrcode', false)) ) : ?>
								<div class="pix-por-piggly--row">
									<div class="pix-por-piggly--column pix-por-piggly--or">
										<span>OU</span>
									</div>
								</div>
								<?php endif; ?>

								<?php if ( $settings->get('gateway')->get('shows_manual', false) ) : ?>
								<div class="pix-por-piggly--row pix-por-piggly--manual">
									<div class="pix-por-piggly--column">
										<div class="pix-por-piggly--item">
											<span class="pix-por-piggly--label">Transfira</span>
											<span class="pix-por-piggly--data">
												<span>R$ <?=\wc_format_decimal($amount);?></span>
											</span>
											<button
												id="pix-copy-amount"
												class="pix-por-piggly--copy"
												onclick="pixCopyText('<?=\wc_format_decimal($amount)?>', 'pix-copy-amount');">
												Copiar Valor
											</button>
										</div>
										<div class="pix-por-piggly--item">
											<span class="pix-por-piggly--label">
												Para a Chave Pix
											</span>
											<span class="pix-por-piggly--data">
												<span><?=$settings->get('account')->get('key_value')?></span>
											</span>
											<span class="pix-por-piggly--data">
												<span><?=Parser::getAlias($settings->get('account')->get('key_type'))?></span>
											</span>
											<button
												id="pix-copy-key"
												class="pix-por-piggly--copy"
												onclick="pixCopyText('<?=$settings->get('account')->get('key_value')?>', 'pix-copy-key');">
												Copiar Chave
											</button>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>

							<?php
						}
						catch ( Exception $e )
						{ 
							?>
							<div class="pgly-wps--notification pgly-wps-is-danger">
								<strong>Um erro foi capturado, informe ao suporte: <code><?=$e->getMessage();?></code>
							</div>
							<?php
						}

					endif;
				endif; ?> 
			</div>
		</div>
	</div>
</div>
