<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\DataPenghuni;

class ProfileController extends Controller
{
    public function index()
    {
        $penghuni = auth()->user()->datapenghuni ?? null;
        $user = Auth::user();

        if ($user->role === 'penghuni') {
            $profile = DataPenghuni::with('kamar')->where('id_user', $user->id)->first();

            return view('profile.index', [
                'user' => $user,
                'profile' => $profile,
                'penghuni' => $penghuni,
            ]);
        } else {
            return view('profile.index', [
                'user' => $user,
                'penghuni' => $penghuni,
            ]);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi data umum
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_telepon' => 'required|string|max:20',
        ];

        // Tambahan validasi untuk penghuni
        if ($user->role === 'penghuni') {
            $rules = array_merge($rules, [
                'nik' => 'required|string|max:20',
                'pekerjaan' => 'required|string|max:100',
                'alamat' => 'required|string|max:255',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update data pengguna
        $user->username = $request->input('nama_lengkap');
        $user->email = $request->input('email');
        $user->no_telepon = $request->input('no_telepon');
        $user->save();

        // Update atau buat data penghuni jika peran adalah penghuni
        if ($user->role === 'penghuni') {
            $dataPenghuni = DataPenghuni::firstOrNew(['id_user' => $user->id]);
            $dataPenghuni->nama_lengkap = $request->input('nama_lengkap');
            $dataPenghuni->nik = $request->input('nik');
            $dataPenghuni->alamat = $request->input('alamat');
            $dataPenghuni->no_telepon = $request->input('no_telepon');
            $dataPenghuni->pekerjaan = $request->input('pekerjaan');
            $dataPenghuni->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $foto = $request->file('foto');
        $filename = time() . '_' . $foto->getClientOriginalName();

        // Simpan ke folder public/images/photoprofile
        $foto->move(public_path('images/photoprofile'), $filename);

        // Hapus foto lama jika ada
        if ($user->foto && file_exists(public_path('images/photoprofile/' . $user->foto))) {
            unlink(public_path('images/photoprofile/' . $user->foto));
        }

        // Update nama file foto di database
        $user->foto = $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'foto' => asset('images/photoprofile/' . $filename),
        ]);
    }
}
