<?php

/**
 * PokemonAdminIcon
 *
 * Loads custom admin menu icons for Pokemon-related post types.
 */
class PokemonAdminIcon {

    /**
     * Return the Pokemon admin menu icon as an SVG data URI.
     *
     * Falls back to a Dashicon when the SVG file cannot be read.
     *
     * @return string
     */
    public static function getMenuIcon() {
        $icon_path = get_template_directory() . '/assets/admin-icons/pokeball-menu.svg';

        if (!file_exists($icon_path)) {
            return 'dashicons-games';
        }

        $svg = file_get_contents($icon_path);
        if ($svg === false || trim($svg) === '') {
            return 'dashicons-games';
        }

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
