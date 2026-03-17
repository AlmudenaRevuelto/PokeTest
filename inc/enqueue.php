<?php
/**
 * Understrap enqueue scripts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme         = wp_get_theme();
		$theme_version     = $the_theme->get( 'Version' );
		$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );
		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Grab asset urls.
		$theme_styles  = "/css/theme{$suffix}.css";
		$theme_scripts = "/js/theme{$suffix}.js";
		if ( 'bootstrap4' === $bootstrap_version ) {
			$theme_styles  = "/css/theme-bootstrap4{$suffix}.css";
			$theme_scripts = "/js/theme-bootstrap4{$suffix}.js";
		}

		$css_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_styles );
		wp_enqueue_style( 'understrap-styles', get_template_directory_uri() . $theme_styles, array(), $css_version );

		// Fix that the offcanvas close icon is hidden behind the admin bar.
		if ( 'bootstrap4' !== $bootstrap_version && is_admin_bar_showing() ) {
			understrap_offcanvas_admin_bar_inline_styles();
		}

		wp_enqueue_script( 'jquery' );

		$js_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_scripts );
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . $theme_scripts, array(), $js_version, true );
		wp_enqueue_script(
			'poke-main',
			get_template_directory_uri() . '/dist/js/main.js',
			[],
			wp_get_theme()->get('Version'),
			true
		);

		wp_localize_script('poke-main', 'wpApiSettings', [
			'ajax_url' => admin_url('admin-ajax.php')
		]);
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // End of if function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );

add_action( 'admin_enqueue_scripts', function($hook) {

    global $post;

    if (($hook === 'post.php' || $hook === 'post-new.php') && isset($post) && $post->post_type === 'pokemon') {
        wp_enqueue_script(
            'pokemon-admin-js',
            get_template_directory_uri() . '/dist/js/main.js',
            [],
            filemtime(get_template_directory() . '/dist/js/main.js'),
            true
        );

        wp_localize_script('pokemon-admin-js', 'wpApiSettings', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }

});

add_action('wp_enqueue_scripts', function() {
    if (is_singular('pokemon')) {
        wp_enqueue_style(
            'pokemon-card-css',
            get_template_directory_uri() . '/css/pokemon-card.css',
            [],
            wp_get_theme()->get('Version')
        );
    }
});

if ( ! function_exists( 'understrap_offcanvas_admin_bar_inline_styles' ) ) {
	/**
	 * Add inline styles for the offcanvas component if the admin bar is visible.
	 *
	 * Fixes that the offcanvas close icon is hidden behind the admin bar.
	 *
	 * @since 1.2.0
	 */
	function understrap_offcanvas_admin_bar_inline_styles() {
		$navbar_type = get_theme_mod( 'understrap_navbar_type', 'collapse' );
		if ( 'offcanvas' !== $navbar_type ) {
			return;
		}

		$css = '
		body.admin-bar .offcanvas.show  {
			margin-top: 32px;
		}
		@media screen and ( max-width: 782px ) {
			body.admin-bar .offcanvas.show {
				margin-top: 46px;
			}
		}';
		wp_add_inline_style( 'understrap-styles', $css );
	}
}
