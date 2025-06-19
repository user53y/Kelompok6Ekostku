<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller
{
    /**
     * Redirect ke provider (Google/Facebook)
     */
    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Callback dari provider
     */
    public function callback($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('auth')->with('error', 'Gagal login dengan ' . ucfirst($provider));
        }

        // Cek apakah user sudah ada berdasarkan email
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Jika user belum ada, buat user baru dengan role 'penghuni'
            $foto = null;
            if ($socialUser->getAvatar()) {
                try {
                    $avatarUrl = $socialUser->getAvatar();
                    $ext = pathinfo(parse_url($avatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $filename = 'soc_' . uniqid() . '_' . time() . '.' . $ext;
                    $imgData = file_get_contents($avatarUrl);
                    $savePath = public_path('images/photoprofile/' . $filename);
                    file_put_contents($savePath, $imgData);
                    $foto = $filename;
                } catch (\Exception $e) {
                    $foto = null;
                }
            }
            $user = User::create([
                'username' => $this->generateUsername($socialUser),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(16)),
                'role' => 'penghuni',
                'foto' => $foto,
            ]);
        }

        // Login otomatis
        Auth::login($user, true);

        // Redirect berdasarkan role
        if ($user->role === 'pemilik') {
            return redirect()->route('dashboard.pemilik');
        }

        return redirect()->route('dashboard.penghuni');
    }

    /**
     * Generate username unik dari nama akun sosial
     */
    protected function generateUsername($socialUser)
    {
        $base = $socialUser->getNickname() ?: Str::slug($socialUser->getName(), '_');
        $base = $base ?: 'user';
        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }
}
