<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Models\Cinema;
use Illuminate\Support\Facades\Route;


Route::get('/', [MovieController::class, 'home'])->name('home');
route::get('/movies/all', [MovieController::class, 'homeAllMovie'])->name('home.movies.all');
//tidak memerlukan data : route - view
//memerlukan data : route - controller - model - controller - view

Route::get('/schedules/{movie_id}', [MovieController::class, 'movieSchedules'])->name('schedules.detail');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/signup', function () {
    return view('signup');
})->name('signup')->middleware('isGuest');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('isGuest');

Route::post('/signup', [UserController::class, 'store'])->name('signup.store')->middleware('isGuest');
Route::post('/login', [UserController::class, 'login'])->name('login.auth')->middleware('isGuest');


//http methods :
//get = untuk mengambil data
//post = untuk mengirim data
//patch/put = untuk mengupdate data
//delete = untuk menghapus data

route::post('/signup', [UserController::class, 'store'])->name('signup.store');
route::post('/login', [UserController::class, 'login'])->name('login.auth');
route::get('/logout', [UserController::class, 'logout'])->name('logout');

//prefix : awalan, semua route yg ada di dlm group ini akan diawali /admin untuk url nya dengan pemanggilan href nya akan diawlai dengan (admin.) sesuai name
//prefix digunakan ketika path akan digunakan berulang-ulang (di beberapa route) untuk mempermudah penulisan pakai prefix
//tanpa prefix
// route::get('/admin/dashboard' .....)->name('admin.dashboard')
// route::get('/admin/cinemas' .....)->name('admin.cinemas')
//dengan prefix
//route::prefix('/admin')->name('admin.')->group(function(){
//    route::get('/dashboard' .....)->name('dashboard')
//    route::get('/cinemas' .....)->name('cinemas')
//})
route::prefix('/admin')->name('admin.')->group(function () {
    route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    //cinemas
    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        //{id} = parameter placeholder, digunakan untuk mengirim data ke controller
        //digunakan untuk mencari spesifikasi data
        //{id} = id, karena uniknya (PK) ada di id
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::get('/export', [CinemaController::class, 'exportExcel'])->name('export');
        Route::delete('/{id}', [CinemaController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        // mengubah jd dikembalikan ke blm terhapus (bukan sampah)
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        // menghapus dari db
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [CinemaController::class, 'dataForDatatables'])->name('datatables');
    });
    //users
    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'storeAdmin'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::get('/export', [UserController::class, 'exportExcel'])->name('export');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        // mengubah jd dikembalikan ke blm terhapus (bukan sampah)
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        // menghapus dari db
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [UserController::class, 'dataForDatatables'])->name('datatables');
    });

    //films
    Route::prefix('/movies')->name('movies.')->group(function () {
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::get('/export', [MovieController::class, 'exportExcel'])->name('export');
        Route::delete('/{id}', [MovieController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}', [MovieController::class, 'patch'])->name('patch');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        // mengubah jd dikembalikan ke blm terhapus (bukan sampah)
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        // menghapus dari db
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [MovieController::class, 'dataForDatatables'])->name('datatables');
    });
});


// akses petugas

Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function () {
    Route::prefix('/promos')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::get('/export', [PromoController::class, 'exportExcel'])->name('export');
        Route::delete('/{id}', [PromoController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}', [PromoController::class, 'patch'])->name('patch');
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        // mengubah jd dikembalikan ke blm terhapus (bukan sampah)
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        // menghapus dari db
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [PromoController::class, 'dataForDatatables'])->name('datatables');
    });

    Route::prefix('/schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        // PUT memungkinkan perubahan semua data, PATCH : perubahan hanya pada beberapa data
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [ScheduleController::class, 'destroy'])->name('destroy');
        // recycle-bin
        // memunculkan data sampah
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        // mengubah jd dikembalikan ke blm terhapus (bukan sampah)
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        // menghapus dari db
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('export');
        Route::get('/datatables', [ScheduleController::class, 'dataForDatatables'])->name('datatables');
    });
});
