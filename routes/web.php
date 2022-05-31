<?php

use App\Http\Livewire\Dashboard\Users;
use App\Http\Livewire\Dashboard\Users\Create;
use App\Http\Livewire\Dashboard\Users\User;
use App\Http\Livewire\Forgotten;
use App\Http\Livewire\Login;
use App\Http\Livewire\Register;
use App\Http\Livewire\RenewPassword;
use App\Http\Livewire\Welcome;
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

Route::get('/', Welcome::class)->name('home');

Route::middleware(['auth.user'])->prefix('/')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgotten', Forgotten::class)->name('forgotten');
    Route::get('/renew-password/{token}', RenewPassword::class)->name('renew-password');

    Route::name('dashboard.')->prefix('/dashboard')->group(function () {
        Route::name('users.')->prefix('/users')->group(function () {
            Route::get('/', Users::class)->name('index');
            Route::get('/create', Create::class)->name('create');
            Route::get('/{user}', User::class)->name('edit');
        });

        Route::get('/logout', function () {
            request()->user->invalidateSession();

            return redirect()->route('login');
        })->name('logout');
    });
});
