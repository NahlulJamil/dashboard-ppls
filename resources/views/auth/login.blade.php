<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen" style="background-color: #7B1113;">
    <div class="bg-white p-10 w-full max-w-sm shadow-2xl flex flex-col items-center" style="border-radius: 32px;">
        
        {{-- Logos Placeholder --}}
        <div class="flex items-center justify-center gap-6 mb-6">
            <img src="{{ asset('images/telkom.png') }}" alt="Telkom University" class="h-10 object-contain">
            <img src="{{ asset('images/bpa.png') }}" alt="BPA" class="h-10 object-contain">
        </div>

        <h1 class="text-xl font-medium mb-8" style="color: #ed3237;">Login admin</h1>

        @if ($errors->any())
            <div class="bg-red-50 text-red-500 text-sm px-4 py-2 rounded-xl mb-6 w-full text-center">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="w-full flex flex-col gap-4">
            @csrf

            <div class="relative w-full">
                <input type="text" name="login_id" placeholder="Email / Username"
                    class="w-full bg-gray-200 text-gray-700 placeholder-gray-500 px-5 py-3 focus:outline-none focus:ring-2 focus:ring-red-400"
                    style="border-radius: 20px; font-size: 13px;"
                    required autofocus value="{{ old('login_id') }}">
            </div>

            <div class="relative w-full">
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full bg-gray-200 text-gray-700 placeholder-gray-500 px-5 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-red-400"
                    style="border-radius: 20px; font-size: 13px;"
                    required>
                <button type="button" class="absolute right-4 top-3 text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword()">
                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </button>
            </div>

            <button type="submit"
                class="w-full mt-4 text-white font-medium py-3 px-4 shadow-sm hover:opacity-90 transition cursor-pointer"
                style="background-color: #358ffcff; border-radius: 20px; font-size: 14px;">
                Login
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
