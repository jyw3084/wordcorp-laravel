<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;
use App\Http\Controllers\ApiController;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    Route::get('order/get-language-combo-rate/{id}', 'OrderController@getLanguageComboRate');
    Route::get('order/get-translator-by-language-combo', 'OrderController@getTranslatorByLanguageCombo');
    Route::get('order/get-editor-by-language-combo', 'OrderController@getEditorByLanguageCombo');
    Route::resource('orders', 'OrderController');

    Route::resource('languages', 'LanguageController');
    Route::resource('language-combination', 'LanguageComboController');

    Route::resource('system-parameters', 'SystemParameterController');

    Route::resource('email-management', 'EmailManagementController');

    Route::get('client/get-translator-by-language-combo', 'UserController@getTranslatorByLanguageCombo');
    Route::resource('manage-clients', 'UserController');

    Route::resource('manage-translators', 'TranslatorController');

	Route::resource('docs', 'DocsController');

	Route::resource('recipient', 'RecipientController');

	Route::resource('payment', 'PaymentController');

    Route::post('order/count_words', 'OrderController@count_wrods');
});
