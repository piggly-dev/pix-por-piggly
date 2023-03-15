<?php

use Symfony\Component\Finder\Finder as Finder;

function pgly_phpscoper_get_list_of_files ( $path )
{
	$files = [];
	$dir = new RecursiveDirectoryIterator(__DIR__.'/'.$path);
	
	/** @var RecursiveDirectoryIterator $ite */
	$ite = new RecursiveIteratorIterator($dir);

	while ( $ite->valid() ) 
	{
		if ( $ite->isDot() || $ite->isDir() ) 
		{ $ite->next(); continue; }

		$files[] = $ite->getPathname();

		$ite->next();
	}

	return $files;
}

$config = [
	'prefix' => 'Piggly\WooPixGateway\Vendor',
	'whitelist-global-constants' => false,
	'whitelist-global-classes'   => false,
	'whitelist-global-functions' => false,
	// see: https://github.com/humbug/php-scoper#whitelist.
	'whitelist' => [],
	// see: https://github.com/humbug/php-scoper#finders-and-paths.
	'finders' => [],
	// see: https://github.com/humbug/php-scoper#patchers.
	'patchers' => [],
];


$_NAME = [
	'*.php', 
	'LICENSE', 
	'composer.json'
];

$_EXCLUDE = [
	'vendor',
	'examples',
	'samples',
	'test',
	'tests',
	'Test',
	'Tests',
	'public',
	'.github'
];

$_LIBS = [
	'vendor/chillerlan/php-qrcode' => $_NAME, 
	'vendor/chillerlan/php-settings-container' => $_NAME,
	'vendor/monolog/monolog' => $_NAME,
	'vendor/piggly/php-pix' => $_NAME,
	'vendor/piggly/wordpress-starter-kit' => $_NAME,
	'vendor/psr/log' => $_NAME
];

// Finders
foreach ( $_LIBS as $_lib => $_name )
{ $config['finders'][] = Finder::create()->files()->ignoreVCS(true)->exclude($_EXCLUDE)->in($_lib)->name($_name); }

// Patches

// Monolog
$config['patchers'][] = function ( $file_path, $prefix, $content ) {
	if (
		strpos( $file_path, 'monolog/monolog/src/Monolog/Handler/PHPConsoleHandler.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Processor/IntrospectionProcessor.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Handler/BrowserConsoleHandler.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Handler/FilterHandler.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Handler/FingersCrossed/ChannelLevelActivationStrategy.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Utils.php' ) !== false ||
		strpos( $file_path, 'monolog/monolog/src/Monolog/Handler/TestHandler.php' ) !== false
	) 
	{
		return str_replace(
			'Monolog\\\\',
			sprintf( '%s\\\\Monolog\\\\', addslashes( $prefix ) ),
			$content
		);
	}

	return $content;
};

// Chillerlan
$config['patchers'][] = function ( $file_path, $prefix, $content ) {
	if ( strpos( $file_path, 'chillerlan/php-qrcode/public/qrcode.php' ) !== false ) 
	{
		return str_replace(
			'chillerlan\\\\',
			sprintf( '%s\\\\chillerlan\\\\', addslashes( $prefix ) ),
			$content
		);
	}

	return $content;
};

// Wordpress Starter Kit
$config['patchers'][] = function ( $file_path, $prefix, $content ) {
	if ( strpos( $file_path, 'piggly/wordpress-starter-kit' ) !== false ) 
	{
		$content = str_replace("\\$prefix\\wp_send_json_success", '\\wp_send_json_success', $content);
		$content = str_replace("\\$prefix\\wp_send_json_error", '\\wp_send_json_error', $content);
		$content = str_replace("\\$prefix\\_n", '\\_n', $content);
		$content = str_replace("\\$prefix\\_e", '\\_e', $content);
		$content = str_replace("\\$prefix\\__", '\\__', $content);
		$content = str_replace("\\$prefix\\load_plugin_textdomain", '\\load_plugin_textdomain', $content);
		$content = str_replace("\\$prefix\\add_action", '\\add_action', $content);
		$content = str_replace("\\$prefix\\add_filter", '\\add_filter', $content);
		$content = str_replace("\\$prefix\\wp_doing_ajax", '\\wp_doing_ajax', $content);
		$content = str_replace("\\$prefix\\is_admin", '\\is_admin', $content);
		$content = str_replace("\\$prefix\\is_user_logged_in", '\\is_user_logged_in', $content);
		$content = str_replace("\\$prefix\\get_transient", '\\get_transient', $content);
		$content = str_replace("\\$prefix\\set_transient", '\\set_transient', $content);
		$content = str_replace("\\$prefix\\delete_transient", '\\delete_transient', $content);
		$content = str_replace("\\$prefix\\wp_kses_post", '\\wp_kses_post', $content);
		$content = str_replace("\\$prefix\\esc_attr", '\\esc_attr', $content);
		$content = str_replace("\\$prefix\\get_option", '\\get_option', $content);
		$content = str_replace("\\$prefix\\delete_option", '\\delete_option', $content);
		$content = str_replace("\\$prefix\\update_option", '\\update_option', $content);
		$content = str_replace("\\$prefix\\register_activation_hook", '\\register_activation_hook', $content);
		$content = str_replace("\\$prefix\\register_deactivation_hook", '\\register_deactivation_hook', $content);
		$content = str_replace("\\$prefix\\check_ajax_referer", '\\check_ajax_referer', $content);
		$content = str_replace("\\$prefix\\current_user_can", '\\current_user_can', $content);
		$content = str_replace("\\$prefix\\wp_verify_nonce", '\\wp_verify_nonce', $content);
		$content = str_replace("\\$prefix\\wp_create_nonce", '\\wp_create_nonce', $content);
		$content = str_replace("\\$prefix\\add_query_arg", '\\add_query_arg', $content);
		$content = str_replace("\\$prefix\\admin_url", '\\admin_url', $content);
		$content = str_replace("\\$prefix\\wp_timezone", '\\wp_timezone', $content);
		$content = str_replace("\\$prefix\\esc_html", '\\esc_html', $content);
		$content = str_replace("\\$prefix\\esc_url", '\\esc_url', $content);
		$content = str_replace("\\$prefix\\esc_attr", '\\esc_attr', $content);
		$content = str_replace("\\$prefix\\ARRAY_A", '\\ARRAY_A', $content);
		$content = str_replace("\\$prefix\\ABSPATH", '\\ABSPATH', $content);
		$content = str_replace("\\$prefix\\wp_enqueue_media", '\\wp_enqueue_media', $content);
		$content = str_replace("\\$prefix\\wp_enqueue_script", '\\wp_enqueue_script', $content);
		$content = str_replace("\\$prefix\\wp_enqueue_style", '\\wp_enqueue_style', $content);
		$content = str_replace("\\$prefix\\wp_localize_script", '\\wp_localize_script', $content);
		$content = str_replace("\\$prefix\\deactivate_plugins", '\\deactivate_plugins', $content);
		$content = str_replace("\\$prefix\\plugin_basename", '\\plugin_basename', $content);
		$content = str_replace("Piggly\\WooPixGateway\\Vendor\\WP_List_Table", 'WP_List_Table', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\dbDelta", 'dbDelta', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\WP_DEBUG", 'WP_DEBUG', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\ABSPATH", 'ABSPATH', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\SCRIPT_DEBUG", 'SCRIPT_DEBUG', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\wp_doing_ajax", 'wp_doing_ajax', $content);
		$content = str_replace("Piggly\\\\WooPixGateway\\\\Vendor\\\\DOING_AJAX", 'DOING_AJAX', $content);
		return $content;
	}

	return $content;
};

return $config;