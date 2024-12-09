<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PPDB MI AR RIDLO</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50">

        <!-- Halaman Pembuka -->
        <div class="min-h-screen flex flex-col justify-center bg-cover bg-center" style="background-image: url('https://scontent.fsub15-1.fna.fbcdn.net/v/t39.30808-6/456823418_463868469812499_7394878024841566540_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=127cfc&_nc_ohc=h9Y5JdGQVeUQ7kNvgErXLfk&_nc_zt=23&_nc_ht=scontent.fsub15-1.fna&_nc_gid=Ac0fcPka1HMx8pfvA1msNaJ&oh=00_AYDg7m4wRNOd0mtWf2JNHsXDGhPs3_8QHjrRVy0mc9VOWQ&oe=675CB3F6');">
            <div class="bg-black bg-opacity-50 text-white p-6 sm:p-12 rounded-xl text-center mx-4">
                <!-- Logo di atas Judul -->
            <div class="mb-6">
                <img src="https://miarridlo.sch.id/wp-content/uploads/2024/01/logo-mi-ar-ridlo-lingkaran-putih.png" alt="Logo MI Ar Ridlo" class="mx-auto w-24 sm:w-32">
            </div>
            
                <h1 class="text-4xl font-bold mb-4">PPDB MI AR RIDLO</h1>
                <p class="text-xl mb-6">Platform untuk Pendaftaran Siswa Baru MI Ar Ridlo.</p>

                {{-- berikan space antara tombol --}}
                <div class="space-x-4">
                    <a href="{{route('filament.admin.auth.register')}}" class="bg-blue-600 hover:bg-blue-700 text-white text-lg py-2 px-6 rounded-full transition ease-in-out duration-300">
                        Daftar Baru
                    </a>
                <a href="{{route('filament.admin.auth.login')}}" class="bg-blue-600 hover:bg-blue-700 text-white text-lg py-2 px-6 rounded-full transition ease-in-out duration-300">
                    Login
                </a>
                </div>
            </div>
        </div>
    
    </body>
</html>