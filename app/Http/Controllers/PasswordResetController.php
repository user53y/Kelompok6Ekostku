<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PasswordResetController
{
    public function sendResetLink(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            Log::info('Attempting to send password reset link', ['email' => $request->email]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('Password reset link sent successfully', ['email' => $request->email]);
                Alert::success('Berhasil', 'Link reset password telah dikirim ke email Anda');
                return back()->with(['status' => __($status)]);
            }

            Log::warning('Failed to send password reset link', [
                'email' => $request->email,
                'status' => $status
            ]);
            Alert::error('Error', __($status));
            return back()->withErrors(['email' => __($status)]);

        } catch (\Exception $e) {
            Log::error('Exception while sending reset link', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            Alert::error('Error', 'Terjadi kesalahan saat mengirim link reset password');
            return back()->withErrors(['email' => 'Terjadi kesalahan saat mengirim link reset password']);
        }
    }

    public function create(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->route('token'),
            'email' => $request->email
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            Log::info('Attempting to reset password', ['email' => $request->email]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                    Log::info('Password reset successful', ['user_id' => $user->id]);
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                Log::info('Password reset completed', ['email' => $request->email]);
                Alert::success('Berhasil', 'Password berhasil direset!');
                return redirect()->route('login');
            }

            Log::warning('Password reset failed', [
                'email' => $request->email,
                'status' => $status
            ]);
            Alert::error('Error', __($status));
            return back()->withErrors(['email' => __($status)]);

        } catch (\Exception $e) {
            Log::error('Exception during password reset', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            Alert::error('Error', 'Terjadi kesalahan saat mereset password');
            return back()->withErrors(['email' => 'Terjadi kesalahan saat mereset password']);
        }
    }
}
