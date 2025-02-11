<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="sticky top-0 bg-white shadow-md flex justify-between items-center px-6 py-3 z-50">
    <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10">

    <div class="relative">
        <!-- Profile Section -->
        <div class="flex items-center space-x-2 cursor-pointer" id="dropdownButton">
        <button onclick="window.history.back()" class="text-xs text-gray-600 hover:text-red-500 hover:underline cursor-pointer focus:outline-none">
    Back
</button>
            <div class="bg-gray-300 rounded-full p-2">
                <img src="/images/user.png" alt="User" class="w-6 h-6 text-gray-600">
            </div>
            <span class="text-gray-600 font-medium">{{ auth()->user()->name }}</span>
            <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg hidden">
            <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">View Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="block">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>


<script>
    // Toggle dropdown visibility
    const dropdownButton = document.getElementById('dropdownButton');
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