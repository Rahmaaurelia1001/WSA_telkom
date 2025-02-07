<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticky Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-md fixed top-0 left-0 w-full z-50 flex justify-between items-center px-4 py-3">
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10"> <!-- Ukuran logo diperkecil -->

        <div class="relative flex items-center space-x-2">
            <div class="bg-gray-300 rounded-full p-2">
                <img src="/images/user.png" alt="User" class="w-6 h-6 text-gray-600"> <!-- Ikon user lebih kecil -->
            </div>
            <span class="text-gray-600 font-medium text-xs hidden md:inline">{{ auth()->user()->name }}</span>

            <!-- Dropdown for History and Logout -->
            <div class="relative">
                <button class="bg-gray-200 text-gray-600 p-2 rounded-full hover:bg-gray-300 focus:outline-none" id="dropdownMenuButton">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="dropdown-menu absolute right-0 mt-2 w-44 bg-white shadow-md rounded-lg p-2 hidden" id="dropdownMenu">
                    <a href="{{ route('profile') }}" class="block px-3 py-2 text-gray-600 hover:bg-gray-200 rounded-md text-xs">
                        Lihat Profil
                    </a>
                    <a href="{{ route('excel.index') }}" class="block px-3 py-2 text-gray-600 hover:bg-gray-200 rounded-md text-xs">
                        Lihat Histori
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-gray-600 hover:bg-gray-200 rounded-md text-xs">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
