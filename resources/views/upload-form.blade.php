<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah dan Gabungkan File</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-10 p-6">
        <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">Unggah dan Gabungkan File</h1>

            <!-- Pesan Sukses -->
            @if(session('success_message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success_message') }}
                </div>
            @endif

            <!-- Pesan Error -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Upload -->
            <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">File Semua Tiket:</label>
                    <input type="file" name="all_ticket" class="border rounded w-full p-2" accept=".xlsx,.xls" required>
                </div>
                <div class="mb-6">
                    <label class="block text-lg font-medium mb-2">File Tiket Ditutup:</label>
                    <input type="file" name="close_ticket" class="border rounded w-full p-2" accept=".xlsx,.xls" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Proses File
                </button>
            </form>
        </div>

        <!-- Tabel Data yang Digabungkan -->
        @if(session('merged_data') && count(session('merged_data')) > 1)
            <div class="bg-white shadow-md rounded-lg p-6 w-full mt-10">
                <h2 class="text-xl font-bold mb-4 text-center">Data yang Digabungkan</h2>
                <p class="text-gray-700 text-center mb-4">
                    Jumlah Baris Data: <strong>{{ $rowCount }}</strong>
                </p>
                <div class="overflow-x-auto overflow-y-auto w-full max-h-screen">
                    <table class="table-auto w-full min-w-max border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                @foreach(session('header') as $header)
                                    <th class="border border-gray-300 px-4 py-2 text-left">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('merged_data') as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Form Filter dan Hapus Data -->
        @if(session('merged_data') && count(session('merged_data')) > 1)
            <div class="mt-6 bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
                <h2 class="text-xl font-bold mb-4">Hapus Data Berdasarkan Kolom</h2>
                <form action="{{ route('upload.delete') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-lg font-medium mb-2">Pilih Kolom:</label>
                        <select name="column" id="column-select" class="border rounded w-full p-2" required>
                            @foreach(session('header') as $header)
                                <option value="{{ $header }}">{{ $header }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-medium mb-2">Pilih Nilai:</label>
                        <div id="checkbox-container" class="grid grid-cols-1 gap-2">
                            <!-- Checkbox akan diisi menggunakan JavaScript -->
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                        Hapus Data
                    </button>
                </form>
            </div>
        @endif
    </div>

    <script>
        const mergedData = @json(session('merged_data', []));
        const header = @json(session('header', []));
        const columnSelect = document.getElementById('column-select');
        const checkboxContainer = document.getElementById('checkbox-container');

        columnSelect.addEventListener('change', function () {
            const selectedColumn = this.value;
            const columnIndex = header.indexOf(selectedColumn);
            const uniqueValues = [...new Set(mergedData.map(row => row[columnIndex]))];

            checkboxContainer.innerHTML = '';
            uniqueValues.forEach(value => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'value[]';  // Multiple values can be selected
                checkbox.value = value;
                checkbox.classList.add('mr-2');

                const label = document.createElement('label');
                label.textContent = value;
                label.classList.add('block', 'text-lg');

                const wrapper = document.createElement('div');
                wrapper.classList.add('flex', 'items-center');
                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);

                checkboxContainer.appendChild(wrapper);
            });
        });
    </script>

</body>
</html>
