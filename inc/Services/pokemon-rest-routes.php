<?php
/**
 * Pokemon REST API Routes
 *
 * Registers REST endpoints to list and get detailed Pokemon data.
 *
 * Endpoints:
 *  GET /wp-json/poketest/v1/pokemon         → List all stored Pokemon
 *  GET /wp-json/poketest/v1/pokemon/<id|name> → Get full data of a specific Pokemon
 */

defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    // Register the endpoint that returns the full stored pokemon list.
    register_rest_route('poketest/v1', '/pokemon', [
        'methods'  => 'GET',
        'callback' => 'poketest_get_pokemon_list',
        'permission_callback' => '__return_true', // Public endpoint
    ]);

    // Register the endpoint that resolves one pokemon by ID, pokedex number, slug, or name.
    register_rest_route('poketest/v1', '/pokemon/(?P<identifier>[^/]+)', [
        'methods'  => 'GET',
        'callback' => 'poketest_get_pokemon_detail',
        'args'     => [
            'identifier' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_scalar($param) && $param !== '';
                },
            ],
        ],
        'permission_callback' => '__return_true', // Public endpoint
    ]);
});

/**
 * Callback: list all stored Pokemon
 *
 * Returns an array of Pokemon with ID as latest Pokedex number and title.
 *
 * @return WP_REST_Response
 */
function poketest_get_pokemon_list() {
    $args = [
        'post_type'      => 'pokemon',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];

    $query = new WP_Query($args);
    $data  = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $data[] = [
                'id'    => (int) get_post_meta(get_the_ID(), '_pokemon_pokedex_latest', true),
                'name'  => get_the_title(),
            ];
        }
        wp_reset_postdata();
    }

    return rest_ensure_response($data);
}

/**
 * Resolve a pokemon post from a REST identifier.
 *
 * Resolution order:
 * 1) Numeric value as WordPress post ID.
 * 2) Numeric value as latest pokedex number (_pokemon_pokedex_latest).
 * 3) Slug/title match by post_name.
 *
 * @param string $identifier
 * @return WP_Post|null
 */
function poketest_resolve_pokemon_post($identifier) {
    $identifier = sanitize_text_field((string) $identifier);

    if ($identifier === '') {
        return null;
    }

    if (ctype_digit($identifier)) {
        $post_id = (int) $identifier;
        $post = get_post($post_id);

        if ($post && $post->post_type === 'pokemon' && $post->post_status === 'publish') {
            return $post;
        }

        $by_pokedex = get_posts([
            'post_type'      => 'pokemon',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => '_pokemon_pokedex_latest',
                    'value'   => $post_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ],
            ],
        ]);

        if (!empty($by_pokedex)) {
            return $by_pokedex[0];
        }
    }

    $slug = sanitize_title($identifier);
    if ($slug !== '') {
        $by_slug = get_posts([
            'post_type'      => 'pokemon',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'name'           => $slug,
        ]);

        if (!empty($by_slug)) {
            return $by_slug[0];
        }
    }

    return null;
}

/**
 * Callback: get full data of a Pokemon by identifier.
 *
 * Returns all meta fields and terms, similar to your single-pokemon template.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function poketest_get_pokemon_detail($request) {
    $identifier = (string) ($request['identifier'] ?? '');
    $post       = poketest_resolve_pokemon_post($identifier);

    if (!$post || $post->post_type !== 'pokemon') {
        return new WP_Error('not_found', 'Pokemon not found', ['status' => 404]);
    }

    $post_id = (int) $post->ID;

    $data = [
        'id'          => $post_id,
        'name'        => get_the_title($post_id),
        'description' => $post->post_content,
        'thumbnail'   => get_the_post_thumbnail_url($post_id, 'medium'),
        'weight'      => get_post_meta($post_id, '_pokemon_weight', true),
        'old_pokedex' => get_post_meta($post_id, '_pokemon_pokedex_old', true),
        'latest_pokedex' => get_post_meta($post_id, '_pokemon_pokedex_latest', true),
        'types'       => wp_list_pluck(get_the_terms($post_id, 'pokemon_type') ?: [], 'name'),
        'moves'       => get_post_meta($post_id, '_pokemon_moves', true) ?: [],
    ];

    return rest_ensure_response($data);
}