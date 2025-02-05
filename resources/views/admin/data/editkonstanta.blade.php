<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konstanta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">
            <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Edit Konstanta</h2>

            <!-- Menampilkan Pesan Error jika Ada -->
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Edit Konstanta -->
            <form method="POST" action="{{ route('admin.data.updateKonstanta', $konstanta->id) }}">
    @csrf
    @method('PUT') <!-- Menggunakan method PUT untuk update data -->

    <!-- Hidden input untuk column dan value -->
    <input type="hidden" name="column" id="column" value="">
    <input type="hidden" name="value" id="value" value="">

    <!-- Dropdown untuk memilih kolom yang akan diperbarui -->
    <div class="mb-4">
        <label for="column_choice" class="block text-sm font-medium text-gray-600">Pilih Kolom untuk Diperbarui</label>
        <select name="column_choice" id="column_choice" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none" onchange="showInput(this)">
            <option value="">Pilih Kolom</option>
            <option value="service_type">Service Type</option>
            <option value="customer_type">Customer Type</option>
            <option value="customer_segment">Customer Segment</option>
            <option value="segmen">Segment</option>
            <option value="status">Status</option>
            <option value="classification">Classification</option>
            <option value="status_closed">Status Closed</option>
            <option value="closed_reopen_by">Closed Reopen By</option>
            <option value="ttr">TTR</option>
            <option value="z">Z</option>
        </select>
    </div>

    <!-- Input untuk kolom yang dipilih -->
    <div class="mb-4" id="input_field" style="display: none;">
        <label for="input_value" class="block text-sm font-medium text-gray-600">Masukkan Nilai Baru</label>
        <input type="text" id="input_value" name="value" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
    </div>

    <!-- Submit Button -->
    <div class="mb-6">
        <button type="submit" class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
            Update Konstanta
        </button>
    </div>

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('admin.data.add') }}" class="text-sm text-red-500 hover:text-red-600 hover:underline">
            Kembali ke Daftar Konstanta
        </a>
    </div>
</form>

<script>
    // Menampilkan input field sesuai dengan kolom yang dipilih
    function showInput(select) {
        var column = select.value;
        if (column) {
            // Set kolom yang dipilih
            document.getElementById('column').value = column;

            // Menampilkan input field untuk nilai
            document.getElementById('input_field').style.display = 'block';
        } else {
            // Jika tidak ada kolom yang dipilih, sembunyikan input field
            document.getElementById('input_field').style.display = 'none';
        }
    }
</script>




        </div>
    </div>

</body>
</html>
