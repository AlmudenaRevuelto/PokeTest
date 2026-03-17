<?php
/**
 * Single template for Pokemon using Twig.
 *
 * This template gathers single Pokemon data and renders it through View layer.
 */

use PokeTest\Core\View;

get_header();

while (have_posts()) : the_post();

    // Build the data array passed to the Twig template as context variables.
    $pokemon_data = [
        'title'           => get_the_title(),
        'content'         => get_the_content(),
        // Thumbnail at 'medium' size; false if no featured image is set.
        'thumbnail'       => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
        // Custom meta fields stored by the Pokemon post type meta boxes.
        'weight'          => get_post_meta(get_the_ID(), '_pokemon_weight', true),
        'old_pokedex'     => get_post_meta(get_the_ID(), '_pokemon_pokedex_old', true),
        'latest_pokedex'  => get_post_meta(get_the_ID(), '_pokemon_pokedex_latest', true),
        // Taxonomy terms as a flat list of names (e.g. ['Fire', 'Flying']).
        'types'           => wp_list_pluck(get_the_terms(get_the_ID(), 'pokemon_type') ?: [], 'name'),
        // Moves are stored as a serialized array of ['name' => ..., 'description' => ...] entries.
        'moves'           => get_post_meta(get_the_ID(), '_pokemon_moves', true) ?: [],
        'id'              => get_the_ID()
    ];

    // Instantiate the View layer pointing at the theme's /views directory.
    $view = new View(get_template_directory() . '/views');
    $view->render('single-pokemon.twig', $pokemon_data);

endwhile;

get_footer();