<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$home_url          = home_url( '/' );
$random_url        = home_url( '/random/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="site" id="page">

	<!-- ******************* The Navbar Area ******************* -->
	<header id="wrapper-navbar">

		<div class="pt-topbar" aria-hidden="true">
			<div class="container d-flex align-items-center justify-content-between">
				<p class="pt-topbar-message mb-0">
					<?php esc_html_e( 'Pokemon encyclopedia, generation tools, and API playground.', 'understrap' ); ?>
				</p>
				<div class="pt-topbar-links">
					<a href="<?php echo esc_url( $home_url ); ?>" class="pt-topbar-link">
						<?php esc_html_e( 'Home', 'understrap' ); ?>
					</a>
					<a href="<?php echo esc_url( $random_url ); ?>" class="pt-topbar-link">
						<?php esc_html_e( 'Random Pokemon', 'understrap' ); ?>
					</a>
				</div>
			</div>
		</div>

		<a class="skip-link <?php echo understrap_get_screen_reader_class( true ); ?>" href="#content">
			<?php esc_html_e( 'Skip to content', 'understrap' ); ?>
		</a>

	</header><!-- #wrapper-navbar -->
