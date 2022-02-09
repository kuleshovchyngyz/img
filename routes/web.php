<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Controllers\AdminController;
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

Auth::routes(['middleware' => 'role:admin','register' => true,'reset' => true]);
//Auth::routes();

Route::get('/test', function () {
    $filesInFolder = \File::files(public_path('shortpixel/61efc9605926d/0'));
    foreach($filesInFolder as $path) {
        $file = pathinfo($path);
        dd(getimagesize($file['dirname'].'/'.$file['basename']));
        // getimagesize();
        
    }
    
});

Route::get('/', function () {
    return redirect('/login');
});
//Auth::routes();

Route::post('/dropzone-store', [App\Http\Controllers\DropZoneController::class, 'dropzoneStrore'])->name('dropzone.store');
Route::post('/dropzone-delete', [App\Http\Controllers\DropZoneController::class, 'dropzoneDelete']);
Route::post('/start-compress', [App\Http\Controllers\DropZoneController::class, 'dropzoneCompress']);
Route::get('/compress/{folder_id}', [App\Http\Controllers\DropZoneController::class, 'compress']);
Route::get('/status', [App\Http\Controllers\DropZoneController::class, 'status']);
Route::get('/a', [App\Http\Controllers\DropZoneController::class, 'perm']);
Route::get('/test/{folder}', [App\Http\Controllers\DropZoneController::class, 'countCompressed']);
//Route::get('/check-progress/{folder}', [App\Http\Controllers\DropZoneController::class, 'progress_percent']);
Route::get('home/{download_zip}', [App\Http\Controllers\DropZoneController::class, 'download_zip']);
Route::get('/shortpixel-file/{id}', [App\Http\Controllers\DropZoneController::class, 'shortpixel_file']);
Route::get('/get-image-sizes/{id}', [App\Http\Controllers\DropZoneController::class, 'get_post_image_sizes']);
Route::get('/get-image-sizes-after-compressed/{id}', [App\Http\Controllers\DropZoneController::class, 'get_post_image_sizes_after_compressed']);
Route::get('/insert-new-sizes/{folder_id}', [App\Http\Controllers\DropZoneController::class, 'insert_new_sizes']);
Route::get('/check-progress/{id}', [App\Http\Controllers\DropZoneController::class, 'countCompressed']);
Route::post('/dz-download-one', [App\Http\Controllers\DropZoneController::class, 'dz_download_one']);
Route::post('/upload-tiny', [App\Http\Controllers\DropZoneController::class, 'uploadTiny']);



Route::get('/home', 'HomeController@index')->name('home');



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix'=>'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.users');
    Route::get('/register-user', function (){ return view('auth.register'); })->name('admin.register.user');
});