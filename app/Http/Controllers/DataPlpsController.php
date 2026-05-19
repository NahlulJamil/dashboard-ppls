<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPlps;
use App\Models\Kegiatan;
use App\Models\Mitra;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Program;
use App\Models\SubProgram;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataPlpsImport;

class DataPlpsController extends Controller
{
    public function index(Request $request)
    {
        $query = DataPlps::with([
            'program',
            'subProgram',
            'mahasiswa.prodi.fakultas',
            'kegiatan',
            'mitra'
        ]);

        // Filter: Program
        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        // Filter: Sub Program
        if ($request->sub_program_id) {
            $query->where('sub_program_id', $request->sub_program_id);
        }

        // Filter: Kegiatan
        if ($request->kegiatan_id) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        // Filter: Mitra
        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        // Filter: Semester
        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        // Filter: Tahun Ajaran
        if ($request->tahun_ajaran) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        // Filter: Penyelenggara
        if ($request->penyelenggara) {
            $query->where('penyelenggara', $request->penyelenggara);
        }

        // Filter: Fakultas
        if ($request->fakultas_id) {
            $query->whereHas('mahasiswa.prodi.fakultas', function ($q) use ($request) {
                $q->where('id', $request->fakultas_id);
            });
        }

        // Filter: Prodi
        if ($request->prodi_id) {
            $query->whereHas('mahasiswa.prodi', function ($q) use ($request) {
                $q->where('id', $request->prodi_id);
            });
        }

        // Filter: SKS
        if ($request->sks) {
            $query->where('sks', $request->sks);
        }

        $data = $query->get();

        // Data untuk dropdown filter
        $programs = Program::all();
        $subPrograms = SubProgram::all();
        $kegiatan = Kegiatan::all();
        $mitra = Mitra::all();
        $fakultas = Fakultas::all();
        $prodi = Prodi::all();

        return view('dashboard', compact(
            'data',
            'programs',
            'subPrograms',
            'kegiatan',
            'mitra',
            'fakultas',
            'prodi'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            $importer = new DataPlpsImport;
            Excel::import($importer, $request->file('file'));

            return back()->with('success', 'Import berhasil');

        } catch (\Exception $e) {
            // Kalau ada error dari importer, pass sebagai array untuk popup modal
            $importer = $importer ?? new DataPlpsImport;
            if (!empty($importer->errors)) {
                return back()->with('import_errors', $importer->errors);
            }

            // Error lain (file corrupt, dsb)
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $data = DataPlps::findOrFail($id);

        $data->update($request->all());

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = DataPlps::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}