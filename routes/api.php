<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('create-order', [APIController::class, 'createOrder']);
Route::post('update-order', [APIController::class, 'updateOrder']);
Route::post('paid-order', [APIController::class, 'paidOrder']);

Route::put('translator-assign-to-me/{id}', [APIController::class, 'translatorAssignToMe']);
Route::put('send-to-editor/{id}', [APIController::class, 'sendToEditor']);
Route::put('editor-assign-to-me/{id}', [APIController::class, 'editorAssignToMe']);
Route::put('send-to-client/{id}', [APIController::class, 'sendToClient']);

Route::get('pay/newebpay/{id}', [APIController::class, 'newebPay']);
Route::post('order_status/{id}', [APIController::class, 'order_status']);
Route::post('newebpay/return', [APIController::class, 'return']);
Route::post('newebpay/receive/notifyRes', [APIController::class, 'receive']);

Route::post('send-sms-code', [APIController::class, 'sendSmsCode']);