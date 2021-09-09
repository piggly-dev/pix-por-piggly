<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">APIs do Pix</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		As APIs do Pix permitem criar novos códigos Pix e
		identificar se esses códigos foram ou não pagos.
		A versão 2.0.0 suporta integração com a APIs, mas
		para isso é necessário que:
	</div>
	<div class="pgly-wps--column">
		👉 Você tenha acesso a API do Pix do banco
		onde a sua Chave Pix está cadastrada.
	</div>
	<div class="pgly-wps--column">
		👉 Você tenha/seja um desenvolvedor para fazer
		a integração ou tenha adquirido um plugin de API
		do Pix para o seu banco com o <strong>Piggly Lab</strong>.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Como funciona?</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Uma API do Pix cria um <code>Payload Dinâmico</code>,
		um Pix com um código identificador único armazenado
		no Banco Emissor.
	</div>
	<div class="pgly-wps--column">
		O cliente realiza o pagamento desse Pix e o pagamento
		é notificado ao Banco Emissor.
	</div>
	<div class="pgly-wps--column">
		É possível, então, consultar o status de pagamento do
		Pix consultando o código identificador único gerado
		pelo Banco Emissor.
	</div>
	<div class="pgly-wps--column">
		Quando um Pix é pago, o plugin atualizará automaticamente
		o pedido e enviará uma notificação de pagamento ao administrador
		e ao cliente.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Como implementar?</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		O plugin conta com diversas <code>actions</code> e
		<code>filters</code> para permitir a implementação
		de um integrador da API.
	</div>
	<div class="pgly-wps--column">
		Utilize o <code>filter</code> <code>pgly_wc_piggly_pix_payload</code>
		para retornar um objeto do tipo <code>Piggly\WooPixGateway\Vendor\Piggly\Pix\DynamicPayload</code>.
		Contendo os dados do Pix gerado pela API do Pix.
	</div>
	<div class="pgly-wps--column">
		Depois, registre uma função na <code>action</code> 
		<code>pgly_wc_piggly_pix_process</code> que deve atualizar
		o objeto <code>PixEntity</code> para o status apropriado
		de acordo com o resultado da consulta na API do Pix.
	</div>
	<div class="pgly-wps--column">
		Também é possível configurar o webhook do Pix, para
		receber as notificações de pagamento, para a URL 
		<code>seudominio.com.br/wc-api/pgly-pix-webhook</code>
		e registrar uma função na <code>action</code> 
		<code>pgly_wc_piggly_pix_webhook</code> para manipular
		o corpo da resposta enviada.
	</div>
</div>



<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Implementações Disponíveis</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		No momento, o <strong>Piggly Lab</strong> possui apenas a
		implementação para o <strong>Banco Sicoob</strong>, envie
		um e-mail para <strong>dev@piggly.com.br</strong> e solicite
		detalhes da implementação e valores do plugin adicional.
	</div>
	<div class="pgly-wps--column">
		Caso você tenha acesso a uma API do Pix, <strong>e você precisa
		deste acesso</strong>, entre em contato com a gente e faremos
		um orçamento para realizar a implementação para você.
	</div>
</div>

<div class="pgly-wps--notification pgly-wps-is-warning">
	⚠ Se o seu banco não liberar o seu acesso à API do Pix, você
	não poderá emitir códigos dinâmicos e atualizar automaticamente
	o pedido. <strong>Consulte o seu gerente sobre a disponibilidade</strong>.
</div>