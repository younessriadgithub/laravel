<?php

use Illuminate\Http\Request;


Route::get('verify', 'VerificationController@verify');
Route::post('register', 'APIRegisterController@register');
Route::post('login', 'APILoginController@login');
Route::post('user', 'APIRegisterController@register');
Route::post('/checkpassword', 'APIRegisterController@checkpassword');
Route::post('/restpassword', 'APIRegisterController@restpassword');
Route::post('rest', 'APIRegisterController@rest');
Route::post('sendtoken', 'APIRegisterController@sendtoken');
Route::post('contact', 'APIRegisterController@contact');

Route::post('activeaccount', ['middleware' => 'jwt.auth', 'uses' => 'APIRegisterController@activeaccount']);


Route::get('profile', 'AuthjwtController@getAuthenticatedUser');
Route::get('updateprofile', 'AuthjwtController@updateprofile');
Route::post('isAdmin', 'AuthjwtController@admin');
Route::get('getrole', 'AuthjwtController@getRole');
Route::put('updateuser/{id}', 'AuthjwtController@updateuser');
Route::put('updateuserprofile', 'AuthjwtController@updateuserprofile');
Route::put('newpassword', 'AuthjwtController@newpassword');
Route::post('/changepassword', 'AuthjwtController@changepassword');
Route::get('verify', 'VerificatiAuthjwtControlleronController@verify');



Route::post('views/{page}/{count}', 'AuthjwtController@viewspag');

Route::post('users/{page}/{count}', 'AuthjwtController@userspag');

Route::post('properties/{page}/{count}', 'AuthjwtController@properties');




                  //****   Property API *****//
Route::post('newproperty', 'AuthjwtController@newproperty');
Route::get('property/{id}', 'AuthjwtController@propertybyid');
Route::put('property/{id}', 'AuthjwtController@updateproperty');

                  //****   Property API *****//


                  //****   VIEWS API *****//
Route::post('newview', 'AuthjwtController@newview');
Route::get('views', 'AuthjwtController@views');
Route::get('countviews', 'AuthjwtController@countviews');
Route::delete('views/{page}', 'AuthjwtController@deleteview');

                  //****   VIEWS API *****//




                  //****   CATEGORY  API *****//
Route::post('newcategory', 'AuthjwtController@newcategory');
Route::put('updatecategory/{id}', 'AuthjwtController@updatecategory');
Route::get('categories', 'AuthjwtController@categories');
Route::get('categorie/{id}', 'AuthjwtController@categorie');
Route::delete('categorie/{id}', 'AuthjwtController@deletecategorie');
Route::post('categories/{page}/{count}', 'AuthjwtController@categoriespag');

                  //****   CATEGORY API *****//





                  //****   statistic API *****//

Route::get('statistic', 'AuthjwtController@statistic');
Route::get('chartusers', 'AuthjwtController@chartusers');
Route::get('chartproperties', 'AuthjwtController@chartproperties');
Route::get('chartviews', 'AuthjwtController@chartviews');

                  //****   statistic API *****//


Route::post('getproperties/{page}/{count}', 'PublicauthController@properties');
Route::get('popularproperties', 'PublicauthController@popularproperties');
Route::get('recentsproperties', 'PublicauthController@recentsproperties');
Route::get('getproperty/{titleurl}', 'PublicauthController@getproperty');
Route::get('getcategories', 'PublicauthController@categories');
Route::get('getcities', 'PublicauthController@cities');

