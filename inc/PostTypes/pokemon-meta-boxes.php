<?php

/**
 * PokemonMetaBoxes
 *
 * Handles registration, display, and saving of custom meta boxes for Pokemon post type.
 */
class PokemonMetaBoxes {

    public function __construct() {

        add_action('add_meta_boxes', [$this, 'registerMetaBoxes']);
        add_action('save_post', [$this, 'saveMetaBoxes']);

    }

    /**
     * registerMetaBoxes
     *
     * Register a meta box for Pokemon additional data fields.
     */
    public function registerMetaBoxes() {

        add_meta_box(
            'pokemon_moves',
            'Pokemon Moves',
            [$this, 'renderMetaBox'],
            'pokemon',
            'normal',
            'default'
        );

    }

    /**
     * renderMetaBox
     *
     * Display form fields for Pokemon weight and Pokedex numbers in the meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function renderMetaBox($post) {

        wp_nonce_field('pokemon_meta_box', 'pokemon_meta_box_nonce');

        $weight = get_post_meta($post->ID, '_pokemon_weight', true);
        $pokedex_old = get_post_meta($post->ID, '_pokemon_pokedex_old', true);
        $pokedex_latest = get_post_meta($post->ID, '_pokemon_pokedex_latest', true);
        $moves = get_post_meta($post->ID, '_pokemon_moves', true) ?: [];

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

        <hr>
        <h4>Moves</h4>

        <button type="button" class="button add-move">Add Move</button>

        <ul id="pokemon-moves-list">
            <?php foreach ($moves as $index => $move): ?>
                <li>
                    <input 
                        type="text" 
                        name="pokemon_moves[<?php echo $index; ?>][name]" 
                        value="<?php echo esc_attr($move['name']); ?>" 
                        placeholder="Move Name"
                    />

                    <textarea 
                        name="pokemon_moves[<?php echo $index; ?>][description]" 
                        placeholder="Move Description"
                    ><?php echo esc_textarea($move['description']); ?></textarea>

                    <button type="button" class="button remove-move">Remove</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php
    }

    /**
     * saveMetaBoxes
     *
     * Handle save operation for Pokemon custom meta fields.
     * Performs nonce check, autosave bypass, post type check and sanitization.
     *
     * @param int $post_id ID of the current post.
     */
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

        // Moves
        if (isset($_POST['pokemon_moves'])) {

            $moves = $_POST['pokemon_moves'];
            $sanitized_moves = [];

            foreach ($moves as $move) {

                if (empty($move['name']) && empty($move['description'])) {
                    continue;
                }

                $sanitized_moves[] = [
                    'name' => sanitize_text_field($move['name'] ?? ''),
                    'description' => sanitize_textarea_field($move['description'] ?? '')
                ];
            }

            update_post_meta($post_id, '_pokemon_moves', $sanitized_moves);
        }
    }
}