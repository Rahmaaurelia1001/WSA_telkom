<!DOCTYPE html>
<html lang="id">
<head>
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
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-auto p-20">
        <div class="bg-white shadow-md rounded-lg p-12 max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">Unggah dan Gabungkan File</h1>

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
                    <label class="block text-lg font-medium mb-2">File Semua Tiket:</label>
                    <input type="file" name="all_ticket" class="border rounded w-full p-2" accept=".xlsx,.xls" required>
                </div>
                <div class="mb-6">
                    <label class="block text-lg font-medium mb-2">File Tiket Ditutup:</label>
                    <input type="file" name="close_ticket" class="border rounded w-full p-2" accept=".xlsx,.xls" required>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                    Proses File
                </button>
            </form>
        </div>

        @if(session('merged_data') && count(session('merged_data')) > 1)
        <div class="mt-6 bg-white shadow-md rounded-lg p-6" style="width: 100vw; margin-left: calc(-50vw + 50%); padding-left: 40px; padding-right: 20px;">
            <h2 class="text-xl font-bold mb-4" style="text-align: center;">Data yang Dihapus</h2>
            <form action="{{ route('upload.delete') }}" method="POST" style="height: 200px!important;">
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
                    <div id="checkbox-container" class="grid grid-cols-5 gap-2 overflow-y-auto max-h-72 border border-gray-300 p-4 rounded">
                    </div>
                    <button type="submit" class="w-auto bg-red-600 text-white py-1 px-4 rounded hover:bg-red-700 transition mt-4 float-right">
                        Hapus Data
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if(session('merged_data') && count(session('merged_data')) > 1)
        <div class="bg-white shadow-md rounded-lg p-6 mt-10" style="width: 100vw; margin-left: calc(-50vw + 50%); padding-left: 20px; padding-right: 20px;">
    <h2 class="text-xl font-bold mb-4 text-center">Data yang Digabungkan</h2>
    <p class="text-gray-700 text-center mb-4">
        Jumlah Baris Data: <strong>{{ $rowCount }}</strong>
    </p>
    <div class="overflow-x-auto overflow-y-auto max-h-screen">
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    @foreach(session('header') as $header)
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 sticky top-0 bg-gray-200 z-10">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(session('merged_data') as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($row as $cell)
                            <td class="border border-gray-300 px-4 py-2 text-center text-sm text-gray-600">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    
            <button id="process-booking-date" class="mt-4 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Proses Data
            </button>
            
            <div id="booking-date-table" class="mt-6 hidden">
                <h3 class="text-lg font-semibold text-center mb-4">Hasil Proses Data</h3>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Booking Date</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Durasi Manja</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Today WO</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Jam</th>
                            </tr>
                        </thead>
                        <tbody id="booking-date-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        const mergedData = @json(session('merged_data', []));
        const header = @json(session('header', []));
        const bookingDateColumnIndex = header.indexOf('BOOKING DATE');

        const processButton = document.getElementById('process-booking-date');
        const bookingDateTable = document.getElementById('booking-date-table');
        const bookingDateTbody = document.getElementById('booking-date-tbody');

        function calculateTimeDifference(bookingDate) {
            if (!bookingDate || bookingDate === "" || bookingDate === undefined || bookingDate === null) {
                return "#VALUE!";
            }

            try {
                const now = dayjs();
                const booking = dayjs(bookingDate);
                
                if (!booking.isValid()) {
                    return "#VALUE!";
                }

                const diffInHours = Math.abs(now.diff(booking, 'hour'));
                return `${diffInHours} jam`;
            } catch (error) {
                console.error('Error calculating time difference:', error);
                return "#VALUE!";
            }
        }

        function isTodayWO(bookingDate) {
            if (!bookingDate || bookingDate === "" || bookingDate === undefined || bookingDate === null) {
                return false;
            }

            try {
                const today = dayjs();
                const booking = dayjs(bookingDate);
                
                if (!booking.isValid()) {
                    return false;
                }

                return booking.format('YYYY-MM-DD') === today.format('YYYY-MM-DD');
            } catch (error) {
                console.error('Error checking Today WO:', error);
                return false;
            }
        }

        function extractHourFromDate(bookingDate) {
        if (!bookingDate || bookingDate === "" || bookingDate === undefined || bookingDate === null) {
            return "";
        }

        try {
            const booking = dayjs(bookingDate);
            
            if (!booking.isValid()) {
                return "";
            }

            return booking.format('HH');
        } catch (error) {
            console.error('Error extracting hour:', error);
            return "";
        }
    }

    processButton.addEventListener('click', function () {
        if (bookingDateColumnIndex === -1) {
            alert('Kolom "BOOKING DATE" tidak ditemukan!');
            return;
        }

        const bookingDates = mergedData.map(row => row[bookingDateColumnIndex]);
        bookingDateTbody.innerHTML = '';

        bookingDates.forEach(date => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            // Booking Date cell
            const dateCell = document.createElement('td');
            dateCell.className = 'border border-gray-300 px-4 py-2 text-center text-sm text-gray-600';
            dateCell.textContent = date || '';
            
            // Time Difference cell
            const diffCell = document.createElement('td');
            diffCell.className = 'border border-gray-300 px-4 py-2 text-center text-sm text-gray-600';
            diffCell.textContent = calculateTimeDifference(date);
            
            // Today WO cell
            const todayWOCell = document.createElement('td');
            todayWOCell.className = 'border border-gray-300 px-4 py-2 text-center text-sm text-gray-600';
            todayWOCell.textContent = isTodayWO(date) ? 'True' : 'False';
            
            // Hour cell
            const hourCell = document.createElement('td');
            hourCell.className = 'border border-gray-300 px-4 py-2 text-center text-sm text-gray-600';
            hourCell.textContent = extractHourFromDate(date);
            
            row.appendChild(dateCell);
            row.appendChild(diffCell);
            row.appendChild(todayWOCell);
            row.appendChild(hourCell);
            bookingDateTbody.appendChild(row);
        });

        bookingDateTable.classList.remove('hidden');
    });
    </script>
</body>
</html>