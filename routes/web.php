<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    TamuUmumController,
    OrangTuaController,
    InstansiController,
    KunjunganController,
    ProfileController,
    LoginController,
    UserController,
    GuestController
};

/*
|--------------------------------------------------------------------------
| 🌐 Halaman Publik (Tanpa Login)
|--------------------------------------------------------------------------
| Ini bisa diakses siapa saja, termasuk tamu.
*/

// Landing Page
Route::get('/', [GuestController::class, 'landing'])->name('landing');

Route::prefix('tamu')->group(function () {
    Route::get('/instansi', [GuestController::class, 'instansi'])->name('guest.instansi');
    Route::post('/instansi', [GuestController::class, 'storeInstansi'])->name('guest.instansi.store');

    Route::get('/umum', [GuestController::class, 'umum'])->name('guest.umum');
    Route::post('/umum', [GuestController::class, 'storeUmum'])->name('guest.umum.store');

    Route::get('/ortu', [GuestController::class, 'ortu'])->name('guest.ortu');
    Route::post('/ortu', [GuestController::class, 'storeOrtu'])->name('guest.ortu.store');
});


/*
|--------------------------------------------------------------------------
| 👥 Form Registrasi User (Publik)
|--------------------------------------------------------------------------
*/
Route::get('/user/form', [UserController::class, 'create'])->name('user.form');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');

/*
|--------------------------------------------------------------------------
| 📤 Export Data Tamu (Bisa publik)
|--------------------------------------------------------------------------
*/
Route::get('/tamu/export', [TamuUmumController::class, 'export'])->name('tamu.export');

/*
|--------------------------------------------------------------------------
| 🔐 Login & Register (Hanya untuk yang belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register']);
});

// Dashboard utama
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/latest-meta', [DashboardController::class, 'latestMeta'])->name('latest_meta');
    Route::get('/stream', [DashboardController::class, 'stream'])->name('stream');
});
/*
|--------------------------------------------------------------------------
| 🚪 Logout (Hanya untuk yang login)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| 🧭 Protected Routes (Hanya untuk admin yang login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard utama
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/latest-meta', [DashboardController::class, 'latestMeta'])->name('latest_meta');
        Route::get('/stream', [DashboardController::class, 'stream'])->name('stream');
    });

    // Modul Tamu Umum
    Route::prefix('tamu-umum')->name('tamu_umum.')->group(function () {
        Route::get('/', [TamuUmumController::class, 'index'])->name('index');
        Route::get('/create', [TamuUmumController::class, 'create'])->name('create');
        Route::post('/store', [TamuUmumController::class, 'store'])->name('store');
        Route::get('/{tamu_umum}/edit', [TamuUmumController::class, 'edit'])->name('edit');
        Route::put('/{tamu_umum}', [TamuUmumController::class, 'update'])->name('update');
        Route::delete('/{tamu_umum}', [TamuUmumController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [TamuUmumController::class, 'export'])->name('export.excel');
    });

    // Modul Orang Tua
    Route::prefix('ortu')->name('ortu.')->group(function () {
        Route::get('/', [OrangTuaController::class, 'index'])->name('index');
        Route::get('/create', [OrangTuaController::class, 'create'])->name('create');
        Route::post('/store', [OrangTuaController::class, 'store'])->name('store');
        Route::get('/{ortu}/edit', [OrangTuaController::class, 'edit'])->name('edit');
        Route::put('/{ortu}', [OrangTuaController::class, 'update'])->name('update');
        Route::delete('/{ortu}', [OrangTuaController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [OrangTuaController::class, 'export'])->name('export.excel');
    });

    // Modul Instansi
    Route::prefix('instansi')->name('instansi.')->group(function () {
        Route::get('/', [InstansiController::class, 'index'])->name('index');
        Route::get('/create', [InstansiController::class, 'create'])->name('create');
        Route::post('/store', [InstansiController::class, 'store'])->name('store');
        Route::get('/{instansi}/edit', [InstansiController::class, 'edit'])->name('edit');
        Route::put('/{instansi}', [InstansiController::class, 'update'])->name('update');
        Route::delete('/{instansi}', [InstansiController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [InstansiController::class, 'export'])->name('export.excel');
    });

    // Modul Kunjungan
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan');

    // Modul Profile (opsional)
    // Route::prefix('profile')->name('profile.')->group(function () {
    //     Route::get('/', [ProfileController::class, 'index'])->name('index');
    //     Route::post('/update', [ProfileController::class, 'update'])->name('update');
    //     Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    // });
});
