<?php

use App\Models\TransportDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return 'test';
// });

Route::get('{path}', AppController::class)->where('path', '^(?!api).*$');
// Route::get('{path}', AppController::class)->where('path', '^(?!api|public).*$');
