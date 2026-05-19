<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 w-full max-w-md rounded shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Login Admin</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-8 relative">
                <input type="text" name="login_id" placeholder="Username / Email"
                    class="w-full border-b border-gray-300 pb-2 focus:outline-none focus:border-blue-500 text-gray-700 placeholder-gray-400"
                    required autofocus value="{{ old('login_id') }}">
            </div>

            <div class="mb-10 relative">
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full border-b border-gray-300 pb-2 focus:outline-none focus:border-blue-500 text-gray-700 placeholder-gray-400 pr-8"
                    required>
                <button type="button" class="absolute right-0 top-0 text-gray-700 hover:text-gray-900 focus:outline-none" onclick="togglePassword()">
                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </button>
            </div>

            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
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
