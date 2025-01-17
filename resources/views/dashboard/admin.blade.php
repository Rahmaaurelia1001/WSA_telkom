<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .dropdown-content {
            display: none;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Header -->
    <div class="bg-white shadow-md flex justify-between items-center px-6 py-3">
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10">

        <div class="relative dropdown">
            <!-- Profile Section -->
            <div class="flex items-center space-x-2 cursor-pointer">
                <div class="bg-gray-300 rounded-full p-2">
                    <img src="/images/user.png" alt="User" class="w-6 h-6 text-gray-600">
                </div>
                <span class="text-gray-600 font-medium">{{ auth()->user()->name }}</span>
                <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <!-- Dropdown Menu -->
            <div class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg dropdown-content">
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

    <!-- Main Content -->
    <div class="flex flex-col items-center justify-center h-screen space-y-4">
        <div class="flex items-center space-x-10" style="margin-top: -100px;">
            <h1 class="text-3xl font-bold mt-6 mb-6">
                Welcome to <br>
                <span class="text-red-600 text-4xl">Admin Dashboard</span>
            </h1>
            <img src="/images/logo-telkom.png" alt="Telkom Indonesia" style="width: 200px; margin-top: -45px;">
        </div>

        <!-- Menu Buttons -->
        <div class="flex space-x-6">
            <!-- Create User Menu -->
            <a href="{{ route('admin.users.create') }}">
                <div class="border-4 border-blue-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2 cursor-pointer">
                    <img src="/images/user-plus.png" alt="Create User" class="h-20">
                    <span class="text-blue-600 text-lg">Create User</span>
                </div>
            </a>

            <!-- Add Data Menu -->
            <a href="{{ route('admin.data.add') }}">
                <div class="border-4 border-green-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2 cursor-pointer">
                    <img src="/images/data-add.png" alt="Add Data" class="h-20">
                    <span class="text-green-600 text-lg">Add Data</span>
                </div>
            </a>

            <!-- User List Menu -->
            <a href="{{ route('admin.users.list') }}">
                <div class="border-4 border-red-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2 cursor-pointer">
                    <img src="/images/user-list.png" alt="User List" class="h-20">
                    <span class="text-red-600 text-lg">User List</span>
                </div>
            </a>
        </div>
    </div>

</body>

</html>
