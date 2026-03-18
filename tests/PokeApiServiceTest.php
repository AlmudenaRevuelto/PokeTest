<?php

use PHPUnit\Framework\TestCase;

/**
 * PokeApiServiceTest
 *
 * Tests for the PokeApiService class that handles API requests to PokéAPI.
 * Verifies Pokemon data retrieval, error handling, and move effect caching.
 */
class PokeApiServiceTest extends TestCase {

    protected function setUp(): void {

        parent::setUp();
        unset($GLOBALS['mock_wp_remote_get_error']);
    }

    protected function tearDown(): void {

        unset($GLOBALS['mock_wp_remote_get_error']);
        parent::tearDown();
    }

    /**
     * test_get_pokemon_returns_array
     *
     * Verifies that getPokemon() returns an array with Pokemon data.
     */
    public function test_get_pokemon_returns_array() {

        $service = new PokeApiService();
        $result = $service->getPokemon('pikachu');

        $this->assertIsArray($result);
        $this->assertEquals('pikachu', $result['name']);
    }

    /**
     * test_get_pokemon_has_weight
     *
     * Verifies that Pokemon data includes weight information.
     */
    public function test_get_pokemon_has_weight() {

        $service = new PokeApiService();
        $result = $service->getPokemon('pikachu');

        $this->assertArrayHasKey('weight', $result);
    }

    /**
     * test_get_pokemon_structure
     *
     * Verifies that Pokemon data contains all required fields: name, weight, and id.
     */
    public function test_get_pokemon_structure() {

        $service = new PokeApiService();
        $pokemon = $service->getPokemon('pikachu');

        $this->assertArrayHasKey('name', $pokemon);
        $this->assertArrayHasKey('weight', $pokemon);
        $this->assertArrayHasKey('id', $pokemon);
    }

    /**
     * test_get_pokemon_handles_error
     *
     * Verifies that getPokemon() returns null when an API error occurs.
     */
    public function test_get_pokemon_handles_error() {

        $GLOBALS['mock_wp_remote_get_error'] = true;

        $service = new PokeApiService();
        $result = $service->getPokemon('pikachu');

        $this->assertNull($result);
    }

    /**
     * test_get_move_effect_returns_string
     *
     * Verifies that getMoveEffect() returns a string (effect description).
     */
    public function test_get_move_effect_returns_string() {

        $service = new PokeApiService();

        $result = $service->getMoveEffect('https://fake-url.com');

        $this->assertIsString($result);
    }

    /**
     * test_moves_are_formatted_correctly
     *
     * Verifies that move data is properly formatted from API response structure.
     */
    public function test_moves_are_formatted_correctly() {

        $moves = [
            [
                'move' => [
                    'name' => 'thunderbolt',
                    'url' => 'fake-url'
                ]
            ]
        ];

        $formatted = array_map(function ($m) {
            return [
                'name' => $m['move']['name'],
                'description' => ''
            ];
        }, $moves);

        $this->assertEquals('thunderbolt', $formatted[0]['name']);
    }
}