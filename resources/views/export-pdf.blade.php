<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Mahasiswa MBKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* PDF Export Styling - Optimized for A4 Print */
        @page { size: A4 portrait; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER */
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-circle {
            width: 40px; height: 40px; border-radius: 50%; background: #fff;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; border: 1px solid #e2e8f0;
        }
        .logo-circle img { width: 100%; height: 100%; object-fit: contain; }
        .logo-text { font-family: 'Merriweather', serif; color: #1e3a5f; }
        .logo-text-title { font-size: 14px; font-weight: 700; }
        .logo-text-sub { font-size: 10px; color: #64748b; font-family: 'Inter', sans-serif; }
        .header-info { text-align: right; font-size: 9px; color: #64748b; line-height: 1.5; }
        
        /* BANNER */
        .banner { background: #1e3a5f; color: #fff; padding: 16px 20px; margin-bottom: 15px; }
        .banner-title { font-family: 'Merriweather', serif; font-size: 18px; font-weight: 700; margin-bottom: 6px; }
        .banner-sub { font-size: 11px; color: #94a3b8; }
        
        /* FILTERS */
        .filters { display: flex; flex-wrap: wrap; gap: 16px; padding: 0 5px; font-size: 10px; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; }
        .filter-item { color: #64748b; }
        .filter-val { color: #0f172a; font-weight: 600; margin-left: 4px; }
        
        /* SECTIONS */
        .section-title { font-size: 10px; font-weight: 700; letter-spacing: 1px; color: #475569; text-transform: uppercase; margin-bottom: 12px; margin-top: 25px; }
        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
        
        /* STATS */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-val { font-size: 28px; font-weight: 800; color: #1e3a5f; line-height: 1; margin-bottom: 6px; }
        .stat-label { font-size: 11px; font-weight: 600; color: #334155; }
        .stat-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
        
        /* CHARTS / BARS */
        .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .bar-label { width: 140px; font-size: 10px; font-weight: 500; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bar-track { flex: 1; height: 8px; border-radius: 4px; display: flex; align-items: center; }
        .bar-fill { height: 100%; border-radius: 4px; }
        .bar-val { width: 40px; text-align: right; font-size: 10px; font-weight: 600; }
        
        /* MITRA */
        .mitra-box { margin-bottom: 20px; }
        .mitra-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .doughnut-placeholder {
            width: 70px; height: 70px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .doughnut-placeholder::before {
            content: ''; position: absolute; width: 54px; height: 54px; background: #fff; border-radius: 50%;
        }
        .doughnut-text-container { position: absolute; z-index: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .doughnut-text { font-size: 14px; font-weight: 800; line-height: 1; text-align: center; }
        .doughnut-sub { font-size: 8px; font-weight: 400; color: #64748b; }
        .mitra-stats { flex: 1; font-size: 10px; }
        .mitra-stats strong { font-size: 14px; color: #0f172a; }
        
        /* FACULTY TAGS */
        .fak-tag {
            display: inline-block; padding: 2px 6px; border-radius: 4px; color: #fff; font-size: 9px; font-weight: 700; width: 35px; text-align: center; margin-right: 6px;
        }
        
        /* FOOTER */
        .footer { margin-top: 40px; padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
        
        /* Print Button */
        .print-btn {
            position: fixed; bottom: 30px; right: 30px; background: #7B1113; color: white; border: none; padding: 12px 24px;
            border-radius: 30px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(123,17,19,0.3);
            display: flex; align-items: center; gap: 8px; z-index: 1000;
        }
        .print-btn:hover { background: #5a0c0e; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg>
        Cetak PDF
    </button>

    <div class="container">
        <!-- HEADER -->
        <div class="header-top">
            <div class="logo-area">
                <div class="logo-circle">
                    <img src="{{ asset('images/bpa.png') }}" alt="BPA">
                </div>
                <div>
                    <div class="logo-text logo-text-title">Badan Pengembangan Akademik</div>
                    <div class="logo-text-sub">Sistem Informasi PLPS</div>
                </div>
            </div>
            <div class="header-info">
                Diekspor: {{ now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}<br>
                Oleh: {{ $exporterName }}<br>
                Halaman 1 dari 1
            </div>
        </div>
        
        <!-- BANNER -->
        <div class="banner">
            <div class="banner-title">Laporan Data Mahasiswa Pembelajaran diluar Program Studi</div>
            <div class="banner-sub">
                Program PLPS &bull; {{ request('fakultas_id') ? count(request('fakultas_id', [])) . ' Fakultas' : 'Semua Fakultas' }} &bull; Tahun Ajaran {{ request('tahun_ajaran') ? implode(', ', (array)request('tahun_ajaran')) : 'Semua' }}
            </div>
        </div>
        
        <!-- FILTERS -->
        <div class="filters">
            <div class="filter-item">Program: <span class="filter-val">{{ request('program_id') ? count((array)request('program_id')) . ' Terpilih' : 'Semua Program' }}</span></div>
            <div class="filter-item">Fakultas: <span class="filter-val">{{ request('fakultas_id') ? count((array)request('fakultas_id')) . ' Terpilih' : 'Semua Fakultas' }}</span></div>
            <div class="filter-item">Semester: <span class="filter-val">{{ !empty($activeSemesterTa) ? implode(', ', $activeSemesterTa) : 'Semua Semester' }}</span></div>
            <div class="filter-item">Penyelenggara: <span class="filter-val">{{ request('penyelenggara') ? implode(', ', (array)request('penyelenggara')) : 'Semua' }}</span></div>
        </div>

        <!-- RINGKASAN UMUM -->
        <div class="section-title">Ringkasan Umum</div>
        <div class="stat-grid">
            <div>
                <div class="stat-val" style="color:#0f172a">{{ number_format($totalMahasiswa, 0, ',', '.') }}</div>
                <div class="stat-label">Total Mahasiswa</div>
                <div class="stat-sub">Berdasarkan filter aktif</div>
            </div>
            <div>
                <div class="stat-val" style="color:#475569">{{ number_format($totalProgram, 0, ',', '.') }}</div>
                <div class="stat-label">Jenis Program</div>
                <div class="stat-sub">MBKM yang diikuti</div>
            </div>
            <div>
                <div class="stat-val" style="color:#2173b5">{{ number_format($totalMitraEksternal, 0, ',', '.') }}</div>
                <div class="stat-label">Mitra Eksternal</div>
                @php $totalMitra = $totalMitraEksternal + $totalMitraInternal; @endphp
                <div class="stat-sub">{{ $totalMitra > 0 ? round(($totalMitraEksternal/$totalMitra)*100, 1) : 0 }}% dari total</div>
            </div>
            <div>
                <div class="stat-val" style="color:#0f766e">{{ number_format($totalMitraInternal, 0, ',', '.') }}</div>
                <div class="stat-label">Mitra Internal</div>
                <div class="stat-sub">{{ $totalMitra > 0 ? round(($totalMitraInternal/$totalMitra)*100, 1) : 0 }}% dari total</div>
            </div>
        </div>

        <!-- MITRA & DISTRIBUSI -->
        <div class="section-title">Distribusi & Top 5 Mitra</div>
        <div class="row">
            <!-- Mitra Eksternal -->
            <div class="col mitra-box">
                <div style="font-size:11px;font-weight:700;color:#2173b5;margin-bottom:12px;display:flex;align-items:center;gap:6px">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#2173b5"></span> Mitra Eksternal
                </div>
                <div class="mitra-header">
                    @php $pctEks = $totalMitra > 0 ? round(($totalMitraEksternal/$totalMitra)*100) : 0; @endphp
                    <div class="doughnut-placeholder" style="background: conic-gradient(#2173b5 0% {{ $pctEks }}%, #bfdbfe {{ $pctEks }}% 100%);">
                        <div class="doughnut-text-container">
                            <div class="doughnut-text">{{ $pctEks }}%</div>
                            <div class="doughnut-sub">dari total</div>
                        </div>
                    </div>
                    <div class="mitra-stats">
                        Total Instansi<br>
                        <strong>{{ number_format($totalMitraEksternal, 0, ',', '.') }} Instansi</strong>
                    </div>
                </div>
                <div style="font-size:9px;font-weight:700;margin-bottom:8px;color:#475569;text-transform:uppercase">Top 5 Mitra Eksternal</div>
                @php $maxEks = $topMitraEksternal->max('total'); @endphp
                @foreach($topMitraEksternal as $i => $mt)
                    <div class="bar-row">
                        <div style="width:12px;font-size:9px;color:#94a3b8;font-weight:700">{{ $i+1 }}</div>
                        <div class="bar-label" style="width:160px;font-size:9px">{{ $mt->nama_mitra }}</div>
                        <div class="bar-track">
                            <div class="bar-fill" style="background:#2173b5;width:{{ $maxEks > 0 ? ($mt->total/$maxEks)*100 : 0 }}%"></div>
                        </div>
                        <div class="bar-val" style="font-size:9px">{{ number_format($mt->total, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Mitra Internal -->
            <div class="col mitra-box">
                <div style="font-size:11px;font-weight:700;color:#0f766e;margin-bottom:12px;display:flex;align-items:center;gap:6px">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0f766e"></span> Mitra Internal
                </div>
                <div class="mitra-header">
                    @php $pctInt = $totalMitra > 0 ? round(($totalMitraInternal/$totalMitra)*100) : 0; @endphp
                    <div class="doughnut-placeholder" style="background: conic-gradient(#0f766e 0% {{ $pctInt }}%, #a7f3d0 {{ $pctInt }}% 100%);">
                        <div class="doughnut-text-container">
                            <div class="doughnut-text">{{ $pctInt }}%</div>
                            <div class="doughnut-sub">dari total</div>
                        </div>
                    </div>
                    <div class="mitra-stats">
                        Total Instansi<br>
                        <strong>{{ number_format($totalMitraInternal, 0, ',', '.') }} Instansi</strong>
                    </div>
                </div>
                <div style="font-size:9px;font-weight:700;margin-bottom:8px;color:#475569;text-transform:uppercase">Top 5 Mitra Internal</div>
                @php $maxInt = $topMitraInternal->max('total'); @endphp
                @foreach($topMitraInternal as $i => $mt)
                    <div class="bar-row">
                        <div style="width:12px;font-size:9px;color:#94a3b8;font-weight:700">{{ $i+1 }}</div>
                        <div class="bar-label" style="width:160px;font-size:9px">{{ $mt->nama_mitra }}</div>
                        <div class="bar-track">
                            <div class="bar-fill" style="background:#0f766e;width:{{ $maxInt > 0 ? ($mt->total/$maxInt)*100 : 0 }}%"></div>
                        </div>
                        <div class="bar-val" style="font-size:9px">{{ number_format($mt->total, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FAKULTAS & PRODI -->
        <div style="border-top: 1px solid #e2e8f0; margin-top: 10px;"></div>
        <div class="section-title">Pengiriman Mahasiswa Per Fakultas & Program Studi</div>
        <div class="row" style="margin-bottom:30px">
            <!-- Fakultas -->
            <div class="col">
                <div style="font-size:11px;font-weight:700;margin-bottom:12px">Per Fakultas</div>
                @php 
                    $maxFak = $mahasiswaPerFakultas->max('total');
                    $fakColors = [
                        'FIF'=>'#c7a12c','FEB'=>'#239e91','FTE'=>'#004f86',
                        'FKB'=>'#cc420c','FIT'=>'#00cc52','FRI'=>'#0d8039',
                        'FIK'=>'#ea580c','FKS'=>'#59329e','TUP'=>'#b91c1c','TUS'=>'#6b7280'
                    ];
                @endphp
                @foreach($mahasiswaPerFakultas as $fak)
                    @php 
                        $tag = $fak->nama_fakultas;
                        $color = $fakColors[$fak->nama_fakultas] ?? '#64748b';
                    @endphp
                    <div class="bar-row">
                        <div class="bar-label" style="width:160px;display:flex;align-items:center">
                            <span class="fak-tag" style="background:{{ $color }}">{{ $tag }}</span>
                            {{ $fak->nama_fakultas }}
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="background:{{ $color }};width:{{ $maxFak > 0 ? ($fak->total/$maxFak)*100 : 0 }}%"></div>
                        </div>
                        <div class="bar-val">{{ number_format($fak->total, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Prodi -->
            <div class="col">
                <div style="font-size:11px;font-weight:700;margin-bottom:12px">Top 8 Program Studi</div>
                @php $maxProdi = $topProdi->max('total'); @endphp
                @foreach($topProdi as $pr)
                    <div class="bar-row">
                        <div class="bar-label" style="width:160px;display:flex;align-items:center;color:#475569">
                            {{ $pr->nama_prodi }}
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="background:#7B1113;width:{{ $maxProdi > 0 ? ($pr->total/$maxProdi)*100 : 0 }}%"></div>
                        </div>
                        <div class="bar-val">{{ number_format($pr->total, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- PROGRAM -->
        <div style="border-top: 1px solid #e2e8f0; margin-top: 10px;"></div>
        <div class="section-title">Distribusi Per Program MBKM</div>
        <div style="margin-bottom:30px">
            @php 
                $maxProg = $distribusiProgram->max('total');
                $progColors = ['#2173b5', '#673db0', '#0f766e', '#ca8a04', '#ea580c', '#4d7c0f', '#0369a1', '#b91c1c'];
            @endphp
            @foreach($distribusiProgram as $i => $prog)
                <div class="bar-row" style="margin-bottom:10px">
                    <div class="bar-label" style="width:250px;font-size:11px">{{ $prog->nama_program }}</div>
                    <div class="bar-track" style="height:10px">
                        <div class="bar-fill" style="background:{{ $progColors[$i % count($progColors)] }};width:{{ $maxProg > 0 ? ($prog->total/$maxProg)*100 : 0 }}%;border-radius:5px"></div>
                    </div>
                    <div class="bar-val" style="font-size:11px">{{ number_format($prog->total, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>

        <!-- MAGANG BIDANG KERJA -->
        <div style="border-top: 1px solid #e2e8f0; margin-top: 10px;"></div>
        <div class="section-title">Posisi / Bidang Kerja Mahasiswa Magang</div>
        <div style="font-size:10px;color:#475569;margin-bottom:15px">Berdasarkan kolom Nama Kegiatan - hanya untuk program Magang/Praktik Industri ({{ number_format($totalMagang, 0, ',', '.') }} mahasiswa)</div>
        
        <div class="row">
            <!-- Split magang to 2 cols -->
            @php 
                $magangChunks = $topMagang->chunk(ceil($topMagang->count() / 2)); 
                $maxMagang = $topMagang->max('total');
            @endphp
            @foreach($magangChunks as $chunk)
            <div class="col">
                @foreach($chunk as $mg)
                    <div class="bar-row" style="margin-bottom:12px">
                        <div class="bar-label" style="width:140px;display:flex;align-items:center;gap:6px">
                            <i class="fas fa-briefcase" style="color:#94a3b8"></i> {{ $mg->nama_kegiatan }}
                        </div>
                        <div class="bar-track" style="height:6px">
                            <div class="bar-fill" style="background:#64748b;width:{{ $maxMagang > 0 ? ($mg->total/$maxMagang)*100 : 0 }}%"></div>
                        </div>
                        <div class="bar-val" style="width:30px">{{ number_format($mg->total, 0, ',', '.') }}</div>
                        <div style="width:30px;text-align:right;font-size:9px;color:#64748b">
                            {{ $totalMagang > 0 ? number_format(($mg->total/$totalMagang)*100, 1, ',', '.') : 0 }}%
                        </div>
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div>
                <strong>Badan Pengembangan Akademik</strong> &bull; Sistem Informasi PLPS<br>
                Dokumen ini digenerate otomatis dan bersifat resmi
            </div>
            <div style="text-align:right">
                Hal. 1 / 1
            </div>
        </div>

    </div>
</body>
</html>
