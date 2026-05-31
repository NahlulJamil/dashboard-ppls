<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-6 md:p-10 min-h-screen">
    
    <div class="max-w-2xl mx-auto flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color: #7B1113;">Super Admin</h1>
            <p class="text-gray-600 mt-1">Tambah admin baru</p>
        </div>
        <div class="flex flex-col items-end justify-center">
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-8" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Admin Baru</h2>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> Terdapat kesalahan pada input.
            </div>
        @endif

        <form action="{{ route('admins.store') }}" method="POST" class="flex flex-col gap-5">
            @csrf
            
            <div class="relative w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" 
                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 focus:bg-white transition"
                    required>
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="relative w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Masukkan password" 
                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 focus:bg-white transition"
                        required oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="absolute right-4 top-3 text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword()">
                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Password requirements --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs space-y-2 mt-1">
                <p id="req-length" class="flex items-center gap-2 text-gray-400">
                    <i class="fa fa-circle text-[8px]"></i> Minimal 8 karakter
                </p>
                <p id="req-upper" class="flex items-center gap-2 text-gray-400">
                    <i class="fa fa-circle text-[8px]"></i> Mengandung huruf besar (A-Z)
                </p>
                <p id="req-special" class="flex items-center gap-2 text-gray-400">
                    <i class="fa fa-circle text-[8px]"></i> Mengandung karakter spesial (!?@#$%^&amp;* dll)
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 mt-4">
                <a class="text-gray-500 hover:text-gray-700 font-medium py-2 px-4 transition" href="{{ route('admins.index') }}">
                    Batal
                </a>
                <button class="bg-[#7B1113] hover:bg-red-900 text-white font-medium py-2 px-6 rounded-lg transition shadow-sm flex items-center gap-2" type="submit">
                    <i class="fas fa-save"></i> Simpan Admin
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(value) {
            const hasLength  = value.length >= 8;
            const hasUpper   = /[A-Z]/.test(value);
            const hasSpecial = /[!?@#$%^&*()\-_=+\[\]{};:'",.<>\/\\`~]/.test(value);

            setReq('req-length',  hasLength);
            setReq('req-upper',   hasUpper);
            setReq('req-special', hasSpecial);
        }

        function setReq(id, passed) {
            const el = document.getElementById(id);
            if (passed) {
                el.classList.remove('text-gray-400', 'text-red-500');
                el.classList.add('text-green-500');
                el.querySelector('i').classList.remove('fa-circle');
                el.querySelector('i').classList.add('fa-check-circle');
            } else {
                el.classList.remove('text-green-500', 'text-gray-400');
                el.classList.add('text-red-500');
                el.querySelector('i').classList.remove('fa-check-circle');
                el.querySelector('i').classList.add('fa-circle');
            }
        }
    </script>
</body>
</html>
