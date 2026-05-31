<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* === SUCCESS MODAL (animated popup) === */
        .success-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:10000;animation:fadeIn .3s}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .success-box{background:#fff;border-radius:16px;padding:40px;text-align:center;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:popIn .4s cubic-bezier(.34,1.56,.64,1)}
        @keyframes popIn{from{transform:scale(.7);opacity:0}to{transform:scale(1);opacity:1}}
        .success-icon{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px;animation:checkBounce .6s .3s both}
        @keyframes checkBounce{0%{transform:scale(0)}50%{transform:scale(1.2)}100%{transform:scale(1)}}
        .success-box h2{font-size:22px;font-weight:700;color:#1e293b;margin-bottom:8px; margin-top:0;}
        .success-box p{font-size:14px;color:#64748b;margin-bottom:24px;line-height:1.6}
        .btn-primary{background-color:#7B1113; color:#fff; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s;}
        .btn-primary:hover{background-color:#5a0c0e;}
    </style>
</head>
<body class="bg-gray-50 p-6 md:p-10 min-h-screen">
    
    <div class="max-w-5xl mx-auto flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color: #7B1113;">Super Admin</h1>
            <p class="text-gray-600 mt-1">Kelola akun admin</p>
        </div>
        <div class="flex flex-col items-end justify-center">
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-sm p-8" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Admin</h2>
            </div>
            <a href="{{ route('admins.create') }}" class="bg-[#7B1113] hover:bg-red-900 text-white font-medium py-2 px-5 rounded-lg flex items-center gap-2 transition shadow-sm">
                <i class="fas fa-plus "></i> Create Admin
            </a>
        </div>

        {{-- Success message handled by SweetAlert below --}}

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color: #7B1113; color: white;">
                        <th class="px-6 py-4 font-semibold text-sm rounded-l-xl">No</th>
                        <th class="px-6 py-4 font-semibold text-sm">Username</th>
                        <th class="px-6 py-4 font-semibold text-sm rounded-r-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach($admins as $index => $admin)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium">{{ $admin->username }}</td>
                        <td class="px-6 py-4 flex gap-3">
                            <a href="{{ route('admins.edit', $admin->id_admin) }}" class="border border-gray-400 text-gray-600 hover:bg-gray-100 font-medium py-1 px-4 rounded-full flex items-center gap-2 transition">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admins.destroy', $admin->id_admin) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete border border-red-500 text-red-500 hover:bg-red-50 font-medium py-1 px-4 rounded-full flex items-center gap-2 transition">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- SUCCESS MODAL (interactive popup) --}}
    @if(session('success'))
    <div class="success-overlay" id="successModal">
        <div class="success-box">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Berhasil!</h2>
            <p>{{ session('success') }}</p>
            <button class="btn-primary" onclick="document.getElementById('successModal').remove()" style="padding:10px 32px;font-size:14px;font-weight:600;">
                Tutup
            </button>
        </div>
    </div>
    @endif

    <script>
        // SweetAlert2 is only used for Delete Confirmation now.

        // Konfirmasi Hapus dengan SweetAlert2
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Admin yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
