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

            Route::group(['prefix' => 'taxon', 'as' => 'taxon.'], function () {
                Route::resource('', 'TaxonController')
                    ->parameters(['' => 'taxon']);

                Route::put('update-tree', [
                    'as' => 'update-tree',
                    'uses' => 'TaxonController@updateTree',
                    'permission' => 'taxon.index',
                ]);
            });

            Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
                Route::resource('', 'TagController')
                    ->parameters(['' => 'tag']);

                Route::get('all', [
                    'as' => 'all',
                    'uses' => 'TagController@getAllTags',
                    'permission' => 'tags.index',
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
            Route::get('search', [
                'as' => 'public.search',
                'uses' => 'PublicController@getSearch',
            ]);
        });
    }
});
