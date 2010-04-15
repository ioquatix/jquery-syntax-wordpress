<?php
/*
Plugin Name: jQuery.Syntax
Plugin URI: http://www.oriontransfer.co.nz/software/jquery-syntax
Description: Syntax highlighting using <a href="http://www.oriontransfer.co.nz/software/jquery-syntax">jQuery.Syntax</a> supporting a wide range of popular languages.  Wrap code blocks with <code>&lt;pre class="syntax brush-LANGUAGE"&gt;</code> and <code>&lt;/pre&gt;</code> where <code>LANGUAGE</code> is a jQuery.Syntax supported brush.
Author: Samuel Williams
Author URI: http://www.oriontransfer.co.nz/
Version: 1.5.1
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
	$attrs = $match[1];
	
	if (preg_match("/escaped/", $attrs)) {
		$code = $match[2];
	} else {
		$code = htmlentities($match[2]);
	}

	return "<pre$attrs>$code</pre>";
}

function jq_syntax_quote($content) {
	$result = preg_replace_callback('/<pre(.*?)>(.*?)<\/pre>/imsu',jq_syntax_htmlentities, $content);
	
	return $result;
}

function jq_syntax_loaded() {
	$path = WP_PLUGIN_URL . '/jquery-syntax/';
	
	wp_deregister_script('jquery');
	wp_register_script('jquery', $path . 'jquery-1.4.1.min.js');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery.syntax', $path . 'jquery-syntax/jquery.syntax.js');
	wp_enqueue_script('jquery.syntax.cache', $path . 'jquery-syntax/jquery.syntax.cache.js');
}

function jq_syntax_header () {
	$plugin_root = plugins_url("/jquery-syntax/");
	$syntax_root = $plugin_root.'jquery-syntax/';
	
?>
	<link rel="stylesheet" href="<?php echo $plugin_root . "wp-fixes.css" ?>" type="text/css" media="screen" />
	<script type="text/javascript">
		jQuery.noConflict(); jQuery(document).ready(function($) { Syntax.root = "<?php echo $syntax_root ?>"; $.syntax({layout: 'table', replace: true}) });
	</script>
<?php
}

add_action('plugins_loaded', 'jq_syntax_loaded');
add_action('wp_head','jq_syntax_header');

add_filter('the_content', 'jq_syntax_quote', 0);
add_filter('the_excerpt', 'jq_syntax_quote', 0);
add_filter('comment_text', 'jq_syntax_quote', 0);

?>