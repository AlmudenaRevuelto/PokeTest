<?php

use PHPUnit\Framework\TestCase;

/**
 * PokemonRestTest
 *
 * Tests for the Pokemon REST resolver function that resolves Pokemon by ID, pokedex number, or slug.
 */
require_once __DIR__ . '/../inc/Services/pokemon-rest-routes.php';

class PokemonRestTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        unset($GLOBALS['mock_get_post_returns_null']);
    }

    protected function tearDown(): void {
        unset($GLOBALS['mock_get_post_returns_null']);
        parent::tearDown();
    }

    /**
     * test_resolve_pokemon_post_by_post_id
     *
     * Verifies that a Pokemon post can be resolved by its post ID.
     */
    public function test_resolve_pokemon_post_by_post_id() {

        $post = poketest_resolve_pokemon_post('1');

        $this->assertIsObject($post);
        $this->assertEquals(1, $post->ID);
        $this->assertEquals('pokemon', $post->post_type);
    }

    /**
     * test_resolve_pokemon_post_by_pokedex_number
     *
     * Verifies that a Pokemon post can be resolved by its Pokedex number.
     */
    public function test_resolve_pokemon_post_by_pokedex_number() {

        $post = poketest_resolve_pokemon_post('25');

        $this->assertIsObject($post);
        $this->assertEquals(1, $post->ID);
        $this->assertEquals('pokemon', $post->post_type);
    }

    /**
     * test_resolve_pokemon_post_returns_null_for_empty_identifier
     *
     * Verifies that an empty identifier returns null.
     */
    public function test_resolve_pokemon_post_returns_null_for_empty_identifier() {

        $post = poketest_resolve_pokemon_post('');

        $this->assertNull($post);
    }
}