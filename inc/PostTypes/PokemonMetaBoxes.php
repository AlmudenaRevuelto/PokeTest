<?php

class PokemonMetaBoxes {

    public function __construct() {

        add_action('add_meta_boxes', [$this, 'registerMetaBoxes']);
        add_action('save_post', [$this, 'saveMetaBoxes']);

    }

    public function registerMetaBoxes() {

        add_meta_box(
            'pokemon_data',
            'Pokemon Data',
            [$this, 'renderMetaBox'],
            'pokemon',
            'normal',
            'default'
        );

    }

    public function renderMetaBox($post) {

        wp_nonce_field('pokemon_meta_box', 'pokemon_meta_box_nonce');

        $weight = get_post_meta($post->ID, '_pokemon_weight', true);
        $pokedex_old = get_post_meta($post->ID, '_pokemon_pokedex_old', true);
        $pokedex_latest = get_post_meta($post->ID, '_pokemon_pokedex_latest', true);

        ?>

        <p>
            <label><strong>Weight</strong></label><br>
            <input type="number" name="pokemon_weight" value="<?php echo esc_attr($weight); ?>" />
        </p>

        <p>
            <label><strong>Old Pokedex Number</strong></label><br>
            <input type="number" name="pokemon_pokedex_old" value="<?php echo esc_attr($pokedex_old); ?>" />
        </p>

        <p>
            <label><strong>Latest Pokedex Number</strong></label><br>
            <input type="number" name="pokemon_pokedex_latest" value="<?php echo esc_attr($pokedex_latest); ?>" />
        </p>

        <?php
    }

    public function saveMetaBoxes($post_id) {

        if (!isset($_POST['pokemon_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['pokemon_meta_box_nonce'], 'pokemon_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'pokemon') {
            return;
        }

        if (isset($_POST['pokemon_weight'])) {
            update_post_meta($post_id, '_pokemon_weight', sanitize_text_field($_POST['pokemon_weight']));
        }

        if (isset($_POST['pokemon_pokedex_old'])) {
            update_post_meta($post_id, '_pokemon_pokedex_old', sanitize_text_field($_POST['pokemon_pokedex_old']));
        }

        if (isset($_POST['pokemon_pokedex_latest'])) {
            update_post_meta($post_id, '_pokemon_pokedex_latest', sanitize_text_field($_POST['pokemon_pokedex_latest']));
        }
    }
}