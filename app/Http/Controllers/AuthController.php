<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /** Flowchart: Login valid? -> simpan sesi -> kembali ke halaman asal */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('popup', [
            'type' => 'login',
            'title' => 'Berhasil masuk!',
            'message' => 'Halo, '.Auth::user()->name.'. Keranjang dan pesananmu sudah siap.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('popup', [
            'type' => 'login',
            'title' => 'Akun berhasil dibuat!',
            'message' => 'Halo, '.$user->name.'. Kamu sudah masuk dan bisa langsung belanja.',
        ]);
    }

    public function logout(Request $request)
    {
        $name = $request->user()?->name;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('popup', [
            'type' => 'logout',
            'title' => 'Berhasil keluar',
            'message' => 'Sampai jumpa'.($name ? ', '.$name : '').'. Masuk lagi kapan saja untuk lanjut belanja.',
        ]);
    }
}
