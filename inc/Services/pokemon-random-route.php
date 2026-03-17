<?php
defined( 'ABSPATH' ) || exit;

/**
 * Rewrite: /random
 */
add_action( 'init', function () {
	add_rewrite_rule(
		'^random/?$',
		'index.php?random_pokemon=1',
		'top'
	);
});

/**
 * Query var
 */
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'random_pokemon';
	return $vars;
});

/**
 * Redirect to random Pokémon
 */
add_action( 'template_redirect', function () {

	if ( get_query_var( 'random_pokemon' ) ) {

		$pokemon = get_posts([
			'post_type'      => 'pokemon',
			'posts_per_page' => 1,
			'orderby'        => 'rand',
			'post_status'    => 'publish'
		]);

		if ( ! empty( $pokemon ) ) {
			wp_redirect( get_permalink( $pokemon[0]->ID ) );
			exit;
		}
	}
});