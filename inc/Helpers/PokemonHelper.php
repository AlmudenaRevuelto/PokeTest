<?php

/**
 * PokemonHelper
 *
 * A small utility class with helper functions for Pokemon data formatting.
 */
class PokemonHelper {

    /**
     * Convert Pokemon weight from decigrams to kilograms.
     *
     * @param float|int $weight Weight value in decigrams.
     * @return string Weight formatted as kilograms with unit.
     */
    public static function formatWeight($weight) {
        return $weight / 10 . " kg";
    }

}