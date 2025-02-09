<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Excel Data Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Configure Axios to always include CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
        
        let debounceTimers = {};
        
        function initializeEditor(cell, rowIndex, cellIndex, currentValue) {
            const cellElement = document.getElementById(`cell-${rowIndex}-${cellIndex}`);
            if (!cellElement) return;

            // Create contenteditable div for inline editing
            cellElement.innerHTML = `
                <div class="flex items-center">
                    <div 
                        class="cell-content outline-none min-h-[1.5rem]" 
                        contenteditable="true"
                        onblur="handleUpdate(this, ${rowIndex}, ${cellIndex})"
                        oninput="handleInput(this, ${rowIndex}, ${cellIndex})"
                    >${currentValue}</div>
                    <button 
                        onclick="handleDelete(${rowIndex}, ${cellIndex})" 
                        class="ml-2 text-red-500 hover:text-red-700 focus:outline-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <span class="ml-2 text-xs text-gray-400 status-indicator"></span>
                </div>
            `;
        }

        function handleInput(element, rowIndex, cellIndex) {
            if (!element) return;
            
            const statusIndicator = element.parentElement?.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.textContent = 'Editing...';
            }

            // Clear existing timer
            if (debounceTimers[`${rowIndex}-${cellIndex}`]) {
                clearTimeout(debounceTimers[`${rowIndex}-${cellIndex}`]);
            }

            // Set new timer
            debounceTimers[`${rowIndex}-${cellIndex}`] = setTimeout(() => {
                handleUpdate(element, rowIndex, cellIndex);
            }, 500);
        }

        async function handleUpdate(element, rowIndex, cellIndex) {
            if (!element) return;

            const newValue = element.textContent || '';
            const statusIndicator = element.parentElement?.querySelector('.status-indicator');
            
            try {
                if (statusIndicator) {
                    statusIndicator.textContent = 'Saving...';
                }
                
                const response = await axios.post('/update-cell', {
                    rowIndex: rowIndex,
                    cellIndex: cellIndex,
                    value: newValue
                });

                if (response.data.success) {
                    if (statusIndicator) {
                        statusIndicator.textContent = 'Saved';
                        statusIndicator.classList.remove('text-red-500');
                        statusIndicator.classList.add('text-green-500');
                        
                        setTimeout(() => {
                            if (statusIndicator) {
                                statusIndicator.textContent = '';
                            }
                        }, 2000);
                    }
                } else {
                    throw new Error(response.data.message || 'Failed to save');
                }
            } catch (error) {
                console.error('Error updating cell:', error);
                if (statusIndicator) {
                    statusIndicator.textContent = 'Error saving!';
                    statusIndicator.classList.remove('text-green-500');
                    statusIndicator.classList.add('text-red-500');
                }
                if (element) {
                    element.classList.add('text-red-500');
                }
            }
        }

        async function handleDelete(rowIndex, cellIndex) {
            const cellElement = document.getElementById(`cell-${rowIndex}-${cellIndex}`);
            if (!cellElement) return;

            const statusIndicator = cellElement.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.textContent = 'Deleting...';
            }

            try {
                const response = await axios.post('/delete-cell', {
                    rowIndex: rowIndex,
                    cellIndex: cellIndex
                });

                if (response.data.success) {
                    // Clear the cell content
                    const cellContent = cellElement.querySelector('.cell-content');
                    if (cellContent) {
                        cellContent.textContent = '';
                    }

                    // Update status indicator
                    if (statusIndicator) {
                        statusIndicator.textContent = 'Deleted';
                        statusIndicator.classList.remove('text-red-500');
                        statusIndicator.classList.add('text-green-500');

                        setTimeout(() => {
                            if (statusIndicator) {
                                statusIndicator.textContent = '';
                            }
                        }, 2000);
                    }
                } else {
                    throw new Error(response.data.message || 'Failed to delete');
                }
            } catch (error) {
                console.error('Error deleting cell:', error);
                if (statusIndicator) {
                    statusIndicator.textContent = 'Error deleting!';
                    statusIndicator.classList.remove('text-green-500');
                    statusIndicator.classList.add('text-red-500');
                }
            }
        }

        async function addNewRow() {
            try {
                const loadingIndicator = document.getElementById('loadingIndicator');
                loadingIndicator.classList.remove('hidden');

                const response = await axios.post('/add-row');
                if (response.data.success) {
                    const tbody = document.querySelector('tbody');
                    const newRow = document.createElement('tr');
                    newRow.className = 'border-b hover:bg-gray-50';
                    
                    // Add cells for each column
                    for (let i = 0; i < response.data.columnCount; i++) {
                        const td = document.createElement('td');
                        td.className = 'py-3 px-4';
                        td.id = `cell-${response.data.newRowIndex}-${i}`;
                        newRow.appendChild(td);
                        
                        // Initialize editor for the new cell
                        initializeEditor(td, response.data.newRowIndex, i, '');
                    }
                    
                    tbody.appendChild(newRow);
                    
                    // Show success message
                    showNotification('Row added successfully', 'success');
                }
            } catch (error) {
                console.error('Error adding new row:', error);
                showNotification('Failed to add new row', 'error');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        async function addNewColumn() {
            const columnName = prompt('Enter column name:', 'New Column');
            if (!columnName) return;

            try {
                const loadingIndicator = document.getElementById('loadingIndicator');
                loadingIndicator.classList.remove('hidden');

                const response = await axios.post('/add-column', { columnName });
                if (response.data.success) {
                    // Add header
                    const headerRow = document.querySelector('thead tr');
                    const newHeader = document.createElement('th');
                    newHeader.className = 'py-3 px-4 text-left';
                    newHeader.textContent = columnName;
                    headerRow.appendChild(newHeader);
                    
                    // Add cells to each row
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach((row, rowIndex) => {
                        const td = document.createElement('td');
                        td.className = 'py-3 px-4';
                        td.id = `cell-${rowIndex}-${response.data.newColumnIndex}`;
                        row.appendChild(td);
                        
                        // Initialize editor for the new cell
                        initializeEditor(td, rowIndex, response.data.newColumnIndex, '');
                    });
                    
                    // Show success message
                    showNotification('Column added successfully', 'success');
                }
            } catch (error) {
                console.error('Error adding new column:', error);
                showNotification('Failed to add new column', 'error');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Initialize all cells when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const cells = document.querySelectorAll('[id^="cell-"]');
            cells.forEach(cell => {
                const [_, rowIndex, cellIndex] = cell.id.split('-').map(Number);
                if (!isNaN(rowIndex) && !isNaN(cellIndex)) {
                    initializeEditor(cell, rowIndex, cellIndex, cell.textContent.trim());
                }
            });
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="hidden"></div>

    <div class="container mx-auto py-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-700">Excel Data Editor</h2>
                    <div class="space-x-4">
                        <button onclick="addNewRow()" 
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                            Add Row
                        </button>
                        <button onclick="addNewColumn()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Add Column
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-red-600 text-white">
                            <tr>
                                @foreach ($headers as $header)
                                    <th class="py-3 px-4 text-left">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($excelData as $rowIndex => $row)
                                <tr class="border-b hover:bg-gray-50">
                                    @foreach ($row as $cellIndex => $cell)
                                        <td class="py-3 px-4" id="cell-{{ $rowIndex }}-{{ $cellIndex }}">
                                            {{ $cell }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="p-6 bg-gray-50 border-t">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full text-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>