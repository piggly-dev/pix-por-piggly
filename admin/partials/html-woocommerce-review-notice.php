<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Notice: Request Review.
 *
 * @package    WC_Piggly_Pix
 * @subpackage WC_Piggly_Pix/admin/partials
 * @author     Caique <caique@piggly.com.br>
 */

$createdAt = stat(WC_PIGGLY_PIX_PLUGIN_PATH)['ctime'];
?>
<div class="notice notice-info">
	<p>
		Olá! Notamos que você já tem utilizado nosso plugin 
		<strong>Pix por Piggly</strong> há algum tempo. Isso é incrível!
		Esperamos que você esteja apreciando os recursos que disponibilizamos
		para você. <strong>Lembre-se:</strong> Nosso suporte está sempre aberto!
		Agora, você pode nos fazer um pequeno favor e avaliar sua experiência com
		nosso plugin no Wordpress? Nos ajude a alcançar mais pessoas e a nos motivarmos
		para mais atualizações!
	</p>
	<p>~ Equipe Piggly</p>
	<ul>
		<li><a href="https://wordpress.org/plugins/pix-por-piggly/#reviews">Ok, vocês merecem uma avaliação</a></li>
		<li><a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&review_stats=2' );?>">Quem sabe depois...</a></li>
		<li><a href="<?=admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_piggly_pix_gateway&review_stats=1' );?>">Eu já avaliei :)</a></li>
	</ul>
</div>