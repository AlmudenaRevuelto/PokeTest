<?php

class PokemonAjax {

    public function __construct() {
        add_action('wp_ajax_get_old_pokedex', [$this, 'getOldPokedex']);
        add_action('wp_ajax_nopriv_get_old_pokedex', [$this, 'getOldPokedex']);
    }

    public function getOldPokedex() {
        // lógica AJAX irá aquí
        wp_send_json_success();
    }

}