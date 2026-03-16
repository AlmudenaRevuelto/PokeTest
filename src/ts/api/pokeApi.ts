// pokeApi.ts
// API helper to fetch Pokemon data from PokeAPI.

export async function getPokemon(name: string) {

    // Fetch Pokemon data by name from the public API endpoint.
    const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${name}`);

    // Parse and return JSON response.
    return await response.json();
}