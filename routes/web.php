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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', 'HomeController@index');

//Route::get('/instag', 'PublicController@instag');

Route::get('/tags', 'PublicController@tags');

Route::get('/mygalleries', 'GalleryController@mygalleries');

Route::post('/addImg2Gal', 'GalleryController@addImg2Gal');
//Route::get('/{username}', 'HomeController@images');

Route::get('/search', 'PublicController@search');

Route::post('/imgcomment', 'GalleryController@imgcomment');

Route::get('/galinfo/{id}','GalleryController@galinfo');

Route::post('/galupdate','GalleryController@galupdate');

Route::post('/galdelete','GalleryController@galdelete');

Route::post('/imglike','GalleryController@imglike');

Route::post('/imgupdate', 'GalleryController@imgupdate');

Route::post('/imgdelete', 'GalleryController@imgdelete');

Route::get('/{username}', 'HomeController@images');

Route::get('/{username}/images', 'HomeController@images');

Route::get('/{username}/galleries', 'HomeController@galleries');

Route::get('/a', 'GalleryController@showUpLoadForm');

Route::post('/upload', 'GalleryController@upload');

Route::post('/galcreate', 'GalleryController@create');

Route::get('/galleries/{id}', 'GalleryController@images');

Route::get('/galimages/{id}', 'PublicController@galimages');

Route::get('/images/{url}', 'PublicController@viewImage');
