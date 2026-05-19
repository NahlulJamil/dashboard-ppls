<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Admin</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admins.store') }}" method="POST">
            @csrf
            <div class="mb-6 relative">
                <input type="text" name="username" placeholder="Username" class="w-full border-b border-gray-300 pb-2 focus:outline-none focus:border-blue-500 text-gray-700 placeholder-gray-400"required>
            </div>
            
            <div class="mb-2 relative">
                <input type="password" id="password" name="password" placeholder="Password" 
                    class="w-full border-b border-gray-300 pb-2 focus:outline-none focus:border-blue-500 text-gray-700 placeholder-gray-400 pr-10"
                    required oninput="checkPasswordStrength(this.value)">
                <button type="button" class="absolute right-0 top-0 text-gray-700 hover:text-gray-900 focus:outline-none" onclick="togglePassword()">
                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            {{-- Password requirements --}}
            <div class="mb-6 text-xs space-y-1">
                <p id="req-length" class="flex items-center gap-1 text-gray-400">
                    <i class="fa fa-circle text-[6px]"></i> Minimal 8 karakter
                </p>
                <p id="req-upper" class="flex items-center gap-1 text-gray-400">
                    <i class="fa fa-circle text-[6px]"></i> Mengandung huruf besar (A-Z)
                </p>
                <p id="req-special" class="flex items-center gap-1 text-gray-400">
                    <i class="fa fa-circle text-[6px]"></i> Mengandung karakter spesial (!?@#$%^&amp;* dll)
                </p>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Create
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('admins.index') }}">
                    Cancel
                </a>
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
