<?php
/*
Plugin Name: jQuery.Syntax
Plugin URI: http://www.oriontransfer.co.nz/software/jquery-syntax
Description: Syntax highlighting using <a href="http://www.oriontransfer.co.nz/software/jquery-syntax">jQuery.Syntax</a> supporting a wide range of popular languages.  Wrap code blocks with <code>&lt;pre class="syntax {language}"&gt;</code> and <code>&lt;/pre&gt;</code> where <code>{language}</code> is a jQuery.Syntax supported brush. You can also use <code>&lt;code&gt;</code> tags for inline highlighting.
Author: Samuel Williams
Author URI: http://www.oriontransfer.co.nz/
Version: 2.3
*/

# 
#	This file is part of the "jQuery.Syntax" project, and is licensed under the GNU AGPLv3.
#
#	See <jquery.syntax.js> for licensing details.
#
#	Copyright 2010 Samuel Williams. All rights reserved.
#

if (!defined("WP_CONTENT_URL")) define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
if (!defined("WP_PLUGIN_URL"))  define("WP_PLUGIN_URL",  WP_CONTENT_URL        . "/plugins");

function jq_syntax_htmlentities ($match) {
	// We only modify those which are syntax highlighted.
	if (!preg_match("/syntax/", $match[0]))
		return $match[0];
	
	$attrs = $match[2];
	
	if (preg_match("/escaped/", $attrs)) {
		$code = $match[3];
	} else {
		$code = htmlentities($match[3]);
	}

	$tag = $match[1];

	return "<$tag$attrs>$code</$tag>";
}

function jq_syntax_quote($content) {
	$content = preg_replace_callback('/<(pre)(.*?)>(.*?)<\/pre>/imsu',jq_syntax_htmlentities, $content);
	$content = preg_replace_callback('/<(code)(.*?)>(.*?)<\/code>/imsu',jq_syntax_htmlentities, $content);
	
	return $content;
}

function jq_syntax_loaded() {
	$path = WP_PLUGIN_URL . '/jquery-syntax/';
	$wp_version = get_bloginfo('version');
	
	// Version 3.0-beta1 has jQuery 1.4.2 :)
	if (version_compare($wp_version, '3', '<')) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', $path . 'jquery-1.4.2.min.js');
	}
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery.syntax', $path . 'jquery-syntax/jquery.syntax.min.js');
}

function jq_syntax_header () {
	$plugin_root = plugins_url("/jquery-syntax/");
	$syntax_root = $plugin_root.'jquery-syntax/';
?>
	<link rel="stylesheet" href="<?php echo $plugin_root . "wp-fixes.css" ?>" type="text/css" media="screen" />
	<script type="text/javascript">
		jQuery.noConflict(); jQuery(document).ready(function($) { $.syntax({root: '<?php echo $syntax_root ?>'}) });
	</script>
<?php
}

add_action('plugins_loaded', 'jq_syntax_loaded');
add_action('wp_head','jq_syntax_header');

add_filter('the_content', 'jq_syntax_quote', 0);
add_filter('the_excerpt', 'jq_syntax_quote', 0);
add_filter('comment_text', 'jq_syntax_quote', 0);

?>