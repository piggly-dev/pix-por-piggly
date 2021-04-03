<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1>Problemas com o Pix?</h1>

<div style="max-width:720px; display: table">
	<p>
		O Pix ainda é muito recente e, além das padronizações do Banco Central do Brasil, 
		muitos bancos criaram algumas variações e definiram os padrões de leituras das chaves. 
		A nossa recomendação principal é: <mark><em>utilize as chaves aleatórias</em></mark>. Assim,
		você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.
	</p>
	
	<p class="notice notice-warning is-dismissible" style="padding: 10px"><em>
		<strong>Enfrentando algum problema?</strong> Não desista do plugin! Estamos
		investindo todos os nossos esforços para democratizar o acesso ao Pix sem taxas
		para lojas Woocommerce. Abra um chamado no suporte do plugin ou envie um e-mail para 
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a> para que possamos 
		continuamente melhorar esse plugin juntos. <em>Mantenha-o sempre atualizado</em>.</em>
	</p>

	<h3>Como substituir os templates de e-mail e da página de obrigado 👇</h3>

	<p>
		Copie os templates originais, disponíveis em 
		<code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/html-woocommerce-thank-you-page.php'?></code>
		e <code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/email-woocommerce-thank-you-page.php'?></code>
		para o diretório do seu tema ativo em <code><?=get_template_directory().WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'/'?></code>.	
	</p>

	<p class="notice notice-warning" style="padding: 10px"><em>
		⚠ <strong>Tenha cuidado!</strong> Ao criar seu próprio template, você pode
		perder funções nativas do plugin. Só faça se souber o que está fazendo.
		O suporte para templates personalizados não será concedido.
	</p>

	<h3>O plugin apresenta erro e não gera o QR Code ou o Código Pix  👇</h3>

	<p>
		Primeiro, anote a mensagem de erro que aparece na sua tela do Wordpress.
		Essa mensagem é importante. Depois vá em <a href="">Logs</a> e copie as últimas
		mensagens de erro.
	</p>

	<p>
		Depois, compartilhe essas informações na página de 
		<a href="https://wordpress.org/support/plugin/pix-por-piggly/">Suporte Gratuito</a>
		do plugin. A comunidade poderá ajudá-lo e conforme disponibilidade 
		responderemos também.
	</p>

	<ul style="list-style: disc; padding: 18px;">
		<li>Versão do Wordpress;</li>
		<li>Versão do WooCommerce;</li>
		<li>Banco Emitente (Conta Pix);</li>
		<li>Banco Pagador (que está utilizando o Código Pix);</li>
		<li>Tipo de Erro;</li>
		<li>Chave Pix gerada;</li>
	</ul>

	<h3>O plugin gera o QR Code, mas não consigo pagá-lo 👇</h3>

	<p>
		Caso o plugin esteja gerando o QR Code, não há um erro no plugin.
		Mas, talvez, em seus dados. Por essa razão, faça as seguintes verificações:
	</p>

	<ul style="list-style: disc; padding: 18px;">
		<?php if ( strlen($data->merchant_name) >= 25 ) : ?>
		<li>
			O <strong>Nome do Titular</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[0-9]/',$data->merchant_name) ) : ?>
		<li>
			O <strong>Nome do Titular</strong> contem números, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Nome do Titular contenha números.
		</li>
		<?php endif; ?>
		<li>
			Se você é cliente do <strong>Banco Itaú</strong>, você deve entrar em contato
			com o gerente para solicitar que o banco libere a geração de códigos QR Codes
			Estáticos fora do aplicativo do banco.
		</li>
		<li>
			Certifique-se de estar preenchendo os dados do Pix corretamente, caso tenha
			dúvidas recomendamos que utilize a ferramento <a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=import' );?>">Importar Pix</a>
			para importar um código Pix válido.
		</li>
	</ul>

	<h3>Necessita de um suporte dedicado? 👇</h3>
	
	<p>
		Abra um chamado enviando um e-mail para
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a>. 
		<mark>Em breve, esse suporte será concedido apenas para quem tiver adquirido a licença do plugin</mark>.
	</p>
</div>