<?php

namespace App\Http\Controllers;

use App\Models\Datakamar;
use Illuminate\Http\Request;
use App\Exports\KamarExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DatakamarController extends Controller
{
    public function index()
    {
        $datakamar = Datakamar::all();
        return view('pemilik.datakamar.index', compact('datakamar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kamar' => 'required|string|unique:datakamar',
            'tipe' => 'required|string',
            'luas' => 'required|string',
            'lantai' => 'required|integer',
            'kapasitas' => 'required|integer',
            'harga_bulanan' => 'required|numeric',
            'status' => 'required|string',
            'gambar.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $imageNames = [];

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        $validated['gambar'] = implode(',', $imageNames);
        $datakamar = Datakamar::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil ditambahkan',
                'data' => $datakamar
            ]);
        }

        return redirect()->route('tampil-kamar')->with('success', 'Kamar berhasil ditambahkan');
    }

    public function edit($id)
    {
        $datakamar = Datakamar::findOrFail($id);
        return response()->json($datakamar);
    }

    public function update(Request $request, $id)
    {
        $datakamar = Datakamar::findOrFail($id);

        $validated = $request->validate([
            'tipe' => 'required|string',
            'luas' => 'required|string',
            'lantai' => 'required|integer',
            'kapasitas' => 'required|integer',
            'harga_bulanan' => 'required|numeric',
            'status' => 'required|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        Log::info("Memulai update kamar ID: $id", [
            'validated_data' => $validated
        ]);

        $imageNames = [];

        if ($request->hasFile('gambar')) {
            if (empty($datakamar->gambar)) {
                Log::info("Tidak ada gambar lama untuk dihapus pada kamar ID: $id");
            }

            $oldImages = explode(',', $datakamar->gambar);
            foreach ($oldImages as $oldImage) {
                $oldImage = trim($oldImage);
                if (!empty($oldImage)) {
                    $oldImagePath = public_path('images/' . $oldImage);
                    if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                        unlink($oldImagePath);
                        Log::info("Gambar lama dihapus", ['nama_file' => $oldImage]);
                    } else {
                        Log::warning("Gagal hapus gambar lama (tidak ditemukan atau bukan file)", ['nama_file' => $oldImage]);
                    }
                }
            }

            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $imageNames[] = $imageName;
                Log::info("Gambar baru diupload", ['nama_file' => $imageName]);
            }

            $validated['gambar'] = implode(',', $imageNames);
        }

        $datakamar->update($validated);
        Log::info("Data kamar berhasil diperbarui", [
            'kamar_id' => $id,
            'data_akhir' => $datakamar->toArray()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kamar berhasil diperbarui'
            ]);
        }

        return redirect()->route('tampil-kamar')->with('success', 'Data kamar berhasil diperbarui');
    }

    public function show($id)
    {
        $datakamar = Datakamar::findOrFail($id);
        return response()->json($datakamar);
    }

    public function view($id)
    {
        $datakamar = Datakamar::findOrFail($id);
        $images = $datakamar->gambar ? explode(',', $datakamar->gambar) : [];
        $datakamar->harga_formatted = 'Rp ' . number_format($datakamar->harga_bulanan, 0, ',', '.');

        return response()->json([
            'success' => true,
            'data' => $datakamar,
            'images' => $images
        ]);
    }

    public function destroy($id)
    {
        $datakamar = Datakamar::findOrFail($id);

        $images = explode(',', $datakamar->gambar);
        foreach ($images as $image) {
            $image = trim($image);
            if (!empty($image) && file_exists(public_path('images/' . $image))) {
                unlink(public_path('images/' . $image));
            }
        }

        $datakamar->delete();
        return redirect()->route('tampil-kamar')->with('success', 'Kamar berhasil dihapus');
    }

    public function pdf()
    {
        $data = Datakamar::all();
        return view('pemilik.datakamar.pdf', ['datakamar' => $data]);
    }

    public function kamarTersedia()
    {
        $penghuni = Auth::user()->datapenghuni;
        $kamar = Datakamar::where('status', 'Tersedia')
                         ->orderBy('created_at', 'desc')
                         ->paginate(8);

        return view('penghuni.kamar_tersedia', compact('kamar', 'penghuni'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        $datakamar = Datakamar::whereIn('id', $ids)->get();

        foreach ($datakamar as $room) {
            $images = explode(',', $room->gambar);
            foreach ($images as $image) {
                $image = trim($image);
                if (!empty($image) && file_exists(public_path('images/' . $image))) {
                    unlink(public_path('images/' . $image));
                }
            }
        }

        Datakamar::whereIn('id', $ids)->delete();
        return response()->json(['success' => true]);
    }

    public function excel()
    {
        return Excel::download(new KamarExport, 'data-kamar-' . date('Y-m-d') . '.xlsx');
    }

    public function kamarDetail($id)
    {
        // Ambil data penghuni beserta kamar dan riwayatnya
        $penghuni = \App\Models\Datapenghuni::with(['datakamar', 'riwayat'])->findOrFail($id);

        return view('penghuni.kamar-detail', compact('penghuni'));
    }
}
