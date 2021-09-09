<?php

use Piggly\WooPixGateway\CoreConnector;

if ( ! defined( 'ABSPATH' ) ) { exit; }

$plugin_page = admin_url('admin.php?page='.CoreConnector::domain());
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.92 26.92" style="width: 24px; height: 24px"><path d="M23.35,23.39a3.93,3.93,0,0,1-2.8-1.16l-4-4a.75.75,0,0,0-1.06,0L11.4,22.25a3.94,3.94,0,0,1-2.79,1.16h-.8l5.12,5.11a4.08,4.08,0,0,0,5.78,0l5.13-5.13Z" transform="translate(-2.36 -2.8)"/><path d="M8.61,9.11a3.9,3.9,0,0,1,2.79,1.16l4.06,4.05a.75.75,0,0,0,1.06,0l4-4a4,4,0,0,1,2.8-1.15h.49L18.71,4a4.08,4.08,0,0,0-5.78,0L7.81,9.11Z" transform="translate(-2.36 -2.8)"/><path d="M28.08,13.37,25,10.27a.54.54,0,0,1-.22,0H23.35a2.82,2.82,0,0,0-2,.81l-4,4a1.94,1.94,0,0,1-1.37.57,1.91,1.91,0,0,1-1.37-.57l-4.06-4.05a2.74,2.74,0,0,0-2-.81H6.88a.65.65,0,0,1-.21,0L3.56,13.37a4.08,4.08,0,0,0,0,5.78l3.11,3.11a.65.65,0,0,1,.21,0H8.61a2.78,2.78,0,0,0,2-.81l4.06-4.05a2,2,0,0,1,2.74,0l4,4a2.78,2.78,0,0,0,2,.81h1.41a.54.54,0,0,1,.22.05l3.1-3.1a4.1,4.1,0,0,0,0-5.78" transform="translate(-2.36 -2.8)"/></svg>
<h1 class="pgly-wps--title pgly-wps-is-6">
	Pix por Piggly
</h1>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-5">Suporte</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		O Pix ainda é muito recente e, além das padronizações do Banco Central do Brasil, 
		muitos bancos criaram algumas variações e definiram os padrões de leituras das chaves. 
	</div>
	<div class="pgly-wps--column">
		A nossa recomendação principal é: <mark><em>utilize as chaves aleatórias</em></mark>. Assim,
		você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.
	</div>
	<div class="pgly-wps--column">
		Se você está enfrentando algum problema, siga as recomendações abaixo 👇
	</div>
</div>

<div class="pgly-wps--notification pgly-wps-is-warning">
	Antes de continuar é importante habilitar o <strong>Modo Debug</strong>
	nas <a href="<?=$plugin_page?>">Configurações do Plugin</a>. Esse modo registrará
	todos os eventos e erros gerados pelo plugin nos arquivos de log localizados
	no menu <a href="<?=$plugin_page?>-logs">Logs</a>.
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-7">O plugin apresenta erro e não gera o QR Code ou o Código Pix 👇</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Ative o Modo Debug, reproduza o erro e acesse os <a href="<?=$plugin_page?>-logs">Logs</a>
		do plugin. Depois disso, compartilhe as últimas linhas do log que estejam marcadas com 
		<strong>ERROR</strong> no <a href="https://wordpress.org/support/plugin/pix-por-piggly/">fórum oficial do plugin</a>.
		Caso não encontre nenhum erro, provavelmente o erro esta relacionado
		ao Wordpress/Woocommerce. Verifique nos logs de erros de ambos.
	</div>
	<div class="pgly-wps--column">
		A comunidade poderá ajudá-lo e conforme disponibilidade responderemos 
		também. Não esqueça de verificar a seção <mark>O que enviar ao entrar em
		contato com o Suporte</mark>, do contrário não poderemos ajudá-lo com
		eficiência.
	</div>
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-7">O plugin gera o QR Code, mas alguns clientes não conseguem pagá-lo 👇</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Se o plugin está gerando o Pix para pagamento, não há problemas
		com o plugin. Entretanto, como há irregularidades de leituras
		entre bancos você deve verificar atentamente os dados que foram
		preenchidos por você nas <a href="<?=$plugin_page?>">Configurações do Plugin</a>
		em <strong>Conta Pix</strong>.
	</div>
</div>

<div class="pgly-wps--notification pgly-wps-is-warning">
	O <strong>Nome da Loja</strong>, <strong>Nome do Titular</strong>
	e a <strong>Cidade do Titular</strong> devem possuir menos de 
	<code>25</code> caracteres e não devem incluir acentos ou qualquer
	outro caractere especial. Certifique-se que esse seja o caso.
</div>

<div class="pgly-wps--notification pgly-wps-is-warning">
	Alguns bancos, como é o caso do <strong>Itaú</strong>,
	exigem que você entre em contato com o gerente para liberar
	a geração de QR Codes <strong>estáticos</strong> fora do 
	aplicativo do banco. Se não tiver liberado, isso impedirá
	o pagamento. Certifique-se que esse seja o caso.
</div>

<div class="pgly-wps--notification pgly-wps-is-warning">
	Tenha certeza que os dados da sua Conta Pix estão preenchidos
	corretamente, se mesmo assim não funcionar recomendamos que
	utilize a ferramenta <strong>Importador Pix</strong> nas
	<a href="<?=$plugin_page?>">Configurações do Plugin</a> para
	extrair as informações de um código Pix válido criado
	pelo seu banco.
</div>

<div class="pgly-wps--space"></div>
<h2 class="pgly-wps--title pgly-wps-is-7">Ainda está com dificuldades 👇</h2>

<div class="pgly-wps--row">
	<div class="pgly-wps--column">
		Entre em contato com o e-mail <strong>
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a></strong>.
		O suporte via e-mail não tem custos, mas pode demorar um pouco
		para ser resolvido.
	</div>
</div>