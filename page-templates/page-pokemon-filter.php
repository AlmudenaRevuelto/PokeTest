<?php
/**
 * Template Name: Pokémon Filter
 *
 * Page template to show a grid of Pokémon with filter and pagination.
 */

use PokeTest\Core\View;

get_header();

// --- Query all Pokémon posts ---
// Fetches every pokemon post in alphabetical order. Pagination is handled
// client-side in JS, so we retrieve all records here in a single query.
$all_pokemon = get_posts([
    'post_type'      => 'pokemon',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

$pokemon_data = [];

foreach ($all_pokemon as $post) {
    $pokemon_data[] = [
        'title'     => get_the_title($post),
        'thumbnail' => get_the_post_thumbnail_url($post, 'medium'),
        // Types are collected as slugs (not names) because the JS filter
        // compares against `data-types` which also uses slugs, and the
        // SCSS `[data-type="..."]` selectors are slug-based too.
        'types'     => wp_list_pluck(get_the_terms($post, 'pokemon_type') ?: [], 'slug'),
        'id'        => $post->ID,
    ];
}

// --- Render Twig ---
$view = new View(get_template_directory() . '/views');
$view->render('page-pokemon-filter.twig', ['pokemon' => $pokemon_data]);
