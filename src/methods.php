<?php

$kirby->set('field::method', 'fuzzySearch', function(Field $field, $query, $fields = []) {
    if (empty(trim($query)) or $field->isEmpty()) {
        return new Field($field->page(), $field->key(), null);
    }

    $results = fuzzySearch($field, $query, $fields);

    // Return search result as a Field object
    // to allow for chaining of field methods
    return new Field(
        $field->page(),
        $field->key(),
        Yaml::encode($results)
    );
});

$kirby->set('page::method', 'fuzzySearch', function(Page $page, $query, $fields = []) {
    return $page->children()->index()->fuzzySearch($query, $fields);
});

$kirby->set('pages::method', 'fuzzySearch', function(Pages $pages, $query, $fields = []) {
    if (empty(trim($query)) or ! $pages->count()) {
        return $pages->limit(0);
    }

    $results = fuzzySearch($pages, $query, $fields);

    // Retrieve pages IDs from results
    // to rebuild Pages collection
    $ids = [];
    foreach ($results as $page) {
        $ids[] = $page['id'];
    }

    return $pages->filterBy('id', 'in', $ids);
});
