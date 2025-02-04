<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Download</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- Link ke CSS yang sudah dikompilasi -->
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Riwayat Download</h2>

        <!-- Tabel dengan Tailwind CSS -->
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Nama File</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Link File Excel</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Waktu Download</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $history)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $history->file_name }}</td>
                        <td class="py-2 px-4 border-b text-sm text-gray-600">
                            <a href="{{ asset('storage/' . $history->file_path) }}" class="text-blue-500 hover:underline" download>Download</a>
                        </td>
                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $history->status }}</td>
                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $history->download_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
