<?php

/**
 * PHPUnit Bootstrap: Minimal WordPress Test Environment
 *
 * Provides mock implementations of WordPress functions and classes required by PokeTest theme.
 * This file simulates a WordPress environment for unit testing without a full WordPress installation.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}

if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

if (!function_exists('get_template_directory')) {
    /**
     * Mock: Returns the theme directory path.
     *
     * @return string The absolute path to the theme directory.
     */
    function get_template_directory() {
        return dirname(__DIR__);
    }
}

if (!class_exists('WP_Error')) {
    class WP_Error {
        public function __construct($code = '', $message = '', $data = []) {
        }
    }
}

if (!function_exists('wp_remote_get')) {
    /**
     * Mock: Makes a remote HTTP GET request.
     *
     * Returns mock Pokemon data or simulates an error when the mock_wp_remote_get_error flag is set.
     *
     * @param string $url The URL to request.
     * @return array|WP_Error Response array or WP_Error instance.
     */
    function wp_remote_get($url) {
        if (!empty($GLOBALS['mock_wp_remote_get_error'])) {
            return new WP_Error();
        }

        return [
            'body' => json_encode([
                'name' => 'pikachu',
                'weight' => 60,
                'id' => 25,
                'types' => [],
                'moves' => []
            ])
        ];
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    /**
     * Mock: Extracts the body from a remote response.
     *
     * @param array $response The response array.
     * @return string The response body.
     */
    function wp_remote_retrieve_body($response) {
        return $response['body'];
    }
}

if (!function_exists('is_wp_error')) {
    /**
     * Mock: Checks if a variable is a WP_Error instance.
     *
     * @param mixed $thing The variable to check.
     * @return bool True if the variable is a WP_Error, false otherwise.
     */
    function is_wp_error($thing) {
        return $thing instanceof WP_Error;
    }
}

if (!function_exists('get_transient')) {
    /**
     * Mock: Retrieves transient data (always returns false in tests).
     *
     * @param string $key The transient key.
     * @return mixed Always returns false for test purposes.
     */
    function get_transient($key) {
        return false;
    }
}

if (!function_exists('set_transient')) {
    /**
     * Mock: Sets transient data (no-op in tests).
     *
     * @param string $key The transient key.
     * @param mixed $value The value to store.
     * @param int $time Expiration time in seconds.
     * @return bool Always returns true for test purposes.
     */
    function set_transient($key, $value, $time) {
        return true;
    }
}

if (!function_exists('get_posts')) {
    /**
     * Mock: Queries posts by custom parameters.
     *
     * Returns mock Pikachu post only if meta_query or name match expected values.
     *
     * @param array $args Query arguments including meta_query or name.
     * @return array Array of post objects, or empty array if no match.
     */
    function get_posts($args) {
        if (isset($args['meta_query'][0]['value']) && (int) $args['meta_query'][0]['value'] !== 25) {
            return [];
        }

        if (isset($args['name']) && $args['name'] !== 'pikachu') {
            return [];
        }

        return [
            (object)[
                'ID' => 1,
                'post_title' => 'Pikachu',
                'post_type' => 'pokemon',
                'post_status' => 'publish',
                'post_content' => 'Electric mouse',
            ]
        ];
    }
}

if (!function_exists('get_post_meta')) {
    /**
     * Mock: Retrieves post metadata.
     *
     * Returns mock Pokemon attributes (pokedex number: 25, weight: 60).
     *
     * @param int $id Post ID.
     * @param string $key Meta key.
     * @param bool $single Whether to return a single value.
     * @return mixed The meta value, or null if not found.
     */
    function get_post_meta($id, $key, $single) {
        if ($key === '_pokemon_pokedex_latest') return 25;
        if ($key === '_pokemon_weight') return 60;
        return null;
    }
}

if (!function_exists('get_the_title')) {
    /**
     * Mock: Retrieves the title of a post.
     *
     * Returns 'Pikachu' from the global mock post or default value.
     *
     * @param int|null $id Post ID (optional).
     * @return string The post title.
     */
    function get_the_title($id = null) {
        if ($id === null && !empty($GLOBALS['mock_current_post']->post_title)) {
            return $GLOBALS['mock_current_post']->post_title;
        }

        return 'Pikachu';
    }
}

if (!function_exists('get_post')) {
    /**
     * Mock: Retrieves a post object.
     *
     * Returns mock Pikachu post only for ID 1, null for all other IDs.
     * Respects the mock_get_post_returns_null flag for error simulation.
     *
     * @param int $id Post ID.
     * @return object|null Post object or null if not found.
     */
    function get_post($id) {
        if (!empty($GLOBALS['mock_get_post_returns_null'])) {
            return null;
        }

        // Only return a valid post for ID 1 (Pikachu), null for unknowns
        if ((int) $id === 1) {
            return (object)[
                'ID' => 1,
                'post_type' => 'pokemon',
                'post_status' => 'publish',
                'post_content' => 'Electric mouse'
            ];
        }

        return null;
    }
}

if (!function_exists('get_the_post_thumbnail_url')) {
    /**
     * Mock: Returns the featured image URL of a post.
     *
     * @return string The mock image URL.
     */
    function get_the_post_thumbnail_url() {
        return 'image.jpg';
    }
}

if (!function_exists('get_the_terms')) {
    /**
     * Mock: Retrieves the terms assigned to a post.
     *
     * Returns a mock 'electric' Pokemon type term.
     *
     * @return array Array of term objects.
     */
    function get_the_terms() {
        return [
            (object)['name' => 'electric']
        ];
    }
}

if (!function_exists('add_action')) {
    /**
     * Mock: Registers an action hook (no-op in tests).
     *
     * @param string $hook The hook name.
     * @param callable $callback The function to call.
     * @return bool Always returns true.
     */
    function add_action($hook, $callback) {
        return true;
    }
}

if (!function_exists('add_filter')) {
    /**
     * Mock: Registers a filter hook (no-op in tests).
     *
     * @param string $hook The hook name.
     * @param callable $callback The function to call.
     * @return bool Always returns true.
     */
    function add_filter($hook, $callback) {
        return true;
    }
}

if (!function_exists('register_rest_route')) {
    /**
     * Mock: Registers a REST API route (no-op in tests).
     *
     * @param string $namespace Route namespace.
     * @param string $route Route path.
     * @param array $args Route configuration.
     * @return bool Always returns true.
     */
    function register_rest_route($namespace, $route, $args) {
        return true;
    }
}

if (!function_exists('rest_ensure_response')) {
    /**
     * Mock: Ensures the response is wrapped properly.
     *
     * Returns data as-is for testing purposes.
     *
     * @param mixed $data The response data.
     * @return mixed The response data unchanged.
     */
    function rest_ensure_response($data) {
        return $data;
    }
}

if (!function_exists('sanitize_text_field')) {
    /**
     * Mock: Sanitizes text input.
     *
     * @param string $value The text to sanitize.
     * @return string Trimmed text.
     */
    function sanitize_text_field($value) {
        return trim((string) $value);
    }
}

if (!function_exists('sanitize_title')) {
    /**
     * Mock: Converts text to a URL-friendly slug.
     *
     * @param string $value The text to convert.
     * @return string The sanitized slug.
     */
    function sanitize_title($value) {
        $slug = strtolower(trim((string) $value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?: '';
        return trim($slug, '-');
    }
}

if (!function_exists('wp_list_pluck')) {
    /**
     * Mock: Extracts a specific field from an array of objects.
     *
     * @param array $list Array of objects or arrays.
     * @param string $field The field name to extract.
     * @return array Array of extracted values.
     */
    function wp_list_pluck($list, $field) {
        $result = [];

        foreach ((array) $list as $item) {
            if (is_object($item) && isset($item->{$field})) {
                $result[] = $item->{$field};
            }
        }

        return $result;
    }
}

if (!function_exists('get_the_ID')) {
    /**
     * Mock: Retrieves the ID of the current post.
     *
     * @return int The post ID from the global mock post.
     */
    function get_the_ID() {
        return isset($GLOBALS['mock_current_post']->ID) ? (int) $GLOBALS['mock_current_post']->ID : 0;
    }
}

if (!function_exists('wp_reset_postdata')) {
    /**
     * Mock: Clears the global post data.
     *
     * Removes the mock_current_post from globals.
     */
    function wp_reset_postdata() {
        unset($GLOBALS['mock_current_post']);
    }
}

if (!class_exists('WP_Query')) {
    /**
     * Mock: WordPress query class for post loops.
     *
     * Simulates WordPress post query with a mock Pikachu post.
     */
    class WP_Query {
        private $posts = [];
        private $index = 0;

        public function __construct($args = []) {
            $this->posts = [
                (object) [
                    'ID' => 1,
                    'post_title' => 'Pikachu',
                    'post_type' => 'pokemon',
                    'post_status' => 'publish',
                    'post_content' => 'Electric mouse',
                ]
            ];
        }

        public function have_posts() {
            return $this->index < count($this->posts);
        }

        public function the_post() {
            $GLOBALS['mock_current_post'] = $this->posts[$this->index];
            $this->index++;
        }
    }
}

if (!function_exists('wp_insert_post')) {
    /**
     * Mock: Creates a new post.
     *
     * Always returns a mock post ID (123).
     *
     * @param array $data Post data.
     * @return int Mock post ID.
     */
    function wp_insert_post($data) {
        return 123;
    }
}

if (!function_exists('update_post_meta')) {
    /**
     * Mock: Updates post metadata (no-op in tests).
     *
     * @return bool Always returns true.
     */
    function update_post_meta() {
        return true;
    }
}

if (!function_exists('wp_set_object_terms')) {
    /**
     * Mock: Assigns taxonomy terms to a post (no-op in tests).
     *
     * @return bool Always returns true.
     */
    function wp_set_object_terms() {
        return true;
    }
}

if (!function_exists('wp_redirect')) {
    /**
     * Mock: Performs a redirect.
     *
     * Returns the URL as-is for test purposes.
     *
     * @param string $url The target URL.
     * @return string The URL.
     */
    function wp_redirect($url) {
        return $url;
    }
}

if (!function_exists('get_permalink')) {
    /**
     * Mock: Generates a permalink for a post.
     *
     * @param int $id Post ID.
     * @return string The mock permalink.
     */
    function get_permalink($id) {
        return "http://test.com/pokemon/$id";
    }
}

if (!class_exists('WP_REST_Request')) {
    /**
     * Mock: WordPress REST request object.
     *
     * Implements ArrayAccess to allow route callbacks to read parameters like $request['identifier'].
     */
    class WP_REST_Request implements ArrayAccess {
        private $params = [];

        public function __construct($method = 'GET', $route = '') {
        }

        public function set_param($key, $value) {
            $this->params[$key] = $value;
        }

        public function get_param($key) {
            return $this->params[$key] ?? null;
        }

        public function offsetExists($offset): bool {
            return array_key_exists($offset, $this->params);
        }

        public function offsetGet($offset): mixed {
            return $this->params[$offset] ?? null;
        }

        public function offsetSet($offset, $value): void {
            $this->params[$offset] = $value;
        }

        public function offsetUnset($offset): void {
            unset($this->params[$offset]);
        }
    }
}

// Cargar tu clase real
require_once __DIR__ . '/../inc/Services/pokemon-api-service.php';