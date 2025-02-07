<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah dan Gabungkan File</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.7/dayjs.min.js"></script>
    <style>
        table {
            table-layout: fixed;
            width: 100%;
        }
        th, td {
            word-wrap: break-word;
        }
        .max-h-72 {
            max-height: 18rem;
        }
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
@include('navbar')
<body class="bg-gray-100">
    <div class="bg-white shadow-md rounded-2xl p-4 sm:p-16 max-w-3xl mx-auto min-h-[300px] sm:min-h-[500px] mt-32">
        <h1 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-6 text-center">Unggah dan Gabungkan File</h1>
        @if(session('success_message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success_message') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-base sm:text-lg font-medium mb-2">All Ticket List:</label>
                <input type="file" name="all_ticket" class="border rounded w-full p-2 sm:p-4 text-sm sm:text-lg" accept=".xlsx,.xls" required>
            </div>
            <div class="mb-6">
                <label class="block text-base sm:text-lg font-medium mb-2">Closed Ticket List:</label>
                <input type="file" name="close_ticket" class="border rounded w-full p-2 sm:p-4 text-sm sm:text-lg" accept=".xlsx,.xls" required>
            </div>
            <button type="submit" class="w-full bg-red-600 text-white py-2 sm:py-3 rounded-lg hover:bg-red-700 transition text-sm sm:text-xl">Proses File</button>
        </form>
    </div>

    @if(session('merged_data') && count(session('merged_data')) > 1)
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white shadow-lg rounded-2xl p-8 mt-12" style="width: 2500px; max-width: 1200px; padding: 40px;">
            <h2 class="text-3xl font-bold mb-6 text-center">Hapus Data</h2>
            <form action="{{ route('upload.delete') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Pilih Kolom:</label>
                    <select name="column" id="column-select" class="border rounded w-full p-4 text-lg" required>
                        @foreach(session('header') as $header)
                            <option value="{{ $header }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-lg font-medium mb-2">Pilih Nilai:</label>
                    <div id="checkbox-container" class="grid grid-cols-5 gap-4 overflow-y-auto max-h-60 border border-gray-300 p-4 rounded">
                        <!-- Checkbox values will be populated here -->
                    </div>
                </div>
                <button type="submit" class="w-auto bg-red-600 text-white py-1 px-4 rounded hover:bg-red-700 transition mt-4 float-right">
                    Hapus Data
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mt-12" style="width: 90vw; margin-left: calc(-45vw + 50%); padding-left: 24px; padding-right: 24px;">
        <h2 class="text-3xl font-bold mb-6 text-center">Data yang Digabungkan</h2>
        <p class="text-gray-700 text-center mb-4">
            Jumlah Baris Data: <strong>{{ count(session('merged_data')) }}</strong>
        </p>
        <div class="overflow-x-auto overflow-y-auto max-h-screen">
            <table id="merged-table" class="table-auto w-full border-collapse border border-gray-300 ">
                <thead class="bg-red-600">
                    <tr>
                        @foreach(session('header') as $header)
                            <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-white sticky top-0 bg-red-600 z-10">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('merged_data') as $row)
                        <tr class="hover:bg-gray-50">
                            @foreach($row as $cell)
                                <td class="border border-gray-300 px-4 py-2 text-justify text-sm text-gray-600">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button id="downloadExcel" class="mt-8 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                    Download Excel
            </button>
    </div>
@endif
    
    <script>
        document.getElementById('downloadExcel').addEventListener('click', async function() {
            try {
                // Ambil data dari session
                const mergedData = @json(session('merged_data', []));
                
                const response = await fetch('/api/save-excel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ data: mergedData }) // Kirim data yang diambil dari session
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Terjadi kesalahan saat mengunduh file.');
                }

                // Read the response as a blob
                const blob = await response.blob();
                const filename = response.headers.get('Content-Disposition').split('filename=')[1].replace(/"/g, ''); // Extract filename from headers

                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename; // Use the filename from the server
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error downloading file:', error);
                alert('Gagal mengunduh file: ' + error.message);
            }
        });
    </script>
    <script>
// Ensure data is properly converted to arrays
const columnSelect = document.getElementById('column-select');
const checkboxContainer = document.getElementById('checkbox-container');

// Convert data to arrays if they're not already
const mergedData = Array.isArray({!! json_encode(session('merged_data', [])) !!}) 
    ? {!! json_encode(session('merged_data', [])) !!} 
    : Object.values({!! json_encode(session('merged_data', [])) !!});

const header = Array.isArray({!! json_encode(session('header', [])) !!})
    ? {!! json_encode(session('header', [])) !!}
    : Object.values({!! json_encode(session('header', [])) !!});

function populateCheckboxes() {
    // Clear existing checkboxes
    checkboxContainer.innerHTML = '';
    
    // Get selected column
    const selectedColumn = columnSelect.value;
    
    // Find column index without using indexOf
    let columnIndex = -1;
    for (let i = 0; i < header.length; i++) {
        if (header[i] === selectedColumn) {
            columnIndex = i;
            break;
        }
    }
    
    if (columnIndex === -1) return;
    
    // Collect all values for the selected column
    const values = new Set();
    
    mergedData.forEach(row => {
        const rowData = Array.isArray(row) ? row : Object.values(row);
        if (rowData[columnIndex] != null && rowData[columnIndex] !== '') {
            values.add(rowData[columnIndex]);
        }
    });
    
    // Convert to array and sort
    const sortedValues = Array.from(values).sort((a, b) => 
        String(a).localeCompare(String(b), undefined, {numeric: true, sensitivity: 'base'})
    );
    
    // Create checkboxes
    sortedValues.forEach(value => {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center mb-2';
        
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-2 cursor-pointer p-2 hover:bg-gray-50 rounded w-full';
        
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.name = 'value[]';
        input.value = value;
        input.className = 'form-checkbox h-4 w-4 text-red-600 rounded';
        
        const span = document.createElement('span');
        span.className = 'ml-2 text-sm text-gray-700';
        span.textContent = value;
        
        label.appendChild(input);
        label.appendChild(span);
        wrapper.appendChild(label);
        checkboxContainer.appendChild(wrapper);
    });
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    if (columnSelect && checkboxContainer) {
        // Log data for debugging
        // console.log('Header:', header);
        // console.log('Merged Data:', mergedData);
        
        // Add change event listener
        columnSelect.addEventListener('change', populateCheckboxes);
        
        // Populate initial values
        populateCheckboxes();
    }
});
</script>
</body>
</html>