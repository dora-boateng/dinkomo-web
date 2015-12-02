<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


//
// Static pages.
//
Route::get('/', 'PageController@home')->name('home');
Route::get('about', 'PageController@about')->name('about');
Route::get('about/in-numbers', 'PageController@stats')->name('stats');
Route::get('about/author', 'PageController@author')->name('author');


//
// Admin area.
//
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function()
{
    // General pages
    Route::get('/',         ['as' => 'admin', 'uses' => 'AdminController@index']);
    Route::get('import',    ['as' => 'admin.import', 'uses' => 'AdminController@import']);
    Route::get('export',    ['as' => 'admin.export', 'uses' => 'AdminController@export']);
    // Route::get('list/def',  'AdminController@getDefinitionList')->name('admin.list.definitions');
    Route::get('list/def',  ['as' => 'admin.list.definitions', 'uses' => 'AdminController@getDefinitionList']);
    // Route::get('list/lang', 'AdminController@getLanguageList')->name('admin.list.languages');
    Route::get('list/lang', ['as' => 'admin.list.languages', 'uses' => 'AdminController@getLanguageList']);

    // Resources
    Route::resource('language',     'LanguageController');
    Route::resource('definition',   'DefinitionController');
    Route::resource('translation',  'TranslationController');
    Route::resource('audio',        'AudioController');

    // Resource import.
    Route::post('import',
        ['as' => 'admin.import.action', 'uses' => 'Data\v041\DataController@import']);

    // Resource export
    Route::get('export/{resource}.{format}',
        ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
});


//
// API v 0.1
//
Route::group(['prefix' => '0.1', 'namespace' => 'API\v01', 'middleware' => ['api.headers', 'api.auth']], function()
{
    Route::get('/', function() {
        return 'Di Nkɔmɔ API 0.1';
    });

    // Definition endpoints.
    Route::get('{definitionType}/count', 'ApiController@count');
    Route::get('{definitionType}/search/{query}', 'ApiController@search');
    Route::get('{definitionType}/title/{title}', 'DefinitionController@findBytitle');
    Route::options('definition/{id?}', 'ApiController@options');
    Route::resource('definition', 'DefinitionController', ['except' => ['create', 'edit']]);

    // Language endpoints.
    Route::resource('language', 'LanguageController', ['except' => ['create', 'store', 'destroy']]);

    // Authentication endpoints.
    Route::post('auth/local', 'AuthController@login');
    Route::options('auth/local', 'ApiController@options');

    // General lookup
    Route::get('search/{query}', 'ApiController@searchAllResources');
});


//
// Authentication routes.
//
Route::group(['prefix' => 'auth'], function()
{
    Route::get('/', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@getLogin']);
    Route::post('/', ['as' => 'auth.login.post', 'uses' => 'Auth\AuthController@postLogin']);
    Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);
});

// OAuth2...
Route::group(['prefix' => 'oauth2'], function()
{

});

// Redirects.
Route::get('home', function() { return redirect(route('home')); });
Route::get('stats', function() { return redirect(route('stats')); });
Route::get('in-numbers', function() { return redirect(route('stats')); });
