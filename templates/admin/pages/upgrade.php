<?php

use Piggly\WooPixGateway\CoreConnector;

if ( ! defined( 'ABSPATH' ) ) { exit; } 
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>

<div class="pgly-wps--notification pgly-wps-is-warning">
	Para operação correta dos links de pagamento e envio
	de comprovante, lembre-se de <strong>atualizar
	os Links Permanentes</strong> do Wordpress. Não esqueça
	de limpar o cachê.
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Versão <?=CoreConnector::plugin()->getVersion();?></h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Na versão <strong>2.0.0</strong> promovemos várias mudanças
		no formato no plugin, tanto para facilitar quando para deixar
		o fluxo de pagamento mais simples e dinâmico. Algumas opções
		foram removidas, enquanto outras foram mantidas. Leia abaixo
		em detalhes tudo que está diferente.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">E-mails</h2>

<h3 class="pgly-wps--title pgly-wps-is-6">👎 Antes</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Era possível escolher o modelo de e-mail na qual
		o pagamento Pix seria anexado e, ainda, escolher
		a posição deste pagamento.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">❌ Por que mudamos?</h3>
<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Muitos relatavam conflitos e dificuldades para gerenciar
		o conteúdo do e-mail, enquanto outros utilizavam plugins
		desatualizados que quebravam os e-mails. Isso acontecia,
		pois dependiamos de uma <code>action</code> localizada
		no modelo de e-mail selecionado para carregar os dados
		do Pix.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">👍 Agora</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Criamos diversos modelos de e-mails, entre eles: quando
		o Pix estiver próximo de expirar, quando o Pix expirar,
		quando o Pix for pago e quando o Pix for criado para pagamento.
	</div>
	<div class="pgly-wps--column">
		👉 Não anexamos mais as informações do Pix no e-mail para
		evitar <strong>SPAM</strong> e compartilhamento desnecessário
		dos dados. Criamos um link único para o cliente acessar e
		visualizar todos os dados de pagamento novamente.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Comprovantes</h2>

<h3 class="pgly-wps--title pgly-wps-is-6">👎 Antes</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Era possível selecionar uma página para enviar
		o comprovante e utilizar qualquer formulário desejado.
		Também era possível utilizar o shortcode <code>[pix-por-piggly-form]</code>
		para utilizar o recurso nativo do plugin para recebimento de
		comprovantes.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">❌ Por que mudamos?</h3>
<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Alguns clientes enviavam de forma errada ou a forma como
		o shortcode <code>[pix-por-piggly-form]</code> era utilizado
		prejudicava a experiência criando diversos comprovantes
		desnecessários e produzindo muito lixo na pasta de uploads.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">👍 Agora</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Será utilizado um link permanente exclusivo para
		que o usuário faça o envio do comprovante Pix, garantindo
		todas as validações necessárias para que o usuário
		envie sempre para o pedido correto.
	</div>
	<div class="pgly-wps--column">
		👉 O comprovante enviado
		será automaticamente associado ao Pix relacionado ao pedido
		e sempre será considerado o último comprovante enviado.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Pedidos</h2>

<h3 class="pgly-wps--title pgly-wps-is-6">👎 Antes</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Ao selecionar o Pix, o pedido automaticamente
		migrava o status para <code>Aguardando o Pagamento</code>,
		também era possível utilizar o status <code>Comprovante Pix Recebido</code>
		quando o comprovante era enviado.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">❌ Por que mudamos?</h3>
<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Alguns usuários acharam o status <code>Comprovante Pix Recebido</code>
		muito complicado e tinham rotinas que impediam o uso.
	</div>
	<div class="pgly-wps--column">
		Migrar para o status <code>Aguardando o Pagamento</code> também
		não é mais uma opção, uma vez que os Pix podem ser confirmados
		tanto por API quanto por comprovantes.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">👍 Agora</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Agora, por padrõa, o pedido ficará como <code>Pendente</code> 
		atéq ue o cliente envie o comprovante ou que uma API Pix
		atualize o Pix como pago.
	</div>
	<div class="pgly-wps--column">
		👉 Quando o cliente enviar um comprovante, o status é migrado
		para sair da situação como <code>Pendente</code>.
	</div>
	<div class="pgly-wps--column">
		👉 Também foi adicionado um recurso para atualizar automaticamente
		o status do pedido para <code>Pago</code> quando o Pix for pago.
	</div>
	<div class="pgly-wps--column">
		👉 Tanto o status para Comprovante Enviado quanto para
		Pedido Pago podem ser configurados. <strong>Não recomendamos
		que o Comprovante Enviado marque o pedido como pago...</strong>
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Endpoints</h2>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">👍 Agora</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Foram criados dois endpoints exclusivos dentro do
		ambiente "Minha Conta" do Woocommerce. Um para o realizar
		o pagamento pendente do Pix e outro para enviar o comprovante
		de pagamento.
	</div>
	<div class="pgly-wps--column">
		👉 Os endpoints podem ser acessados a qualquer momento
		desde que o cliente tenha autorização e eles estejam liberados
		para acesso.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Templates</h2>

<div class="pgly-wps--space"></div>
<h3 class="pgly-wps--title pgly-wps-is-6">👍 Agora</h3>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		👉 Atualizamos todos os templates, será necessário revisá-los
		para que eles funcionem corretamente caso você tenha realizado
		alguma personalização.
	</div>
</div>