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
<body class="flex items-center justify-center min-h-screen" style="background-color: #7B1113;">
    <div class="bg-white p-10 w-full max-w-sm shadow-2xl flex flex-col items-center" style="border-radius: 32px;">
        
        {{-- Logos Placeholder --}}
        <div class="flex items-center justify-center gap-6 mb-4">
            <img src="{{ asset('images/telkom.png') }}" alt="Telkom University" class="h-8 object-contain">
            <img src="{{ asset('images/bpa.png') }}" alt="BPA" class="h-8 object-contain">
        </div>

        <h1 class="text-xl font-medium mb-1" style="color: #ed3237;">Verifikasi CAPTCHA</h1>
        <p class="text-xs text-gray-400 mb-8">Langkah 2 dari 2 — Khusus Super Admin</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-500 text-sm px-4 py-2 rounded-xl mb-6 w-full text-center">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.captcha.verify') }}" class="w-full flex flex-col gap-4">
            @csrf

            {{-- CAPTCHA image + refresh --}}
            <div class="flex flex-col items-center gap-3 w-full mb-2">
                <div id="captcha-container" class="rounded-xl overflow-hidden border border-gray-200">
                    {!! captcha_img('flat') !!}
                </div>
            </div>

            <div class="relative w-full flex items-center gap-2">
                <input type="text" name="captcha" id="captcha" placeholder="Masukkan kode captcha"
                    class="w-full bg-gray-200 text-gray-700 placeholder-gray-500 px-5 py-3 focus:outline-none focus:ring-2 focus:ring-red-400"
                    style="border-radius: 20px; font-size: 13px;"
                    required autofocus autocomplete="off">
                    
                <button type="button" onclick="refreshCaptcha()"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none text-lg shrink-0 px-2 transition" title="Refresh CAPTCHA">
                    <i class="fa fa-rotate-right"></i>
                </button>
            </div>

            <button type="submit"
                class="w-full mt-4 text-white font-medium py-3 px-4 shadow-sm hover:opacity-90 transition cursor-pointer"
                style="background-color: #358ffcff; border-radius: 20px; font-size: 14px;">
                Verifikasi & Masuk
            </button>
            
            <a href="{{ route('login') }}" class="text-center text-xs text-gray-400 hover:text-gray-600 mt-2 transition">
                &larr; Kembali ke halaman login
            </a>
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
