<?php

/**
 * PokemonAjax
 *
 * Class to handle AJAX requests related to the Pokedex.
 * Registers necessary hooks for logged-in and non-logged-in users.
 */
class PokemonAjax {

    /**
     * Constructor
     *
     * Registers WordPress AJAX actions for the `get_old_pokedex` endpoint.
     * - wp_ajax_*: for authenticated users.
     * - wp_ajax_nopriv_*: for unauthenticated visitors.
     */
    public function __construct() {
        add_action('wp_ajax_get_old_pokedex', [$this, 'get_old_pokedex']);
        add_action('wp_ajax_nopriv_get_old_pokedex', [$this, 'get_old_pokedex']);
    }

    /**
     * getOldPokedex
     *
     * AJAX callback to return Pokedex data in JSON format.
     * Pokemon fetching, filtering, and response building should be implemented here.
     *
     * Currently it always returns an empty success response; add data handling.
     */
    public function get_old_pokedex() {
        $post_id = intval($_POST['post_id'] ?? 0);

        if (!$post_id) {
            wp_send_json_error(['message' => 'No post ID provided']);
            wp_die();
        }

        $old_pokedex = get_post_meta($post_id, '_pokemon_pokedex_old', true);
        $version_name = 'Red/Blue'; // Adjust this based on your game logic

        wp_send_json_success([
            'old_pokedex' => $old_pokedex,
            'game' => $version_name,
        ]);

        wp_die();
    }

}