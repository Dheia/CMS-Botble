<?php

return [
    [
        'name' => 'Collection',
        'flag' => 'plugins.collection',
    ],
    [
        'name' => 'Subjects',
        'flag' => 'subjects.index',
        'parent_flag' => 'plugins.collection',
    ],
    [
        'name' => 'Create',
        'flag' => 'subjects.create',
        'parent_flag' => 'subjects.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'subjects.edit',
        'parent_flag' => 'subjects.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'subjects.destroy',
        'parent_flag' => 'subjects.index',
    ],

    [
        'name' => 'Taxon',
        'flag' => 'taxon.index',
        'parent_flag' => 'plugins.collection',
    ],
    [
        'name' => 'Create',
        'flag' => 'taxon.create',
        'parent_flag' => 'taxon.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'taxon.edit',
        'parent_flag' => 'taxon.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'taxon.destroy',
        'parent_flag' => 'taxon.index',
    ],

    [
        'name' => 'Tags',
        'flag' => 'tags.index',
        'parent_flag' => 'plugins.collection',
    ],
    [
        'name' => 'Create',
        'flag' => 'tags.create',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'tags.edit',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'tags.destroy',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Collection Settings',
        'flag' => 'collection.settings',
        'parent_flag' => 'plugins.collection',
    ],
];
