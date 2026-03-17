<?php
/**
 * Pokemon API Routes
 *
 * Registers the virtual URL structure and template routing for Pokemon detail
 * pages loaded live from PokeAPI (no WordPress post required).
 *
 * URL pattern: /pokemon-api/{name}/  (pretty permalinks)
 *              /?api_pokemon={name}  (fallback without rewrite)
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the rewrite rule and tag so WordPress recognises /pokemon-api/{name}/.
 * Flushing permalinks once in WP Admin > Settings > Permalinks activates this rule.
 */
add_action( 'init', function () {
	add_rewrite_tag( '%api_pokemon%', '([^&]+)' );
	add_rewrite_rule( '^pokemon-api/([^/]+)/?$', 'index.php?api_pokemon=$matches[1]', 'top' );
} );

/**
 * Expose the api_pokemon query var to WP_Query so get_query_var() can read it.
 */
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'api_pokemon';
	return $vars;
} );

/**
 * When api_pokemon is set, bypass normal template resolution and load the
 * dedicated PokeAPI detail template instead.
 */
add_filter( 'template_include', function ( $template ) {
	if ( get_query_var( 'api_pokemon' ) ) {
		return get_template_directory() . '/single-pokemon-api.php';
	}

	return $template;
} );
