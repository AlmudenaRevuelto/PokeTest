<?php

class Loader {

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