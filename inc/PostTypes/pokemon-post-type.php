<?php

/**
 * PokemonPostType
 *
 * Registers the custom Pokemon post type and taxonomy for the theme.
 */
class PokemonPostType {

    public function __construct() {
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomy']);
    }

    /**
     * registerPostType
     *
     * Register the 'pokemon' custom post type with basic settings and support.
     */
    public function registerPostType() {

        $labels = [
            'name' => 'Pokémon',
            'singular_name' => 'Pokemon',
            'menu_name' => 'Pokémon'
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'pokemon'],
            'menu_icon' => PokemonAdminIcon::getMenuIcon(),
            'supports' => ['title','editor','thumbnail'],
            'show_in_rest' => true
        ];

        register_post_type('pokemon', $args);
    }

    /**
     * registerTaxonomy
     *
     * Register the 'pokemon_type' hierarchical taxonomy for Pokemon post type.
     */
    public function registerTaxonomy() {

        $labels = [
            'name' => 'Pokemon Types',
            'singular_name' => 'Pokemon Type',
            'search_items' => 'Search Types',
            'all_items' => 'All Types',
            'edit_item' => 'Edit Type',
            'update_item' => 'Update Type',
            'add_new_item' => 'Add New Type',
            'new_item_name' => 'New Type Name',
            'menu_name' => 'Types'
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => 'pokemon-type'],
            'show_in_rest' => true
        ];

        register_taxonomy('pokemon_type', ['pokemon'], $args);
    }

}