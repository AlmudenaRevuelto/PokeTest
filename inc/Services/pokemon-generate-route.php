<?php
defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/Services/pokemon-api-service.php';

/**
 * Rewrite: /generate
 */
add_action('init', function () {
    add_rewrite_rule(
        '^generate/?$',
        'index.php?generate_pokemon=1',
        'top'
    );
});

/**
 * Query var
 */
add_filter('query_vars', function ($vars) {
    $vars[] = 'generate_pokemon';
    return $vars;
});

/**
 * Generate a random Pokemon and store it as a WordPress post.
 */
add_action('template_redirect', function () {

    if (!get_query_var('generate_pokemon')) return;

    // Restrict generation to users who can edit posts.
    if (!current_user_can('edit_posts')) {
        wp_die('You do not have permission to generate Pokemon.');
    }

    $api = new PokeApiService();

    // Pick a random Pokemon from the original Kanto range.
    $random_id = rand(1, 151);
    $pokemon = $api->getPokemon($random_id);

    if (!$pokemon) {
        wp_die('Error fetching Pokemon from PokeAPI.');
    }

    $name = ucfirst($pokemon['name']);

    // Avoid creating duplicates when a Pokemon already exists.
    $existing = get_posts([
        'post_type' => 'pokemon',
        'title' => $name,
        'posts_per_page' => 1
    ]);

    if ($existing) {
        wp_redirect(get_permalink($existing[0]->ID));
        exit;
    }

    // Pull species flavor text so generated posts have a real Pokédex description.
    $description = $api->getPokemonDescription($pokemon['id'] ?? $random_id);

    // Create the Pokemon post.
    $post_id = wp_insert_post([
        'post_title'   => $name,
        'post_type'    => 'pokemon',
        'post_status'  => 'publish',
        'post_content' => $description ?: 'Generated from PokéAPI'
    ]);

    if (!$post_id) {
        wp_die('Error creating the Pokemon post.');
    }

    // Save core metadata.
    update_post_meta($post_id, '_pokemon_weight', $pokemon['weight']);
    update_post_meta($post_id, '_pokemon_pokedex_latest', $pokemon['id']);

    // Assign Pokemon types taxonomy terms.
    $types = array_map(function ($t) {
        return $t['type']['name'];
    }, $pokemon['types']);

    wp_set_object_terms($post_id, $types, 'pokemon_type');

    // Store a subset of moves with descriptions fetched from PokeAPI.
    $moves = array_slice($pokemon['moves'], 0, 5);

    $formatted_moves = [];

    foreach ($moves as $move) {
        $move_name = $move['move']['name'];
        $move_url  = $move['move']['url'];

        $formatted_moves[] = [
            'name' => $move_name,
            'description' => $api->getMoveEffect($move_url)
        ];
    }

    update_post_meta($post_id, '_pokemon_moves', $formatted_moves);

    // Download and set the official artwork as featured image.
    $image_url = $pokemon['sprites']['other']['official-artwork']['front_default'];

    if ($image_url) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_sideload_image($image_url, $post_id, null, 'id');

        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    // Redirect to the newly created post.
    wp_redirect(get_permalink($post_id));
    exit;
});