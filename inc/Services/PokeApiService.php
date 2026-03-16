<?php

/**
 * PokeApiService
 *
 * Service class to fetch Pokemon data from the external PokeAPI REST service.
 */
class PokeApiService {

    private $apiUrl = "https://pokeapi.co/api/v2/";

    /**
     * getPokemon
     *
     * Requests Pokemon details by name from PokeAPI.
     * Returns parsed JSON array or null on error.
     *
     * @param string $name Pokemon slug or name.
     * @return array|null
     */
    public function getPokemon($name) {

        $response = wp_remote_get($this->apiUrl . "pokemon/" . $name);

        if (is_wp_error($response)) {
            return null;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

}