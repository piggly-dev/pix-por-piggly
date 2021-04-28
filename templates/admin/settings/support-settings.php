<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; } 

$log_link  = admin_url( 'admin.php?page=wc-status&tab=logs' );
$test_link = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&screen=testing' );
?>
<h1 class="wpgly-title">Problemas com o Pix?</h1>

<div class="wpgly-wrapper">
	<p>
		O Pix ainda é muito recente e, além das padronizações do Banco Central do Brasil, 
		muitos bancos criaram algumas variações e definiram os padrões de leituras das chaves. 
		A nossa recomendação principal é: <mark><em>utilize as chaves aleatórias</em></mark>. Assim,
		você não expõe seus dados e ao mesmo tempo tem compatibilidade total de pagamentos.
	</p>
	
	<div class="wpgly-notice-box wpgly-warning">
		<strong>Enfrentando algum problema?</strong> Não desista do plugin! Estamos
		investindo todos os nossos esforços para democratizar o acesso ao Pix sem taxas
		para lojas Woocommerce. Abra um chamado no suporte do plugin ou envie um e-mail para 
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a> para que possamos 
		continuamente melhorar esse plugin juntos. <em>Mantenha-o sempre atualizado</em>.
	</div>

	<h3 class="wpgly-title">Habilite o modo de debug para investigação de erros 🐞</h3>

	<div class="wpgly-field">
		<span class="wpgly-label">Modo Debug</span>
		<label class="wpgly-label wpgly-checkbox" for="<?=$this->get_field_name('debug')?>">
			<input type="checkbox" name="<?=$this->get_field_name('debug')?>" id="<?=$this->get_field_name('debug')?>" value="1" <?=(($this->debug) ? 'checked="checked"' : '');?>> Habilitar o registro de erros, informações e avisos.
		</label>
		<p class="description">Habilite apenas quando solicitado pelo Suporte ou para investigação de erros e/ou processos.</p>
	</div>

	<p>
		Os logs de informações e erros do plugin, quando o <strong>Modo Debug</strong>
		estiver ativado, será salvo em <a href="<?=$log_link?>">Logs do Woocommerce</a>
		em um arquivo com o seguinte formato <code><?=WC_PIGGLY_PIX_PLUGIN_NAME?>-{ano}-{mes}-{dia}-{hash}.log</code>.
	</p>

	<p class="submit wpgly-submit">
		<button name="save" class="wpgly-button wpgly-action woocommerce-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>
	</p>

	<h3 class="wpgly-title">O que enviar ao entrar em contato com o Suporte 👇</h3>

	<ul>
		<li>
			✅ Se o seu Wordpress apresentou erro fatal ao gerar o código Pix, acesse
			<a href="<?=$log_link?>">Logs do Woocommerce</a>, encontre o último log
			com o nome <code>fatal-errors</code> e compartilhe conosco.
		</li>
		<li>
			✅ Se o seu Wordpress não apresentou erro, mas o plugin acusou um erro
			compartilhe a mensagem de erro apresentada ou
			habilite o Modo Debug, reproduza novamente o erro, acesse
			<a href="<?=$log_link?>">Logs do Woocommerce</a>, encontre o último log
			com o nome <code>wc-piggly-pix</code> e compartilhe conosco.
		</li>
		<li>
			✅ Se os seus clientes não conseguem efetuar pagamento do Pix,
			gere um novo <a href="<?=$test_link?>">Teste</a> e compartilhe
			conosco os dados de depuração.
		</li>
	</ul>

	<h3 class="wpgly-title">Como substituir os templates de e-mail e da página de obrigado 👇</h3>

	<p>
		Copie os templates originais, disponíveis em 
		<code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/html-woocommerce-thank-you-page.php'?></code>
		e <code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/email-woocommerce-thank-you-page.php'?></code>
		para o diretório do seu tema ativo em <code><?=get_template_directory().'/'.WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'/'?></code>.	
	</p>

	<div class="wpgly-notice-box wpgly-warning">
		⚠ <strong>Tenha cuidado!</strong> Ao criar seu próprio template, você pode
		perder funções nativas do plugin. Só faça se souber o que está fazendo.
		O suporte para templates personalizados não será concedido.
	</div>

	<h4 class="wpgly-title">Template do Shortcode de Formulário 👇</h4>

	<p>
		Copie os templates originais, disponíveis em 
		<code><?='wp-content/plugins/'.\WC_PIGGLY_PIX_DIR_NAME.'/templates/html-woocommerce-form.php'?></code>
		para o diretório do seu tema ativo em <code><?=get_template_directory().WC()->template_path().\WC_PIGGLY_PIX_DIR_NAME.'/'?></code>.	
	</p>

	<div class="wpgly-notice-box wpgly-warning">
		⚠ <strong>Tenha cuidado!</strong> Ao criar seu próprio template, você pode
		perder funções nativas do plugin. Só faça se souber o que está fazendo.
		O suporte para templates personalizados não será concedido.
	</div>

	<h3 class="wpgly-title">O plugin apresenta erro e não gera o QR Code ou o Código Pix  👇</h3>

	<p>
		Depois, compartilhe sua solicitação de suporte em
		<a href="https://wordpress.org/support/plugin/pix-por-piggly/">Suporte Gratuito</a>
		do plugin. A comunidade poderá ajudá-lo e conforme disponibilidade 
		responderemos também. Não esqueça de verificar <mark>O que enviar ao entrar em
		contato com o Suporte</mark>.
	</p>

	<h3 class="wpgly-title">O plugin gera o QR Code, mas alguns clientes não conseguem pagá-lo 👇</h3>

	<p>
		Caso o plugin esteja gerando o QR Code, não há um erro no plugin.
		Mas, talvez, em seus dados. Por essa razão, faça as seguintes verificações:
	</p>

	<ul style="list-style: disc; padding: 18px;">
		<?php if ( strlen($this->store_name) >= 25 ) : ?>
		<li>
			O <strong>Nome do Loja</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_name) ) : ?>
		<li>
			O <strong>Nome da Loja</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Nome da Loja contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( strlen($this->merchant_name) >= 25 ) : ?>
		<li>
			O <strong>Nome do Titular</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_name) ) : ?>
		<li>
			O <strong>Nome do Titular</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Nome do Titular contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( strlen($this->merchant_city) >= 25 ) : ?>
		<li>
			A <strong>Cidade do Titular</strong> possuí mais de <code>25</code> caracteres.
			Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
			por tanto, reduzir o nome.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z\s]/',$this->merchant_city) ) : ?>
		<li>
			A <strong>Cidade do Titular</strong> contem números ou caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso a Cidade do Titular contenha números ou caracteres inválidos.
		</li>
		<?php endif; ?>
		<?php if ( preg_match('/[^A-Za-z0-9\{\}]/',$this->identifier) ) : ?>
		<li>
			O <strong>Identificador</strong> contem caracteres inválidos, remova-os. Alguns bancos
			não serão capazes de ler o código caso o Identificador contenha caracteres inválidos.
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

	<h3 class="wpgly-title">Necessita de um suporte dedicado? 👇</h3>
	
	<p>
		Abra um chamado enviando um e-mail para
		<a href="mailto:dev@piggly.com.br">dev@piggly.com.br</a>. 
		<mark>Em breve, esse suporte será concedido apenas para quem tiver adquirido a licença do plugin</mark>.
	</p>
</div>