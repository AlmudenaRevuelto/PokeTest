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

    /**
     * getMoveByUrl
     *
     * Fetches move details from a full PokeAPI URL and returns the English
     * short_effect string. Results are cached as transients for 24 hours to
     * avoid repeated remote calls on every page load.
     *
     * @param string $url Full PokeAPI move URL, e.g. https://pokeapi.co/api/v2/move/14/
     * @return string English short effect, or empty string on failure.
     */
    public function getMoveEffect($url) {

        // Use a transient keyed by URL so each unique move is cached once.
        $cache_key = 'pokeapi_move_' . md5($url);
        $cached    = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return '';
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // Find the English short_effect inside effect_entries.
        $effect = '';
        foreach ($data['effect_entries'] ?? [] as $entry) {
            if (($entry['language']['name'] ?? '') === 'en') {
                $effect = (string) ($entry['short_effect'] ?? '');
                break;
            }
        }

        set_transient($cache_key, $effect, DAY_IN_SECONDS);

        return $effect;
    }

    /**
     * getPokemonDescription
     *
     * Fetches the Pokemon species record and extracts an English flavor text.
     * Data is cached for 24 hours to reduce repeated requests.
     *
     * @param string|int $idOrName Pokemon ID or slug.
     * @return string English description, or empty string on failure.
     */
    public function getPokemonDescription($idOrName) {

        $species = rawurlencode((string) $idOrName);
        $cache_key = 'pokeapi_species_desc_' . md5($species);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $response = wp_remote_get($this->apiUrl . 'pokemon-species/' . $species);

        if (is_wp_error($response)) {
            return '';
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $entries = $data['flavor_text_entries'] ?? [];

        $description = '';

        // Prefer the classic Red version text when available.
        foreach ($entries as $entry) {
            if (($entry['language']['name'] ?? '') === 'en' && ($entry['version']['name'] ?? '') === 'red') {
                $description = (string) ($entry['flavor_text'] ?? '');
                break;
            }
        }

        // Fallback to the first available English flavor text.
        if ($description === '') {
            foreach ($entries as $entry) {
                if (($entry['language']['name'] ?? '') === 'en') {
                    $description = (string) ($entry['flavor_text'] ?? '');
                    break;
                }
            }
        }

        $description = trim(str_replace(["\n", "\f"], ' ', $description));
        $description = preg_replace('/\s+/', ' ', $description) ?: '';

        set_transient($cache_key, $description, DAY_IN_SECONDS);

        return $description;
    }

}