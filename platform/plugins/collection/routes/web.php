<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Collection\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'collection'], function () {
            Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {
                Route::resource('', 'SubjectController')
                    ->parameters(['' => 'subject']);

                Route::get('widgets/recent-subjects', [
                    'as' => 'widget.recent-subjects',
                    'uses' => 'SubjectController@getWidgetRecentSubjects',
                    'permission' => 'subjects.index',
                ]);
            });

            Route::group(['prefix' => 'taxons', 'as' => 'taxons.'], function () {
                Route::resource('', 'TaxonController')
                    ->parameters(['' => 'taxon']);

                Route::put('update-tree', [
                    'as' => 'update-tree',
                    'uses' => 'TaxonController@updateTree',
                    'permission' => 'taxons.index',
                ]);
            });
        });

        Route::group(['prefix' => 'settings/collection', 'as' => 'collection.settings', 'permission' => 'collection.settings'], function () {
            Route::get('/', [
                'uses' => 'Settings\CollectionSettingController@edit',
            ]);

            Route::put('/', [
                'as' => '.update',
                'uses' => 'Settings\CollectionSettingController@update',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function () {
            Route::get('subject_search', [
                'as' => 'public.subject_search',
                'uses' => 'PublicController@getSearch',
            ]);
        });
    }
});
