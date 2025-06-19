<?php

namespace App\Http\Controllers;

use App\Models\DataPengeluaran;
use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        $datapengeluaran = DataPengeluaran::all();
        $jenisPengeluaran = JenisPengeluaran::all();
        return view('pemilik.datapengeluaran.index', compact('datapengeluaran', 'jenisPengeluaran'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_jenis' => 'required|exists:jenispengeluaran,id',
                'jumlah_pengeluaran' => 'required',
                'tanggal_pengeluaran' => 'required|date',
            ]);

            // Clean the jumlah_pengeluaran value
            $validated['jumlah_pengeluaran'] = (float) preg_replace('/[^0-9]/', '', $validated['jumlah_pengeluaran']);

            // Add id_user to validated data
            $validated['id_user'] = auth()->id();

            $pengeluaran = DataPengeluaran::create($validated);

            if($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengeluaran berhasil ditambahkan',
                    'data' => $pengeluaran
                ]);
            }

            return redirect()->route('tampil-pengeluaran');
        } catch (\Exception $e) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan pengeluaran: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menambahkan pengeluaran');
        }
    }

    public function edit($id)
    {
        $datapengeluaran = DataPengeluaran::findOrFail($id);
        return response()->json($datapengeluaran);
    }

    public function update(Request $request, $id)
    {
        try {
            $datapengeluaran = DataPengeluaran::findOrFail($id);

            $validated = $request->validate([
                'id_jenis' => 'required|exists:jenispengeluaran,id',
                'jumlah_pengeluaran' => 'required',
                'tanggal_pengeluaran' => 'required|date',
            ]);

            // Clean the jumlah_pengeluaran value
            $validated['jumlah_pengeluaran'] = (float) preg_replace('/[^0-9]/', '', $validated['jumlah_pengeluaran']);

            $datapengeluaran->update($validated);

            if($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('tampil-pengeluaran');
        } catch (\Exception $e) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate pengeluaran: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal mengupdate pengeluaran');
        }
    }

    public function show($id)
    {
        $datapengeluaran = DataPengeluaran::findOrFail($id);
        return response()->json($datapengeluaran);
    }

    public function destroy(DataPengeluaran $datapengeluaran)
    {
        $datapengeluaran->delete();
        return redirect()->route('tampil-pengeluaran')->with('success', 'Pengeluaran berhasil dihapus');
    }

    public function storeJenis(Request $request)
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

    public function destroyJenis($id)
    {
        $jenis = JenisPengeluaran::findOrFail($id);

        try {
            // Check if jenis is being used
            $isUsed = $jenis->dataPengeluaran()->exists();

            if($isUsed) {
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

    public function bulkDelete(Request $request)
    {
        try {
            DataPengeluaran::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
