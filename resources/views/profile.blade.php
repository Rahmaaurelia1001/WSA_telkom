<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <div class="bg-white shadow-md flex justify-between items-center px-6 py-3">
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10">
    </div>

    <!-- Profil Content -->
    <div class="flex flex-col items-center justify-center min-h-screen space-y-6">
        <h1 class="text-4xl font-bold text-gray-800">Profil Pengguna</h1>

        <!-- Card Profil -->
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex flex-col items-center space-y-4">
                <!-- Gambar Profil -->
                <div class="bg-gray-200 rounded-full w-32 h-32 flex items-center justify-center">
                    <img src="/images/user.png" alt="Foto Profil" class="w-28 h-28 rounded-full">
                </div>

                <!-- Informasi Pengguna -->
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="text-gray-600"><strong>Bergabung Sejak:</strong> {{ $user->created_at->format('d M Y') }}</p>
                </div>

                <!-- Tombol Kembali ke Dashboard di dalam Card -->
                <a href="{{ route('dashboard') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-red-700 mt-4">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

</body>
</html>
