<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
$home_url  = home_url( '/' );
$random_url = home_url( '/random/' );
$generate_url = home_url( '/generate/' );
$rest_list_url = rest_url( 'poketest/v1/pokemon' );
$rest_detail_url = rest_url( 'poketest/v1/pokemon/bulbasaur' );
$api_pretty_url = home_url( '/pokemon-api/bulbasaur/' );
$api_query_url = add_query_arg( 'api_pokemon', 'bulbasaur', $home_url );

$latest_pokemon = get_posts([
	'post_type'      => 'pokemon',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
]);
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer">

	<div class="<?php echo esc_attr( $container ); ?>">

		<div class="row">

			<div class="col-md-12">

				<footer class="site-footer" id="colophon">
					<div class="pt-footer-main row">
						<div class="col-lg-5 col-md-12 mb-4 mb-lg-0">
							<h2 class="pt-footer-title mb-2"><?php bloginfo( 'name' ); ?></h2>
							<p class="pt-footer-description mb-0">
								<?php esc_html_e( 'A clean Pokemon hub with filtering, generation routes, and API-powered pages.', 'understrap' ); ?>
							</p>
						</div>

						<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
							<h3 class="pt-footer-heading"><?php esc_html_e( 'Quick Links', 'understrap' ); ?></h3>
							<ul class="pt-footer-links list-unstyled mb-0">
								<li><a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'understrap' ); ?></a></li>
								<li><a href="<?php echo esc_url( $random_url ); ?>"><?php esc_html_e( 'Random Pokemon', 'understrap' ); ?></a></li>
								<li><a href="<?php echo esc_url( $generate_url ); ?>"><?php esc_html_e( 'Generate Pokemon', 'understrap' ); ?></a></li>
							</ul>
						</div>

						<div class="col-lg-4 col-md-6">
							<h3 class="pt-footer-heading"><?php esc_html_e( 'Latest Entries', 'understrap' ); ?></h3>
							<ul class="pt-footer-links list-unstyled mb-0">
								<?php if ( ! empty( $latest_pokemon ) ) : ?>
									<?php foreach ( $latest_pokemon as $pokemon_post ) : ?>
										<li>
											<a href="<?php echo esc_url( get_permalink( $pokemon_post->ID ) ); ?>">
												<?php echo esc_html( get_the_title( $pokemon_post->ID ) ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								<?php else : ?>
									<li><?php esc_html_e( 'No Pokemon entries yet.', 'understrap' ); ?></li>
								<?php endif; ?>
							</ul>
						</div>
					</div>

					<div class="pt-endpoints mt-4">
						<h3 class="pt-footer-heading mb-3"><?php esc_html_e( 'API Endpoints', 'understrap' ); ?></h3>
						<ul class="pt-endpoint-list list-unstyled mb-0">
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $rest_list_url ); ?>">/wp-json/poketest/v1/pokemon</a></li>
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $rest_detail_url ); ?>">/wp-json/poketest/v1/pokemon/{identifier}</a></li>
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $api_pretty_url ); ?>">/pokemon-api/{name}/</a></li>
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $api_query_url ); ?>">/?api_pokemon={name}</a></li>
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $random_url ); ?>">/random/</a></li>
							<li><a class="pt-endpoint-link" href="<?php echo esc_url( $generate_url ); ?>">/generate/</a></li>
						</ul>
					</div>

					<div class="site-info pt-subfooter mt-4 pt-3">

						<?php understrap_site_info(); ?>

					</div><!-- .site-info -->

				</footer><!-- #colophon -->

			</div><!-- col -->

		</div><!-- .row -->

	</div><!-- .container(-fluid) -->

</div><!-- #wrapper-footer -->

<?php // Closing div#page from header.php. ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>

