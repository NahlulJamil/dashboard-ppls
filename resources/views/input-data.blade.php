@extends('layouts.app')

@section('title', 'Data Input - PLPS')

@section('styles')
<style>
    .page-title{font-size:26px;font-weight:800;color:#1e293b;margin-bottom:6px}
    .page-desc{font-size:14px;color:#64748b;line-height:1.7;margin-bottom:32px}

    /* Template Section */
    .template-grid{display:grid;grid-template-columns:1fr 1.2fr;gap:24px;margin-bottom:32px}
    @media(max-width:900px){.template-grid{grid-template-columns:1fr}}

    /* Info Card */
    .info-card{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);padding:28px;display:flex;flex-direction:column}
    .info-card-title{display:flex;align-items:center;gap:10px;font-size:17px;font-weight:700;margin-bottom:18px;color:#1e293b}
    .info-card-title i{color:#7B1113;font-size:20px}
    .info-note{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:20px;flex:1}
    .info-note h4{font-size:14px;font-weight:700;color:#334155;margin-bottom:10px}
    .info-note ul{list-style:none;padding:0}
    .info-note ul li{font-size:13px;color:#64748b;padding:4px 0;padding-left:16px;position:relative;line-height:1.6}
    .info-note ul li::before{content:'•';position:absolute;left:0;color:#7B1113;font-weight:700}
    .download-btn{width:100%;padding:14px;background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;font-family:inherit}
    .download-btn:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 4px 12px rgba(123,17,19,.3)}

    /* Field Table */
    .field-table-card{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden}
    .field-table{width:100%;border-collapse:collapse;font-size:13px}
    .field-table thead{background:#f8fafc}
    .field-table th{padding:14px 18px;text-align:left;font-weight:700;color:#334155;border-bottom:2px solid #e2e8f0;font-size:13px}
    .field-table td{padding:12px 18px;border-bottom:1px solid #f1f5f9;color:#475569;line-height:1.5}
    .field-table tbody tr:hover{background:#fef9f9}
    .type-badge{display:inline-block;padding:2px 10px;border-radius:6px;font-size:11px;font-weight:700;letter-spacing:.3px}
    .type-text{background:#dbeafe;color:#1d4ed8}
    .type-number{background:#fce7f3;color:#be185d}
    .type-year{background:#fef3c7;color:#b45309}
    .type-enum{background:#d1fae5;color:#047857}

    /* Upload Zone */
    .upload-section{margin-bottom:32px}
    .upload-zone{border:2.5px dashed #d1d5db;border-radius:16px;padding:48px 24px;text-align:center;transition:all .3s;cursor:pointer;background:#fafbfc}
    .upload-zone:hover,.upload-zone.dragover{border-color:#7B1113;background:#fef9f9}
    .upload-zone.dragover{border-style:solid;box-shadow:0 0 0 4px rgba(123,17,19,.1)}
    .upload-zone-icon{font-size:42px;color:#d1d5db;margin-bottom:14px;transition:color .3s}
    .upload-zone:hover .upload-zone-icon,.upload-zone.dragover .upload-zone-icon{color:#7B1113}
    .upload-zone h3{font-size:17px;font-weight:700;color:#1e293b;margin-bottom:6px}
    .upload-zone p{font-size:13px;color:#94a3b8;margin-bottom:18px}
    .upload-zone-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 24px;border:2px solid #7B1113;color:#7B1113;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;background:transparent;font-family:inherit}
    .upload-zone-btn:hover{background:#7B1113;color:#fff}
    .upload-zone input[type=file]{display:none}

    /* Upload progress */
    .upload-progress{display:none;margin-top:20px}
    .upload-progress.show{display:block}
    .progress-info{display:flex;align-items:center;gap:12px;padding:14px 20px;background:#f8fafc;border-radius:10px;border:1.5px solid #e2e8f0}
    .progress-info .file-icon{font-size:24px;color:#7B1113}
    .progress-info .file-details{flex:1}
    .progress-info .file-name{font-size:13px;font-weight:600;color:#334155}
    .progress-info .file-size{font-size:11px;color:#94a3b8}
    .upload-actions{display:flex;gap:10px;margin-top:12px;justify-content:flex-end}

    /* History Table */
    .history-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
    .history-header h3{font-size:18px;font-weight:700;color:#1e293b}
    .history-table{width:100%;border-collapse:collapse;font-size:13px}
    .history-table thead{background:#f8fafc}
    .history-table th{padding:12px 16px;text-align:left;font-weight:600;color:#64748b;border-bottom:2px solid #e2e8f0}
    .history-table td{padding:14px 16px;border-bottom:1px solid #f1f5f9;color:#334155}
    .history-table tbody tr:hover{background:#fef9f9}
    .status-success{display:inline-flex;align-items:center;gap:5px;background:#dcfce7;color:#16a34a;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600}
    .history-empty{text-align:center;padding:40px;color:#94a3b8;font-size:14px}
</style>
@endsection

@section('content')

<h1 class="page-title">Data Input & Template Excel</h1>
<br><br>

{{-- ERROR MODAL --}}
<div class="modal-overlay" id="errorModal" style="{{ session('import_errors') ? 'display:flex' : 'display:none' }}">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:22px">⚠️</span>
                <div>
                    <div style="font-size:16px;font-weight:700">Validasi Gagal</div>
                    <div style="font-size:12px;opacity:.9" id="errorCountText">{{ session('import_errors') ? count(session('import_errors')) : 0 }} error ditemukan</div>
                </div>
            </div>
            <button class="close-btn" onclick="document.getElementById('errorModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            <table class="modal-table">
                <thead><tr><th style="width:90px">Baris</th><th>Keterangan Error</th></tr></thead>
                <tbody id="errorTableBody">
                @if(session('import_errors'))
                    @foreach(session('import_errors') as $error)
                        @php
                            $parts = explode(': ', $error, 2);
                            $lineNum = str_replace('Baris ', '', $parts[0] ?? '');
                            $message = $parts[1] ?? $error;
                            $isSimilarity = str_contains($error, 'Kemungkinan typo') || str_contains($error, 'kemiripan');
                        @endphp
                        <tr style="{{ $isSimilarity ? 'background:#fffbeb' : '' }}">
                            <td><span class="error-badge" style="{{ $isSimilarity ? 'background:#fef3c7;color:#b45309' : '' }}">Baris {{ $lineNum }}</span></td>
                            <td style="color:#374151;line-height:1.5">
                                @if($isSimilarity)<i class="fas fa-exclamation-triangle" style="color:#d97706;margin-right:4px"></i>@endif
                                {{ $message }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="document.getElementById('errorModal').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="genericErrorModal" style="{{ session('error') ? 'display:flex' : 'display:none' }}">
    <div class="modal-box">
        <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:22px">⚠️</span>
                <div>
                    <div style="font-size:16px;font-weight:700" id="genericErrorTitle">Terjadi Kesalahan</div>
                </div>
            </div>
            <button class="close-btn" onclick="document.getElementById('genericErrorModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            <div style="padding:20px; text-align:center; color:#374151; line-height:1.6" id="genericErrorContent">
                {{ session('error') }}
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="document.getElementById('genericErrorModal').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

{{-- TEMPLATE INFO + FIELD TABLE --}}
<div class="template-grid">
    {{-- Left: Info Card --}}
    <div class="info-card">
        <div class="info-card-title"><i class="fas fa-info-circle"></i> Informasi Template</div>
        <div class="info-note">
            <h4>Yang harus diperhatikan:</h4>
            <ul>
                <li>Nama sheet dan format template tidak boleh diubah.</li>
                <li>Format data harus sesuai seperti text, number, year, dll</li>
                <li>Baris pertama (header) akan otomatis di-skip, jadi pastikan masukkan data mulai dari baris kedua.</li>
                <li>Data duplikat akan otomatis terdeteksi.</li>
                <li>Download template Excel untuk memastikan format data sesuai.</li>
            </ul>
        </div>
        <a href="/input-data/template" class="download-btn">
            <i class="fas fa-download"></i> Download Template Excel
        </a>
    </div>

    {{-- Right: Field Table --}}
    <div class="field-table-card">
        <table class="field-table">
            <thead><tr><th>Field</th><th>Tipe / Format</th><th>Catatan</th></tr></thead>
            <tbody>
                <tr><td>Program</td><td><span class="type-badge type-text">Text</span></td><td>Nama program MBKM (e.g. Asistensi Mengajar)</td></tr>
                <tr><td>Sub Program</td><td><span class="type-badge type-text">Text</span></td><td>Nama sub program (e.g. Kampus Mengajar)</td></tr>
                <tr><td>Fakultas</td><td><span class="type-badge type-text">Text</span></td><td>Harus sesuai referensi fakultas di Telyu</td></tr>
                <tr><td>Program Studi</td><td><span class="type-badge type-text">Text</span></td><td>Nama prodi (e.g. S1 Informatika)</td></tr>
                <tr><td>NIM</td><td><span class="type-badge type-number">Number</span></td><td>Identitas mahasiswa; perlu unik, valid & tidak duplikat</td></tr>
                <tr><td>Nama Mahasiswa</td><td><span class="type-badge type-text">Text</span></td><td>Nama lengkap mahasiswa</td></tr>
                <tr><td>Tahun Ajaran</td><td><span class="type-badge type-year">Year</span></td><td>Contoh: 2020/2021</td></tr>
                <tr><td>Semester</td><td><span class="type-badge type-enum">Enum</span></td><td>GANJIL atau GENAP</td></tr>
                <tr><td>Semester TA</td><td><span class="type-badge type-text">Text</span></td><td>Contoh: 2020/2021 S2</td></tr>
                <tr><td>Program Kegiatan</td><td><span class="type-badge type-text">Text</span></td><td>Nama kegiatan MBKM</td></tr>
                <tr><td>Penyelenggara</td><td><span class="type-badge type-enum">Enum</span></td><td>Eksternal atau Internal</td></tr>
                <tr><td>Mitra</td><td><span class="type-badge type-text">Text</span></td><td>Nama instansi mitra</td></tr>
                <tr><td>Dosen Pembimbing</td><td><span class="type-badge type-text">Text</span></td><td>Nama dosen pembimbing (opsional)</td></tr>
                <tr><td>Jumlah SKS</td><td><span class="type-badge type-number">Number</span></td><td>Jumlah SKS konversi</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- UPLOAD ZONE --}}
<div class="upload-section">
    <form action="{{ route('input.validate') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf
        <div class="upload-zone" id="uploadZone">
            <div class="upload-zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
            <h3>Tarik & Letakkan File Excel Di Sini</h3>
            <p>Format didukung: .xlsx, .csv</p>
            <button type="button" class="upload-zone-btn" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-folder-open"></i> Pilih File dari Komputer
            </button>
            <input type="file" id="fileInput" name="file" accept=".xlsx,.csv" required>
        </div>

        <div class="upload-progress" id="uploadProgress">
            <div class="progress-info">
                <div class="file-icon"><i class="fas fa-file-excel"></i></div>
                <div class="file-details">
                    <div class="file-name" id="fileName">-</div>
                    <div class="file-size" id="fileSize">-</div>
                </div>
                <button type="button" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:16px" onclick="clearFile()"><i class="fas fa-times"></i></button>
            </div>
            <div class="upload-actions">
                <button type="button" class="btn btn-outline" onclick="clearFile()">Batal</button>
                <button type="submit" class="btn btn-primary" id="uploadBtn">
                    Validasi Data
                </button>
            </div>
        </div>
    </form>
</div>

{{-- HISTORY --}}
<div class="card">
    <div class="history-header">
        <h3><i class="fas fa-history" style="color:#7B1113;margin-right:8px"></i>Riwayat Upload Terakhir</h3>
    </div>

    @if($histories->isEmpty())
        <div class="history-empty">
            <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px"></i>
            Belum ada riwayat upload
        </div>
    @else
    <div style="overflow-x:auto">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Tanggal & Waktu</th>
                    <th>Diunggah Oleh</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $h)
                <tr>
                    <td><i class="fas fa-file-excel" style="color:#16a34a;margin-right:6px"></i>{{ $h->filename }}</td>
                    <td>{{ $h->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}</td>
                    <td>{{ $h->admin->username ?? '-' }}</td>
                    <td><span class="status-success"><i class="fas fa-check-circle"></i> Berhasil ({{ number_format($h->rows_count) }} baris)</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Progress Overlay Modal -->
<div class="modal-overlay" id="progressModal" style="display:none; z-index: 9999;">
    <div class="modal-box" style="max-width: 500px; text-align: center; padding: 36px 24px;">
        <div style="font-size: 40px; margin-bottom: 16px;" id="progressIcon">
            <i class="fas fa-spinner fa-spin" style="color: #7B1113;"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px;" id="progressTitle">Memvalidasi Data...</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;" id="progressStatus">Mengunggah berkas ke server...</p>
        
        <div style="background: #e2e8f0; border-radius: 999px; height: 10px; overflow: hidden; margin-bottom: 12px; position: relative;">
            <div id="progressBar" style="background: linear-gradient(135deg,#7B1113,#A41E1E); width: 0%; height: 100%; transition: width 0.2s ease-out; border-radius: 999px;"></div>
        </div>
        <div style="font-size: 14px; font-weight: 700; color: #1e293b;" id="progressPercent">0%</div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const uploadProgress = document.getElementById('uploadProgress');
const uploadForm = document.getElementById('uploadForm');

// Drag & Drop
['dragenter', 'dragover'].forEach(e => {
    uploadZone.addEventListener(e, ev => { ev.preventDefault(); uploadZone.classList.add('dragover'); });
});
['dragleave', 'drop'].forEach(e => {
    uploadZone.addEventListener(e, ev => { ev.preventDefault(); uploadZone.classList.remove('dragover'); });
});
uploadZone.addEventListener('drop', e => {
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFile(file);
    }
});

// File input change
fileInput.addEventListener('change', function() {
    if (this.files[0]) showFile(this.files[0]);
});

function showFile(file) {
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatSize(file.size);
    uploadProgress.classList.add('show');
    uploadZone.style.display = 'none';
}

function clearFile() {
    fileInput.value = '';
    uploadProgress.classList.remove('show');
    uploadZone.style.display = '';
}

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// Intercept form submit for AJAX chunk validation
uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const file = fileInput.files[0];
    if (!file) return;

    // Show Progress Modal
    const progressModal = document.getElementById('progressModal');
    const progressTitle = document.getElementById('progressTitle');
    const progressStatus = document.getElementById('progressStatus');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    progressModal.style.display = 'flex';
    progressTitle.textContent = "Memvalidasi Data...";
    progressStatus.textContent = "Mengunggah berkas ke server...";
    progressBar.style.width = "0%";
    progressPercent.textContent = "0%";

    // Prepare upload data
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("input.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json().then(err => { throw err; });
            } else {
                return response.text().then(text => {
                    throw { message: `Kesalahan Server (${response.status}): Silakan periksa log server untuk detail.` };
                });
            }
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const totalRows = data.total_rows;
            const tempPath = data.temp_path;

            if (totalRows <= 0) {
                throw { message: "Berkas Excel tidak memiliki baris data (kosong)." };
            }

            // Start validating in chunks
            const chunkSize = 1000;
            const allErrors = [];
            validateChunk(tempPath, 2, chunkSize, totalRows, allErrors);
        } else {
            throw { message: data.message || "Gagal mengunggah berkas." };
        }
    })
    .catch(err => {
        progressModal.style.display = 'none';
        showGenericError(err.message || "Terjadi kesalahan koneksi saat mengunggah berkas.");
    });
});

function validateChunk(tempPath, offset, limit, totalRows, allErrors) {
    const progressStatus = document.getElementById('progressStatus');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    // Update status text
    const endRow = Math.min(offset + limit - 1, totalRows + 1);
    progressStatus.textContent = `Memvalidasi baris ${offset - 1} s/d ${endRow - 1} dari ${totalRows}...`;

    // Calculate percentage
    const processed = offset - 2;
    const percent = Math.floor((processed / totalRows) * 100);
    progressBar.style.width = `${percent}%`;
    progressPercent.textContent = `${percent}%`;

    // Process chunk request
    fetch('{{ route("input.process-chunk") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            temp_path: tempPath,
            mode: 'validate',
            offset: offset,
            limit: limit
        })
    })
    .then(response => {
        if (!response.ok) {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json().then(err => { throw err; });
            } else {
                return response.text().then(text => {
                    throw { message: `Kesalahan Server (${response.status}): Silakan periksa log server untuk detail.` };
                });
            }
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Collect any validation errors
            if (data.errors && data.errors.length > 0) {
                allErrors.push(...data.errors);
            }

            const nextOffset = offset + limit;
            if (nextOffset <= totalRows + 1) {
                // Process next chunk
                validateChunk(tempPath, nextOffset, limit, totalRows, allErrors);
            } else {
                // Done! Update progress to 100%
                progressBar.style.width = "100%";
                progressPercent.textContent = "100%";
                
                setTimeout(() => {
                    document.getElementById('progressModal').style.display = 'none';

                    if (allErrors.length > 0) {
                        // Render and show errors modal
                        renderValidationErrors(allErrors);
                    } else {
                        // All chunks validated successfully! Redirect to confirm page
                        window.location.href = '{{ route("input.confirm.show") }}';
                    }
                }, 400);
            }
        } else {
            throw { message: data.message || "Gagal memproses validasi data." };
        }
    })
    .catch(err => {
        document.getElementById('progressModal').style.display = 'none';
        showGenericError(err.message || `Terjadi kesalahan saat memvalidasi baris ${offset} - ${endRow}.`);
    });
}

function renderValidationErrors(errors) {
    const errorCountText = document.getElementById('errorCountText');
    const errorTableBody = document.getElementById('errorTableBody');

    errorCountText.textContent = `${errors.length} error ditemukan`;
    errorTableBody.innerHTML = '';

    errors.forEach(error => {
        const parts = error.split(': ');
        const lineNum = parts[0].replace('Baris ', '');
        const message = parts[1] || error;
        const isSimilarity = error.includes('Kemungkinan typo') || error.includes('kemiripan');

        const tr = document.createElement('tr');
        if (isSimilarity) tr.style.background = '#fffbeb';

        tr.innerHTML = `
            <td><span class="error-badge" ${isSimilarity ? 'style="background:#fef3c7;color:#b45309"' : ''}>Baris ${lineNum}</span></td>
            <td style="color:#374151;line-height:1.5">
                ${isSimilarity ? '<i class="fas fa-exclamation-triangle" style="color:#d97706;margin-right:4px"></i>' : ''}
                ${message}
            </td>
        `;
        errorTableBody.appendChild(tr);
    });

    document.getElementById('errorModal').style.display = 'flex';
}

function showGenericError(message) {
    document.getElementById('genericErrorContent').textContent = message;
    document.getElementById('genericErrorModal').style.display = 'flex';
}
</script>
@endsection
