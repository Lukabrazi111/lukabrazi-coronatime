<?php

use App\Http\Livewire\ForgotPassword;
use App\Http\Livewire\Login;
use App\Http\Livewire\Register;
use App\Http\Livewire\ResetPassword;
use App\Mail\UserRegisteredMail;
use App\Models\User;
use App\Notifications\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

// Testing route
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/by-country', function () {
        return view('dashboard-by-country');
    })->name('dashboard.country');
});

Route::middleware('guest')->group(function () {
    // Route login
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [Login::class, 'store'])->name('login.store');

    // Route register
    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::post('register', [Register::class, 'store'])->name('register.store');

    // Route forgot-password
    Route::get('/forgot-password', function () {
        return view('forgot-password');
    })->name('forgot.password');

    Route::post('/forgot-password', [ForgotPassword::class, 'send'])->name('forgot-password.send');

    // Route reset-password
    Route::get('/reset-password/{token}', function ($token) {
        return view('reset-password', ['token' => $token, 'email' => request()->email]);
    })->name('reset-password.get');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|same:repeat_password',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'repeat_password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );


        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    })->name('reset-password.post');

    // Route password-changed
    Route::get('/password-changed', function () {
        return view('password-changed');
    })->name('password.changed');

    // Route account-confirm
    Route::get('/account-confirmed', function () {
        return view('account-confirmed');
    })->name('account.confirmed');
});

Route::get('/user/verify/{token}', [Register::class, 'verifyEmail'])->name('verify.email');
