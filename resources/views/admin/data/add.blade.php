<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function showEditForm(rowIndex, cellIndex, currentValue) {
            const form = document.getElementById(`edit-form-${rowIndex}-${cellIndex}`);
            const input = document.getElementById(`edit-input-${rowIndex}-${cellIndex}`);
            input.value = currentValue;
            form.classList.remove('hidden');
        }

        function deleteRow(rowIndex) {
            const row = document.getElementById(`row-${rowIndex}`);
            row.remove();
        }

        function updateCell(rowIndex, cellIndex) {
            const input = document.getElementById(`edit-input-${rowIndex}-${cellIndex}`);
            const newValue = input.value;

            // Update the cell in the table
            const cell = document.querySelector(`#row-${rowIndex} td:nth-child(${cellIndex + 1})`);
            cell.innerHTML = newValue + 
                `<button onclick="showEditForm(${rowIndex}, ${cellIndex}, '${newValue}')" class="ml-2 text-blue-500">Edit</button>` +
                `<button onclick="deleteRow(${rowIndex})" class="ml-2 text-red-500">Delete</button>`;
            input.value = '';
            document.getElementById(`edit-form-${rowIndex}-${cellIndex}`).classList.add('hidden');
        }

        function saveChanges() {
            // Collect all the data from the table
            const data = [];
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    rowData.push(cell.innerText);
                });
                data.push(rowData);
            });

            // Send the updated data to the server
            fetch('/save-excel-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                },
                body: JSON.stringify({ data: data })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data saved successfully!');
                } else {
                    alert('Error saving data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-bold text-center text-gray-700 pb-6">Excel Sheet Data</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm text-gray-700">
                <thead class="bg-red-600 text-white">
                    <tr class="text-left">
                        @foreach ($headers as $header)
                            <th class="py-3 px-4">{{ $header }}</th>
                        @endforeach
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($excelData as $rowIndex => $row)
                        <tr id="row-{{ $rowIndex }}" class="border-b hover:bg-gray-50 text-center">
                            @foreach ($row as $cellIndex => $cell)
                                <td class="py-3 px-4">
                                    {{ $cell }}
                                    <button onclick="showEditForm({{ $rowIndex }}, {{ $cellIndex }}, '{{ $cell }}')" class="ml-2 text-blue-500">Edit</button>
                                    <button onclick="deleteRow({{ $rowIndex }})" class="ml-2 text-red-500">Delete</button>
                                    <form id="edit-form-{{ $rowIndex }}-{{ $cellIndex }}" class="hidden mt-2">
                                        <input type="text" id="edit-input-{{ $rowIndex }}-{{ $cellIndex }}" class="border rounded p-1" />
                                        <button type="button" onclick="updateCell({{ $rowIndex }}, {{ $cellIndex }})" class="ml-2 text-green-500">Save</button>
                                    </form>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button onclick="saveChanges()" class="w-full block text-center px-6 py-3 bg-green-600 text-white rounded-lg shadow-lg hover:bg-green-700">
                Save All Changes
            </button>
            <a href="{{ route('dashboard') }}" class="w-full block text-center px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 mt-2">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>