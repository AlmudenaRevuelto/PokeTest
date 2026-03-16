import { initPokemonFilter } from "./components/pokemonFilter";

// main.ts
// Application entry point for the PokeTest frontend.

document.addEventListener("DOMContentLoaded", () => {

    // Ensure the document is loaded before initializing UI modules.
    console.log("PokeTest frontend loaded");

    initPokemonFilter();

});