<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Konfirmasi penghapusan
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this user?')) {
                event.preventDefault(); // Batalkan penghapusan jika tidak yakin
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container max-w-4xl mx-auto p-6">
        <!-- Card Wrapper (Tengah Layar) -->
        <div class="bg-white shadow-md rounded-lg p-6 w-full">
            <!-- Judul dalam Card -->
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">Users List</h2>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <!-- Kepala Tabel Warna Merah -->
                    <thead class="bg-red-600">
                        <tr>
                            <th class="py-3 px-6 text-left text-white font-semibold border-b">Name</th>
                            <th class="py-3 px-6 text-left text-white font-semibold border-b">Email</th>
                            <th class="py-3 px-6 text-left text-white font-semibold border-b">Actions</th>
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
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="confirmDelete(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tombol Kembali ke Dashboard dalam Card -->
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full bg-red-500 text-white text-center py-3 rounded-lg shadow-lg hover:bg-red-600">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</body>


</html>
