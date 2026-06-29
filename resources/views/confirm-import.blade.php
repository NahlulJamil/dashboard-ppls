@extends('layouts.app')

@section('title', 'Konfirmasi Import - PLPS')

@section('styles')
<style>
    .confirm-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;max-width:900px;margin:0 auto}

    /* Header */
    .confirm-header{background:linear-gradient(135deg,#f5f3ff,#ede9fe);padding:28px 32px;display:flex;align-items:center;gap:16px;border-bottom:1px solid #e2e8f0}
    .confirm-header-icon{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
    .confirm-header h2{font-size:22px;font-weight:800;color:#1e293b;margin-bottom:2px}
    .confirm-header p{font-size:13px;color:#64748b}

    /* Body */
    .confirm-body{padding:32px}

    /* Stats */
    .confirm-stats{background:#f8fafc;border-radius:12px;padding:32px;text-align:center;margin-bottom:28px;border:1.5px solid #e2e8f0}
    .confirm-check{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 12px}
    .confirm-count{font-size:40px;font-weight:800;color:#1e293b;line-height:1}
    .confirm-label{font-size:14px;color:#64748b;margin-top:6px}

    /* Detail Cards */
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:0}
    @media(max-width:600px){.detail-grid{grid-template-columns:1fr}}
    .detail-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:20px;display:flex;gap:14px}
    .detail-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
    .detail-card h4{font-size:14px;font-weight:700;color:#1e293b;margin-bottom:4px}
    .detail-card p{font-size:12px;color:#64748b;line-height:1.6}

    /* Footer */
    .confirm-footer{padding:20px 32px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:12px;background:#f9fafb}

    /* ── Preview Table ── */
    .preview-section{margin-top:32px}
    .preview-section-header{display:flex;align-items:center;gap:10px;margin-bottom:14px}
    .preview-section-header h3{font-size:17px;font-weight:700;color:#1e293b;margin:0}
    .preview-section-header .badge-count{background:#fef2f2;color:#7B1113;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px}
    .preview-notice{display:flex;align-items:center;gap:8px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:10px 16px;font-size:12.5px;color:#92400e;margin-bottom:14px}
    .preview-notice i{font-size:14px;color:#d97706;flex-shrink:0}

    .preview-wrap{overflow-x:auto;border-radius:12px;border:1.5px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .preview-table{width:100%;border-collapse:collapse;font-size:12.5px;white-space:nowrap}
    .preview-table thead{position:sticky;top:0;z-index:1}
    .preview-table thead tr{background:linear-gradient(135deg,#1e293b,#334155)}
    .preview-table th{padding:11px 14px;text-align:left;font-weight:600;color:#f1f5f9;border-right:1px solid rgba(255,255,255,.08);font-size:12px;letter-spacing:.3px}
    .preview-table th:first-child{border-radius:0}
    .preview-table td{padding:10px 14px;border-bottom:1px solid #f1f5f9;border-right:1px solid #f8fafc;color:#374151;vertical-align:middle;max-width:220px;overflow:hidden;text-overflow:ellipsis}
    .preview-table tbody tr:hover td{background:#fef9f9}
    .preview-table tbody tr:last-child td{border-bottom:none}
    .row-num{display:inline-flex;align-items:center;justify-content:center;width:30px;height:24px;background:#f1f5f9;border-radius:6px;font-size:11px;font-weight:700;color:#64748b}
    .nim-cell{font-family:monospace;font-size:12px;color:#1e293b;font-weight:600}
    .sem-badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:10.5px;font-weight:700}
    .sem-ganjil{background:#fef3c7;color:#92400e}
    .sem-genap{background:#dbeafe;color:#1d4ed8}
    .pen-badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:10.5px;font-weight:700}
    .pen-ek{background:#fce7f3;color:#be185d}
    .pen-in{background:#d1fae5;color:#047857}
    .sks-cell{text-align:center;font-weight:700;color:#7B1113}
    .empty-cell{color:#cbd5e1;font-style:italic}
</style>
@endsection

@section('content')

<div class="confirm-card">
    <div class="confirm-header">
        <div class="confirm-header-icon"><i class="fas fa-clipboard-check"></i></div>
        <div>
            <h2>Konfirmasi Penyimpanan Data</h2>
            <p>Tahap akhir sebelum data diintegrasikan ke dalam dashboard global</p>
        </div>
    </div>

    <div class="confirm-body">
        <div class="confirm-stats">
            <div class="confirm-check"><i class="fas fa-check"></i></div>
            <div class="confirm-count">{{ number_format($rowCount) }}</div>
            <div class="confirm-label">Data valid siap disimpan</div>
        </div>

        <div class="detail-grid">
            <div class="detail-card">
                <div class="detail-icon" style="background:#fef2f2;color:#7B1113"><i class="fas fa-file-excel"></i></div>
                <div>
                    <h4>Informasi Berkas</h4>
                    <p style="margin: 4px 0 0; font-weight: 600; color:#1e293b">{{ $originalName ?? 'Data Excel' }}</p>
                    <p style="margin: 4px 0 0; font-size: 13px; color:#64748b">File berhasil diunggah dan telah melewati semua proses validasi format data.</p>
                </div>
            </div>
            
            <div class="detail-card">
                <div class="detail-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h4>Status Validasi</h4>
                    <p style="margin: 4px 0 0; font-weight: 600; color:#16a34a">Lolos 100%</p>
                    <p style="margin: 4px 0 0; font-size: 13px; color:#64748b">Tidak ada duplikasi data, tidak ada baris ganda, dan seluruh relasi hierarki Program-Kegiatan berstatus aman.</p>
                </div>
            </div>
        </div>
        <div class="confirm-footer">
        <a href="/input-data" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batalkan</a>
        <form action="{{ route('input.confirm') }}" method="POST" style="display:inline" id="confirmForm">
            @csrf
            <button type="submit" class="btn btn-primary" id="confirmBtn" style="padding:10px 28px">
                <i class="fas fa-database"></i> Simpan ke Database
            </button>
        </form>
    </div>

        {{-- ─── PREVIEW TABLE ─── --}}
        @if(!empty($previewRows))
        <div class="preview-section">
            <div class="preview-section-header">
                <i class="fas fa-table" style="color:#7B1113;font-size:18px"></i>
                <h3>Preview Data</h3>
                <span class="badge-count">{{ count($previewRows) }} baris</span>
            </div>

            <div class="preview-notice">
                <i class="fas fa-eye"></i>
                <span>Menampilkan preview 50 baris pertama untuk verifikasi. Jika terdapat kesalahan data, silakan perbarui file template Excel dan unggah kembali file tersebut.</span>
            </div>

            <div class="preview-wrap">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th style="min-width:52px">Baris</th>
                            <th style="min-width:140px">Program</th>
                            <th style="min-width:140px">Sub Program</th>
                            <th style="min-width:100px">Fakultas</th>
                            <th style="min-width:160px">Prodi</th>
                            <th style="min-width:110px">NIM</th>
                            <th style="min-width:180px">Nama Mahasiswa</th>
                            <th style="min-width:100px">Tahun Ajaran</th>
                            <th style="min-width:80px">Semester</th>
                            <th style="min-width:120px">Semester TA</th>
                            <th style="min-width:160px">Program Kegiatan</th>
                            <th style="min-width:90px">Penyelenggara</th>
                            <th style="min-width:200px">Mitra</th>
                            <th style="min-width:160px">Dosen Pembimbing</th>
                            <th style="min-width:60px;text-align:center">SKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $row)
                        <tr class="preview-row" style="display: none;">
                            <td><span class="row-num">{{ $row['excel_row'] }}</span></td>
                            <td>{{ $row['program'] ?: '-' }}</td>
                            <td>{{ $row['sub_program'] ?: '-' }}</td>
                            <td>{{ $row['fakultas'] ?: '-' }}</td>
                            <td>{{ $row['prodi'] ?: '-' }}</td>
                            <td class="nim-cell">{{ $row['nim'] ?: '-' }}</td>
                            <td>{{ $row['nama'] ?: '-' }}</td>
                            <td>{{ $row['tahun_ajaran'] ?: '-' }}</td>
                            <td>
                                @php $sem = strtoupper($row['semester'] ?? ''); @endphp
                                @if($sem === 'GANJIL')
                                    <span class="sem-badge sem-ganjil">GANJIL</span>
                                @elseif($sem === 'GENAP')
                                    <span class="sem-badge sem-genap">GENAP</span>
                                @else
                                    <span class="empty-cell">{{ $row['semester'] ?: '-' }}</span>
                                @endif
                            </td>
                            <td>{{ $row['semester_ta'] ?: '-' }}</td>
                            <td>{{ $row['kegiatan'] ?: '-' }}</td>
                            <td>
                                @php $pen = $row['penyelenggara'] ?? ''; @endphp
                                @if(strtolower($pen) === 'eksternal')
                                    <span class="pen-badge pen-ek">Eksternal</span>
                                @elseif(strtolower($pen) === 'internal')
                                    <span class="pen-badge pen-in">Internal</span>
                                @else
                                    <span class="empty-cell">{{ $pen ?: '-' }}</span>
                                @endif
                            </td>
                            <td title="{{ $row['mitra'] }}">{{ $row['mitra'] ?: '-' }}</td>
                            <td>{{ $row['dosen_pembimbing'] ?: '-' }}</td>
                            <td class="sks-cell">{{ $row['sks'] ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-info" id="tableInfo" style="display:flex; justify-content:space-between; align-items:center; padding: 15px 20px; background:#fff; border: 1.5px solid #e2e8f0; border-top: none; border-radius: 0 0 12px 12px;">
                <span style="font-size:13px; color:#64748b;">Menampilkan <span id="tableShown" style="font-weight:600; color:#1e293b;">0</span> dari <span id="tableTotal" style="font-weight:600; color:#1e293b;">0</span> data</span>
                <div class="pagination-controls" style="display:flex; gap:15px; align-items:center;">
                    <span id="pageText" style="font-size:13px; font-weight:600; color:#475569">0 - 0 / 0</span>
                    <div style="display:flex; gap:5px;">
                        <button class="btn btn-outline" id="prevPageBtn" onclick="prevPage()" style="padding:6px 12px" title="Previous Page"><i class="fas fa-chevron-left"></i></button>
                        <button class="btn btn-outline" id="nextPageBtn" onclick="nextPage()" style="padding:6px 12px" title="Next Page"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        {{-- ─── END PREVIEW TABLE ─── --}}
    </div>

    
</div>

<!-- Progress Overlay Modal -->
<div class="modal-overlay" id="progressModal" style="display:none; z-index: 9999;">
    <div class="modal-box" style="max-width: 500px; text-align: center; padding: 36px 24px;">
        <div style="font-size: 40px; margin-bottom: 16px;" id="progressIcon">
            <i class="fas fa-database fa-spin" style="color: #7B1113;"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px;" id="progressTitle">Menyimpan ke Database...</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;" id="progressStatus">Menyiapkan penyimpanan data...</p>
        
        <div style="background: #e2e8f0; border-radius: 999px; height: 10px; overflow: hidden; margin-bottom: 12px; position: relative;">
            <div id="progressBar" style="background: linear-gradient(135deg,#7B1113,#A41E1E); width: 0%; height: 100%; transition: width 0.2s ease-out; border-radius: 999px;"></div>
        </div>
        <div style="font-size: 14px; font-weight: 700; color: #1e293b;" id="progressPercent">0%</div>
    </div>
</div>

<!-- Generic Error Modal -->
<div class="modal-overlay" id="genericErrorModal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:22px">⚠️</span>
                <div>
                    <div style="font-size: 16px; font-weight: 700;" id="genericErrorTitle">Terjadi Kesalahan</div>
                </div>
            </div>
            <button class="close-btn" onclick="document.getElementById('genericErrorModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            <div style="padding:20px; text-align:center; color:#374151; line-height:1.6" id="genericErrorContent">
                -
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="document.getElementById('genericErrorModal').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const confirmForm = document.getElementById('confirmForm');
const tempPath = '{{ session("last_import_path") }}';
const previewTotalRows = {{ $rowCount }};

confirmForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Show Progress Modal
    const progressModal = document.getElementById('progressModal');
    const progressTitle = document.getElementById('progressTitle');
    const progressStatus = document.getElementById('progressStatus');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    progressModal.style.display = 'flex';
    progressTitle.textContent = "Menyimpan ke Database...";
    progressStatus.textContent = "Menyiapkan penyimpanan...";
    progressBar.style.width = "0%";
    progressPercent.textContent = "0%";

    // Start importing in chunks
    const chunkSize = 1000;
    importChunk(tempPath, 2, chunkSize, previewTotalRows);
});

function importChunk(tempPath, offset, limit, totalRows) {
    const progressStatus = document.getElementById('progressStatus');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    // Update status text
    const endRow = Math.min(offset + limit - 1, totalRows + 1);
    progressStatus.textContent = `Menyimpan baris ${offset - 1} s/d ${endRow - 1} dari ${totalRows}...`;

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
            mode: 'import',
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
            const nextOffset = offset + limit;
            if (nextOffset <= totalRows + 1) {
                // Process next chunk
                importChunk(tempPath, nextOffset, limit, totalRows);
            } else {
                // Done! Update progress to 100%
                progressBar.style.width = "100%";
                progressPercent.textContent = "100%";
                
                setTimeout(() => {
                    document.getElementById('progressModal').style.display = 'none';
                    // Redirect back to input page with success message
                    window.location.href = '{{ url("/input-data") }}';
                }, 400);
            }
        } else {
            throw { message: data.message || "Gagal menyimpan data." };
        }
    })
    .catch(err => {
        document.getElementById('progressModal').style.display = 'none';
        showGenericError(err.message || `Terjadi kesalahan saat menyimpan baris ${offset} - ${endRow}.`);
    });
}

function showGenericError(message) {
    document.getElementById('genericErrorContent').textContent = message;
    document.getElementById('genericErrorModal').style.display = 'flex';
}

// Pagination Logic
const allRows = document.querySelectorAll('.preview-row');
const totalRows = allRows.length;
const perPage = 100;
let currentPage = 1;
const totalPages = Math.ceil(totalRows / perPage);

function renderTable() {
    const start = (currentPage - 1) * perPage;
    const end = Math.min(start + perPage, totalRows);

    allRows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('tableShown').textContent = Math.min(perPage, totalRows - start);
    document.getElementById('tableTotal').textContent = totalRows;
    document.getElementById('pageText').textContent = `${totalRows > 0 ? start + 1 : 0} - ${end} / ${totalRows}`;

    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    if (currentPage <= 1) {
        prevBtn.disabled = true;
        prevBtn.style.opacity = '0.5';
    } else {
        prevBtn.disabled = false;
        prevBtn.style.opacity = '1';
    }

    if (currentPage >= totalPages) {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
    } else {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
}

// Initial render
if (totalRows > 0) {
    renderTable();
}
</script>
@endsection
