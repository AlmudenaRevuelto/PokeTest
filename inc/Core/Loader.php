<?php

/**
 * Loader
 *
 * Class responsible for including required modules and initializing core components.
 * This class centralizes loading to keep theme bootstrapping in one place.
 */
class Loader {

    /**
     * init
     *
     * Requires class files for post types, AJAX handlers, services and helpers,
     * then instantiates the main objects.
     */
    public function init() {

        require_once get_template_directory() . '/inc/PostTypes/PokemonPostType.php';
        require_once get_template_directory() . '/inc/Ajax/PokemonAjax.php';
        require_once get_template_directory() . '/inc/Services/PokeApiService.php';
        require_once get_template_directory() . '/inc/Helpers/PokemonHelper.php';
        require_once get_template_directory() . '/inc/PostTypes/PokemonMetaBoxes.php';

        new PokemonPostType();
        new PokemonAjax();
        new PokemonMetaBoxes();
    }

}