<?php
/**
 * Single template for Pokemon using Twig.
 *
 * This template gathers single Pokemon data and renders it through View layer.
 */

use PokeTest\Core\View;

get_header();

while (have_posts()) : the_post();

    $pokemon_data = [
        'title' => get_the_title(),
        'content' => get_the_content(),
        'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
        'weight' => get_post_meta(get_the_ID(), '_pokemon_weight', true),
        'old_pokedex' => get_post_meta(get_the_ID(), '_pokemon_pokedex_old', true),
        'latest_pokedex' => get_post_meta(get_the_ID(), '_pokemon_pokedex_latest', true),
        'types' => wp_list_pluck(get_the_terms(get_the_ID(), 'pokemon_type') ?: [], 'name'),
        'moves' => get_post_meta(get_the_ID(), '_pokemon_moves', true) ?: [],
        'id' => get_the_ID()
    ];

    $view = new View(get_template_directory() . '/views');
    $view->render('single-pokemon.twig', $pokemon_data);

endwhile;

get_footer();