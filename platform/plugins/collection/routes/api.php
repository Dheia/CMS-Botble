<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/v1',
    'namespace' => 'Botble\Collection\Http\Controllers\API',
], function () {
    Route::get('search', 'SubjectController@getSearch');
    Route::get('subjects', 'SubjectController@index');
    Route::get('categories', 'CategoryController@index');
    Route::get('tags', 'TagController@index');

    Route::get('subjects/filters', 'SubjectController@getFilters');
    Route::get('subjects/{slug}', 'SubjectController@findBySlug');
    Route::get('categories/filters', 'CategoryController@getFilters');
    Route::get('categories/{slug}', 'CategoryController@findBySlug');
});
