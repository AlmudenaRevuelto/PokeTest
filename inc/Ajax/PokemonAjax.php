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
        add_action('wp_ajax_get_old_pokedex', [$this, 'getOldPokedex']);
        add_action('wp_ajax_nopriv_get_old_pokedex', [$this, 'getOldPokedex']);
    }

    /**
     * getOldPokedex
     *
     * AJAX callback to return Pokedex data in JSON format.
     * Pokemon fetching, filtering, and response building should be implemented here.
     *
     * Currently it always returns an empty success response; add data handling.
     */
    public function getOldPokedex() {
        // AJAX logic goes here

        // Example successful response; adjust with real data as needed.
        wp_send_json_success();
    }

}