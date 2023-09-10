<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormsController;

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

Route::get('/forms',[FormsController::class,'forms']);//Формы
Route::prefix('forms')->group(function() {
    Route::get('/reports',[FormsController::class,'forms']);//Список форм
    Route::get('/new_forms',[FormsController::class,'forms']);//Создание новой формы
    Route::post('/save_form',[FormsController::class,'save_form']);//Сохранение формы
    Route::post('/get_forms',[FormsController::class,'get_forms']);//Получение списка форм
    Route::get('/get_form',[FormsController::class,'get_form']);//Получение формы
    Route::get('/edit_forms',[FormsController::class,'forms']);
    Route::post('/delete_form',[FormsController::class,'delete_form']);//Удаление формы
    Route::get('/fill_form',[FormsController::class,'forms']);//Заполнение формы
    Route::post('/save_report',[FormsController::class,'save_report']);//Сохранение заполненной формы
    Route::get('/view_result_form',[FormsController::class,'forms']);//Просмотр результатов формы
    Route::post('/get_result_form',[FormsController::class,'get_result_form']);//Получение результатов формы
    Route::get('/get_excel/{id}',[FormsController::class,'get_excel']);//Получение excel
    Route::get('/get_word/{id}',[FormsController::class,'get_word']);//Получение word
});