<form action="/import" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Import</button>
</form>

@if(session('success'))
<div style="color:green;">
    {{ session('success') }}
</div>
@endif

{{-- ============ ERROR POPUP MODAL ============ --}}
@if(session('import_errors'))
<div id="errorModal" style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
">
    <div style="
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 700px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        overflow: hidden;
    ">
        {{-- Header --}}
        <div style="
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">⚠️</span>
                <div>
                    <div style="font-size: 16px; font-weight: 700;">Import Gagal</div>
                    <div style="font-size: 12px; opacity: 0.9;">
                        {{ count(session('import_errors')) }} error ditemukan — perbaiki file Excel lalu import ulang
                    </div>
                </div>
            </div>
            <button onclick="document.getElementById('errorModal').style.display='none'"
                style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: #fff;
                    font-size: 20px;
                    cursor: pointer;
                    border-radius: 6px;
                    width: 32px; height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">✕</button>
        </div>

        {{-- Error List --}}
        <div style="
            overflow-y: auto;
            padding: 16px 24px;
            flex: 1;
        ">
            <table style="
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            ">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th style="text-align: left; padding: 8px 12px; color: #6b7280; font-weight: 600; width: 90px;">Baris</th>
                        <th style="text-align: left; padding: 8px 12px; color: #6b7280; font-weight: 600;">Keterangan Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('import_errors') as $error)
                    @php
                        // Parse "Baris 5: Penyelenggara tidak valid" → line=5, message=Penyelenggara tidak valid
                        $parts = explode(': ', $error, 2);
                        $lineNum = str_replace('Baris ', '', $parts[0] ?? '');
                        $message = $parts[1] ?? $error;
                    @endphp
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="
                            padding: 10px 12px;
                            vertical-align: top;
                        ">
                            <span style="
                                background: #fef2f2;
                                color: #dc2626;
                                padding: 2px 10px;
                                border-radius: 12px;
                                font-weight: 700;
                                font-size: 12px;
                                white-space: nowrap;
                            ">Baris {{ $lineNum }}</span>
                        </td>
                        <td style="
                            padding: 10px 12px;
                            color: #374151;
                            line-height: 1.5;
                        ">{{ $message }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div style="
            padding: 14px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            background: #f9fafb;
        ">
            <button onclick="document.getElementById('errorModal').style.display='none'"
                style="
                    background: #ef4444;
                    color: #fff;
                    border: none;
                    padding: 8px 20px;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                ">Tutup</button>
        </div>
    </div>
</div>
@endif

{{-- Fallback: error biasa (bukan dari import) --}}
@if(session('error') && !session('import_errors'))
<div style="color:red;">
    {!! nl2br(session('error')) !!}
</div>
@endif