@extends('layouts.app')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('styles')
<style>
    /* Filter */
    .filter-grid{display:flex;flex-wrap:wrap;gap:10px}
    .filter-actions{display:flex;gap:10px;margin-top:14px;justify-content:flex-end}

    /* Checkbox Multi-Select Dropdown */
    .ms-dropdown{position:relative;min-width:160px;flex:1;max-width:220px}
    .ms-trigger{display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#334155;background:#fff;cursor:pointer;transition:all .2s;gap:6px;user-select:none;white-space:nowrap;overflow:hidden}
    .ms-trigger:hover{border-color:#7B1113;background:#fef9f9}
    .ms-trigger.active{border-color:#7B1113;box-shadow:0 0 0 3px rgba(123,17,19,.12)}
    .ms-trigger.has-selection{background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff;border-color:#7B1113}
    .ms-trigger.has-selection:hover{opacity:.92}
    .ms-trigger .ms-label{overflow:hidden;text-overflow:ellipsis;flex:1;text-align:left}
    .ms-trigger .ms-badge{background:rgba(255,255,255,.25);color:#fff;font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;min-width:20px;text-align:center;flex-shrink:0}
    .ms-trigger .ms-arrow{font-size:10px;transition:transform .2s;flex-shrink:0;margin-left:4px}
    .ms-trigger.active .ms-arrow{transform:rotate(180deg)}
    .ms-panel{position:absolute;top:calc(100% + 6px);left:0;width:280px;background:#fff;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.18),0 2px 8px rgba(0,0,0,.08);z-index:200;display:none;overflow:hidden;border:1px solid #e2e8f0;animation:msFadeIn .15s ease}
    @keyframes msFadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
    .ms-panel.show{display:block}
    .ms-panel-header{padding:10px 14px;background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600}
    .ms-panel-header label{display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600}
    .ms-panel-header input[type=checkbox]{accent-color:#fff;width:15px;height:15px;cursor:pointer}
    .ms-search{padding:8px 12px;border-bottom:1px solid #f1f5f9}
    .ms-search input{width:100%;padding:7px 10px 7px 30px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:12px;font-family:inherit;background:#f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat 9px center;outline:none;transition:border-color .2s}
    .ms-search input:focus{border-color:#7B1113}
    .ms-options{max-height:220px;overflow-y:auto;padding:4px 0}
    .ms-options::-webkit-scrollbar{width:5px}
    .ms-options::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
    .ms-option{display:flex;align-items:center;gap:10px;padding:8px 14px;cursor:pointer;transition:background .12s;font-size:13px;color:#334155}
    .ms-option:hover{background:#fef2f2}
    .ms-option.hidden{display:none}
    .ms-option input[type=checkbox]{accent-color:#7B1113;width:15px;height:15px;cursor:pointer;flex-shrink:0}
    .ms-option span{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .ms-count{flex:0 0 auto !important;background:#f1f5f9;color:#64748b;font-size:11px;font-weight:600;padding:1px 8px;border-radius:10px;min-width:22px;text-align:center}
    .ms-option:hover .ms-count{background:#fecaca;color:#991b1b}
    .ms-no-result{padding:16px;text-align:center;color:#94a3b8;font-size:12px;font-style:italic}

    /* Unavailable options (no data for current filter) */
    .ms-option.unavailable{opacity:.5}

    /* Active Filter Tags */
    .filter-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .filter-tag{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;background:linear-gradient(135deg,#fef2f2,#fff1f2);border:1px solid #fecaca;border-radius:20px;font-size:12px;font-weight:500;color:#991b1b;animation:tagIn .2s ease}
    @keyframes tagIn{from{opacity:0;transform:scale(.9)}to{opacity:1;transform:scale(1)}}
    .filter-tag i{font-size:10px}
    .filter-tag .tag-remove{cursor:pointer;margin-left:2px;opacity:.6;transition:opacity .15s;font-size:13px;font-weight:700;line-height:1}
    .filter-tag .tag-remove:hover{opacity:1}

    /* Summary */
    .summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .summary-card{background:#fff;border-radius:14px;padding:22px 24px;box-shadow:0 1px 4px rgba(0,0,0,.06);display:flex;align-items:center;gap:16px;transition:transform .2s}
    .summary-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.1)}
    .summary-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px}
    .summary-card .value{font-size:32px;font-weight:800;line-height:1}
    .summary-card .label{font-size:12px;color:#64748b;font-weight:500;margin-top:4px}
    /* Charts */
    .chart-row{display:grid;grid-template-columns:1.2fr 1fr;gap:20px;margin-bottom:20px}
    .chart-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px}
    .chart-title{font-size:16px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px}
    /* Mitra ranking */
    .mitra-list{list-style:none}
    .mitra-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f1f5f9}
    .mitra-item:last-child{border:none}
    .mitra-rank{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7B1113,#c0392b);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}
    .mitra-name{flex:1;font-size:14px;font-weight:500}
    .mitra-count{font-size:14px;font-weight:700;color:#7B1113}
    /* Table */
    .data-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px}
    .data-table thead{color:#fff}
    .data-table th{padding:12px 14px;text-align:left;font-weight:600;white-space:nowrap;vertical-align:middle;height:48px;border-bottom:1px solid #7B1113}
    .data-table td{padding:10px 14px;border-bottom:1px solid #f1f5f9;white-space:nowrap;vertical-align:middle;height:48px}
    .data-table input[type="checkbox"]{margin:0;vertical-align:middle;cursor:pointer}
    .data-table tbody tr:hover{background:#fef2f2}
    .search-bar{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap}
    .search-bar input{padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;min-width:220px}
    .search-bar input:focus{outline:none;border-color:#7B1113}
    /* Lazy loading table */
    .table-wrap{overflow-x:auto;max-height:600px;overflow-y:auto;position:relative}
    .table-wrap::-webkit-scrollbar{width:6px;height:6px}
    .table-wrap::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
    .table-info{display:flex;align-items:center;justify-content:space-between;margin-top:12px;font-size:12px;color:#64748b}
    .table-info .count{font-weight:600;color:#334155}
    .table-status{text-align:center;padding:20px;font-size:13px;color:#94a3b8}
    .table-status i{margin-right:6px}
    .skeleton-row td{padding:12px 14px}
    .skeleton-bar{height:14px;background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);background-size:200% 100%;border-radius:4px;animation:shimmer 1.5s infinite}
    @keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
    .scroll-sentinel{height:1px}
    .data-table thead th{position:sticky;top:0;z-index:2;background:#7B1113}
    /* Empty */
    .empty-state{text-align:center;padding:80px 20px;color:#94a3b8}
    .empty-state i{font-size:56px;margin-bottom:16px;display:block}
    .empty-state p{font-size:16px;font-weight:500}

    /* Edit Modal */
    .edit-modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:9000;display:none;align-items:center;justify-content:center;animation:fadeIn .2s ease}
    .edit-modal-overlay.show{display:flex}
    .edit-modal{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);width:520px;max-width:95vw;max-height:90vh;overflow-y:auto;animation:popIn .3s ease}
    .edit-modal-header{padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between}
    .edit-modal-header h3{font-size:18px;font-weight:700;color:#1e293b}
    .edit-modal-close{background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;padding:4px}
    .edit-modal-close:hover{color:#1e293b}
    .edit-modal-body{padding:20px 24px}
    .edit-field{margin-bottom:16px}
    .edit-field label{display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px}
    .edit-field input,.edit-field select{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .2s}
    .edit-field input:focus,.edit-field select:focus{outline:none;border-color:#7B1113}
    .edit-modal-footer{padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    @keyframes popIn{from{opacity:0;transform:scale(.9)}to{opacity:1;transform:scale(1)}}

    /* Bulk Delete Toolbar */
    .bulk-toolbar{display:none;align-items:center;gap:12px;padding:10px 16px;background:linear-gradient(135deg,#fef2f2,#fff1f2);border:1px solid #fecaca;border-radius:10px;margin-bottom:12px;animation:tagIn .2s ease}
    .bulk-toolbar.show{display:flex}
    .bulk-toolbar .bulk-count{font-size:14px;font-weight:600;color:#991b1b;flex:1}
    .btn-danger{background:#dc2626;color:#fff;font-size:13px}.btn-danger:hover{background:#b91c1c}

    /* Responsive */
    @media(max-width:1100px){.chart-row-3{grid-template-columns:1fr}}
    @media(max-width:900px){.chart-row{grid-template-columns:1fr}.ms-dropdown{max-width:none}}
    @media(max-width:600px){.summary-grid{grid-template-columns:1fr 1fr}.filter-grid{flex-direction:column}.ms-dropdown{max-width:none}}

    /* Filter loading indicator */
    .filter-loading{position:relative;pointer-events:none;opacity:.6}
    .filter-loading::after{content:'';position:absolute;top:50%;left:50%;width:18px;height:18px;margin:-9px 0 0 -9px;border:2.5px solid #e2e8f0;border-top-color:#7B1113;border-radius:50%;animation:filterSpin .6s linear infinite;z-index:10}
    @keyframes filterSpin{to{transform:rotate(360deg)}}
    .ms-option.unavailable{opacity:.35;order:1;pointer-events:none;cursor:not-allowed;user-select:none}
    .ms-option.unavailable input[type=checkbox]{pointer-events:none}
    .ms-option:not(.unavailable){order:0}
    .ms-options{display:flex;flex-direction:column}
</style>
@endsection

@section('content')

@if(session('error'))
<div class="card" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">{!! nl2br(session('error')) !!}</div>
@endif

@if(!($hasData ?? false))
{{-- EMPTY STATE --}}
<div class="card">
    <div class="empty-state">
        <i class="fas fa-database"></i>
        <p>Belum ada data</p>
    </div>
</div>
@else

{{-- FILTER --}}
<div class="card" id="filterCard">
    <div class="chart-title"><i class="fas fa-filter"></i> Filter Data</div>
    <form method="GET" action="/dashboard" id="filterForm">
        <div class="filter-grid">

            {{-- Program --}}
            <div class="ms-dropdown" data-name="program_id" data-label="Program">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Program</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari program..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($programs as $p)
                        @php $isChecked = is_array(request('program_id')) && in_array($p->id, request('program_id')); @endphp
                        <label class="ms-option" data-id="{{ $p->id }}" data-initial-count="{{ $p->total }}">
                            <input type="checkbox" name="program_id[]" value="{{ $p->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $p->nama_program }}</span>
                            <span class="ms-count">{{ $p->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sub Program --}}
            <div class="ms-dropdown" data-name="sub_program_id" data-label="Sub Program">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Sub Program</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari sub program..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($subPrograms as $sp)
                        @php $isChecked = is_array(request('sub_program_id')) && in_array($sp->id, request('sub_program_id')); @endphp
                        <label class="ms-option" data-id="{{ $sp->id }}" data-initial-count="{{ $sp->total }}">
                            <input type="checkbox" name="sub_program_id[]" value="{{ $sp->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $sp->nama_sub_program }}</span>
                            <span class="ms-count">{{ $sp->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Fakultas --}}
            <div class="ms-dropdown" data-name="fakultas_id" data-label="Fakultas">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Fakultas</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari fakultas..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($fakultas as $f)
                        @php $isChecked = is_array(request('fakultas_id')) && in_array($f->id, request('fakultas_id')); @endphp
                        <label class="ms-option" data-id="{{ $f->id }}" data-initial-count="{{ $f->total }}">
                            <input type="checkbox" name="fakultas_id[]" value="{{ $f->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $f->nama_fakultas }}</span>
                            <span class="ms-count">{{ $f->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Prodi --}}
            <div class="ms-dropdown" data-name="prodi_id" data-label="Prodi">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Prodi</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari prodi..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($prodi as $pr)
                        @php $isChecked = is_array(request('prodi_id')) && in_array($pr->id, request('prodi_id')); @endphp
                        <label class="ms-option" data-id="{{ $pr->id }}" data-initial-count="{{ $pr->total }}">
                            <input type="checkbox" name="prodi_id[]" value="{{ $pr->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $pr->nama_prodi }}</span>
                            <span class="ms-count">{{ $pr->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Penyelenggara --}}
            <div class="ms-dropdown" data-name="penyelenggara" data-label="Penyelenggara">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Penyelenggara</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-options">
                        @php
                            $penyelenggaraValues = (array) request('penyelenggara', []);
                            $countEksternal = \App\Models\DataPlps::where('penyelenggara', 'Eksternal')->count();
                            $countInternal = \App\Models\DataPlps::where('penyelenggara', 'Internal')->count();
                        @endphp
                        <label class="ms-option" data-id="Eksternal" data-initial-count="{{ $countEksternal }}">
                            <input type="checkbox" name="penyelenggara[]" value="Eksternal" {{ in_array('Eksternal', $penyelenggaraValues) ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>Eksternal</span>
                            <span class="ms-count">{{ $countEksternal }}</span>
                        </label>
                        <label class="ms-option" data-id="Internal" data-initial-count="{{ $countInternal }}">
                            <input type="checkbox" name="penyelenggara[]" value="Internal" {{ in_array('Internal', $penyelenggaraValues) ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>Internal</span>
                            <span class="ms-count">{{ $countInternal }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Mitra --}}
            <div class="ms-dropdown" data-name="mitra_id" data-label="Mitra">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Mitra</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari mitra..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($allMitra as $m)
                        @php $isChecked = is_array(request('mitra_id')) && in_array($m->id, request('mitra_id')); @endphp
                        <label class="ms-option" data-id="{{ $m->id }}" data-initial-count="{{ $m->total }}">
                            <input type="checkbox" name="mitra_id[]" value="{{ $m->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $m->nama_mitra }}</span>
                            <span class="ms-count">{{ $m->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Semester TA --}}
            @php
                $semesterTaValues = (array) request('semester_ta', []);
                $isFirstLoad = !request()->has('program_id') && !request()->has('sub_program_id') && !request()->has('fakultas_id') && !request()->has('prodi_id') && !request()->has('penyelenggara') && !request()->has('mitra_id') && !request()->has('semester_ta') && !request()->has('tahun_ajaran');
                if ($isFirstLoad && isset($defaultSemesterTa)) {
                    $semesterTaValues = $defaultSemesterTa;
                }
            @endphp
            <div class="ms-dropdown" data-name="semester_ta" data-label="Semester TA">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Semester TA</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari semester TA..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($allSemesterTa ?? [] as $staObj)
                        @php $isChecked = in_array($staObj->semester_ta, $semesterTaValues); @endphp
                        <label class="ms-option" data-id="{{ $staObj->semester_ta }}" data-initial-count="{{ $staObj->total }}">
                            <input type="checkbox" name="semester_ta[]" value="{{ $staObj->semester_ta }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $staObj->semester_ta }}</span>
                            <span class="ms-count">{{ $staObj->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="ms-dropdown" data-name="tahun_ajaran" data-label="Tahun Ajaran">
                <div class="ms-trigger" onclick="togglePanel(this)">
                    <span class="ms-label">Semua Tahun Ajaran</span>
                    <span class="ms-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <label><input type="checkbox" class="ms-select-all" onchange="toggleAll(this)"> Pilih Semua</label>
                    </div>
                    <div class="ms-search"><input type="text" placeholder="Cari tahun ajaran..." oninput="searchOptions(this)"></div>
                    <div class="ms-options">
                        @foreach($allTahunAjaran ?? [] as $taObj)
                        @php $isChecked = is_array(request('tahun_ajaran')) && in_array($taObj->tahun_ajaran, request('tahun_ajaran')); @endphp
                        <label class="ms-option" data-id="{{ $taObj->tahun_ajaran }}" data-initial-count="{{ $taObj->total }}">
                            <input type="checkbox" name="tahun_ajaran[]" value="{{ $taObj->tahun_ajaran }}" {{ $isChecked ? 'checked' : '' }} onchange="updateDropdown(this)">
                            <span>{{ $taObj->tahun_ajaran }}</span>
                            <span class="ms-count">{{ $taObj->total }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Active Filter Tags --}}
        <div class="filter-tags" id="filterTags"></div>

        <div class="filter-actions">
            <a href="/dashboard" class="btn btn-outline"><i class="fas fa-undo"></i> Reset</a>
            <button type="button" class="btn btn-outline" onclick="openExportModal()"><i class="fas fa-download"></i> Export</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Terapkan Filter</button>
        </div>
    </form>
</div>

<script>
// === Multi-Select Dropdown Logic ===

function togglePanel(trigger) {
    const panel = trigger.nextElementSibling;
    const isOpen = panel.classList.contains('show');

    // Close all other panels first
    document.querySelectorAll('.ms-panel.show').forEach(p => {
        p.classList.remove('show');
        p.closest('.ms-dropdown').querySelector('.ms-trigger').classList.remove('active');
    });

    if (!isOpen) {
        panel.classList.add('show');
        trigger.classList.add('active');
        // Focus search input if exists
        const searchInput = panel.querySelector('.ms-search input');
        if (searchInput) setTimeout(() => searchInput.focus(), 100);
    }
}

// Close panels when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.ms-dropdown')) {
        document.querySelectorAll('.ms-panel.show').forEach(p => {
            p.classList.remove('show');
            p.closest('.ms-dropdown').querySelector('.ms-trigger').classList.remove('active');
        });
    }
});

function toggleAll(selectAllCb) {
    const panel = selectAllCb.closest('.ms-panel');
    const checkboxes = panel.querySelectorAll('.ms-options input[type=checkbox]');
    checkboxes.forEach(cb => {
        if (!cb.closest('.ms-option').classList.contains('hidden')) {
            cb.checked = selectAllCb.checked;
        }
    });
    updateDropdown(selectAllCb);
}

function searchOptions(input) {
    const panel = input.closest('.ms-panel');
    const query = input.value.toLowerCase().trim();
    const options = panel.querySelectorAll('.ms-option');
    let found = 0;
    options.forEach(opt => {
        const text = opt.querySelector('span').textContent.toLowerCase();
        const match = text.includes(query);
        opt.classList.toggle('hidden', !match);
        if (match) found++;
    });
    // Show/hide no-result message
    let noResult = panel.querySelector('.ms-no-result');
    if (found === 0) {
        if (!noResult) {
            noResult = document.createElement('div');
            noResult.className = 'ms-no-result';
            noResult.textContent = 'Tidak ditemukan';
            panel.querySelector('.ms-options').appendChild(noResult);
        }
        noResult.style.display = '';
    } else if (noResult) {
        noResult.style.display = 'none';
    }
    // Update "select all" state
    updateSelectAllState(panel);
}

function updateSelectAllState(panel) {
    const selectAll = panel.querySelector('.ms-select-all');
    if (!selectAll) return;
    const visible = panel.querySelectorAll('.ms-option:not(.hidden) input[type=checkbox]');
    const checked = panel.querySelectorAll('.ms-option:not(.hidden) input[type=checkbox]:checked');
    selectAll.checked = visible.length > 0 && visible.length === checked.length;
    selectAll.indeterminate = checked.length > 0 && checked.length < visible.length;
}

function refreshTriggerLabel(dropdown) {
    const panel = dropdown.querySelector('.ms-panel');
    const trigger = dropdown.querySelector('.ms-trigger');
    const label = trigger.querySelector('.ms-label');
    const dataLabel = dropdown.dataset.label;
    const allCbs = panel.querySelectorAll('.ms-options input[type=checkbox]');
    const checked = panel.querySelectorAll('.ms-options input[type=checkbox]:checked');
    const visibleCbs = panel.querySelectorAll('.ms-option:not(.unavailable) input[type=checkbox]');

    // Remove old badge
    const oldBadge = trigger.querySelector('.ms-badge');
    if (oldBadge) oldBadge.remove();

    if (checked.length === 0 || (checked.length === visibleCbs.length && visibleCbs.length === allCbs.length)) {
        label.textContent = 'Semua ' + dataLabel;
        trigger.classList.remove('has-selection');
    } else if (checked.length === 1) {
        label.textContent = checked[0].closest('.ms-option').querySelector('span').textContent;
        trigger.classList.add('has-selection');
    } else {
        label.textContent = dataLabel;
        trigger.classList.add('has-selection');
        const badge = document.createElement('span');
        badge.className = 'ms-badge';
        badge.textContent = checked.length;
        trigger.insertBefore(badge, trigger.querySelector('.ms-arrow'));
    }
}

function updateDropdown(changedCb) {
    const dropdown = changedCb.closest('.ms-dropdown');
    const panel = dropdown.querySelector('.ms-panel');

    refreshTriggerLabel(dropdown);
    updateSelectAllState(panel);
    updateFilterTags();

    // Trigger cascading filter update via AJAX
    fetchFilterOptions();
}

function updateFilterTags() {
    const container = document.getElementById('filterTags');
    container.innerHTML = '';

    document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
        const dataLabel = dropdown.dataset.label;
        const allCbs = dropdown.querySelectorAll('.ms-options input[type=checkbox]');
        const checked = dropdown.querySelectorAll('.ms-options input[type=checkbox]:checked');

        // Skip adding individual tags if ALL options are checked
        if (checked.length === allCbs.length && allCbs.length > 0) {
            return;
        }

        checked.forEach(cb => {
            const name = cb.closest('.ms-option').querySelector('span').textContent;
            const tag = document.createElement('div');
            tag.className = 'filter-tag';
            tag.innerHTML = `<i class="fas fa-filter"></i> <strong>${dataLabel}:</strong> ${name} <span class="tag-remove" title="Hapus filter" data-cb-id="${cb.name}-${cb.value}">&times;</span>`;
            tag.querySelector('.tag-remove').addEventListener('click', function(e) {
                e.preventDefault();
                cb.checked = false;
                updateDropdown(cb);
            });
            container.appendChild(tag);
        });
    });
}

// === Cascading Filter AJAX Logic ===
let filterFetchTimer = null;
let filterAbortController = null;

function getActiveFilters() {
    const filters = {};
    document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
        const name = dropdown.dataset.name;
        const allCbs = dropdown.querySelectorAll('.ms-options input[type=checkbox]');
        const checked = dropdown.querySelectorAll('.ms-options input[type=checkbox]:checked');
        
        if (checked.length > 0 && checked.length < allCbs.length) {
            filters[name] = Array.from(checked).map(cb => cb.value);
        }
    });
    return filters;
}

function fetchFilterOptions() {
    // Debounce: wait 200ms before fetching
    if (filterFetchTimer) clearTimeout(filterFetchTimer);
    if (filterAbortController) filterAbortController.abort();

    filterFetchTimer = setTimeout(() => {
        const filters = getActiveFilters();
        const hasAnyFilter = Object.keys(filters).length > 0;

        // If no filter is active, reset all dropdowns to show all options with initial counts
        if (!hasAnyFilter) {
            document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
                dropdown.classList.remove('filter-loading');
                dropdown.querySelectorAll('.ms-option').forEach(opt => {
                    opt.classList.remove('unavailable');
                    const countEl = opt.querySelector('.ms-count');
                    if (countEl) {
                        countEl.textContent = opt.dataset.initialCount || '0';
                    }
                });
                const panel = dropdown.querySelector('.ms-panel');
                if (panel) updateSelectAllState(panel);
                refreshTriggerLabel(dropdown);
            });
            updateFilterTags();
            return;
        }

        // Build query string
        const params = new URLSearchParams();
        for (const [key, values] of Object.entries(filters)) {
            values.forEach(v => params.append(key + '[]', v));
        }

        // Show loading state on all dropdowns
        document.querySelectorAll('.ms-dropdown').forEach(d => d.classList.add('filter-loading'));

        filterAbortController = new AbortController();

        fetch('/api/filter-options?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            signal: filterAbortController.signal
        })
        .then(res => res.json())
        .then(data => {
            // Update each dropdown with new options
            document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
                const filterName = dropdown.dataset.name;
                const options = data[filterName];
                if (!options) return;

                // Build a map of id -> total from the API response
                const availableMap = {};
                options.forEach(opt => {
                    availableMap[String(opt.id)] = opt.total;
                });

                // Update each option in this dropdown
                dropdown.querySelectorAll('.ms-option').forEach(optEl => {
                    const optId = String(optEl.dataset.id);
                    const countEl = optEl.querySelector('.ms-count');
                    const cb = optEl.querySelector('input[type=checkbox]');

                    if (cb) cb.disabled = false;
                    if (availableMap.hasOwnProperty(optId)) {
                        // Option is available
                        optEl.classList.remove('unavailable');
                        if (countEl) countEl.textContent = availableMap[optId];
                    } else {
                        // Option has no data under the current filters — show 0 and make it faded
                        optEl.classList.add('unavailable');
                        if (countEl) countEl.textContent = '0';
                    }
                });

                // Update select-all state and trigger label
                const panel = dropdown.querySelector('.ms-panel');
                if (panel) updateSelectAllState(panel);
                refreshTriggerLabel(dropdown);

                dropdown.classList.remove('filter-loading');
            });

            // Update filter tags after all dropdowns are updated
            updateFilterTags();
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Filter fetch error:', err);
            }
            document.querySelectorAll('.ms-dropdown').forEach(d => d.classList.remove('filter-loading'));
        });
    }, 200);
}

// Initialize all dropdowns on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
        const firstCb = dropdown.querySelector('.ms-options input[type=checkbox]');
        if (firstCb) {
            refreshTriggerLabel(dropdown);
            const panel = dropdown.querySelector('.ms-panel');
            updateSelectAllState(panel);
        }
    });

    // Update filter tags
    updateFilterTags();

    // If there are active filters on page load, fetch cascading options
    const activeFilters = getActiveFilters();
    if (Object.keys(activeFilters).length > 0) {
        fetchFilterOptions();
    }
});
</script>

{{-- SUMMARY CARDS --}}
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-icon" style="background:#fef2f2;color:#dc2626"><i class="fas fa-user-graduate"></i></div>
        <div><div class="value">{{ number_format($totalMahasiswa) }}</div><div class="label">Total Mahasiswa PLPS</div></div>
    </div>
    <div class="summary-card">
        <div class="summary-icon" style="background:#eff6ff;color:#2563eb"><i class="fas fa-building"></i></div>
        <div><div class="value">{{ number_format($totalMitra) }}</div><div class="label">Total Mitra</div></div>
    </div>
    <div class="summary-card">
        <div class="summary-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-folder-open"></i></div>
        <div><div class="value">{{ number_format($totalProgram) }}</div><div class="label">Total Program</div></div>
    </div>
    <div class="summary-card">
        <div class="summary-icon" style="background:#fefce8;color:#ca8a04"><i class="fas fa-layer-group"></i></div>
        <div><div class="value">{{ number_format($totalSubProgram) }}</div><div class="label">Total Sub Program</div></div>
    </div>
</div>

{{-- TREN PER SEMESTER (moved above) --}}
<div class="card">
    <div class="chart-title"><i class="fas fa-chart-line" style="color:#7B1113"></i> Grafik Per Semester</div>
    <canvas id="chartTren" height="120"></canvas>
</div>

{{-- TOP 5 ROW: Fakultas | Prodi | Program --}}
<div class="chart-row-3">
    <div class="card">
        <div class="chart-title"><i class="fas fa-chart-bar" style="color:#7B1113"></i> Top 5 Fakultas</div>
        <canvas id="chartFakultas" height="250"></canvas>
    </div>
    <div class="card">
        <div class="chart-title"><i class="fas fa-chart-bar" style="color:#7B1113"></i> Top 5 Prodi Terbanyak</div>
        <canvas id="chartProdi" height="250"></canvas>
    </div>
    <div class="card">
        <div class="chart-title"><i class="fas fa-chart-bar" style="color:#7B1113"></i> Top 5 Program Terbanyak</div>
        <canvas id="chartProgram" height="250"></canvas>
    </div>
</div>

{{-- MITRA RANKING (INTERNAL & EKSTERNAL) --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
    {{-- MITRA EKSTERNAL --}}
    <div class="card">
        <div class="chart-title"><i class="fas fa-circle" style="color:#2173b5"></i> Mitra Eksternal</div>
        <div style="display:flex;align-items:center;margin-bottom:20px">
            <div style="width:100px;height:100px;position:relative;flex-shrink:0">
                <canvas id="chartMitraEksternal"></canvas>
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#1e293b">
                    <span id="pctEksternal">0%</span>
                    <span style="font-size:10px;font-weight:500;color:#64748b;margin-top:-2px">dari total</span>
                </div>
            </div>
            <div style="flex:1;text-align:center">
                <div style="font-size:36px;font-weight:800;color:#2173b5;line-height:1">{{ number_format($totalMitraEksternal) }}</div>
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-top:6px">Total Mitra Eksternal</div>
            </div>
        </div>
        <div style="font-size:12px;font-weight:700;margin-bottom:12px;text-transform:uppercase;color:#475569">Top 5 Mitra Eksternal</div>
        @if($topMitraEksternal->isEmpty())
            <p style="color:#94a3b8;font-size:13px;text-align:center">Tidak ada data mitra eksternal</p>
        @else
            <ul class="mitra-list">
            @php $maxEks = $topMitraEksternal->max('total'); @endphp
            @foreach($topMitraEksternal as $i => $mt)
                <li style="display:flex;align-items:center;padding:10px 0;gap:14px;border-bottom:none">
                    <div style="width:16px;text-align:right;font-size:13px;font-weight:600;color:#64748b">{{ $i+1 }}</div>
                    <div style="flex:1">
                        <div style="font-size:13px;margin-bottom:6px;font-weight:500;color:#334155">{{ $mt->nama_mitra }}</div>
                        <div style="background:#f1f5f9;height:6px;border-radius:3px;width:100%;overflow:hidden">
                            <div style="height:100%;background:#2173b5;border-radius:3px;width:{{ $maxEks > 0 ? ($mt->total / $maxEks) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div style="font-size:13px;font-weight:600;width:32px;text-align:right;color:#1e293b">{{ number_format($mt->total) }}</div>
                </li>
            @endforeach
            </ul>
        @endif
    </div>

    {{-- MITRA INTERNAL --}}
    <div class="card">
        <div class="chart-title"><i class="fas fa-circle" style="color:#0f766e"></i> Mitra Internal</div>
        <div style="display:flex;align-items:center;margin-bottom:20px">
            <div style="width:100px;height:100px;position:relative;flex-shrink:0">
                <canvas id="chartMitraInternal"></canvas>
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#1e293b">
                    <span id="pctInternal">0%</span>
                    <span style="font-size:10px;font-weight:500;color:#64748b;margin-top:-2px">dari total</span>
                </div>
            </div>
            <div style="flex:1;text-align:center">
                <div style="font-size:36px;font-weight:800;color:#0f766e;line-height:1">{{ number_format($totalMitraInternal) }}</div>
                <div style="font-size:12px;color:#64748b;font-weight:500;margin-top:6px">Total Mitra Internal</div>
            </div>
        </div>
        <div style="font-size:12px;font-weight:700;margin-bottom:12px;text-transform:uppercase;color:#475569">Top 5 Mitra Internal</div>
        @if($topMitraInternal->isEmpty())
            <p style="color:#94a3b8;font-size:13px;text-align:center">Tidak ada data mitra internal</p>
        @else
            <ul class="mitra-list">
            @php $maxInt = $topMitraInternal->max('total'); @endphp
            @foreach($topMitraInternal as $i => $mt)
                <li style="display:flex;align-items:center;padding:10px 0;gap:14px;border-bottom:none">
                    <div style="width:16px;text-align:right;font-size:13px;font-weight:600;color:#64748b">{{ $i+1 }}</div>
                    <div style="flex:1">
                        <div style="font-size:13px;margin-bottom:6px;font-weight:500;color:#334155">{{ $mt->nama_mitra }}</div>
                        <div style="background:#f1f5f9;height:6px;border-radius:3px;width:100%;overflow:hidden">
                            <div style="height:100%;background:#0f766e;border-radius:3px;width:{{ $maxInt > 0 ? ($mt->total / $maxInt) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div style="font-size:13px;font-weight:600;width:32px;text-align:right;color:#1e293b">{{ number_format($mt->total) }}</div>
                </li>
            @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- DISTRIBUSI PER PROGRAM MBKM --}}
<div class="card" style="margin-bottom:20px;padding-bottom:30px">
    <div style="font-size:14px;font-weight:700;margin-bottom:24px;text-transform:uppercase;color:#1e293b;letter-spacing:0.5px">Distribusi Per Program MBKM</div>
    @if($distribusiProgram->isEmpty())
        <p style="color:#94a3b8;font-size:13px;text-align:center">Tidak ada data program</p>
    @else
        <div style="display:flex;flex-direction:column;gap:14px">
        @php
            $maxProg = $distribusiProgram->max('total');
            $progColorsList = ['#2173b5', '#673db0', '#0f766e', '#ca8a04', '#ea580c', '#4d7c0f', '#0369a1', '#b91c1c'];
        @endphp
        @foreach($distribusiProgram as $i => $prog)
            <div style="display:flex;align-items:center;gap:20px">
                <div style="width:260px;font-size:14px;font-weight:500;color:#334155">{{ $prog->nama_program }}</div>
                <div style="flex:1;background:transparent;height:12px;display:flex;align-items:center">
                    <div style="height:12px;background:{{ $progColorsList[$i % count($progColorsList)] }};border-radius:6px;width:{{ $maxProg > 0 ? ($prog->total / $maxProg) * 100 : 0 }}%"></div>
                </div>
                <div style="width:80px;text-align:right;font-size:14px;font-weight:500;color:#1e293b">{{ number_format($prog->total, 0, ',', '.') }}</div>
            </div>
        @endforeach
        </div>
    @endif
</div>

{{-- DATA MAHASISWA MBKM TABLE --}}
<div class="card" id="tableCard">
    <div class="chart-title"><i class="fas fa-table" style="color:#7B1113"></i> Data Mahasiswa MBKM</div>
    <div class="search-bar" id="tableSearchBar">
        <input type="text" id="searchNama" placeholder="Cari Nama Mahasiswa..." value="{{ request('search_nama') }}">
        <input type="text" id="searchNim" placeholder="Cari NIM..." value="{{ request('search_nim') }}">
        <button type="button" class="btn btn-primary" onclick="resetAndLoadTable()"><i class="fas fa-search"></i> Cari</button>
    </div>

    @if($isAdmin ?? false)
    <div class="bulk-toolbar" id="bulkToolbar">
        <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="selectedCount">0</span> data dipilih</span>
        <button type="button" class="btn btn-danger" onclick="bulkDeleteSelected()"><i class="fas fa-trash"></i> Hapus Terpilih</button>
        <button type="button" class="btn btn-outline" onclick="clearAllSelections()" style="font-size:13px"><i class="fas fa-times"></i> Batal</button>
    </div>
    @endif

    <div class="table-wrap" id="tableWrap">
        <table class="data-table" id="lazyTable">
            <thead>
                <tr>
                    @if($isAdmin ?? false)<th style="width:40px"><input type="checkbox" id="selectAllRows" onchange="toggleSelectAll(this)" title="Pilih Semua"></th>@endif
                    <th>No</th><th>Program</th><th>Sub Program</th><th>Fakultas</th><th>Program Studi</th>
                    <th>NIM</th><th>Nama Mahasiswa</th><th>Tahun Ajaran</th><th>Semester</th><th>Semester TA</th>
                    <th>Program Kegiatan</th><th>Penyelenggara</th><th>Mitra</th>
                    <th>Dosen Pembimbing</th><th>Jumlah Konversi SKS</th>
                    @if($isAdmin ?? false)<th style="width:60px">Aksi</th>@endif
                </tr>
            </thead>
            <tbody id="tableBody">
            </tbody>
        </table>
        <!-- No sentinel needed for standard pagination -->
    </div>
    <div class="table-info" id="tableInfo" style="display:none; justify-content:flex-end; align-items:center; padding: 15px 20px;">
        <div class="pagination-controls" style="display:flex; gap:15px; align-items:center;">
            <span id="pageText" style="font-size:13px; font-weight:600; color:#475569">0 - 0 / 0</span>
            <div style="display:flex; gap:5px;">
                <button class="btn btn-outline" id="prevPageBtn" onclick="prevPage()" style="padding:6px 12px" title="Previous Page"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-outline" id="nextPageBtn" onclick="nextPage()" style="padding:6px 12px" title="Next Page"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
    <div class="table-status" id="tableStatus"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>
</div>

{{-- EDIT MODAL --}}
@if($isAdmin ?? false)
<div class="edit-modal-overlay" id="editModalOverlay" onmousedown="if(event.target===this)this.dataset.close='1';" onmouseup="if(this.dataset.close==='1' && event.target===this){closeEditModal();} this.dataset.close='0';">
    <div class="edit-modal">
        <div class="edit-modal-header">
            <h3><i class="fas fa-edit" style="color:#7B1113"></i> Edit Data</h3>
            <button class="edit-modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="edit-modal-body">
            <input type="hidden" id="editRowId">
            <div class="edit-field">
                <label>Tahun Ajaran</label>
                <input type="text" id="editTahunAjaran" placeholder="contoh: 2024/2025">
            </div>
            <div class="edit-field">
                <label>Semester</label>
                <select id="editSemester">
                    <option value="GANJIL">GANJIL</option>
                    <option value="GENAP">GENAP</option>
                </select>
            </div>
            <div class="edit-field">
                <label>Semester TA</label>
                <input type="text" id="editSemesterTa" placeholder="contoh: 2024/2025 S1">
            </div>
            <div class="edit-field">
                <label>Kegiatan</label>
                <input type="text" id="editKegiatan" placeholder="Nama kegiatan">
            </div>
            <div class="edit-field">
                <label>Penyelenggara</label>
                <select id="editPenyelenggara">
                    <option value="Eksternal">Eksternal</option>
                    <option value="Internal">Internal</option>
                </select>
            </div>
            <div class="edit-field">
                <label>Mitra</label>
                <input type="text" id="editMitra" placeholder="Nama mitra">
            </div>
            <div class="edit-field">
                <label>Dosen Pembimbing</label>
                <input type="text" id="editDosen" placeholder="Nama dosen pembimbing">
            </div>
            <div class="edit-field">
                <label>Jumlah Konversi SKS</label>
                <input type="number" id="editSks" min="0" placeholder="0">
            </div>
        </div>
        <div class="edit-modal-footer">
            <button class="btn btn-outline" onclick="closeEditModal()">Batal</button>
            <button class="btn btn-primary" id="editSaveBtn" onclick="saveEdit()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
@endif

{{-- EXPORT MODAL --}}
<div class="edit-modal-overlay" id="exportModalOverlay" onmousedown="if(event.target===this)this.dataset.close='1';" onmouseup="if(this.dataset.close==='1' && event.target===this){closeExportModal();} this.dataset.close='0';">
    <div class="edit-modal" style="max-width:400px;text-align:center">
        <div class="edit-modal-header">
            <h3><i class="fas fa-download" style="color:#7B1113"></i> Export Data</h3>
            <button class="edit-modal-close" onclick="closeExportModal()">&times;</button>
        </div>
        <div class="edit-modal-body" style="padding:30px">
            <p style="margin-bottom:20px;font-size:14px;color:#475569">Pilih format file untuk mengunduh laporan sesuai filter yang sedang aktif.</p>
            <div style="display:flex;gap:15px;justify-content:center">
                <button type="button" class="btn btn-outline" onclick="doExport('excel')" style="flex:1;padding:15px;color:#0f766e;border-color:#0f766e">
                    <i class="fas fa-file-excel" style="font-size:24px;margin-bottom:8px;display:block"></i> Excel
                </button>
                <button type="button" class="btn btn-outline" onclick="doExport('pdf')" style="flex:1;padding:15px;color:#b91c1c;border-color:#b91c1c">
                    <i class="fas fa-file-pdf" style="font-size:24px;margin-bottom:8px;display:block"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div class="edit-modal-overlay" id="deleteConfirmOverlay" onmousedown="if(event.target===this)this.dataset.close='1';" onmouseup="if(this.dataset.close==='1' && event.target===this){closeDeleteModal();} this.dataset.close='0';">
    <div class="edit-modal" style="max-width:440px;text-align:center">
        <div class="edit-modal-header" style="border-bottom:none;padding-bottom:0">
            <h3 style="color:#b91c1c"><i class="fas fa-exclamation-triangle" style="color:#dc2626"></i> Konfirmasi Hapus Data</h3>
            <button class="edit-modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="edit-modal-body" style="padding:20px 24px">
            <div style="width:60px;height:60px;border-radius:50%;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 16px">
                <i class="fas fa-trash-alt"></i>
            </div>
            <p style="font-size:15px;color:#1e293b;font-weight:600;margin-bottom:8px">Apakah Anda yakin ingin menghapus data?</p>
            <p style="font-size:13px;color:#64748b;line-height:1.5" id="deleteConfirmText">Tindakan ini akan menghapus data terpilih secara permanen dari database dan tidak dapat dibatalkan.</p>
        </div>
        <div class="edit-modal-footer" style="justify-content:center;gap:12px;background:#f8fafc;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px">
            <button class="btn btn-outline" onclick="closeDeleteModal()" style="padding:10px 20px">Batal</button>
            <button class="btn btn-red" id="confirmDeleteBtn" onclick="executeBulkDelete()" style="padding:10px 24px;background:#dc2626">
                <i class="fas fa-trash-alt"></i> Ya, Hapus Data
            </button>
        </div>
    </div>
</div>

{{-- DASHBOARD ERROR / ALERT MODAL --}}
<div class="edit-modal-overlay" id="dashboardErrorOverlay" onmousedown="if(event.target===this)this.dataset.close='1';" onmouseup="if(this.dataset.close==='1' && event.target===this){closeDashboardErrorModal();} this.dataset.close='0';">
    <div class="edit-modal" style="max-width:460px">
        <div class="edit-modal-header" style="background:#fef2f2;border-bottom:1px solid #fecaca;border-radius:16px 16px 0 0">
            <h3 style="color:#991b1b;font-size:16px;display:flex;align-items:center;gap:8px">
                <i class="fas fa-exclamation-circle" style="color:#dc2626;font-size:18px"></i> <span id="dashboardErrorTitle">Kesalahan Validasi</span>
            </h3>
            <button class="edit-modal-close" onclick="closeDashboardErrorModal()" style="color:#991b1b">&times;</button>
        </div>
        <div class="edit-modal-body" style="padding:20px 24px">
            <div id="dashboardErrorContent" style="color:#374151;font-size:13.5px;line-height:1.6"></div>
        </div>
        <div class="edit-modal-footer" style="background:#f9fafb;border-top:1px solid #e5e7eb">
            <button class="btn btn-red" onclick="closeDashboardErrorModal()" style="padding:8px 20px">Tutup</button>
        </div>
    </div>
</div>

<script>
// === Lazy Loading Table ===
const isAdmin = {{ ($isAdmin ?? false) ? 'true' : 'false' }};
const colCount = isAdmin ? 17 : 15;
let tablePage = 1;
let tableLastPage = 1;
let tableLoading = false;
let tableTotal = 0;
let tableLoadedCount = 0;
let tableAbortController = null;
let selectedIds = new Set();

function getTableFilters() {
    const params = new URLSearchParams();
    // Get active filters from dropdowns
    document.querySelectorAll('.ms-dropdown').forEach(dropdown => {
        const name = dropdown.dataset.name;
        const allCbs = dropdown.querySelectorAll('.ms-options input[type=checkbox]');
        const checked = dropdown.querySelectorAll('.ms-options input[type=checkbox]:checked');
        
        if (checked.length > 0 && checked.length < allCbs.length) {
            checked.forEach(cb => params.append(name + '[]', cb.value));
        }
    });
    // Get search inputs
    const searchNama = document.getElementById('searchNama').value.trim();
    const searchNim = document.getElementById('searchNim').value.trim();
    if (searchNama) params.set('search_nama', searchNama);
    if (searchNim) params.set('search_nim', searchNim);
    return params;
}

function loadTablePage(page) {
    if (tableLoading || page > tableLastPage) return;
    tableLoading = true;

    const statusEl = document.getElementById('tableStatus');
    if (page === 1) {
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat data...';
        statusEl.style.display = '';
    } else {
        // Show skeleton rows for loading more
        showSkeletonRows();
    }

    if (tableAbortController) tableAbortController.abort();
    tableAbortController = new AbortController();

    const params = getTableFilters();
    params.set('page', page);

    fetch('/api/table-data?' + params.toString(), {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        signal: tableAbortController.signal
    })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('tableBody');

        // Remove skeleton rows and old data
        tbody.innerHTML = '';
        tableLoadedCount = 0;

        if (data.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" style="text-align:center;padding:30px;color:#94a3b8">Tidak ada data yang sesuai</td></tr>`;
            statusEl.style.display = 'none';
            document.getElementById('tableInfo').style.display = 'none';
            tableLoading = false;
            return;
        }

        data.data.forEach(row => {
            const tr = document.createElement('tr');
            tr.dataset.id = row.id;
            let html = '';
            if (isAdmin) {
                const checked = selectedIds.has(row.id) ? 'checked' : '';
                html += `<td><input type="checkbox" class="row-cb" value="${row.id}" ${checked} onchange="toggleRowSelect(this)"></td>`;
            }
            html += `
                <td>${row.no}</td>
                <td>${esc(row.program)}</td>
                <td>${esc(row.sub_program)}</td>
                <td>${esc(row.fakultas)}</td>
                <td>${esc(row.prodi)}</td>
                <td>${esc(row.nim)}</td>
                <td>${esc(row.nama)}</td>
                <td>${esc(row.tahun_ajaran)}</td>
                <td>${esc(row.semester)}</td>
                <td>${esc(row.semester_ta)}</td>
                <td>${esc(row.kegiatan)}</td>
                <td>${esc(row.penyelenggara)}</td>
                <td>${esc(row.mitra)}</td>
                <td>${esc(row.dosen_pembimbing)}</td>
                <td>${row.sks}</td>
            `;
            if (isAdmin) {
                html += `<td><button class="btn" style="padding:4px 8px;font-size:12px;background:#7B1113;color:#fff" onclick="openEditModal(${row.id}, this)" title="Edit"><i class="fas fa-edit"></i></button></td>`;
            }
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });

        tableLoadedCount = data.data.length;
        tablePage = data.current_page;
        tableLastPage = data.last_page;
        tableTotal = data.total;
        let start = data.from || 0;
        let end = data.to || 0;

        // Update info bar
        const infoEl = document.getElementById('tableInfo');
        infoEl.style.display = 'flex';
        document.getElementById('pageText').textContent = `${start} - ${end} / ${tableTotal}`;
        
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        
        if(tablePage <= 1) {
            prevBtn.disabled = true;
            prevBtn.style.opacity = '0.5';
        } else {
            prevBtn.disabled = false;
            prevBtn.style.opacity = '1';
        }
        
        if(tablePage >= tableLastPage) {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.5';
        } else {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
        }

        statusEl.style.display = 'none';

        tableLoading = false;
    })
    .catch(err => {
        if (err.name !== 'AbortError') {
            console.error('Table fetch error:', err);
            document.getElementById('tableStatus').innerHTML = '<i class="fas fa-exclamation-circle" style="color:#dc2626"></i> Gagal memuat data';
        }
        tableLoading = false;
    });
}

function showSkeletonRows() {
    const tbody = document.getElementById('tableBody');
    for (let i = 0; i < 5; i++) {
        const tr = document.createElement('tr');
        tr.className = 'skeleton-row';
        let cells = '';
        for (let j = 0; j < colCount; j++) {
            const w = 40 + Math.random() * 60;
            cells += `<td><div class="skeleton-bar" style="width:${w}%"></div></td>`;
        }
        tr.innerHTML = cells;
        tbody.appendChild(tr);
    }
}

function resetAndLoadTable() {
    tablePage = 1;
    tableLastPage = 1;
    tableLoadedCount = 0;
    loadTablePage(1);
}

function esc(str) {
    if (str === null || str === undefined) return '-';
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}

function prevPage() {
    if (tablePage > 1 && !tableLoading) {
        loadTablePage(tablePage - 1);
    }
}

function nextPage() {
    if (tablePage < tableLastPage && !tableLoading) {
        loadTablePage(tablePage + 1);
    }
}

// Search on Enter key
document.getElementById('searchNama')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); resetAndLoadTable(); } });
document.getElementById('searchNim')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); resetAndLoadTable(); } });

// Load first page on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to let filters initialize first
    setTimeout(() => loadTablePage(1), 100);
});

// === Selection & Bulk Delete ===
function toggleRowSelect(cb) {
    const id = parseInt(cb.value);
    if (cb.checked) { selectedIds.add(id); } else { selectedIds.delete(id); }
    updateBulkToolbar();
}

function toggleSelectAll(masterCb) {
    document.querySelectorAll('.row-cb').forEach(cb => {
        cb.checked = masterCb.checked;
        const id = parseInt(cb.value);
        if (masterCb.checked) { selectedIds.add(id); } else { selectedIds.delete(id); }
    });
    updateBulkToolbar();
}

function clearAllSelections() {
    selectedIds.clear();
    document.querySelectorAll('.row-cb').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('selectAllRows');
    if (selectAll) selectAll.checked = false;
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const toolbar = document.getElementById('bulkToolbar');
    if (!toolbar) return;
    const count = selectedIds.size;
    document.getElementById('selectedCount').textContent = count;
    toolbar.classList.toggle('show', count > 0);
}

function bulkDeleteSelected() {
    if (selectedIds.size === 0) return;
    const count = selectedIds.size;
    document.getElementById('deleteConfirmText').textContent = `Tindakan ini akan menghapus ${count} data terpilih secara permanen dari database dan tidak dapat dibatalkan.`;
    document.getElementById('deleteConfirmOverlay').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmOverlay').classList.remove('show');
}

function executeBulkDelete() {
    if (selectedIds.size === 0) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

    fetch('/api/data-plps/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ids: Array.from(selectedIds) })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
        closeDeleteModal();

        if (data.success) {
            showToast(data.message);
            selectedIds.clear();
            updateBulkToolbar();
            resetAndLoadTable();
        } else {
            showDashboardErrorModal('Gagal Menghapus Data', data.message || 'Terjadi kesalahan saat menghapus data.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
        closeDeleteModal();
        showDashboardErrorModal('Gagal Menghapus Data', err.message || 'Terjadi kesalahan koneksi.');
    });
}

function showDashboardErrorModal(title, message) {
    document.getElementById('dashboardErrorTitle').textContent = title;
    const contentDiv = document.getElementById('dashboardErrorContent');
    if (typeof message === 'string' && message.includes('\n')) {
        const lines = message.split('\n').filter(l => l.trim() !== '');
        contentDiv.innerHTML = '<ul style="margin:0;padding-left:18px;display:flex;flex-direction:column;gap:6px">' + 
            lines.map(l => `<li>${esc(l)}</li>`).join('') + 
            '</ul>';
    } else {
        contentDiv.textContent = message;
    }
    document.getElementById('dashboardErrorOverlay').classList.add('show');
}

function closeDashboardErrorModal() {
    document.getElementById('dashboardErrorOverlay').classList.remove('show');
}

function showToast(msg) {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<i class="fas fa-check-circle"></i> ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// === Edit Modal ===
let editRowData = {};

function openEditModal(id, btn) {
    // ... logic remains untouched inside here for now

    const tr = btn.closest('tr');
    const cells = tr.querySelectorAll('td');
    // Get data from the row cells (offset by 1 if admin because of checkbox column)
    const offset = isAdmin ? 1 : 0;
    editRowData = {
        id: id,
        tahun_ajaran: cells[7 + offset]?.textContent?.trim() || '',
        semester: cells[8 + offset]?.textContent?.trim() || '',
        semester_ta: cells[9 + offset]?.textContent?.trim() || '',
        kegiatan: cells[10 + offset]?.textContent?.trim() || '',
        penyelenggara: cells[11 + offset]?.textContent?.trim() || '',
        mitra: cells[12 + offset]?.textContent?.trim() || '',
        dosen_pembimbing: cells[13 + offset]?.textContent?.trim() || '',
        sks: cells[14 + offset]?.textContent?.trim() || '0',
    };

    document.getElementById('editRowId').value = id;
    document.getElementById('editTahunAjaran').value = editRowData.tahun_ajaran;
    document.getElementById('editSemester').value = editRowData.semester;
    document.getElementById('editSemesterTa').value = editRowData.semester_ta;
    document.getElementById('editKegiatan').value = editRowData.kegiatan === '-' ? '' : editRowData.kegiatan;
    document.getElementById('editPenyelenggara').value = editRowData.penyelenggara;
    document.getElementById('editMitra').value = editRowData.mitra === '-' ? '' : editRowData.mitra;
    document.getElementById('editDosen').value = editRowData.dosen_pembimbing === '-' ? '' : editRowData.dosen_pembimbing;
    document.getElementById('editSks').value = parseInt(editRowData.sks) || 0;

    document.getElementById('editModalOverlay').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editModalOverlay').classList.remove('show');
}

function saveEdit() {
    const id = document.getElementById('editRowId').value;
    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const payload = {
        kegiatan_nama: document.getElementById('editKegiatan').value.trim(),
        mitra_nama: document.getElementById('editMitra').value.trim(),
        penyelenggara: document.getElementById('editPenyelenggara').value,
        semester: document.getElementById('editSemester').value,
        semester_ta: document.getElementById('editSemesterTa').value.trim(),
        tahun_ajaran: document.getElementById('editTahunAjaran').value.trim(),
        dosen_pembimbing: document.getElementById('editDosen').value || null,
        sks: parseInt(document.getElementById('editSks').value) || 0,
    };

    fetch(`/api/data-plps/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        return data;
    })
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        if (data.success) {
            closeEditModal();
            showToast('Data berhasil diperbarui');
            resetAndLoadTable();
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        let errMsg = err.message || 'Gagal menyimpan data';
        if (err.errors) {
            errMsg = Object.values(err.errors).flat().join('\n');
        }
        showDashboardErrorModal('Kesalahan Validasi', errMsg);
    });
}

// === Export Modal ===
function openExportModal() {
    document.getElementById('exportModalOverlay').classList.add('show');
}

function closeExportModal() {
    document.getElementById('exportModalOverlay').classList.remove('show');
}

function doExport(type) {
    const qs = window.location.search;
    if (type === 'excel') {
        window.location.href = '/api/export-excel' + qs;
    } else {
        window.open('/api/export-pdf' + qs, '_blank');
    }
    closeExportModal();
}
</script>

{{-- CHART.JS SCRIPTS --}}
<script>
// Color map for fakultas
const fakultasColors = {
    'FTE':'#004f86','FIF':'#c7a12c','FEB':'#239e91','FRI':'#0d8039',
    'FKS':'#59329e','FIK':'#ea580c','FIT':'#00cc52','FKB':'#cc420c',
    'TUP':'#b91c1c','TUS':'#6b7280'
};
function getFakColor(name) {
    return fakultasColors[name.toUpperCase()] || '#9ca3af';
}

// 1. Tren Per Semester (Line) — rendered first since it's at top
const trenLabels = @json($semesters);
const trenSeries = @json($trenSeries);
const trenDatasets = trenSeries.map(s => ({
    label: s.label,
    data: s.data,
    borderColor: getFakColor(s.label),
    backgroundColor: getFakColor(s.label),
    tension: 0.3,
    borderWidth: 3.5,
    pointRadius: 5,
    pointHoverRadius: 7,
    fill: false
}));
new Chart(document.getElementById('chartTren'), {
    type: 'line',
    data: { labels: trenLabels, datasets: trenDatasets },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, padding: 16, font: { size: 11 }}},
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' }},
            x: { grid: { display: false }}
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false }
    }
});

// 2. Top 5 Fakultas (Horizontal Bar — bar chart miring)
const fakLabels = @json($mahasiswaPerFakultas->pluck('nama_fakultas'));
const fakData = @json($mahasiswaPerFakultas->pluck('total'));
new Chart(document.getElementById('chartFakultas'), {
    type: 'bar',
    data: {
        labels: fakLabels,
        datasets: [{
            label: 'Mahasiswa',
            data: fakData,
            backgroundColor: fakLabels.map(n => getFakColor(n)),
            borderRadius: 6,
            maxBarThickness: 32
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.x.toLocaleString() + ' Mahasiswa' }}
        },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' }},
            y: { grid: { display: false }}
        }
    }
});

// 3. Top 5 Prodi (Horizontal Bar)
const prodiLabels = @json($topProdi->pluck('nama_prodi'));
const prodiData = @json($topProdi->pluck('total'));
const prodiColors = prodiLabels.map((_, i) => {
    const opacity = 1 - (i * 0.08);
    return `rgba(123,17,19,${Math.max(opacity, 0.55)})`;
});
new Chart(document.getElementById('chartProdi'), {
    type: 'bar',
    data: {
        labels: prodiLabels,
        datasets: [{
            label: 'Mahasiswa',
            data: prodiData,
            backgroundColor: prodiColors,
            borderRadius: 6,
            maxBarThickness: 32
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        layout: { padding: { left: 5 } },
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.x.toLocaleString() + ' Mahasiswa' }}
        },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' }},
            y: { grid: { display: false }, ticks: { font: { size: 11 }, autoSkip: false, callback: function(value) {
                const label = this.getLabelForValue(value);
                if (label.length > 15) return label.substring(0, 15) + '...';
                return label;
            }}}
        }
    }
});

// 4. Top 5 Program (Horizontal Bar)
const progLabels = @json($topProgram->pluck('nama_program'));
const progData = @json($topProgram->pluck('total'));
const progColors = ['#005A98', '#D3B048', '#31BAAD', '#109344', '#673DB0'];
new Chart(document.getElementById('chartProgram'), {
    type: 'bar',
    data: {
        labels: progLabels,
        datasets: [{
            label: 'Mahasiswa',
            data: progData,
            backgroundColor: progColors.slice(0, progLabels.length),
            borderRadius: 6,
            maxBarThickness: 32
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        layout: { padding: { left: 5 } },
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.x.toLocaleString() + ' Mahasiswa' }}
        },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' }},
            y: { grid: { display: false }, ticks: { font: { size: 11 }, autoSkip: false, callback: function(value) {
                const label = this.getLabelForValue(value);
                if (label.length > 25) return label.substring(0, 25) + '...';
                return label;
            }}}
        }
    }
});

// MITRA DOUGHNUT CHARTS
const totalEks = {{ $totalMitraEksternal ?? 0 }};
const totalInt = {{ $totalMitraInternal ?? 0 }};
const totalAllMitra = totalEks + totalInt;
const pctEks = totalAllMitra > 0 ? Math.round((totalEks / totalAllMitra) * 100) : 0;
const pctInt = totalAllMitra > 0 ? Math.round((totalInt / totalAllMitra) * 100) : 0;

if(document.getElementById('pctEksternal')) document.getElementById('pctEksternal').innerText = pctEks + '%';
if(document.getElementById('pctInternal')) document.getElementById('pctInternal').innerText = pctInt + '%';

if(document.getElementById('chartMitraEksternal')){
    new Chart(document.getElementById('chartMitraEksternal'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [totalEks, totalInt],
                backgroundColor: ['#2173b5', '#e2e8f0'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { tooltip: { enabled: false }, legend: { display: false } },
            animation: { animateScale: true }
        }
    });
}

if(document.getElementById('chartMitraInternal')){
    new Chart(document.getElementById('chartMitraInternal'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [totalInt, totalEks],
                backgroundColor: ['#0f766e', '#e2e8f0'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { tooltip: { enabled: false }, legend: { display: false } },
            animation: { animateScale: true }
        }
    });
}
</script>

@endif {{-- end hasData --}}

@endsection