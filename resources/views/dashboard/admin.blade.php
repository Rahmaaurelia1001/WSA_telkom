<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-6">
        <!-- Dashboard Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Admin Dashboard</h2>
            <a href="{{ route('logout') }}" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Logout</a>
        </div>

        <!-- Create New User Form -->
        <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4 text-center">Create New User</h2>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Name Field -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" class="w-full p-2 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('name') border-red-500 @enderror"
                           name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" class="w-full p-2 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('email') border-red-500 @enderror"
                           name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" class="w-full p-2 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 @error('password') border-red-500 @enderror"
                           name="password" required>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg focus:outline-none hover:bg-blue-600">Create User</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
