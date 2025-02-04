<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-md flex justify-between items-center px-6 py-3">
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10">
        <div class="relative flex items-center space-x-2">
            <div class="bg-gray-300 rounded-full p-2">
                <img src="/images/user.png" alt="User" class="w-6 h-6 text-gray-600">
            </div>
            <span class="text-gray-600 font-medium hidden md:inline">{{ auth()->user()->name }}</span>

            <!-- Dropdown untuk Histori dan Logout -->
            <div class="relative">
                <button class="bg-gray-200 text-gray-600 p-2 rounded-full hover:bg-gray-300 focus:outline-none" id="dropdownMenuButton">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden" id="dropdownMenu">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-md">
                        Lihat Profil
                    </a>
                    <a href="{{ route('excel.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md mt-4">
                        Lihat Histori
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-md">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col items-center justify-center h-screen">
        <div class="flex items-center space-x-10" style="margin-top: -100px;">
            <h1 class="text-3xl font-bold mt-6 mb-6">
                Welcome to <br>
                <span class="text-red-600 text-4xl">Dashboard Page</span>
            </h1>
            <img src="/images/logo-telkom.png" alt="Telkom Indonesia" style="width: 200px; margin-top: -45px;">
        </div>

        <!-- WSA Icon -->
        <a href="{{ route('upload.form') }}" class="mt-4">
            <div class="border-4 border-red-600 rounded-lg p-4 flex items-center justify-center bg-white shadow-md overflow-hidden">
                <img src="/images/assurance.png" alt="WSA" class="h-20">
            </div>
        </a>
    </div>

    <script>
        // Toggle dropdown visibility
        const dropdownButton = document.getElementById('dropdownMenuButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Optional: Close the dropdown if clicked outside
        window.addEventListener('click', (event) => {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>