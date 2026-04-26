<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPlps;
use App\Models\Kegiatan;
use App\Models\Instansi;
use App\Models\Fakultas;
use App\Models\Prodi;

class DataPlpsController extends Controller
{
    public function index(Request $request)
    {
        $query = DataPlps::with([
            'mahasiswa.prodi.fakultas',
            'kegiatan',
            'instansi'
        ]);

        // buat filter
        if ($request->kegiatan_id) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        if ($request->instansi_id) {
            $query->where('instansi_id', $request->instansi_id);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->program_owner) {
            $query->where('program_owner', $request->program_owner);
        }

        if ($request->fakultas_id) {
            $query->whereHas('mahasiswa.prodi.fakultas', function ($q) use ($request) {
                $q->where('id', $request->fakultas_id);
            });
        }

        if ($request->prodi_id) {
            $query->whereHas('mahasiswa.prodi', function ($q) use ($request) {
                $q->where('id', $request->prodi_id);
            });
        }

        if ($request->sks) {
            $query->where('sks', $request->sks);
        }

        $data = $query->get();

        // buat dropdown filter
        $kegiatan = Kegiatan::all();
        $instansi = Instansi::all();
        $fakultas = Fakultas::all();
        $prodi = Prodi::all();

        return view('dashboard', compact(
            'data',
            'kegiatan',
            'instansi',
            'fakultas',
            'prodi'
        ));

        
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
