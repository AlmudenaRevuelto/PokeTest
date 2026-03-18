<?php

use PHPUnit\Framework\TestCase;

/**
 * PokemonRestEndpointTest
 *
 * Tests for the Pokemon REST API endpoints that return Pokemon list and detail.
 */
require_once __DIR__ . '/../inc/Services/pokemon-rest-routes.php';

class PokemonRestEndpointTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        unset($GLOBALS['mock_get_post_returns_null']);
    }

    protected function tearDown(): void {
        unset($GLOBALS['mock_get_post_returns_null']);
        parent::tearDown();
    }

    /**
     * test_rest_list_endpoint
     *
     * Verifies that the REST list endpoint returns an array of Pokemon with correct structure.
     */
    public function test_rest_list_endpoint() {

        $response = poketest_get_pokemon_list();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $this->assertArrayHasKey('id', $response[0]);
        $this->assertArrayHasKey('name', $response[0]);
    }

    /**
     * test_rest_detail_endpoint
     *
     * Verifies that the REST detail endpoint returns complete Pokemon information.
     */
    public function test_rest_detail_endpoint() {

        $request = new WP_REST_Request('GET', '/poketest/v1/pokemon/1');
        $request->set_param('identifier', '1');

        $response = poketest_get_pokemon_detail($request);

        $this->assertIsArray($response);

        $this->assertEquals(1, $response['id']);
        $this->assertEquals('Pikachu', $response['name']);
    }

    /**
     * test_rest_detail_not_found
     *
     * Verifies that the REST detail endpoint returns WP_Error when Pokemon is not found.
     */
    public function test_rest_detail_not_found() {

        $request = new WP_REST_Request('GET', '/poketest/v1/pokemon/999');
        $request->set_param('identifier', '999');

        $response = poketest_get_pokemon_detail($request);

        $this->assertInstanceOf(WP_Error::class, $response);
    }
}