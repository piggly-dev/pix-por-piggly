<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Configurações de Automação do Pix</h1>

<div class="wpgly-wrapper">
<?php if ( empty($this->bank) ) : ?>
	<div class="wpgly-notice-box wpgly-error">
		Antes de iniciar a automação do Pix, acesse
		<a href="<?=$baseUrl.'&screen=pix'?>">Dados do Pix</a>
		e selecione o banco emissor da sua Conta Pix.
	</div>
<?php else : ?>
	<div class="wpgly-notice-box wpgly-warning">
		Infelizmente, nosso plugin ainda não implementa a API Pix
		para o seu Banco Emissor. Caso, você já tenha acesso a API Pix, 
		entre em contato via e-mail <a href="mailto:dev@piggly.com.br">
		dev@piggly.com.br</a> com o assunto <strong>API Pix</strong>,
		para que possamos implementar a API no plugin.
	</div>
<?php endif; ?>
</div>