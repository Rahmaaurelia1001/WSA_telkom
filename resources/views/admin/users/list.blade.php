<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 relative min-h-screen">

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-700">Users List</h2>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-left text-gray-700 font-semibold border-b">Name</th>
                        <th class="py-3 px-6 text-left text-gray-700 font-semibold border-b">Email</th>
                        <th class="py-3 px-6 text-left text-gray-700 font-semibold border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 border-b text-gray-600">{{ $user->name }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $user->email }}</td>
                            <td class="py-3 px-6 border-b flex items-center space-x-4">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                    class="text-blue-500 hover:underline">Edit</a>
                                
                                <!-- Delete Form -->
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Button di kanan paling bawah -->
    <div class="absolute bottom-6 right-6">
        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-red-500 text-white rounded-lg shadow-lg hover:bg-red-600">
            Kembali ke Dashboard
        </a>
    </div>

</body>
</html>
