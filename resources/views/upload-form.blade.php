<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files - Telkom WSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-8 p-4">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-lg mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center">Upload Files</h2>

            <!-- Menampilkan pesan sukses -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Menampilkan pesan error -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form untuk upload file -->
            <form action="{{ route('file.process') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label class="block mb-2 text-lg font-medium">All Ticket List:</label>
        <input type="file" name="all_ticket" class="border p-2 rounded w-full" accept=".xlsx,.xls" required>
        @error('all_ticket')
            <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block mb-2 text-lg font-medium">Close Ticket List:</label>
        <input type="file" name="close_ticket" class="border p-2 rounded w-full" accept=".xlsx,.xls" required>
        @error('close_ticket')
            <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded w-full hover:bg-red-700 transition duration-200">
        Process Files
    </button>
</form>

        </div>
    </div>

</body>
</html>
