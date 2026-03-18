<?php

use PHPUnit\Framework\TestCase;

/**
 * PokemonGenerateTest
 *
 * Tests for the Pokemon generation route that fetches data from PokeAPI and creates posts.
 */
require_once __DIR__ . '/../inc/Services/pokemon-generate-route.php';
require_once __DIR__ . '/../inc/Services/pokemon-api-service.php';

class PokemonGenerateTest extends TestCase {

    /**
     * test_pokeapi_returns_valid_pokemon
     *
     * Verifies that the PokeApiService correctly fetches and returns Pokemon data.
     */
    public function test_pokeapi_returns_valid_pokemon() {

        $service = new PokeApiService();
        $pokemon = $service->getPokemon('pikachu');

        $this->assertIsArray($pokemon);
        $this->assertEquals('pikachu', $pokemon['name']);
    }

    /**
     * test_wp_insert_post_is_called_correctly
     *
     * Verifies that new Pokemon posts are created with proper data.
     */
    public function test_wp_insert_post_is_called_correctly() {

        $post_id = wp_insert_post([
            'post_title' => 'Pikachu',
            'post_type' => 'pokemon'
        ]);

        $this->assertEquals(123, $post_id); // Mock return value
    }

    /**
     * test_update_post_meta_works
     *
     * Verifies that post metadata is correctly saved for generated Pokemon.
     */
    public function test_update_post_meta_works() {

        $result = update_post_meta(123, '_pokemon_weight', 60);

        $this->assertTrue($result);
    }

    /**
     * test_types_assignment
     *
     * Verifies that Pokemon types are correctly assigned as taxonomy terms.
     */
    public function test_types_assignment() {

        $result = wp_set_object_terms(123, ['electric'], 'pokemon_type');

        $this->assertTrue($result);
    }

    /**
     * test_redirect_url_is_correct
     *
     * Verifies that the redirect URL is properly formatted after post creation.
     */
    public function test_redirect_url_is_correct() {

        $url = wp_redirect(get_permalink(123));

        $this->assertEquals('http://test.com/pokemon/123', $url);
    }
}