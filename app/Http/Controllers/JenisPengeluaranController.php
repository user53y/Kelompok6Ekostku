<?php

namespace App\Http\Controllers;

use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;

class JenisPengeluaranController extends Controller
{
    public function index()
    {
        $jenisPengeluaran = JenisPengeluaran::all();
        return view('pemilik.master.jenis-pengeluaran', compact('jenisPengeluaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_pengeluaran' => 'required|string|max:255',
            'nama_pengeluaran' => 'required|string|max:255',
        ]);

        JenisPengeluaran::create($validated);

        if($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Jenis pengeluaran berhasil ditambahkan');
    }

    public function destroy($id)
    {
        try {
            $jenis = JenisPengeluaran::findOrFail($id);

            // Check if jenis is being used
            if($jenis->dataPengeluaran()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis pengeluaran ini sedang digunakan dan tidak dapat dihapus'
                ], 422);
            }

            $jenis->delete();
            return response()->json([
                'success' => true,
                'message' => 'Jenis pengeluaran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }
}
