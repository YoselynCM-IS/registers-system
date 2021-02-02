<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::name('files.')->prefix('files')->group(function () {
    Route::post('save_file', 'FileController@store')->name('save_file');
    Route::get('download_file/{id}', 'FileController@download' )->name('download_file');
});

Route::name('folios.')->prefix('folios')->group(function () {
    Route::get('search_folios', 'FolioController@search_folios')->name('search_folios');
    Route::post('get_folio', 'FolioController@get_folio')->name('get_folio');
    Route::post('load_folios', 'FolioController@store')->name('load_folios');
    Route::get('show_folio', 'FolioController@show' )->name('show_folio');
    Route::get('by_date', 'FolioController@by_date' )->name('by_date');
    Route::get('by_date_abono', 'FolioController@by_date_abono' )->name('by_date_abono');
});

Route::name('student.')->prefix('student')->group(function () {
    Route::get('/show_registers', 'StudentController@show_registers' )->name('show_registers');
    Route::get('books_to_email', 'StudentController@books_to_email' )->name('books_to_email');

    Route::get('/register', 'StudentController@register' )->name('register');
    // Route::post('save_student', 'StudentController@store')->name('save_student');
    Route::post('preregister', 'StudentController@store')->name('preregister');
    Route::get('/consult_data/{date}/{id}', 'StudentController@consult_data' )->name('consult_data');
    Route::get('/download_emails/{school}/{book}/{type}', 'StudentController@download_emails' )->name('download_emails');
    Route::get('/download_all/{school}', 'StudentController@download_all' )->name('download_all');
    Route::post('send_codes', 'StudentController@send_codes')->name('send_codes');

    Route::delete('delete', 'StudentController@delete')->name('delete');
    Route::get('/download_tutorial', 'StudentController@download_tutorial' )->name('download_tutorial');

    Route::get('/show_students', 'StudentController@show_students' )->name('show_students');
    Route::get('/by_school', 'StudentController@by_school' )->name('by_school');

    Route::put('update_status', 'StudentController@update_status')->name('update_status');
    Route::put('update_delivery', 'StudentController@update_delivery')->name('update_delivery');

    Route::put('mark_delivery', 'StudentController@mark_delivery')->name('mark_delivery');
});

Route::name('schools.')->prefix('schools')->group(function () {
    Route::get('schools_to_email', 'SchoolController@schools_to_email' )->name('schools_to_email');
    Route::get('show_schools', 'SchoolController@show_schools' )->name('show_schools');
    Route::get('show_school', 'SchoolController@show' )->name('show_school');
    Route::get('get_schools', 'SchoolController@get_schools' )->name('get_schools');
    Route::post('new_school', 'SchoolController@store')->name('new_school');
    Route::put('update_school', 'SchoolController@update')->name('update_school');
    Route::get('get_books', 'SchoolController@get_books' )->name('get_books');
});

Route::name('registros.')->prefix('registros')->group(function () {
    Route::get('by_date', 'RegistroController@by_date' )->name('by_date');
    Route::get('by_type', 'RegistroController@by_type' )->name('by_type');
    Route::get('by_folio', 'RegistroController@by_folio' )->name('by_folio');
    Route::get('by_total', 'RegistroController@by_total' )->name('by_total');
    Route::get('by_book', 'RegistroController@by_book' )->name('by_book');
    Route::get('by_status', 'RegistroController@by_status' )->name('by_status');
    Route::get('by_student', 'RegistroController@by_student' )->name('by_student');
    Route::get('download/{temporal1}', 'RegistroController@download' )->name('download');
    Route::get('download_status/{status}', 'RegistroController@download_status' )->name('download_status');
    Route::put('update_status', 'RegistroController@update_status')->name('update_status');
    Route::put('update_rejected', 'RegistroController@update_rejected')->name('update_rejected');
    Route::put('resend_mail', 'RegistroController@resend_mail')->name('resend_mail');
});

Route::name('administrator.')->prefix('administrator')
->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/files', 'AdministratorController@files' )->name('files');
    Route::get('/folios', 'AdministratorController@folios' )->name('folios');
    Route::get('/registers', 'AdministratorController@registers' )->name('registers');
});

Route::name('manager.')->prefix('manager')
->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/folios', 'ManagerController@folios' )->name('folios');
    Route::get('/files', 'ManagerController@files' )->name('files');
    Route::get('/registers', 'ManagerController@registers' )->name('registers');
});

Route::name('reviewer.')->prefix('reviewer')
->middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/registers', 'ReviewerController@registers' )->name('registers');
    Route::get('/codes', 'ReviewerController@codes' )->name('codes');
    Route::get('/schools', 'ReviewerController@schools' )->name('schools');
    Route::get('/books', 'ReviewerController@books' )->name('books');
    Route::get('/folios', 'ReviewerController@folios' )->name('folios');
});

Route::name('books.')->prefix('books')->group(function () {
    Route::post('new_book', 'BookController@store')->name('new_book');
    Route::put('update_book', 'BookController@update')->name('update_book');
    Route::post('assign_book', 'BookController@assign_book')->name('assign_book');
    Route::get('get_schools', 'BookController@get_schools' )->name('get_schools');
    Route::get('show_books', 'BookController@show_books' )->name('show_books');
    Route::put('update_price', 'BookController@update_price')->name('update_price');
    Route::get('get_editoriales', 'BookController@get_editoriales' )->name('get_editoriales');
});

Route::name('capturist.')->prefix('capturist')
->middleware(['auth', 'role:capturist'])->group(function () {
    Route::get('registers', 'CapturistController@registers' )->name('registers');
});
