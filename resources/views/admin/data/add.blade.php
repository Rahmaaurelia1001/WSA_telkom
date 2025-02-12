<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Excel Data Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Add FontAwesome for better icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .fade-enter {
            opacity: 0;
        }
        .fade-enter-active {
            opacity: 1;
            transition: opacity 200ms ease-in;
        }
        .fade-exit {
            opacity: 1;
        }
        .fade-exit-active {
            opacity: 0;
            transition: opacity 200ms ease-in;
        }
        
        .delete-button {
            opacity: 0;
            transition: all 0.2s ease-in-out;
        }
        
        .cell-wrapper:hover .delete-button {
            opacity: 1;
        }
        
        .saving-indicator {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('admin/navbar-admin')

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
            <span class="text-gray-700 font-medium">Processing...</span>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="hidden fixed top-4 right-4 max-w-md transform transition-all duration-300 z-50">
        <div class="flex items-center p-4 rounded-lg shadow-lg">
            <div class="flex-shrink-0 mr-3">
                <i class="fas fa-check-circle text-xl notification-icon"></i>
            </div>
            <div class="flex-1 notification-message"></div>
        </div>
    </div>

    <div class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">Excel Data Editor</h2>
                    <div class="flex gap-3">
                        <button onclick="addNewRow()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center gap-2">
                            <i class="fas fa-plus text-sm"></i>
                            <span>Add Row</span>
                        </button>
                        <button onclick="addNewColumn()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center gap-2">
                            <i class="fas fa-plus text-sm"></i>
                            <span>Add Column</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Table Section -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach ($headers as $header)
                                    <th class="py-4 px-6 text-left text-sm font-semibold text-gray-900 border-b">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($excelData as $rowIndex => $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    @foreach ($row as $cellIndex => $cell)
                                        <td class="py-4 px-6" id="cell-{{ $rowIndex }}-{{ $cellIndex }}">
                                            <div class="cell-wrapper flex items-center gap-3 min-h-[2rem]">
                                                <div class="cell-content outline-none flex-1 px-2 py-1 rounded hover:bg-gray-100 focus:bg-white focus:ring-2 focus:ring-red-200 transition-all"
                                                     contenteditable="true"
                                                     onblur="handleUpdate(this, {{ $rowIndex }}, {{ $cellIndex }})"
                                                     oninput="handleInput(this, {{ $rowIndex }}, {{ $cellIndex }})">
                                                    {{ $cell }}
                                                </div>
                                                <button onclick="confirmDelete({{ $rowIndex }}, {{ $cellIndex }})" 
                                                        class="delete-button p-1.5 hover:bg-red-50 rounded-lg group transition-all">
                                                    <i class="fas fa-trash-alt text-red-500 group-hover:text-red-600 text-sm"></i>
                                                </button>
                                                <span class="status-indicator text-xs text-gray-400 w-16 text-right"></span>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Footer Section -->
            <div class="p-6 bg-gray-50 border-t">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full text-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Configure Axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
        
        let debounceTimers = {};
        
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationIcon = notification.querySelector('.notification-icon');
            const notificationMessage = notification.querySelector('.notification-message');
            
            // Set styles based on type
            if (type === 'success') {
                notification.firstElementChild.className = 'flex items-center p-4 bg-green-50 border border-green-200 rounded-lg shadow-lg';
                notificationIcon.className = 'fas fa-check-circle text-xl text-green-500';
            } else {
                notification.firstElementChild.className = 'flex items-center p-4 bg-red-50 border border-red-200 rounded-lg shadow-lg';
                notificationIcon.className = 'fas fa-exclamation-circle text-xl text-red-500';
            }
            
            notificationMessage.textContent = message;
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        function confirmDelete(rowIndex, cellIndex) {
            if (confirm('Are you sure you want to delete this cell content?')) {
                handleDelete(rowIndex, cellIndex);
            }
        }

        function handleInput(element, rowIndex, cellIndex) {
            if (!element) return;
            
            const statusIndicator = element.parentElement?.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.textContent = 'Editing...';
                statusIndicator.className = 'status-indicator text-xs text-blue-500 w-16 text-right';
            }

            if (debounceTimers[`${rowIndex}-${cellIndex}`]) {
                clearTimeout(debounceTimers[`${rowIndex}-${cellIndex}`]);
            }

            debounceTimers[`${rowIndex}-${cellIndex}`] = setTimeout(() => {
                handleUpdate(element, rowIndex, cellIndex);
            }, 500);
        }

        async function handleUpdate(element, rowIndex, cellIndex) {
            if (!element) return;

            const newValue = element.textContent || '';
            const statusIndicator = element.closest('.cell-wrapper')?.querySelector('.status-indicator');
            
            try {
                if (statusIndicator) {
                    statusIndicator.textContent = 'Saving...';
                    statusIndicator.className = 'status-indicator text-xs text-yellow-500 w-16 text-right';
                }
                
                const response = await axios.post('/update-cell', {
                    rowIndex: rowIndex,
                    cellIndex: cellIndex,
                    value: newValue
                });

                if (response.data.success) {
                    if (statusIndicator) {
                        statusIndicator.textContent = 'Saved';
                        statusIndicator.className = 'status-indicator text-xs text-green-500 w-16 text-right';
                        
                        setTimeout(() => {
                            statusIndicator.textContent = '';
                        }, 2000);
                    }
                    showNotification('Changes saved successfully');
                }
            } catch (error) {
                console.error('Error updating cell:', error);
                if (statusIndicator) {
                    statusIndicator.textContent = 'Error!';
                    statusIndicator.className = 'status-indicator text-xs text-red-500 w-16 text-right';
                }
                showNotification('Failed to save changes', 'error');
            }
        }

        async function handleDelete(rowIndex, cellIndex) {
            const cellElement = document.getElementById(`cell-${rowIndex}-${cellIndex}`);
            if (!cellElement) return;

            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await axios.post('/delete-cell', {
                    rowIndex: rowIndex,
                    cellIndex: cellIndex
                });

                if (response.data.success) {
                    const cellContent = cellElement.querySelector('.cell-content');
                    if (cellContent) {
                        cellContent.textContent = '';
                        cellContent.classList.add('bg-red-50');
                        setTimeout(() => {
                            cellContent.classList.remove('bg-red-50');
                        }, 300);
                    }
                    showNotification('Content deleted successfully');
                }
            } catch (error) {
                console.error('Error deleting cell:', error);
                showNotification('Failed to delete content', 'error');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        async function addNewRow() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await axios.post('/add-row');
                if (response.data.success) {
                    location.reload();
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

            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await axios.post('/add-column', { columnName });
                if (response.data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error adding new column:', error);
                showNotification('Failed to add new column', 'error');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        // Initialize all cells when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const cells = document.querySelectorAll('.cell-content');
            cells.forEach(cell => {
                cell.addEventListener('focus', function() {
                    this.classList.add('bg-white', 'ring-2', 'ring-red-200');
                });
                cell.addEventListener('blur', function() {
                    this.classList.remove('bg-white', 'ring-2', 'ring-red-200');
                });
            });
        });
    </script>
</body>
</html>