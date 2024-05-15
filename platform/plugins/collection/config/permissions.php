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
        'name' => 'Categories',
        'flag' => 'categories.index',
        'parent_flag' => 'plugins.collection',
    ],
    [
        'name' => 'Create',
        'flag' => 'categories.create',
        'parent_flag' => 'categories.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'categories.edit',
        'parent_flag' => 'categories.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'categories.destroy',
        'parent_flag' => 'categories.index',
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
