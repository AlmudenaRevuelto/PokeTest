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

        require_once get_template_directory() . '/inc/PostTypes/pokemon-post-type.php';
        require_once get_template_directory() . '/inc/Ajax/pokemon-ajax.php';
        require_once get_template_directory() . '/inc/Services/pokemon-api-service.php';
        require_once get_template_directory() . '/inc/Helpers/pokemon-helper.php';
        require_once get_template_directory() . '/inc/Helpers/pokemon-admin-icon.php';
        require_once get_template_directory() . '/inc/PostTypes/pokemon-meta-boxes.php';
        // Non-class file: registers rewrite rules and template routing for
        // virtual Pokemon API pages (/pokemon-api/{name}/ or ?api_pokemon={name}).
        require_once get_template_directory() . '/inc/Services/pokemon-api-routes.php';
        require_once get_template_directory() . '/inc/Services/pokemon-random-route.php';
        require_once get_template_directory() . '/inc/Services/pokemon-generate-route.php';
        require_once get_template_directory() . '/inc/Services/pokemon-rest-routes.php';

        new PokemonPostType();
        new PokemonAjax();
        new PokemonMetaBoxes();
    }

}