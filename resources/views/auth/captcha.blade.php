<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi CAPTCHA - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 w-full max-w-md rounded shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi CAPTCHA</h1>
        <p class="text-sm text-gray-500 mb-6">Langkah 2 dari 2 — Khusus Super Admin</p>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.captcha.verify') }}">
            @csrf

            {{-- CAPTCHA image + refresh --}}
            <div class="flex items-center gap-3 mb-4">
                <div id="captcha-container">
                    {!! captcha_img('flat') !!}
                </div>
                <button type="button" onclick="refreshCaptcha()"
                    class="text-gray-600 hover:text-gray-900 focus:outline-none text-lg" title="Refresh CAPTCHA">
                    <i class="fa fa-rotate-right"></i>
                </button>
            </div>

            <div class="mb-8 relative">
                <input type="text" name="captcha" id="captcha" placeholder="Masukkan kode di atas"
                    class="w-full border-b border-gray-300 pb-2 focus:outline-none focus:border-blue-500 text-gray-700 placeholder-gray-400"
                    required autofocus autocomplete="off">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Verifikasi & Masuk
                </button>
                <a href="{{ route('login') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    &larr; Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
        function refreshCaptcha() {
            $.ajax({
                type: 'GET',
                url: '/refresh-captcha',
                success: function (data) {
                    $('#captcha-container').html(data.captcha);
                    $('#captcha').val('').focus();
                }
            });
        }
    </script>
</body>
</html>
