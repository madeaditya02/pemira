<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KegiatanController extends Controller
{

    /**
     * Check if the authenticated user is an admin.
     */
    private function isAdmin()
    {
        $role = auth('web')->user()->is_admin;
        return match ($role) {
            0 => false,
            1 => true,
        };
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check admin access
        $isAdmin = $this->isAdmin();
        if (!$isAdmin) {
            return redirect()->route('dashboard');
        }

        // Fetch kegiatan data with related program studi
        $kegiatan = Kegiatan::with('programStudi')->get();
        $programStudi = ProgramStudi::all();
        return Inertia::render('kegiatan/Index', [
            'kegiatan' => $kegiatan,
            'programStudi' => $programStudi,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check admin access
        $isAdmin = $this->isAdmin();
        if (!$isAdmin) {
            return redirect()->route('dashboard');
        }
        
        // Validate input data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'ruang_lingkup' => 'required|string|in:fakultas,program studi',
            'tahun' => 'required|integer|min:2000|max:2100',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'id_program_studi' => 'nullable|exists:program_studi,id',
            'foto' => 'required|image|max:4096|mimes:jpeg,png,jpg,webp',
        ], [
            'nama.required' => 'Nama kegiatan wajib diisi.',
            'ruang_lingkup.in' => 'Ruang lingkup harus berupa fakultas atau program studi.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus sama dengan atau setelah waktu mulai.',
            'id_program_studi.exists' => 'Program studi tidak valid.',
            'foto.required' => 'Foto kegiatan wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 4MB.',
            'foto.mimes' => 'Foto harus berupa file dengan ekstensi jpeg, png, jpg, atau webp.',
        ]);

        try {
            // Check ruang lingkup and set program studi accordingly
            if ($validatedData['ruang_lingkup'] === 'fakultas') {
                $validatedData['id_program_studi'] = null;
            } else if ($validatedData['ruang_lingkup'] === 'program studi' && empty($validatedData['id_program_studi'])) {
                return redirect()->back()->with('error', 'Program studi wajib diisi untuk ruang lingkup program studi.');
            }

            // Store foto if exists
            $fotoPath = null;
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                // Get the uploaded file
                $fotoFile = $request->file('foto');
                $fotoName = time() . '_' . $fotoFile->getClientOriginalName();

                // Define the avatar destination path
                $fotoDestination = Storage::disk('public')->path('foto-kegiatan');
                if (!file_exists($fotoDestination)) {
                    mkdir($fotoDestination, 0755, true);
                }

                // Store the foto file
                Storage::disk('public')->putFileAs('foto-kegiatan', $fotoFile, $fotoName);
                $fotoPath = 'foto-kegiatan/' . $fotoName;
            }

            // Create new kegiatan record
            Kegiatan::create([
                'nama' => $validatedData['nama'],
                'ruang_lingkup' => $validatedData['ruang_lingkup'],
                'tahun' => $validatedData['tahun'],
                'waktu_mulai' => $validatedData['waktu_mulai'],
                'waktu_selesai' => $validatedData['waktu_selesai'],
                'id_program_studi' => $validatedData['id_program_studi'],
                'foto' => $fotoPath,
            ]);

            // Redirect with success message
            return redirect()->back()->with('success', 'Data kegiatan berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat data kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Check admin access
        $isAdmin = $this->isAdmin();
        if (!$isAdmin) {
            return redirect()->route('dashboard');
        }

        // Validate input data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'ruang_lingkup' => 'required|string|in:fakultas,program studi',
            'tahun' => 'required|integer|min:2000|max:2100',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'id_program_studi' => 'nullable|exists:program_studi,id',
            'foto' => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp',
        ], [
            'nama.required' => 'Nama kegiatan wajib diisi.',
            'ruang_lingkup.in' => 'Ruang lingkup harus berupa fakultas atau program studi.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus sama dengan atau setelah waktu mulai.',
            'id_program_studi.exists' => 'Program studi tidak valid.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 4MB.',
            'foto.mimes' => 'Foto harus berupa file dengan ekstensi jpeg, png, jpg, atau webp.',
        ]);

        try {
            // Check ruang lingkup and set program studi accordingly
            if ($validatedData['ruang_lingkup'] === 'fakultas') {
                $validatedData['id_program_studi'] = null;
            } else if ($validatedData['ruang_lingkup'] === 'program studi' && empty($validatedData['id_program_studi'])) {
                return redirect()->back()->with('error', 'Program studi wajib diisi untuk ruang lingkup program studi.');
            }

            // Find the kegiatan to update
            $kegiatan = Kegiatan::findOrFail($id);

            // Handle foto upload
            $fotoPath = $kegiatan->foto;
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                // Delete old foto if exists
                if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                    Storage::disk('public')->delete($fotoPath);
                }

                // Store new foto
                $fotoFile = $request->file('foto');
                $fotoName = time() . '_' . $fotoFile->getClientOriginalName();
                Storage::disk('public')->putFileAs('foto-kegiatan', $fotoFile, $fotoName);
                $fotoPath = 'foto-kegiatan/' . $fotoName;
            }

            // Update kegiatan
            $kegiatan->update([
                'nama' => $validatedData['nama'],
                'ruang_lingkup' => $validatedData['ruang_lingkup'],
                'tahun' => $validatedData['tahun'],
                'waktu_mulai' => $validatedData['waktu_mulai'],
                'waktu_selesai' => $validatedData['waktu_selesai'],
                'id_program_studi' => $validatedData['id_program_studi'],
                'foto' => $fotoPath,
            ]);

            // Redirect with success message
            return redirect()->back()->with('success', 'Data kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check admin access
        $isAdmin = $this->isAdmin();
        if (!$isAdmin) {
            return redirect()->route('dashboard');
        }

        try {
            // Find kegiatan
            $kegiatan = Kegiatan::findOrFail($id);

            // Delete foto if exists
            if ($kegiatan->foto && Storage::disk('public')->exists($kegiatan->foto)) {
                Storage::disk('public')->delete($kegiatan->foto);
            }

            // Delete kegiatan
            $kegiatan->delete();
            return redirect()->back()->with('success', 'Data kegiatan berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data kegiatan: ' . $e->getMessage());
        }
    }
}
