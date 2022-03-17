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
Route::post('home/testauction', 'HomeController@testauction')->name('home.testauction');

Route::post('home/makeoffer', 'HomeController@makeoffer')->name('home.makeoffer');

Route::post('home/deleteauction', 'HomeController@deleteauction')->name('home.deleteauction');

Route::post('home/initmanualauction', 'HomeController@initmanualauction')->name('home.initmanualauction');

Route::post('home/closeauction', 'HomeController@closeauction')->name('home.closeauction');

Route::post('home/cancelauction', 'HomeController@cancelauction')->name('home.cancelauction');

Route::post('home/loadauction', 'HomeController@loadauction')->name('home.loadauction');

Route::post('home/generatepwd', 'HomeController@generatepwd')->name('home.generatepwd');

Route::post('home/updatePlayerPrice', 'HomeController@updatePlayerPrice')->name('home.updatePlayerPrice');

Route::post('home/postdata','HomeController@postdata')->name('home.postdata');

Route::get('home/searchplayer','HomeController@searchplayer')->name('home.searchplayer');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/settings', 'AuctionSettingsController@index')->name('settings');

Route::post('settings/addplayertofavs','AuctionSettingsController@addPlayerToFavs')->name('settings.addplayertofavs');

Route::post('settings/deletefavs','AuctionSettingsController@deleteFavs')->name('settings.deletefavs');
/*
Route::get('/events', function () {
    return view('events');
});

Route::get('home/generatepwd',function($secret){
    //$password = Hash::make('secret');
    $password = Hash::make($secret);
    print_r($password);
    return $password;
});*/

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('logout', function ()
{
    auth()->logout();
    Session()->flush();

    return Redirect::to('/');
})->name('logout');
