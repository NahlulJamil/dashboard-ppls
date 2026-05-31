@extends('layouts.app')

@section('title', 'Konfirmasi Import - PLPS')

@section('styles')
<style>
    .confirm-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;max-width:780px;margin:0 auto}

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
</div>

@endsection

@section('scripts')
<script>
document.getElementById('confirmForm').addEventListener('submit', function() {
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});
</script>
@endsection
