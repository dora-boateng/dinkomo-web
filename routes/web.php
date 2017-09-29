<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix'     => Localization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect']
], function() {
    // General routes
    Route::get('/',         'PageController@home')->name('home');
    Route::get('/search',   'PageController@search')->name('search');
    Route::get('/search.xml',   'PageController@openSearchDescription');
    Route::get('/about',    'PageController@about')->name('about');

    // Definition routes
    Route::get('/gem/+/{type?}/{lang?}', 'DefinitionController@create')->name('definition.create');
    Route::get('/random/{lang?}', 'DefinitionController@random')->name('definition.random');
    Route::resource('gem', 'DefinitionController', [
        'except' => ['index', 'create'],
        'names' => [
            'store' => 'definition.store',
            'show' => 'definition.show',
            'edit' => 'definition.edit',
            'update' => 'definition.update',
            'destroy' => 'definition.destroy',
        ]
    ]);

    // Language routes
    Route::get('/lang/{code}', 'LanguageController@show')->name('language');

    // Member routes
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login')->name('login.post');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('member', 'MemberController@index');
    Route::get('member/settings', 'MemberController@settings');

    // TEST: API helper routes
    Route::get('/api/check-title/{lang}/{title}', 'ApiController@checkTitle');
});

// Miscellaneous
Route::get('/auth/{service}', 'PageController@notImplemented')->name('oauth');
Route::get('/map', 'PageController@sitemap')->name('sitemap');
Route::get('/_noga', 'PageController@setNoTrackingCookie');
