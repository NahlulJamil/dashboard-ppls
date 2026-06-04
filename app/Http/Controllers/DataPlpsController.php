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
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataPlpsImport;

class DataPlpsController extends Controller
{
    public function index(Request $request)
    {
        $hasData = DataPlps::exists();

        $programs = DB::table('programs')
            ->leftJoin('data_plps', 'programs.id', '=', 'data_plps.program_id')
            ->select('programs.id', 'programs.nama_program', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('programs.id', 'programs.nama_program')
            ->orderBy('programs.nama_program')
            ->get();

        $subPrograms = DB::table('sub_programs')
            ->leftJoin('data_plps', 'sub_programs.id', '=', 'data_plps.sub_program_id')
            ->select('sub_programs.id', 'sub_programs.nama_sub_program', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('sub_programs.id', 'sub_programs.nama_sub_program')
            ->orderBy('sub_programs.nama_sub_program')
            ->get();

        $fakultas = DB::table('fakultas')
            ->leftJoin('prodis', 'fakultas.id', '=', 'prodis.fakultas_id')
            ->leftJoin('mahasiswas', 'prodis.id', '=', 'mahasiswas.prodi_id')
            ->leftJoin('data_plps', 'mahasiswas.nim', '=', 'data_plps.nim')
            ->select('fakultas.id', 'fakultas.nama_fakultas', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('fakultas.id', 'fakultas.nama_fakultas')
            ->orderBy('fakultas.nama_fakultas')
            ->get();

        $prodi = DB::table('prodis')
            ->leftJoin('mahasiswas', 'prodis.id', '=', 'mahasiswas.prodi_id')
            ->leftJoin('data_plps', 'mahasiswas.nim', '=', 'data_plps.nim')
            ->select('prodis.id', 'prodis.nama_prodi', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('prodis.id', 'prodis.nama_prodi')
            ->orderBy('prodis.nama_prodi')
            ->get();

        $allMitra = DB::table('mitras')
            ->leftJoin('data_plps', 'mitras.id', '=', 'data_plps.mitra_id')
            ->select('mitras.id', 'mitras.nama_mitra', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('mitras.id', 'mitras.nama_mitra')
            ->orderBy('mitras.nama_mitra')
            ->get();

        // Semester TA & Tahun Ajaran filter options
        $allSemesterTa = DB::table('data_plps')
            ->select('semester_ta')
            ->distinct()
            ->whereNotNull('semester_ta')
            ->orderBy('semester_ta')
            ->pluck('semester_ta');

        $allTahunAjaran = DB::table('data_plps')
            ->select('tahun_ajaran')
            ->distinct()
            ->whereNotNull('tahun_ajaran')
            ->orderBy('tahun_ajaran')
            ->pluck('tahun_ajaran');

        // Default: 5 semester TA terbaru (auto-checked saat pertama kali buka)
        $defaultSemesterTa = $allSemesterTa->sortDesc()->take(5)->values()->toArray();

        if (!$hasData) {
            return view('dashboard', [
                'hasData' => false,
                'programs' => $programs,
                'subPrograms' => $subPrograms,
                'fakultas' => $fakultas,
                'prodi' => $prodi,
                'allMitra' => $allMitra,
                'allSemesterTa' => $allSemesterTa,
                'allTahunAjaran' => $allTahunAjaran,
                'defaultSemesterTa' => $defaultSemesterTa,
            ]);
        }

        // === SUMMARY STATS ===
        // Auto-apply default semester_ta filter on first load (no filter params in URL)
        $isFirstLoad = !$request->has('program_id') && !$request->has('sub_program_id')
            && !$request->has('fakultas_id') && !$request->has('prodi_id')
            && !$request->has('penyelenggara') && !$request->has('mitra_id')
            && !$request->has('semester_ta') && !$request->has('tahun_ajaran');

        if ($isFirstLoad && !empty($defaultSemesterTa)) {
            $request->merge(['semester_ta' => $defaultSemesterTa]);
        }

        $baseQuery = DataPlps::query();
        $this->applyEloquentFilters($baseQuery, $request);

        $totalMahasiswa = (clone $baseQuery)->distinct()->count('nim');
        $totalMitra = (clone $baseQuery)->distinct()->count('mitra_id');
        $totalProgram = (clone $baseQuery)->distinct()->count('program_id');
        $totalSubProgram = (clone $baseQuery)->distinct()->count('sub_program_id');

        // === TOP 5 FAKULTAS ===
        $mahasiswaPerFakultas = $this->chartQuery($request)
            ->select('fakultas.nama_fakultas', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('fakultas.nama_fakultas')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // === TOP 5 PRODI ===
        $topProdi = $this->chartQuery($request)
            ->select('prodis.nama_prodi', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('prodis.nama_prodi')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // === TOP 5 PROGRAM ===
        $topProgram = $this->chartQuery($request)
            ->join('programs', 'data_plps.program_id', '=', 'programs.id')
            ->select('programs.nama_program', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('programs.nama_program')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // === DISTRIBUSI PROGRAM ===
        $distribusiProgram = $this->chartQuery($request)
            ->join('programs', 'data_plps.program_id', '=', 'programs.id')
            ->select('programs.nama_program', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('programs.nama_program')
            ->orderByDesc('total')
            ->get();

        // === TOTAL MITRA INTERNAL/EKSTERNAL ===
        $totalMitraEksternal = (clone $baseQuery)->where('penyelenggara', 'Eksternal')->distinct()->count('mitra_id');
        $totalMitraInternal = (clone $baseQuery)->where('penyelenggara', 'Internal')->distinct()->count('mitra_id');

        // === TOP 5 MITRA EKSTERNAL ===
        $topMitraEksternal = $this->chartQuery($request)
            ->where('penyelenggara', 'Eksternal')
            ->join('mitras', 'data_plps.mitra_id', '=', 'mitras.id')
            ->select('mitras.nama_mitra', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('mitras.nama_mitra', 'mitras.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // === TOP 5 MITRA INTERNAL ===
        $topMitraInternal = $this->chartQuery($request)
            ->where('penyelenggara', 'Internal')
            ->join('mitras', 'data_plps.mitra_id', '=', 'mitras.id')
            ->select('mitras.nama_mitra', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('mitras.nama_mitra', 'mitras.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // === TREN PER SEMESTER ===
        $trenRaw = $this->chartQuery($request)
            ->select('fakultas.nama_fakultas', 'data_plps.semester_ta', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('fakultas.nama_fakultas', 'data_plps.semester_ta')
            ->orderBy('data_plps.semester_ta')
            ->get();

        $semesters = $trenRaw->pluck('semester_ta')->unique()->sort()->values()->toArray();
        $fakultasInTren = $trenRaw->pluck('nama_fakultas')->unique()->values();
        $trenSeries = [];
        foreach ($fakultasInTren as $fak) {
            $data = [];
            foreach ($semesters as $sem) {
                $entry = $trenRaw->where('nama_fakultas', $fak)->where('semester_ta', $sem)->first();
                $data[] = $entry ? $entry->total : 0;
            }
            $trenSeries[] = ['label' => $fak, 'data' => $data];
        }

        // Check if user is admin for edit/delete permissions
        $isAdmin = auth()->guard('admin')->check();

        return view('dashboard', compact(
            'hasData',
            'programs',
            'subPrograms',
            'fakultas',
            'prodi',
            'allMitra',
            'allSemesterTa',
            'allTahunAjaran',
            'defaultSemesterTa',
            'totalMahasiswa',
            'totalMitra',
            'totalProgram',
            'totalSubProgram',
            'mahasiswaPerFakultas',
            'topProdi',
            'topProgram',
            'distribusiProgram',
            'totalMitraEksternal',
            'totalMitraInternal',
            'topMitraEksternal',
            'topMitraInternal',
            'semesters',
            'trenSeries',
            'isAdmin'
        ));
    }

    /**
     * API endpoint for lazy-loaded table data.
     * Returns paginated JSON data (50 per page).
     */
    public function tableData(Request $request)
    {
        $query = DataPlps::with(['program', 'subProgram', 'mahasiswa.prodi.fakultas', 'kegiatan', 'mitra']);
        $this->applyEloquentFilters($query, $request);

        if ($request->search_nama) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('nama', 'LIKE', '%' . $request->search_nama . '%');
            });
        }
        if ($request->search_nim) {
            $query->where('nim', 'LIKE', '%' . $request->search_nim . '%');
        }

        $paginated = $query->orderBy('id', 'desc')->paginate(100);

        // Transform data for frontend
        $rows = $paginated->getCollection()->map(function ($row, $index) use ($paginated) {
            return [
                'id' => $row->id,
                'no' => $paginated->firstItem() + $index,
                'program' => $row->program->nama_program ?? '-',
                'sub_program' => $row->subProgram->nama_sub_program ?? '-',
                'fakultas' => $row->mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                'prodi' => $row->mahasiswa->prodi->nama_prodi ?? '-',
                'nama' => $row->mahasiswa->nama ?? '-',
                'nim' => $row->nim,
                'kegiatan' => $row->kegiatan->nama_kegiatan ?? '-',
                'mitra' => $row->mitra->nama_mitra ?? '-',
                'penyelenggara' => $row->penyelenggara,
                'semester' => $row->semester,
                'semester_ta' => $row->semester_ta,
                'tahun_ajaran' => $row->tahun_ajaran,
                'dosen_pembimbing' => $row->dosen_pembimbing ?? '-',
                'sks' => $row->sks,
            ];
        });

        return response()->json([
            'data' => $rows,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    /**
     * Expand comma-separated ID values from multi-select checkboxes.
     * e.g. ["1,2", "3"] => [1, 2, 3]
     */
    private function expandIds($input)
    {
        $values = (array) $input;
        $expanded = [];
        foreach ($values as $v) {
            foreach (explode(',', $v) as $id) {
                $id = trim($id);
                if ($id !== '') $expanded[] = $id;
            }
        }
        return array_unique($expanded);
    }

    /**
     * Apply filters to Eloquent DataPlps query
     */
    private function applyEloquentFilters($query, Request $request)
    {
        if ($request->has('program_id') && !empty(array_filter((array)$request->program_id))) {
            $query->whereIn('program_id', $this->expandIds($request->program_id));
        }
        if ($request->has('sub_program_id') && !empty(array_filter((array)$request->sub_program_id))) {
            $query->whereIn('sub_program_id', $this->expandIds($request->sub_program_id));
        }
        if ($request->has('mitra_id') && !empty(array_filter((array)$request->mitra_id))) {
            $query->whereIn('mitra_id', $this->expandIds($request->mitra_id));
        }
        if ($request->has('penyelenggara') && !empty(array_filter((array)$request->penyelenggara))) {
            $query->whereIn('penyelenggara', (array)$request->penyelenggara);
        }
        if ($request->has('fakultas_id') && !empty(array_filter((array)$request->fakultas_id))) {
            $ids = $this->expandIds($request->fakultas_id);
            $query->whereHas('mahasiswa.prodi', fn($q) => $q->whereIn('fakultas_id', $ids));
        }
        if ($request->has('prodi_id') && !empty(array_filter((array)$request->prodi_id))) {
            $ids = $this->expandIds($request->prodi_id);
            $query->whereHas('mahasiswa', fn($q) => $q->whereIn('prodi_id', $ids));
        }
        if ($request->has('semester_ta') && !empty(array_filter((array)$request->semester_ta))) {
            $query->whereIn('semester_ta', (array)$request->semester_ta);
        }
        if ($request->has('tahun_ajaran') && !empty(array_filter((array)$request->tahun_ajaran))) {
            $query->whereIn('tahun_ajaran', (array)$request->tahun_ajaran);
        }
    }

    /**
     * Base DB query builder with joins + filters for chart queries
     */
    private function chartQuery(Request $request)
    {
        $q = DB::table('data_plps')
            ->join('mahasiswas', 'data_plps.nim', '=', 'mahasiswas.nim')
            ->join('prodis', 'mahasiswas.prodi_id', '=', 'prodis.id')
            ->join('fakultas', 'prodis.fakultas_id', '=', 'fakultas.id');

        if ($request->has('program_id') && !empty(array_filter((array)$request->program_id))) {
            $q->whereIn('data_plps.program_id', $this->expandIds($request->program_id));
        }
        if ($request->has('sub_program_id') && !empty(array_filter((array)$request->sub_program_id))) {
            $q->whereIn('data_plps.sub_program_id', $this->expandIds($request->sub_program_id));
        }
        if ($request->has('mitra_id') && !empty(array_filter((array)$request->mitra_id))) {
            $q->whereIn('data_plps.mitra_id', $this->expandIds($request->mitra_id));
        }
        if ($request->has('penyelenggara') && !empty(array_filter((array)$request->penyelenggara))) {
            $q->whereIn('data_plps.penyelenggara', (array)$request->penyelenggara);
        }
        if ($request->has('fakultas_id') && !empty(array_filter((array)$request->fakultas_id))) {
            $q->whereIn('prodis.fakultas_id', $this->expandIds($request->fakultas_id));
        }
        if ($request->has('prodi_id') && !empty(array_filter((array)$request->prodi_id))) {
            $q->whereIn('mahasiswas.prodi_id', $this->expandIds($request->prodi_id));
        }
        if ($request->has('semester_ta') && !empty(array_filter((array)$request->semester_ta))) {
            $q->whereIn('data_plps.semester_ta', (array)$request->semester_ta);
        }
        if ($request->has('tahun_ajaran') && !empty(array_filter((array)$request->tahun_ajaran))) {
            $q->whereIn('data_plps.tahun_ajaran', (array)$request->tahun_ajaran);
        }

        return $q;
    }

    /**
     * API endpoint for cascading filters.
     * Returns available options + counts for each filter,
     * computed by applying all OTHER active filters (cross-filter).
     */
    public function getFilterOptions(Request $request)
    {
        // Helper: build a base query with specific filters applied
        $buildBase = function (array $excludeFilters = []) use ($request) {
            $q = DB::table('data_plps')
                ->join('mahasiswas', 'data_plps.nim', '=', 'mahasiswas.nim')
                ->join('prodis', 'mahasiswas.prodi_id', '=', 'prodis.id')
                ->join('fakultas', 'prodis.fakultas_id', '=', 'fakultas.id');

            if (!in_array('program_id', $excludeFilters) && $request->has('program_id') && !empty(array_filter((array)$request->program_id))) {
                $q->whereIn('data_plps.program_id', $this->expandIds($request->program_id));
            }
            if (!in_array('sub_program_id', $excludeFilters) && $request->has('sub_program_id') && !empty(array_filter((array)$request->sub_program_id))) {
                $q->whereIn('data_plps.sub_program_id', $this->expandIds($request->sub_program_id));
            }
            if (!in_array('mitra_id', $excludeFilters) && $request->has('mitra_id') && !empty(array_filter((array)$request->mitra_id))) {
                $q->whereIn('data_plps.mitra_id', $this->expandIds($request->mitra_id));
            }
            if (!in_array('penyelenggara', $excludeFilters) && $request->has('penyelenggara') && !empty(array_filter((array)$request->penyelenggara))) {
                $q->whereIn('data_plps.penyelenggara', (array)$request->penyelenggara);
            }
            if (!in_array('fakultas_id', $excludeFilters) && $request->has('fakultas_id') && !empty(array_filter((array)$request->fakultas_id))) {
                $q->whereIn('prodis.fakultas_id', $this->expandIds($request->fakultas_id));
            }
            if (!in_array('prodi_id', $excludeFilters) && $request->has('prodi_id') && !empty(array_filter((array)$request->prodi_id))) {
                $q->whereIn('mahasiswas.prodi_id', $this->expandIds($request->prodi_id));
            }
            if (!in_array('semester_ta', $excludeFilters) && $request->has('semester_ta') && !empty(array_filter((array)$request->semester_ta))) {
                $q->whereIn('data_plps.semester_ta', (array)$request->semester_ta);
            }
            if (!in_array('tahun_ajaran', $excludeFilters) && $request->has('tahun_ajaran') && !empty(array_filter((array)$request->tahun_ajaran))) {
                $q->whereIn('data_plps.tahun_ajaran', (array)$request->tahun_ajaran);
            }

            return $q;
        };

        // Programs: apply all filters EXCEPT program_id
        $programs = $buildBase(['program_id'])
            ->join('programs', 'data_plps.program_id', '=', 'programs.id')
            ->select('programs.id', 'programs.nama_program as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('programs.id', 'programs.nama_program')
            ->orderBy('programs.nama_program')
            ->get();

        // Sub Programs: apply all filters EXCEPT sub_program_id
        $subPrograms = $buildBase(['sub_program_id'])
            ->join('sub_programs', 'data_plps.sub_program_id', '=', 'sub_programs.id')
            ->select('sub_programs.id', 'sub_programs.nama_sub_program as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('sub_programs.id', 'sub_programs.nama_sub_program')
            ->orderBy('sub_programs.nama_sub_program')
            ->get();

        // Fakultas: apply all filters EXCEPT fakultas_id
        $fakultas = $buildBase(['fakultas_id'])
            ->select('fakultas.id', 'fakultas.nama_fakultas as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('fakultas.id', 'fakultas.nama_fakultas')
            ->orderBy('fakultas.nama_fakultas')
            ->get();

        // Prodi: apply all filters EXCEPT prodi_id
        $prodi = $buildBase(['prodi_id'])
            ->select('prodis.id', 'prodis.nama_prodi as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('prodis.id', 'prodis.nama_prodi')
            ->orderBy('prodis.nama_prodi')
            ->get();

        // Mitra: apply all filters EXCEPT mitra_id
        $allMitra = $buildBase(['mitra_id'])
            ->join('mitras', 'data_plps.mitra_id', '=', 'mitras.id')
            ->select('mitras.id', 'mitras.nama_mitra as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('mitras.id', 'mitras.nama_mitra')
            ->orderBy('mitras.nama_mitra')
            ->get();

        // Penyelenggara: apply all filters EXCEPT penyelenggara
        $penyelenggara = $buildBase(['penyelenggara'])
            ->select('data_plps.penyelenggara as label', DB::raw('COUNT(data_plps.id) as total'))
            ->groupBy('data_plps.penyelenggara')
            ->orderBy('data_plps.penyelenggara')
            ->get()
            ->map(function ($item) {
                // Use penyelenggara value as id too
                $item->id = $item->label;
                return $item;
            });

        // Semester TA: apply all filters EXCEPT semester_ta
        $semesterTa = $buildBase(['semester_ta'])
            ->select('data_plps.semester_ta as label', DB::raw('COUNT(data_plps.id) as total'))
            ->whereNotNull('data_plps.semester_ta')
            ->groupBy('data_plps.semester_ta')
            ->orderBy('data_plps.semester_ta')
            ->get()
            ->map(function ($item) {
                $item->id = $item->label;
                return $item;
            });

        // Tahun Ajaran: apply all filters EXCEPT tahun_ajaran
        $tahunAjaran = $buildBase(['tahun_ajaran'])
            ->select('data_plps.tahun_ajaran as label', DB::raw('COUNT(data_plps.id) as total'))
            ->whereNotNull('data_plps.tahun_ajaran')
            ->groupBy('data_plps.tahun_ajaran')
            ->orderBy('data_plps.tahun_ajaran')
            ->get()
            ->map(function ($item) {
                $item->id = $item->label;
                return $item;
            });

        return response()->json([
            'program_id' => $programs,
            'sub_program_id' => $subPrograms,
            'fakultas_id' => $fakultas,
            'prodi_id' => $prodi,
            'mitra_id' => $allMitra,
            'penyelenggara' => $penyelenggara,
            'semester_ta' => $semesterTa,
            'tahun_ajaran' => $tahunAjaran,
        ]);
    }

    // =============================================
    // INPUT DATA PAGE METHODS
    // =============================================

    /**
     * Show the Data Input page with upload zone and history.
     */
    public function inputData()
    {
        $histories = \App\Models\ImportHistory::with('admin')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('input-data', compact('histories'));
    }

    /**
     * Step 1: Validate uploaded Excel without saving to DB.
     * If valid → store temp file + redirect to confirmation.
     * If errors → redirect back with error modal.
     */
    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240'
        ]);

        $originalName = $request->file('file')->getClientOriginalName();

        // Store file temporarily
        $storedPath = $request->file('file')->store('temp-imports');
        session(['last_import_path' => $storedPath]);
        session(['last_import_filename' => $originalName]);

        $fullPath = \Illuminate\Support\Facades\Storage::path($storedPath);

        try {
            // Validate only — do not save to DB
            $importer = new DataPlpsImport(true);
            Excel::import($importer, $fullPath);

            // Validation passed! Store row count and redirect to confirm page
            session(['import_row_count' => $importer->validRowCount]);

            return redirect('/input-data/confirm');

        } catch (\Exception $e) {
            $importer = $importer ?? new DataPlpsImport;
            if (!empty($importer->errors)) {
                return back()
                    ->with('import_errors', $importer->errors);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Step 2a: Show the confirmation page.
     */
    public function showConfirmImport()
    {
        $rowCount = session('import_row_count', 0);
        $storedPath = session('last_import_path');
        $originalName = session('last_import_filename', 'file.xlsx');

        if (!$storedPath || !$rowCount) {
            return redirect('/input-data')->with('error', 'Tidak ada data untuk dikonfirmasi. Silakan upload ulang.');
        }

        return view('confirm-import', compact('rowCount', 'originalName'));
    }

    /**
     * Step 2b: Actually import and save to DB.
     */
    public function confirmImport(Request $request)
    {
        $storedPath = session('last_import_path');
        $originalName = session('last_import_filename', 'file.xlsx');

        if (!$storedPath || !\Illuminate\Support\Facades\Storage::exists($storedPath)) {
            return redirect('/input-data')->with('error', 'File import tidak ditemukan. Silakan upload ulang.');
        }

        try {
            $importer = new DataPlpsImport(false); // validateOnly = false, save to DB
            $fullPath = \Illuminate\Support\Facades\Storage::path($storedPath);
            Excel::import($importer, $fullPath);

            // Record history
            \App\Models\ImportHistory::create([
                'filename' => $originalName,
                'admin_id' => \Illuminate\Support\Facades\Auth::guard('admin')->id(),
                'rows_count' => $importer->validRowCount,
            ]);

            // Clean up
            \Illuminate\Support\Facades\Storage::delete($storedPath);
            session()->forget(['last_import_path', 'last_import_filename', 'import_row_count']);

            return redirect('/input-data')
                ->with('success', "{$importer->validRowCount} data berhasil diimport ke database!")
                ->with('show_success_modal', true);

        } catch (\Exception $e) {
            return redirect('/input-data')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\TemplateExport, 'template_data_plps.xlsx');
    }

    /**
     * Export data to Excel based on filters.
     */
    public function exportExcel(Request $request)
    {
        // Apply default filter if no filters are selected (first load behavior)
        if (!$request->has('program_id') && !$request->has('sub_program_id') && !$request->has('fakultas_id') && !$request->has('prodi_id') && !$request->has('penyelenggara') && !$request->has('mitra_id') && !$request->has('semester_ta') && !$request->has('tahun_ajaran')) {
            $defaultSemesterTa = DataPlps::select('semester_ta')->distinct()->orderBy('semester_ta', 'desc')->limit(5)->pluck('semester_ta')->toArray();
            $request->merge(['semester_ta' => $defaultSemesterTa]);
        }

        $query = DataPlps::query();
        $this->applyEloquentFilters($query, $request);
        $query->with(['program', 'subProgram', 'mahasiswa.prodi.fakultas', 'kegiatan', 'mitra']);
        
        return Excel::download(new \App\Exports\DataPlpsExport($query), 'data_mahasiswa_mbkm.xlsx');
    }

    /**
     * Export data to PDF (HTML view printable) based on filters.
     */
    public function exportPdf(Request $request)
    {
        // Apply default filter if no filters are selected
        $isFirstLoad = !$request->has('program_id') && !$request->has('sub_program_id') && !$request->has('fakultas_id') && !$request->has('prodi_id') && !$request->has('penyelenggara') && !$request->has('mitra_id') && !$request->has('semester_ta') && !$request->has('tahun_ajaran');
        if ($isFirstLoad) {
            $defaultSemesterTa = DataPlps::select('semester_ta')->distinct()->orderBy('semester_ta', 'desc')->limit(5)->pluck('semester_ta')->toArray();
            $request->merge(['semester_ta' => $defaultSemesterTa]);
        }

        // Re-use logic from index() to get chart data for PDF
        $baseQuery = $this->chartQuery($request);
        
        $totalMahasiswa = (clone $baseQuery)->distinct()->count('data_plps.nim');
        $totalProgram = (clone $baseQuery)->distinct()->count('data_plps.program_id');
        $totalMitraEksternal = (clone $baseQuery)->where('penyelenggara', 'Eksternal')->distinct()->count('mitra_id');
        $totalMitraInternal = (clone $baseQuery)->where('penyelenggara', 'Internal')->distinct()->count('mitra_id');

        $topMitraEksternal = $this->chartQuery($request)
            ->where('penyelenggara', 'Eksternal')
            ->join('mitras', 'data_plps.mitra_id', '=', 'mitras.id')
            ->select('mitras.nama_mitra', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('mitras.nama_mitra', 'mitras.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topMitraInternal = $this->chartQuery($request)
            ->where('penyelenggara', 'Internal')
            ->join('mitras', 'data_plps.mitra_id', '=', 'mitras.id')
            ->select('mitras.nama_mitra', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('mitras.nama_mitra', 'mitras.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $mahasiswaPerFakultas = $this->chartQuery($request)
            ->select('fakultas.nama_fakultas', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('fakultas.nama_fakultas')
            ->orderByDesc('total')
            ->get();


        $topProdi = $this->chartQuery($request)
            ->select('prodis.nama_prodi', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('prodis.nama_prodi')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $distribusiProgram = $this->chartQuery($request)
            ->join('programs', 'data_plps.program_id', '=', 'programs.id')
            ->select('programs.nama_program', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('programs.nama_program')
            ->orderByDesc('total')
            ->get();

        // Bidang kerja magang
        $magangData = $this->chartQuery($request)
            ->join('programs', 'data_plps.program_id', '=', 'programs.id')
            ->join('kegiatans', 'data_plps.kegiatan_id', '=', 'kegiatans.id')
            ->whereRaw('LOWER(programs.nama_program) LIKE ?', ['%magang%'])
            ->select('kegiatans.nama_kegiatan', DB::raw('COUNT(DISTINCT data_plps.nim) as total'))
            ->groupBy('kegiatans.nama_kegiatan')
            ->orderByDesc('total')
            ->get();
            
        $totalMagang = $magangData->sum('total');
        // Group top 10, rest as "Lainnya"
        $topMagang = $magangData->take(10);
        if ($magangData->count() > 10) {
            $otherMagang = $magangData->slice(10)->sum('total');
            $topMagang->push((object)['nama_kegiatan' => 'Lainnya / Tidak Terklasifikasi', 'total' => $otherMagang]);
        }

        $exporterName = auth()->guard('admin')->check() 
            ? (auth()->guard('admin')->user()->username ?? auth()->guard('admin')->user()->name)
            : 'Guest';

        $isFirstLoad = !$request->has('program_id') && !$request->has('sub_program_id') && !$request->has('fakultas_id') && !$request->has('prodi_id') && !$request->has('penyelenggara') && !$request->has('mitra_id') && !$request->has('semester_ta') && !$request->has('tahun_ajaran');
        $activeSemesterTa = (array)$request->semester_ta;
        
        if ($isFirstLoad && empty($activeSemesterTa)) {
            $activeSemesterTa = DataPlps::select('semester_ta')->distinct()->orderBy('semester_ta', 'desc')->limit(5)->pluck('semester_ta')->toArray();
        }

        return view('export-pdf', compact(
            'totalMahasiswa', 'totalProgram', 'totalMitraEksternal', 'totalMitraInternal',
            'topMitraEksternal', 'topMitraInternal', 'mahasiswaPerFakultas', 'topProdi',
            'distribusiProgram', 'topMagang', 'totalMagang', 'exporterName', 'activeSemesterTa'
        ));
    }

    /**
     * API: Update a single DataPlps row (inline edit from dashboard table).
     */
    public function updateRow(Request $request, $id)
    {
        $data = DataPlps::findOrFail($id);

        $validated = $request->validate([
            'kegiatan_nama' => 'nullable|string',
            'mitra_nama' => 'nullable|string',
            'penyelenggara' => 'sometimes|in:Eksternal,Internal',
            'semester' => 'sometimes|in:GANJIL,GENAP',
            'semester_ta' => ['sometimes', 'string', 'regex:/^\d{4}\/\d{4} S\d$/'],
            'tahun_ajaran' => ['sometimes', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'dosen_pembimbing' => 'nullable|string',
            'sks' => 'sometimes|integer|min:0',
        ], [
            'semester_ta.regex' => 'Format Semester TA harus seperti "2022/2023 S2"',
            'tahun_ajaran.regex' => 'Format Tahun Ajaran harus seperti "2022/2023"',
        ]);

        $updateData = [];
        if (isset($validated['penyelenggara'])) $updateData['penyelenggara'] = $validated['penyelenggara'];
        if (isset($validated['semester'])) $updateData['semester'] = $validated['semester'];
        if (isset($validated['semester_ta'])) $updateData['semester_ta'] = $validated['semester_ta'];
        if (isset($validated['tahun_ajaran'])) $updateData['tahun_ajaran'] = $validated['tahun_ajaran'];
        if (array_key_exists('dosen_pembimbing', $validated)) $updateData['dosen_pembimbing'] = $validated['dosen_pembimbing'];
        if (isset($validated['sks'])) $updateData['sks'] = $validated['sks'];

        if (!empty($validated['kegiatan_nama'])) {
            $kegiatan = $this->fuzzyFirstOrCreate(\App\Models\Kegiatan::class, 'nama_kegiatan', $validated['kegiatan_nama']);
            $updateData['kegiatan_id'] = $kegiatan->id;
        }

        if (!empty($validated['mitra_nama'])) {
            $mitra = $this->fuzzyFirstOrCreate(\App\Models\Mitra::class, 'nama_mitra', $validated['mitra_nama']);
            $updateData['mitra_id'] = $mitra->id;
        }

        // Validate uniqueness if semester/tahun_ajaran is being changed
        $checkSemester = $updateData['semester'] ?? $data->semester;
        $checkTahunAjaran = $updateData['tahun_ajaran'] ?? $data->tahun_ajaran;
        
        $exists = \App\Models\DataPlps::where('nim', $data->nim)
            ->where('program_id', $data->program_id)
            ->where('semester', $checkSemester)
            ->where('tahun_ajaran', $checkTahunAjaran)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'semester' => ["Data duplikat (NIM: {$data->nim}, Semester: {$checkSemester}, TA: {$checkTahunAjaran}) sudah ada."]
                ]
            ], 422);
        }

        $data->update($updateData);

        // Reload with relations for response
        $data->load(['kegiatan', 'mitra']);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data' => $data,
        ]);
    }

    /**
     * API: Bulk delete DataPlps rows.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:data_plps,id',
        ]);

        $count = DataPlps::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} data berhasil dihapus",
            'deleted_count' => $count,
        ]);
    }

    /**
     * Fuzzy firstOrCreate for dynamic data (Mitra, Kegiatan) during edit.
     */
    private function fuzzyFirstOrCreate(string $modelClass, string $column, string $value, float $threshold = 80.0)
    {
        $value = preg_replace('/\s+/', ' ', trim($value));
        if (empty($value)) return null;

        $lowerValue = mb_strtolower($value);

        // 1. Exact match (case-insensitive)
        $exact = $modelClass::whereRaw("LOWER({$column}) = ?", [$lowerValue])->first();
        if ($exact) return $exact;

        // 2. Fuzzy match - check all existing records
        $allRecords = $modelClass::all();
        foreach ($allRecords as $record) {
            $existingValue = $record->$column;
            similar_text($lowerValue, mb_strtolower($existingValue), $percent);

            if ($percent >= $threshold) {
                // Return 422 if typo detected
                abort(response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        $column === 'nama_mitra' ? 'mitra_nama' : 'kegiatan_nama' => [
                            "Kemungkinan typo: \"{$value}\" mirip dengan \"{$existingValue}\" " .
                            "({$percent}% kemiripan). Gunakan nama yang sudah ada atau perbaiki ejaan."
                        ]
                    ]
                ], 422));
            }
        }

        // 3. Create new
        return $modelClass::create([$column => $value]);
    }
}