<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dropdown-content { display: none; }
        .dropdown:hover .dropdown-content { display: block; }
    </style>
</head>
<body class="bg-gray-100">
    @include('admin/navbar-admin')

    <div class="flex flex-col items-center justify-center min-h-screen space-y-6">
        <!-- Welcome Message -->
        <div class="flex flex-col md:flex-row items-center gap-3 text-center md:text-left mt-32">
            <h1 class="text-3xl font-bold text-gray-800">
                Welcome to <br>
                <span class="text-red-600 text-4xl">Admin Dashboard</span>
            </h1>
            <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="mx-auto mt-2 w-48">
        </div>

        <!-- Menu Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.users.create') }}" class="hover:scale-105 transform transition">
                <div class="border-4 border-red-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2">
                    <img src="/images/add-user.png" alt="Create User" class="h-20">
                    <span class="text-gray-800 text-lg font-medium">Create User</span>
                </div>
            </a>

            <a href="{{ route('admin.data.add') }}" class="hover:scale-105 transform transition">
                <div class="border-4 border-red-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2">
                    <img src="/images/add-data.png" alt="Add Data" class="h-20">
                    <span class="text-gray-800 text-lg font-medium">Add Data</span>
                </div>
            </a>

            <a href="{{ route('admin.users.list') }}" class="hover:scale-105 transform transition">
                <div class="border-4 border-red-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2">
                    <img src="/images/user-list.png" alt="User  List" class="h-20">
                    <span class="text-gray-800 text-lg font-medium">User  List</span>
                </div>
            </a>
        </div>

        <!-- Tabel Hasil Excel Download -->
        <div class="container mx-auto mt-10 px-4">
            <h2 class="text-2xl font-bold mb-4 mt-10">Daftar File Excel yang Diunduh</h2>

            @if(session('error'))
                <div class="bg-red-500 text-white p-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="flex space-x-2">
                            <input type="text" id="dateStart" placeholder="Start Date" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <input type="text" id="dateEnd" placeholder="End Date" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                    </div>

                    <!-- Search Fields -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search by Filename</label>
                        <input type="text" id="searchFilename" placeholder="Search filename..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search by User</label>
                        <input type="text" id="searchUser" placeholder="Search user..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex justify-end mt-4 space-x-2">
                    <button onclick="resetFilters()" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        Reset Filters
                    </button>
                    <button onclick="applyFilters()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Total Downloads Today with Date -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
                    <div class="w-full flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Total Rekap Hari Ini</h3>
                        <span id="currentDate" class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded"></span>
                    </div>
                    <p class="text-[250px] font-bold text-red-600 flex flex-col items-center justify-center text-center" id="totalDownloadsToday">0</p>
                </div>
                <!-- Downloads Per User with Date Range -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Rekap Per Orang</h3>
                        <div class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                            <span id="rekapDateStart"></span> - <span id="rekapDateEnd"></span>
                        </div>
                    </div>
                    <canvas id="downloadsChart" class="h-48"></canvas>
                    <ul id="downloadsPerUser" class="space-y-2 mt-4"></ul>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table id="downloadsTable" class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloaded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($excelFiles as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->filename }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->downloaded_by }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->downloaded_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center">Tidak ada data yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="flex justify-between items-center mt-4">
                <button id="prevPage" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors" onclick="changePage(-1)">Previous</button>
                <span id="pageInfo" class="text-gray-700"></span>
                <button id="nextPage" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors" onclick="changePage(1)">Next</button>
            </div>
        </div>
    </div>

    <script>
        // Format date function
        function formatDate(date) {
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Initialize date pickers
        flatpickr("#dateStart", {
            enableTime: false,
            dateFormat: "Y-m-d"
        });
        
        flatpickr("#dateEnd", {
            enableTime: false,
            dateFormat: "Y-m-d"
        });

        // Update current date display
        document.getElementById('currentDate').textContent = formatDate(new Date());

        // Pagination variables
        let currentPage = 1;
        const rowsPerPage = 5; // Change this to the number of rows you want per page

        function changePage(direction) {
            currentPage += direction;
            displayRows();
        }

        function displayRows() {
            const table = document.getElementById('downloadsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            const totalRows = visibleRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);

            // Ensure currentPage is within bounds
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            // Hide all rows
            for (let row of rows) {
                row.style.display = 'none';
            }

            // Calculate start and end index for the current page
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Show rows for the current page
            for (let i = start; i < end && i < totalRows; i++) {
                visibleRows[i].style.display = '';
            }

            // Update page info
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
        }

        // Filter functions
        function applyFilters() {
            const dateStart = document.getElementById('dateStart').value;
            const dateEnd = document.getElementById('dateEnd').value;
            const searchFilename = document.getElementById('searchFilename').value.toLowerCase();
            const searchUser = document.getElementById('searchUser').value.toLowerCase();

            // Update rekap date range display
            document.getElementById('rekapDateStart').textContent = dateStart ? formatDate(new Date(dateStart)) : formatDate(new Date());
            document.getElementById('rekapDateEnd').textContent = dateEnd ? formatDate(new Date(dateEnd)) : formatDate(new Date());

            const table = document.getElementById('downloadsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                const filename = row.cells[0].innerText.toLowerCase();
                const user = row.cells[1].innerText.toLowerCase();
                const date = row.cells[2].innerText;

                let showRow = true;

                // Date range filter
                if (dateStart && dateEnd) {
                    const rowDate = new Date(date);
                    const start = new Date(dateStart);
                    const end = new Date(dateEnd);
                    end.setHours(23, 59, 59);
                    
                    if (rowDate < start || rowDate > end) {
                        showRow = false;
                    }
                }

                // Filename filter
                if (searchFilename && !filename.includes(searchFilename)) {
                    showRow = false;
                }

                // User filter
                if (searchUser && !user.includes(searchUser)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            }

            calculateDownloads();
            displayRows(); // Update the displayed rows after filtering
        }

        function resetFilters() {
            document.getElementById('dateStart').value = '';
            document.getElementById('dateEnd').value = '';
            document.getElementById('searchFilename').value = '';
            document.getElementById('searchUser').value = '';

            // Reset date displays
            document.getElementById('rekapDateStart').textContent = formatDate(new Date());
            document.getElementById('rekapDateEnd').textContent = formatDate(new Date());

            const table = document.getElementById('downloadsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let row of rows) {
                row.style.display = '';
            }

            calculateDownloads();
            displayRows(); // Update the displayed rows after resetting
        }

        // Fungsi calculateDownloads() menghitung SEMUA BARIS, tidak peduli apakah baris tersebut terlihat atau tidak
        function calculateDownloads() {
            const downloadsTable = document.getElementById('downloadsTable');
            const rows = downloadsTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const totalDownloadsToday = document.getElementById('totalDownloadsToday');
            const downloadsPerUser = document.getElementById('downloadsPerUser');

            let userDownloadCount = {};
            let today = new Date();
            today.setHours(0, 0, 0, 0);

            let totalCount = 0;

            // Loop melalui SEMUA BARIS, tidak peduli apakah baris tersebut terlihat atau tidak
            for (let row of rows) {
                const downloadedBy = row.cells[1].innerText;
                const downloadedAt = new Date(row.cells[2].innerText);

                if (downloadedAt >= today) {
                    totalCount++;
                    userDownloadCount[downloadedBy] = (userDownloadCount[downloadedBy] || 0) + 1;
                }
            }

            totalDownloadsToday.innerText = totalCount;

            downloadsPerUser.innerHTML = '';
            const chartData = {
                labels: [],
                data: []
            };

            Object.entries(userDownloadCount)
                .sort((a, b) => b[1] - a[1])
                .forEach(([user, count]) => {
                    const li = document.createElement('li');
                    li.className = 'flex justify-between items-center text-gray-700 p-2 hover:bg-gray-50 rounded';
                    li.innerHTML = `
                        <span>${user}</span>
                        <span class="font-semibold">${count} kali</span>
                    `;
                    downloadsPerUser.appendChild(li);
                    chartData.labels.push(user);
                    chartData.data.push(count);
                });

            if (downloadsPerUser.children.length === 0) {
                downloadsPerUser.innerHTML = '<li class="text-gray-500 italic text-center">No data available</li>';
            }

            updateChart(chartData);
        }

        // Chart.js setup
        const ctx = document.getElementById('downloadsChart').getContext('2d');
        let downloadsChart;

        function updateChart(chartData) {
            if (downloadsChart) {
                downloadsChart.destroy();
            }

            downloadsChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Downloads per User',
                        data: chartData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Downloads per User'
                        }
                    }
                }
            });
        }

        // Initialize calculations and dates
        calculateDownloads();
        document.getElementById('rekapDateStart').textContent = formatDate(new Date());
        document.getElementById('rekapDateEnd').textContent = formatDate(new Date());

        // Call displayRows initially to show the first page
        displayRows();

        // Add event listeners for real-time filtering
        document.getElementById('searchFilename').addEventListener('input', applyFilters);
        document.getElementById('searchUser').addEventListener('input', applyFilters);
    </script>
</body>
</html>