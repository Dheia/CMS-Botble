<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/v1',
    'namespace' => 'Botble\Collection\Http\Controllers\API',
], function () {
    Route::get('search', 'SubjectController@getSearch');
    Route::get('subjects', 'SubjectController@index');
    Route::get('taxons', 'TaxonController@index');

    Route::get('subjects/filters', 'SubjectController@getFilters');
    Route::get('subjects/{slug}', 'SubjectController@findBySlug');
    Route::get('taxons/filters', 'TaxonController@getFilters');
    Route::get('taxons/{slug}', 'TaxonController@findBySlug');
});
