<?php
/**
 * Virtual single page for Pokemon loaded from PokeAPI.
 *
 * URL pattern: /pokemon-api/{name}/
 */

use PokeTest\Core\View;

get_header();

$pokemon_name = sanitize_title( (string) get_query_var( 'api_pokemon' ) );
$service      = new PokeApiService();
$pokemon      = $service->getPokemon( $pokemon_name );

if ( ! $pokemon ) {
    status_header( 404 );
    nocache_headers();
    get_template_part( 'loop-templates/content', 'none' );
    get_footer();
    return;
}

$types = array_map(
    static function ( $entry ) {
        return ucfirst( (string) ( $entry['type']['name'] ?? '' ) );
    },
    $pokemon['types'] ?? []
);

$description = $service->getPokemonDescription( (string) ( $pokemon['id'] ?? $pokemon_name ) );

// Fetch the English short_effect for each move from its individual PokeAPI endpoint.
// Results are cached via transients so repeat page loads are fast.
$moves = array_map(
    static function ( $entry ) use ( $service ) {
        $url    = (string) ( $entry['move']['url'] ?? '' );
        $effect = $url ? $service->getMoveEffect( $url ) : '';

        return [
            'name'        => ucfirst( str_replace( '-', ' ', (string) ( $entry['move']['name'] ?? '' ) ) ),
            'description' => $effect ?: 'No description available.',
        ];
    },
    array_slice( $pokemon['moves'] ?? [], 0, 10 )
);

// Find the Pokédex index for the 'red' version in the game_indices list.
$red_index = '-';
foreach ( $pokemon['game_indices'] ?? [] as $entry ) {
    if ( ( $entry['version']['name'] ?? '' ) === 'red' ) {
        $red_index = (string) $entry['game_index'];
        break;
    }
}

$view_data = [
    'title'          => ucfirst( (string) ( $pokemon['name'] ?? $pokemon_name ) ),
    'content'        => '<p>' . esc_html( $description ?: 'No description available.' ) . '</p>',
    'thumbnail'      => $pokemon['sprites']['other']['official-artwork']['front_default']
        ?? ( $pokemon['sprites']['front_default'] ?? '' ),
    'weight'         => PokemonHelper::formatWeight( (int) ( $pokemon['weight'] ?? 0 ) ),
    'old_pokedex'    => $red_index,
    'latest_pokedex' => (string) ( $pokemon['id'] ?? '-' ),
    'types'          => $types,
    'moves'          => $moves,
    'id'             => (int) ( $pokemon['id'] ?? 0 ),
];

$view = new View( get_template_directory() . '/views' );
$view->render( 'single-pokemon.twig', $view_data );

get_footer();
