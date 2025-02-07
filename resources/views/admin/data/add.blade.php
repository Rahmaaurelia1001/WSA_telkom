<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constanta List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">

    <!-- Card Container -->
    <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-bold text-center text-gray-700 pb-6">Constanta List</h2>

        <!-- Tabel Responsif -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm text-gray-700">
                <thead class="bg-red-600 text-white">
                    <tr class="text-left">
                        <th class="py-3 px-4">Service Type</th>
                        <th class="py-3 px-4">Customer Type</th>
                        <th class="py-3 px-4">Customer Segment</th>
                        <th class="py-3 px-4">Segment</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Classification</th>
                        <th class="py-3 px-4">Status Closed</th>
                        <th class="py-3 px-4">Closed Reopen By</th>
                        <th class="py-3 px-4">TTR</th>
                        <th class="py-3 px-4">Marking Type</th>
                        <th class="py-3 px-4">Z</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($markingData as $data)
                        <tr class="border-b hover:bg-gray-50 text-center">
                            <td class="py-3 px-4">{{ $data->service_type }}</td>
                            <td class="py-3 px-4">{{ $data->customer_type }}</td>
                            <td class="py-3 px-4">{{ $data->customer_segment }}</td>
                            <td class="py-3 px-4">{{ $data->segmen }}</td>
                            <td class="py-3 px-4">{{ $data->status }}</td>
                            <td class="py-3 px-4">{{ $data->classification }}</td>
                            <td class="py-3 px-4">{{ $data->status_closed }}</td>
                            <td class="py-3 px-4">{{ $data->closed_reopen_by }}</td>
                            <td class="py-3 px-4">{{ $data->ttr }}</td>
                            <td class="py-3 px-4">{{ $data->marking_type }}</td>
                            <td class="py-3 px-4">{{ $data->z }}</td>
                            <td class="py-3 px-4 flex justify-center space-x-3">
                                <a href="{{ route('admin.data.editkonstanta', $data->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('admin.data.deleteKonstanta', $data->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus konstanta ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Tambah Konstanta -->
        <div class="mt-6">
            <button id="showFormBtn" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700">
                Tambah Konstanta
            </button>
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="w-full block text-center px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700">
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Form Tambah Konstanta -->
        <div id="addConstantaForm" class="mt-6 hidden bg-gray-50 shadow-md rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Konstanta Baru</h3>

            <form action="{{ route('admin.data.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="column" class="block text-sm font-medium text-gray-700">Pilih Kolom</label>
                    <select id="column" name="column" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500">
                        <option value="service_type">Service Type</option>
                        <option value="customer_type">Customer Type</option>
                        <option value="customer_segment">Customer Segment</option>
                        <option value="segmen">Segment</option>
                        <option value="status">Status</option>
                        <option value="classification">Classification</option>
                        <option value="status_closed">Status Closed</option>
                        <option value="closed_reopen_by">Closed Reopen By</option>
                        <option value="ttr">TTR</option>
                        <option value="marking_type">Marking Type</option>
                        <option value="z">Z</option>
                    </select>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">Nilai Konstanta</label>
                    <input type="text" id="value" name="value" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500" placeholder="Masukkan nilai konstanta" required>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-md shadow-md hover:bg-red-700 transition duration-300">
                        Simpan Konstanta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('showFormBtn').addEventListener('click', function () {
            document.getElementById('addConstantaForm').classList.toggle('hidden');
        });
    </script>

</body>
</html>
