<?php

class PokeApiService {

    private $apiUrl = "https://pokeapi.co/api/v2/";

    public function getPokemon($name) {

        $response = wp_remote_get($this->apiUrl . "pokemon/" . $name);

        if (is_wp_error($response)) {
            return null;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

}