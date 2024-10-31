<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controller;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\TranslatorController;
use App\Http\Controllers\PaymentStatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EditorController;
use Illuminate\Support\Facades\App;
use App\Models\User;
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

/*
Route::get('/', function () {
	return view('welcome');
});
*/
Route::get('set-locale/{locale}', function ($locale) {
	App::setLocale($locale);
	session()->put('locale', $locale);
	return redirect()->back();
})->middleware('checkLocale')->name('locale.setting');
Route::resource('/customers', CustomersController::class);
Route::get('', [FrontEndController::class, 'index']);
Route::get('faq', [FrontEndController::class, 'faq']);
Route::get('terms', [FrontEndController::class, 'terms']);
Route::post('authAjax', [AuthController::class, 'authAjax']);
Route::group(['prefix' => 'new-order'], function () {
	Route::get('select-billing', [FrontEndController::class, 'select_billing']);
	Route::get('ntd', [FrontEndController::class, 'ntd']);
	Route::get('usd', [FrontEndController::class, 'usd']);
	Route::get('order/{order_number}', [FrontEndController::class, 'order_quote']); 
	Route::get('quote/{order_number}', [FrontEndController::class, 'show_quote']); 
	Route::get('success/{order_number}', [FrontEndController::class, 'order_success']);
	Route::get('failed', [FrontEndController::class, 'order_failed']);
	
	//dynamic data
	Route::get('get-languages', [FrontEndController::class, 'getLanguages']);
	Route::get('get-languages-to/{code}', [FrontEndController::class, 'getLanguagesTo']);
	Route::get('get-language-combo-by-code', [FrontEndController::class, 'getlanguageComboByCode']);
	Route::post('uploadfile', [FrontEndController::class, 'uploadFile']);
	Route::get('invoice/{email}', [FrontEndController::class, 'invoice']);
});
Route::group(['prefix' => 'designation'], function () {
	Route::get('{type}/{language_combination}/{client}/{user}', function($type, $language_combination, $client_id, $user_id){
		$client = User::find($client_id);
		$combs = json_decode($client->language_combination);
		if($combs)
		{
			foreach($combs as $k => $v)
			{
				if($v->language == $language_combination)
				{
					$combs[$k]->$type = $user_id;
				}
			}
		}
		else
		{
			$combs[] = array(
				'language' => $language_combination,
				$type => $user_id
			);
		}
		$client->language_combination = json_encode($combs);
		if($client->save())
		{
			echo '<script>alert("指定成功");window.close()</script>';
		}
	});
});
Route::group(array('before' => 'auth.logout'), function () {
	Route::get('login', [FrontEndController::class, 'login'])->name('login');
	Route::get('logout', [FrontEndController::class, 'logout'])->name('logout');
	Route::get('register', [FrontEndController::class, 'register']);
	Route::post('authLogin', [AuthController::class, 'authLogin']);
	Route::get('logout', [AuthController::class, 'logout'])->name('logout');
	Route::post('user/registration', [UserController::class, 'registration']);
	Route::get('email-password-reset/{email}', [FrontEndController::class, 'email_password_reset']);
	Route::post('password-reset', [UserController::class, 'password_reset']);
});
Route::group(['middleware' => 'auth'], function(){
	Route::group(['middleware' => 'translator'], function(){
		Route::group(['prefix' => 'translator'], function () {
			Route::get('translator-bin', [TranslatorController::class, 'translator_bin']);
			Route::get('my-translations', [TranslatorController::class, 'my_translations']);
			Route::get('editor-bin', [TranslatorController::class, 'editor_bin']);
			Route::get('my-editing', [TranslatorController::class, 'my_editing']);
			Route::get('my-history', [TranslatorController::class, 'my_history']);
			Route::get('my-languages', [TranslatorController::class, 'my_languages']);
			Route::get('my-profile', [TranslatorController::class, 'my_profile']);
			Route::get('account/change-password', [TranslatorController::class, 'change_password']);
		});
	});
	Route::group(['prefix' => 'user'], function () {
		Route::get('change-password', [FrontEndController::class, 'change_password']);
	});
});
Route::post('/upload', function(Request $request) {
	dd(Storage::disk('google')->url($request->file("thing")->store(env('GOOGLE_DRIVE_FOLDER_ID'), "google")));
});
Route::post('api/trans_deliver', 'ApiController@trans_deliver');
Route::post('api/editor_deliver', 'ApiController@editor_deliver');
Route::post('api/change-payment', 'ApiController@change_payment');
Route::post('api/change-note', 'ApiController@change_note');
Route::post('api/do-notice', 'ApiController@do_notice');
Route::group(['prefix' => 'admin/stat'], function () {
	Route::get('payout', [PaymentStatController::class, 'payout']);
	Route::get('getPayoutDetails/{period}', [PaymentStatController::class, 'get_payout_details'])->name('getPayoutDetails');
});
