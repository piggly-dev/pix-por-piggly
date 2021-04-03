<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<style>
	#mainform p.submit { display: none; }
	#mainform p.force-submit { display: block !important; }
	.piggly-label { font-size: 14px; font-weight: bold; margin-bottom: 4px; display: block; }
	.piggly-checkbox { display: table; margin: 12px 0; }
	.piggly-featured { background-color: #00bcd4; color: #000; font-size: 12px; }
</style>
<?php

use Piggly\Pix\Payload;

$baseUrl    = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway' );
$actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$items = [
	'news' => ['label'=>'Novidades','url'=>$baseUrl.'&screen=news'],
	'main' => ['label'=>'Configurações Gerais','url'=>$baseUrl],
	'pix' => ['label'=>'Dados do Pix','url'=>$baseUrl.'&screen=pix'],
	'import' => ['label'=>'Importar Pix','url'=>$baseUrl.'&screen=import'],
	'shortcode' => ['label'=>'Shortcode','url'=>$baseUrl.'&screen=shortcode'],
	'orders' => ['label'=>'Pedidos & E-mails','url'=>$baseUrl.'&screen=orders'],
	'receipt' => ['label'=>'Comprovante Pix','url'=>$baseUrl.'&screen=receipt'],
	'testing' => ['label'=>'Teste o seu Pix','url'=>$baseUrl.'&screen=testing'],
	'faq' => ['label'=>'Perguntas Frequentes','url'=>$baseUrl.'&screen=faq'],
	'support' => ['label'=>'Suporte','url'=>$baseUrl.'&screen=support']
];

$i = 0;

echo '<ul class="subsubsub">';

foreach ( $items as $key => $item )
{ 
	$i++;

	$current = $key === $screen ? 'class="current"' : '';
	$after   = $i !== count($items) ? ' | ' : '';

	echo sprintf('<li><a href="%s" %s>%s</a>%s</li>', $item['url'], $current, $item['label'], $after); 
}

echo '</ul>';
echo '<br class="clean"/>';
?>

<?php if ( ((float)phpversion('Core') < 7.2) ) : ?>
<div class="error">
	<p><strong>O plugin exige a versão do PHP 7.2 ou acima</strong>. Contate seu servidor de hospedagem para atualizar.</p>
</div>
<?php endif; ?>

<?php if ( !Payload::supportQrCode() ) : ?>
<div class="error">
	<p>Você precisa da extensão <strong>GD</strong> do PHP para gerar os <strong>QR Codes</strong>. Instale e habilite a extensão no seu servidor web.</p>
</div>
<?php endif; ?>