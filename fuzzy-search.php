<?php

/**
 * Kirby Fuzzy Search Plugin
 *
 * @version   1.0.0-beta
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/kirby-fuzzy-search
 * @license   MIT
 */

// Load Composer dependencies
require __DIR__.DS.'vendor'.DS.'autoload.php';

// Load Kirby custom methods
require __DIR__.DS.'src'.DS.'methods.php';

use PedroBorges\FuzzySearch\FuzzySearch;

if (! function_exists('fuzzySearch')) {
    /**
     * Perform fuzzy-search on an array of data.
     *
     * @param mixed $data
     * @param string $query
     * @param array $fields
     * @return array
     */
    function fuzzySearch($data, string $query, $fields = []) {
        $fuzzy = new FuzzySearch($data, $fields);

        return $fuzzy->search($query);
    }
}
