<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constanta List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 relative min-h-screen">

    <div class="container mx-auto p-6">
        <!-- Judul Ditengah dan Bold -->
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Constanta List</h2>

        <!-- Constanta Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full border-collapse border border-gray-300">
                <!-- Kepala Tabel Warna Merah -->
                <thead class="bg-red-600">
                    <tr>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Service Type</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Customer Type</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Customer Segment</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Status</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Classification</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Status Closed</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Marking Type</th>
                        <th class="py-3 px-6 text-left text-white font-semibold border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($markingData as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->service_type }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->customer_type }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->customer_segment }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->status }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->classification }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->status_closed }}</td>
                            <td class="py-3 px-6 border-b text-gray-600">{{ $data->marking_type }}</td>
                            <td class="py-3 px-6 border-b flex items-center space-x-4">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.data.editkonstanta', $data->id) }}"
                                    class="text-blue-500 hover:underline">Edit</a>
                                
                                <!-- Delete Form -->
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

        <!-- Button Tambah Konstanta -->
        <button id="showFormBtn" class="px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 mt-6">
            Tambah Konstanta
        </button>

        <!-- Form Tambah Konstanta -->
        <div id="addConstantaForm" class="mt-6 hidden">
            <form action="{{ route('admin.data.store') }}" method="POST">
                @csrf
                <!-- Pilihan Kolom -->
                <div class="mb-4">
                    <label for="column" class="block text-gray-700 font-semibold">Pilih Kolom</label>
                    <select id="column" name="column" class="form-select mt-2 block w-full bg-gray-50 border border-gray-300 p-3 rounded-lg">
                        <option value="service_type">Service Type</option>
                        <option value="customer_type">Customer Type</option>
                        <option value="customer_segment">Customer Segment</option>
                        <option value="status">Status</option>
                        <option value="classification">Classification</option>
                        <option value="status_closed">Status Closed</option>
                        <option value="marking_type">Marking Type</option>
                    </select>
                </div>

                <!-- Input Nilai Konstanta -->
                <div class="mb-4">
                    <label for="value" class="block text-gray-700 font-semibold">Tambah Nilai Konstanta</label>
                    <input type="text" id="value" name="value" class="form-input mt-2 block w-full bg-gray-50 border border-gray-300 p-3 rounded-lg" placeholder="Masukkan nilai konstanta baru" required>
                    @error('value')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Input Marking Type -->
                <div class="mb-4">
                    <label for="marking_type" class="block text-gray-700 font-semibold">Pilih Marking Type</label>
                    <select id="marking_type" name="marking_type" class="form-select mt-2 block w-full bg-gray-50 border border-gray-300 p-3 rounded-lg" required>
                        <option value="type1">Marking 36 Jam Non HVC</option>
                        <option value="type2">Marking Platinum</option>
                        <option value="type3">Marking Diamond</option>
                    </select>
                    @error('marking_type')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tombol Simpan -->
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700">
                    Simpan Konstanta
                </button>
            </form>
        </div>
    </div>

    <!-- Button di kanan bawah untuk kembali ke dashboard -->
    <div class="absolute bottom-6 right-6">
        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700">
            Kembali ke Dashboard
        </a>
    </div>

    <script>
        // Menampilkan atau menyembunyikan form untuk menambahkan konstanta
        document.getElementById('showFormBtn').addEventListener('click', function () {
            const form = document.getElementById('addConstantaForm');
            form.classList.toggle('hidden');
        });
    </script>

</body>

</html>
