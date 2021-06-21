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

//router to show project plan
Route::get('/projectplan', 'ProjectPlanController@index');

//router to show ball assignment
Route::get('/projectplan/ballassignment', 'ProjectPlanController@ballassignment');
