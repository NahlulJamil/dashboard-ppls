<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard PLPS')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('head')
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f0f2f5;color:#1e293b;min-height:100vh;display:flex}

        /* === MAIN AREA === */
        .main-area{flex:1;width:100%;min-height:100vh;display:flex;flex-direction:column}

        /* === TOP HEADER === */
        .top-header{background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff;padding:0 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,.15);height:64px}
        .top-header-left{display:flex;align-items:center;gap:24px;height:100%}
        .top-header h1{font-size:20px;font-weight:700;letter-spacing:-.3px;margin-right:16px;display:flex;align-items:center;gap:8px}
        
        .top-nav{display:flex;height:100%;align-items:center;gap:8px}
        .top-nav a{display:flex;align-items:center;gap:8px;padding:0 16px;height:100%;color:rgba(255,255,255,.8);text-decoration:none;font-weight:600;font-size:14px;transition:all .2s;position:relative}
        .top-nav a:hover{color:#fff;background:rgba(255,255,255,.1)}
        .top-nav a.active{color:#fff}
        .top-nav a.active::after{content:'';position:absolute;bottom:0;left:0;width:100%;height:4px;background:#fff;border-radius:4px 4px 0 0}
        
        .top-header-right{display:flex;align-items:center;gap:16px}
        .admin-info{text-align:right;font-size:13px;opacity:.9}
        .burger-btn{display:none;background:none;border:none;font-size:20px;color:#fff;cursor:pointer;padding:4px}

        /* === CONTENT === */
        .content-area{flex:1;padding:24px 32px;max-width:1400px;width:100%;margin:0 auto}

        /* === FOOTER === */
        .app-footer{padding:16px 32px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;background:#fff}

        /* === BUTTONS (shared) === */
        .btn{padding:8px 20px;border-radius:8px;border:none;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:6px}
        .btn-white{background:#fff;color:#7B1113}.btn-white:hover{background:#f1f1f1}
        .btn-red{background:#7B1113;color:#fff}.btn-red:hover{background:#5a0d0e}
        .btn-outline{background:transparent;border:1.5px solid #cbd5e1;color:#475569}.btn-outline:hover{border-color:#94a3b8}
        .btn-primary{background:linear-gradient(135deg,#7B1113,#A41E1E);color:#fff}.btn-primary:hover{opacity:.9}

        /* === CARD (shared) === */
        .card{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);padding:24px;margin-bottom:20px}

        /* === TOAST === */
        .toast{position:fixed;top:20px;right:20px;background:#16a34a;color:#fff;padding:14px 24px;border-radius:10px;font-size:14px;font-weight:600;z-index:10000;box-shadow:0 4px 20px rgba(0,0,0,.15);animation:slideIn .4s ease}
        @keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}

        /* === MODAL (shared) === */
        .modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;z-index:9999}
        .modal-box{background:#fff;border-radius:12px;width:90%;max-width:700px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden}
        .modal-header{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;padding:18px 24px;display:flex;align-items:center;justify-content:space-between}
        .modal-body{overflow-y:auto;padding:16px 24px;flex:1}
        .modal-footer{padding:14px 24px;border-top:1px solid #e5e7eb;display:flex;justify-content:flex-end;background:#f9fafb;gap:10px}
        .modal-table{width:100%;border-collapse:collapse;font-size:13px}
        .modal-table th{text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;border-bottom:2px solid #e5e7eb}
        .modal-table td{padding:10px 12px;border-bottom:1px solid #f3f4f6}
        .error-badge{background:#fef2f2;color:#dc2626;padding:2px 10px;border-radius:12px;font-weight:700;font-size:12px;white-space:nowrap}
        .close-btn{background:rgba(255,255,255,.2);border:none;color:#fff;font-size:20px;cursor:pointer;border-radius:6px;width:32px;height:32px;display:flex;align-items:center;justify-content:center}

        /* === SUCCESS MODAL (animated popup) === */
        .success-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:10000;animation:fadeIn .3s}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .success-box{background:#fff;border-radius:16px;padding:40px;text-align:center;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:popIn .4s cubic-bezier(.34,1.56,.64,1)}
        @keyframes popIn{from{transform:scale(.7);opacity:0}to{transform:scale(1);opacity:1}}
        .success-icon{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px;animation:checkBounce .6s .3s both}
        @keyframes checkBounce{0%{transform:scale(0)}50%{transform:scale(1.2)}100%{transform:scale(1)}}
        .success-box h2{font-size:22px;font-weight:700;color:#1e293b;margin-bottom:8px}
        .success-box p{font-size:14px;color:#64748b;margin-bottom:24px;line-height:1.6}

        /* === MOBILE MENU OVERLAY === */
        .mobile-menu{display:none;position:fixed;top:64px;left:0;width:100%;background:linear-gradient(135deg,#7B1113,#A41E1E);padding:16px;z-index:90;box-shadow:0 10px 20px rgba(0,0,0,.15);animation:slideDown .3s ease}
        .mobile-menu.show{display:flex;flex-direction:column;gap:8px}
        @keyframes slideDown{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
        .mobile-menu a{display:flex;align-items:center;gap:10px;padding:12px 16px;color:#fff;text-decoration:none;font-weight:600;font-size:15px;border-radius:8px;transition:background .2s}
        .mobile-menu a:hover,.mobile-menu a.active{background:rgba(255,255,255,.15)}

        /* === RESPONSIVE === */
        @media(max-width:900px){
            .top-nav{display:none}
            .burger-btn{display:block}
            .content-area{padding:16px}
            .top-header h1 span{display:none}
        }
    </style>
    @yield('styles')
</head>
<body>

{{-- MAIN AREA --}}
<div class="main-area">
    {{-- TOP HEADER (Admin Only) --}}
    @auth('admin')
    <header class="top-header">
        <div class="top-header-left">
            <h1><i class="fas fa-chart-line"></i> <span>Dashboard PLPS</span></h1>
            
            <nav class="top-nav">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="/input-data" class="{{ request()->is('input-data*') ? 'active' : '' }}">
                    <i class="fas fa-file-upload"></i> Input Data
                </a>
            </nav>
        </div>
        <div class="top-header-right">
            <div class="admin-info">{{ Auth::guard('admin')->user()->username ?? 'Admin' }}</div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-white" style="padding:6px 14px;font-size:12px;color:#7B1113"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
            <button class="burger-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
        </div>
    </header>

    {{-- MOBILE MENU --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="/input-data" class="{{ request()->is('input-data*') ? 'active' : '' }}">
            <i class="fas fa-file-upload"></i> Data Input
        </a>
    </div>
    @endauth

    {{-- CONTENT --}}
    <div class="content-area">
        {{-- SUCCESS TOAST --}}
        @if(session('success') && !session('show_success_modal'))
        <div class="toast" id="successToast">✅ {{ session('success') }}</div>
        <script>setTimeout(()=>{const t=document.getElementById('successToast');if(t)t.style.display='none'},3000)</script>
        @endif

        {{-- SUCCESS MODAL (interactive popup) --}}
        @if(session('show_success_modal'))
        <div class="success-overlay" id="successModal">
            <div class="success-box">
                <div class="success-icon"><i class="fas fa-check"></i></div>
                <h2>Import Berhasil!</h2>
                <p>{{ session('success') }}</p>
                <button class="btn btn-primary" onclick="document.getElementById('successModal').remove()" style="padding:10px 32px;font-size:14px">
                    Tutup
                </button>
            </div>
        </div>
        @endif

        @yield('content')
    </div>

    {{-- FOOTER --}}
    <footer class="app-footer">
        &copy; 2026 PLPS Admin System. All Rights Reserved.
    </footer>
</div>

<script>
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.toggle('show');
}
</script>
@yield('scripts')
</body>
</html>
