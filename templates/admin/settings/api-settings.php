<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<h1 class="wpgly-title">Configurações de Automação do Pix</h1>

<div class="wpgly-wrapper">
<?php if ( empty($this->bank) ) : ?>
	<div class="wpgly-notice-box wpgly-error">
		Antes de iniciar a automação do Pix, acesse
		<a href="<?=$baseUrl.'&screen=pix'?>">Dados do Pix</a>
		e selecione o banco emissor da sua Conta Pix.
	</div>
<?php elseif ( empty($this->api) ) : ?>
	<div class="wpgly-notice-box wpgly-warning">
		Infelizmente, nosso plugin ainda não implementa a API Pix
		para o seu Banco Emissor. Caso, você já tenha acesso a API Pix
		e queira colaborar, entre em contato via e-mail <a href="mailto:dev@piggly.com.br">
		dev@piggly.com.br</a> com o assunto <strong>API Pix</strong>,
		para que possamos implementar a API, sem custos, no plugin.
	</div>
	
	<h3 class="wpgly-title">Regras de Implementação 👇</h3>

	<p>
		Acreditamos na democratização do Pix e nosso propósito é tornar o plugin 
		<strong>Pix por Piggly</strong> o melhor do repositório gratuito do Wordpress. 
		Mas, para isso acontecer precisamos de você.
	</p>
	<p>
		Por essa razão, não cobraremos por qualquer implementação da API Pix no plugin,
		desde que, ao entrar em contato, solicitando a implementação, você esteja ciente que:
	</p>
	<ul>
		<li>
			O prazo de desenvolvimento irá variar conforme a disponibilidade do nosso 
			time de desenvolvimento;
		</li>
		<li>
			A implementação da nova API Pix não é exclusividade sua, ela será disponibilizada
			para todas as pessoas com o plugin na atualização subsequente a implementação;
		</li>
		<li>
			Você pode ter prioridade de implementação, isto é.
		</li>
	</ul>
<?php endif; ?>
</div>