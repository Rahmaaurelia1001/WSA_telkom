<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constanta List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-full">

<div class="flex flex-col h-full">
    
    <!-- Wrapper untuk tabel dengan background putih -->
    <div class="flex-grow bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-bold text-center text-gray-700 py-6">Constanta List</h2>

        <!-- Tabel Responsif -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-red-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Service Type</th>
                        <th class="py-3 px-4 text-left font-semibold">Customer Type</th>
                        <th class="py-3 px-4 text-left font-semibold">Customer Segment</th>
                        <th class="py-3 px-4 text-left font-semibold">Segment</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                        <th class="py-3 px-4 text-left font-semibold">Classification</th>
                        <th class="py-3 px-4 text-left font-semibold">Status Closed</th>
                        <th class="py-3 px-4 text-left font-semibold">Closed Reopen By</th>
                        <th class="py-3 px-4 text-left font-semibold">TTR</th>
                        <th class="py-3 px-4 text-left font-semibold">Marking Type</th>
                        <th class="py-3 px-4 text-left font-semibold">Z</th>
                        <th class="py-3 px-4 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($markingData as $data)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-700">{{ $data->service_type }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->customer_type }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->customer_segment }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->segmen }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->status }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->classification }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->status_closed }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->closed_reopen_by }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->ttr }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->marking_type }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $data->z }}</td>
                            <td class="py-3 px-4 flex space-x-3">
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
    </div>

    <!-- Tombol Tambah Konstanta yang Lebar -->
    <div class="px-6 mt-4">
        <button id="showFormBtn" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700">
            Tambah Konstanta
        </button>
    </div>
    <div class="flex justify-end mt-4">
    <a href="{{ route('dashboard') }}" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 text-center block">
        Kembali ke Dashboard
    </a>
</div>

    
    <!-- Form Tambah Konstanta -->
    <div id="addConstantaForm" class="mt-6 hidden bg-white shadow-md rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Konstanta Baru</h3>

        <form action="{{ route('admin.data.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilihan Kolom -->
            <div>
                <label for="column" class="block text-sm font-medium text-gray-700">Pilih Kolom</label>
                <select id="column" name="column" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none">
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

            <!-- Input Nilai Konstanta -->
            <div>
                <label for="value" class="block text-sm font-medium text-gray-700">Nilai Konstanta</label>
                <input type="text" id="value" name="value" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none" placeholder="Masukkan nilai konstanta" required>
                @error('value')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilihan Marking Type -->
            <!-- <div>
                <label for="marking_type" class="block text-sm font-medium text-gray-700">Pilih Marking Type</label>
                <select id="marking_type" name="marking_type" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:outline-none" required>
                    <option value="1">Marking 36 Jam Non HVC</option>
                    <option value="2">Marking Platinum</option>
                    <option value="3">Marking Diamond</option>
                </select>
                @error('marking_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div> -->

            <!-- Tombol Simpan -->
            <!-- Tombol Simpan -->
<div class="flex justify-end mt-4">
    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-red-700 transition duration-300">
        Simpan Konstanta
    </button>
</div>

        </form>

        <!-- Tombol Kembali ke Dashboard -->
        


    </div>

</div>

<script>
    document.getElementById('showFormBtn').addEventListener('click', function () {
        document.getElementById('addConstantaForm').classList.toggle('hidden');
    });
</script>

</body>
</html>
