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

$kirby->set('field::method', 'fuzzySearch', function(Field $field, string $query, $fields = []) {
    $results = fuzzySearch($field, $query, $fields);

    // Return search result as a Field object
    // to allow for chaining of field methods
    return new Field(
        $field->page(),
        $field->key(),
        Yaml::encode($results)
    );
});

$kirby->set('pages::method', 'fuzzySearch', function(Pages $pages, string $query, $fields = []) {
    $results = fuzzySearch($pages, $query, $fields);

    // Retrieve pages IDs from results
    // to rebuild Pages collection
    $ids = [];
    foreach ($results as $page) {
        $ids[] = $page['id'];
    }

    return $pages->filterBy('id', 'in', $ids);
});
