<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login with NIS/NIP/email.
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'NIS atau ID belum diisi.',
            'password.required' => 'Kata sandi belum diisi.',
        ]);

        $identifier = $request->input('identifier');
        $password = $request->input('password');

        // Try login by NIS, NIP, or email
        $fields = ['nis', 'nip', 'email'];
        $authenticated = false;

        foreach ($fields as $field) {
            if (Auth::attempt([$field => $identifier, 'password' => $password], $request->boolean('remember'))) {
                $authenticated = true;
                break;
            }
        }

        if (! $authenticated) {
            return back()
                ->withInput($request->only('identifier', 'remember'))
                ->withErrors(['identifier' => 'NIS/NIP atau kata sandi belum cocok. Coba lagi, ya!']);
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard(Auth::user());
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show change password form.
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Handle password change.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Kata sandi baru belum diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = $request->user();
        $user->update([
            'password' => $request->input('password'),
            'must_change_password' => false,
        ]);

        return $this->redirectToDashboard($user);
    }

    /**
     * Redirect to the appropriate dashboard based on role.
     */
    protected function redirectToDashboard($user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => redirect()->route('guru.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect('/'),
        };
    }
}
