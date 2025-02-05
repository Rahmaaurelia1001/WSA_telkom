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
<body class= bg-gray-100 >
    <!-- Container -->
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
                <h2 class="text-3xl font-bold mb-6 text-center">Data yang Dihapus</h2>
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
                        </div>
                        <button type="submit" class="w-auto bg-red-600 text-white py-1 px-4 rounded hover:bg-red-700 transition mt-4 float-right">
                            Hapus Data
                        </button>
                    </div>
                </form>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mt-12" style="width: 90vw; margin-left: calc(-45vw + 50%); padding-left: 24px; padding-right: 24px;">
                    <h2 class="text-3xl font-bold mb-6 text-center">Data yang Digabungkan</h2>
                    <p class="text-gray-700 text-center mb-4">
                        Jumlah Baris Data: <strong>{{ $rowCount }}</strong>
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
                    <button id="process-booking-date" class="mt-8 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                    Proses Data
                    </button>
                </div>

                <div id="booking-date-table" class="bg-white shadow-md rounded-lg p-6 mt-12 w-[90vw] mx-auto">
    <h3 class="text-3xl font-bold text-center mb-4">Hasil Proses Data</h3>
    
    <!-- Wrapper untuk membuat tabel bisa di-scroll dalam card -->
    <div class="overflow-x-auto max-h-[500px] border border-gray-300 rounded-lg">
        <table class="table-auto w-full border-collapse border border-gray-300 text-sm sm:text-base">
            <!-- Header Sticky -->
            <thead class="bg-red-600 text-white sticky top-0 z-10">
                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">BOOKING DATE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">DURASI MANJA </th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">TODAY WO</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">JAM MANJA</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">DURASI TIKET</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">REG-1</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">SERVICE TYPE?</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">CUSTOMER SEGMENT (PL-TSEL)</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">CUSTOMER ONLY</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">CLASSIFICATION (TECH ONLY)</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">VALID TICKET GRUP</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">PDA</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">NOT GUARANTEE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">TIKET AKTIF</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">GUARANTEE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">CLOSED</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #FFFF00; color: Black;">FILTER ASSURANCE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #FFFF00; color: Black;" >ASSURANCE CLOSE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">HVC DIAMOND</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">GRUP DIAMOND</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">HVC PLATINUM</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">GRUP PLATINUM</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">NON HVC</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">GRUP NON HVC</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #CAEDFB; color: Black;">FCR</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">TTR RESOLVED dari OPEN</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">Manja</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">TTR RESOLVED dari MANJA</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">VALID CLOSE</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">COMPLY TTR 3 Jam Manja</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">COMPLY TTR 3 Diamond</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">COMPLY TTR 3 Platinum</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">COMPLY TTR 3 Non HVC</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">CLOSED HI</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">IS MANJA</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">SISA DURASI</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">GRUP DURASI SISA ORDER TTR OPEN</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">IS NOT GAMAS</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700" style="background-color: #D51100; color: White;">IS DUPLICATE</th>
                                </tr>
                            </thead>
                            <tbody id="booking-date-tbody" class="overflow-y-auto">
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                            <button id="downloadExcel" class="mt-8 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                                Download Excel
                            </button>
                            <!-- <button id="shareWhatsApp" class="mt-2 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Share on WhatsApp
                            </button> -->
                    </div>
                </div>
            @endif
        </div>
        <!-- Scripts remain the same -->
</body>
</html>

<!-- <script>
document.addEventListener("DOMContentLoaded", function () {
    let dropdownButton = document.getElementById("dropdownMenuButton");
    let dropdownMenu = document.getElementById("dropdownMenu");

    function toggleDropdown(event) {
        event.preventDefault();
        dropdownMenu.classList.toggle("hidden");
    }

    dropdownButton.addEventListener("click", toggleDropdown);
    dropdownButton.addEventListener("touchstart", toggleDropdown);
});
</script> -->
@php
    $directStatusClosedTypes = DB::table('marking_data')
        ->select('status_closed')
        ->distinct()
        ->pluck('status_closed')
        ->toArray();
@endphp
<script>
        const mergedData = @json(session('merged_data', []));
        const header = @json(session('header', []));
        const serviceTypes = @json(session('service_types', []));
        // const statusClosedTypes = @json(session('statusclosed_types', []));
        const statusClosedTypes = @json($directStatusClosedTypes);
        console.log('Direct query status closed types:', statusClosedTypes);
        const closedTypes = @json(session('closed_types', []));
        const segmens = @json(session('segmens', []));
        const customerTypes = @json(session('customer_types', []));
        const classificationTypes = @json(session('classification_types', []));
        const customerSegments = @json(session('customer_segments', []));
        const zTypes = @json(session('zs', []));
        const maxValues = @json($maxValues);
        const idValues = @json($idValues);
        const maxValues2 = @json($maxValues2);
        const idValues2 = @json($idValues2);
        const maxValues3 = @json($maxValues3);
        const idValues3 = @json($idValues3);
        const maxValues4 = @json($maxValues);
        const idValues4 = @json($idValues4);
        const ttrThreshold = @json($ttrThreshold);
        const ttrThreshold1 = @json($ttrThreshold1);
        const ttrThreshold2 = @json($ttrThreshold2);
        const ttrThreshold3 = @json($ttrThreshold3);
        const secondCustomerSegment = @json($secondCustomerSegment);
        const thirdCustomerSegment = @json($thirdCustomerSegment);
        const firstNonHVCValue = @json($firstNonHVCValue);
        const columnSelect = document.getElementById('column-select');
        const checkboxContainer = document.getElementById('checkbox-container');
        const incidentColumnIndex = header.indexOf('INCIDENT');
        const bookingDateColumnIndex = header.indexOf('BOOKING DATE');
        const reportedDateColumnIndex = header.indexOf('REPORTED DATE');
        const TiketColumnIndex = header.indexOf('TICKET ID GAMAS');
        const regionColumnIndex = header.indexOf('REGION');
        const serviceColumnIndex = header.indexOf('SERVICE TYPE');
        const statusclosedColumnIndex = header.indexOf('STATUS');
        const segmenColumnIndex = header.indexOf('CUSTOMER SEGMENT');
        const customertypeColumnIndex = header.indexOf('SOURCE TICKET');
        const classificationtypeColumnIndex = header.indexOf('CLASSIFICATION FLAG');
        const customersegmentsColumnIndex = header.indexOf('CUSTOMER TYPE');
        const symptomColumnIndex = header.indexOf('SYMPTOM');
        const zColumnIndex = header.indexOf('GUARANTE STATUS');
        const statusColumnIndex = header.indexOf('STATUS');
        const closedColumnIndex = header.indexOf('CLOSED / REOPEN by');
        const resolveDateColumnIndex = header.indexOf('RESOLVE DATE');
        const processButton = document.getElementById('process-booking-date');
        const bookingDateTable = document.getElementById('booking-date-table');
        const bookingDateTbody = document.getElementById('booking-date-tbody');
        const bookingDateColumnIndexProcessed = mergedData[0].indexOf('BOOKING DATE');
        const reportedDateColumnIndexProcessed = mergedData[0].indexOf('REPORTED DATE');
        const regionColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'REG-1');
        const serviceColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'Service Type');
        const segmenColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'Customer Segment');
        const customertypeColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'Customer Only');
        const classificationtypeColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'Classification');
        const customersegmentsColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'Valid Ticket Group');
        const symptomColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'PDA');
        const zColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'NOT GUARANTEE');
        const statusColumnIndexProcessed = mergedData[0].findIndex(col => String(col).trim() === 'TIKET AKTIF');

        // Log the results
        // Log the indexes to check if they are found
        console.log('Region Column Index:', regionColumnIndexProcessed);
        console.log('Service Column Index:', serviceColumnIndexProcessed);
        console.log('Customer Segment Column Index:', segmenColumnIndexProcessed);
        console.log('Customer Type Column Index:', customertypeColumnIndexProcessed);
        console.log('Classification Column Index:', classificationtypeColumnIndexProcessed);
        console.log('Customer Segments Column Index:', customersegmentsColumnIndexProcessed);
        console.log('Symptom Column Index:', symptomColumnIndexProcessed);
        console.log('Z Column Index:', zColumnIndexProcessed);
        console.log('Status Column Index:', statusColumnIndexProcessed);

        function populateCheckboxes() {
            const selectedColumn = columnSelect.value;
            const columnIndex = header.indexOf(selectedColumn);

            if (columnIndex === -1) return;

            const values = mergedData.map(row => row[columnIndex]);
            checkboxContainer.innerHTML = '';

            const uniqueValues = [...new Set(values)];
            uniqueValues.forEach(value => {
                const label = document.createElement('label');
                label.className = 'flex items-center space-x-2';

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'value[]';
                input.value = value;
                input.className = 'form-checkbox';

                label.appendChild(input);
                label.appendChild(document.createTextNode(value));
                checkboxContainer.appendChild(label);
            });
        }

        function calculateTimeDifference(date) {
            if (!date || date === "" || date === undefined || date === null) {
                return "#VALUE!";
            }

            try {
                const now = dayjs();
                const targetDate = dayjs(date);
                
                if (!targetDate.isValid()) {
                    return "#VALUE!";
                }

                const diffInHours = Math.abs(now.diff(targetDate, 'hour', true));
                
                if (diffInHours > 1000000) {
                    console.error('Unreasonable time difference:', {
                        date,
                        now: now.format(),
                        diff: diffInHours
                    });
                    return "#ERROR!";
                }
                
                return `${diffInHours.toFixed(2)} jam`;
            } catch (error) {
                console.error('Error calculating time difference:', error);
                return "#VALUE!";
            }
        }

        function isTodayWO(date) {
            if (!date || date === "" || date === undefined || date === null) {
                return false;
            }

            try {
                const today = dayjs();
                const targetDate = dayjs(date);
                
                if (!targetDate.isValid()) {
                    return false;
                }

                return targetDate.format('YYYY-MM-DD') === today.format('YYYY-MM-DD');
            } catch (error) {
                console.error('Error checking Today WO:', error);
                return false;
            }
        }

        function extractHourFromDate(date) {
            if (!date || date === "" || date === undefined || date === null) {
                return "";
            }

            try {
                const targetDate = dayjs(date);

                if (!targetDate.isValid()) {
                    return "";
                }

                return targetDate.format('HH');
            } catch (error) {
                console.error('Error extracting hour:', error);
                return "";
            }
        }

        function isRegionOne(region) {
            if (!region || region === "" || region === undefined || region === null) {
                return false;
            }
            return region.trim().toUpperCase() === 'REG-1';
        }

        function isValidServiceType(service) {
                console.log('Checking service type value:', service);
                console.log('Available service types from database:', serviceTypes);
                
                // Check if service is null, undefined, or an empty string
                if (!service || service === "" || service === undefined || service === null) {
                    return false;
                }

            // Normalize service type for comparison
            const normalizedService = service.trim().toLowerCase();
            
            // Compare with service types from database
            return serviceTypes.some(type => {
                // Check if the type is valid (not null or undefined)
                if (type && typeof type === 'string') {
                    const normalizedType = type.toLowerCase().trim();
                    return normalizedType === normalizedService;
                }
                return false; // If type is invalid, return false
            });
        }

        function isValidSegmen(segmen) {
            console.log('Checking segmen type value:', segmen);
            console.log('Available segmen ice type from database:', segmens);
            if (!segmen || segmen === "" || segmen === undefined || segmen === null) {
                return false;
            }
            
            const normalizedSegmen = segmen.trim().toLowerCase();
            
            return segmens.some(type => {
                const normalizedType = type.toLowerCase().trim();
                return normalizedType === normalizedSegmen;
            });
        }

        function isValidCustomerType(customer) {
            // Detailed logging
            console.log('Input customer type:', customer);
            console.log('Available customer types:', customerTypes);
            
            // Input validation
            if (!customer || customer === "" || customer === undefined || customer === null) {
                console.log('Invalid customer input');
                return false;
            }
            
            // Normalize the input
            const normalizedCustomer = customer.toString().trim().toLowerCase();
            console.log('Normalized customer type:', normalizedCustomer);
            
            // Check each type with logging
            const result = customerTypes.some(type => {
                if (!type) {
                    console.log('Invalid type in customerTypes array:', type);
                    return false;
                }
                const normalizedType = type.toString().trim().toLowerCase();
                const matches = normalizedType === normalizedCustomer;
                console.log(`Comparing: "${normalizedType}" with "${normalizedCustomer}". Match: ${matches}`);
                return matches;
            });
            
            console.log('Final result for customer type validation:', result);
            return result;
        }

        function isValidClassificationType(classification) {
            // Detailed logging
            console.log('Input classification:', classification);
            console.log('Available classification types:', classificationTypes);
            
            // Input validation
            if (!classification || classification === "" || classification === undefined || classification === null) {
                console.log('Invalid classification input');
                return false;
            }
            
            // Normalize the input
            const normalizedClassification = classification.toString().trim().toLowerCase();
            console.log('Normalized classification:', normalizedClassification);
            
            // Check each type with logging
            const result = classificationTypes.some(type => {
                if (!type) {
                    console.log('Invalid type in classificationTypes array:', type);
                    return false;
                }
                const normalizedType = type.toString().trim().toLowerCase();
                const matches = normalizedType === normalizedClassification;
                console.log(`Comparing: "${normalizedType}" with "${normalizedClassification}". Match: ${matches}`);
                return matches;
            });
            
            console.log('Final result for classification validation:', result);
            return result;
        }

        function isValidCustomerSegment(customersegment) {
            // Detailed logging
            console.log('Input customer segment type:', customersegment);
            console.log('Available customer segment types:', customerSegments);
            
            // Input validation
            if (!customersegment || customersegment === "" || customersegment === undefined || customersegment === null) {
                console.log('Invalid customer input');
                return false;
            }
            
            // Normalize the input
            const normalizedCustomerSegment = customersegment.toString().trim().toLowerCase();
            console.log('Normalized customer segment type:', normalizedCustomerSegment);
            
            // Check each type with logging
            const result = customerSegments.some(type => {
                if (!type) {
                    console.log('Invalid type in customerSegments array:', type);
                    return false;
                }
                const normalizedType = type.toString().trim().toLowerCase();
                const matches = normalizedType === normalizedCustomerSegment; // Fixed: was comparing with normalizedCustomer
                console.log(`Comparing: "${normalizedType}" with "${normalizedCustomerSegment}". Match: ${matches}`);
                return matches;
            });
            
            console.log('Final result for customer segment validation:', result);
            return result;
        }

        function isValidZType(z) {
            // Detailed logging
            console.log('Input z type:', z);
            console.log('Available z types:', zTypes);
            
            // Input validation
            if (!z || z === "" || z === undefined || z === null) {
                console.log('Invalid customer input');
                return false;
            }
            
            // Normalize the input
            const normalizedZ = z.toString().trim().toLowerCase();
            console.log('Normalized customer type:', normalizedZ);
            
            // Check each type with logging
            const result = zTypes.some(type => {
                if (!type) {
                    console.log('Invalid type in z array:', type);
                    return false;
                }
                const normalizedType = type.toString().trim().toLowerCase();
                const matches = normalizedType === normalizedZ;
                console.log(`Comparing: "${normalizedType}" with "${normalizedZ}". Match: ${matches}`);
                return matches;
            });
            
            console.log('Final result for customer type validation:', result);
            return result;
        }

        // Add new function for PDA check with console logging
        function checkPDAInSymptom(symptom) {
            console.log('=== PDA Check Start ===');
            console.log('Input SYMPTOM:', symptom);
    
            // Handle undefined, null, or empty string cases
            if (symptom === undefined || symptom === null || symptom === '') {
                console.log('SYMPTOM is empty/null/undefined');
                console.log('Returning: false');
                console.log('=== PDA Check End ===');
                return false;
            }
    
            // Convert to string if not already (handles number inputs too)
            const symptomStr = String(symptom);
            const upperSymptom = symptomStr.trim().toUpperCase();
            console.log('Normalized SYMPTOM:', upperSymptom);
            
            const hasPDA = upperSymptom.includes('PDA');
            console.log('Contains PDA:', hasPDA);
            
            // Return true if PDA is NOT found (inverse of hasPDA)
            const result = !hasPDA;
            console.log('Final result:', result);
            console.log('=== PDA Check End ===');
            
            return result;
    }

    // Helper function to validate symptom data before processing
    function validateSymptomColumnData(data, symptomColumnIndex) {
        console.log('=== Validating Symptom Column ===');
        console.log('Symptom Column Index:', symptomColumnIndex);
        
        if (symptomColumnIndex === -1) {
            console.error('Symptom column not found in data');
            return false;
        }
        
        // Check if we have valid data to process
        if (!Array.isArray(data) || data.length === 0) {
            console.error('Invalid or empty data array');
            return false;
        }
        
        return true;
    }

    function isGuaranteeStatus(status) {
        if (!status || status === "" || status === undefined || status === null) {
            return false;
        }

            return status.trim().toUpperCase() === 'GUARANTEE';
    }

        processButton.addEventListener('click', function () {
            console.log('=== Process Button Clicked ===');
            console.log('Booking Date Column Index:', bookingDateColumnIndex);
            
            if (bookingDateColumnIndex === -1) {
                console.error('Booking Date column not found!');
                alert('Kolom "BOOKING DATE" tidak ditemukan!');
                return;
            }

            bookingDateTbody.innerHTML = '';
            console.log('Cleared table body');


    console.log('Processing', mergedData.length, 'rows of data');

    function isValidStatusType(statusclosed) {
        console.log('Checking status closed type value:', statusclosed);
                console.log('Available status closed types from database:', statusClosedTypes);
                
                // Check if service is null, undefined, or an empty string
                if (!statusclosed || statusclosed === "" || statusclosed === undefined || statusclosed === null) {
                    return false;
                }

            // Normalize service type for comparison
            const normalizedStatusClosed = statusclosed.trim().toLowerCase();
            
            // Compare with service types from database
            return statusClosedTypes.some(type => {
                // Check if the type is valid (not null or undefined)
                if (type && typeof type === 'string') {
                    const normalizedType = type.toLowerCase().trim();
                    return normalizedType === normalizedStatusClosed;
                }
                return false; // If type is invalid, return false
            });
    }

    function determineTicketStatus(status) {
    const isClosed = isValidStatusType(status);
    }

    function isValidClosedType(closed) {
    // Detailed logging
        console.log('Input closed type:', closed);
        console.log('Available closed types:', closedTypes);
        
        // Handle null input (treat it as valid, or decide how to handle it)
        if (closed === null) {
            console.log('Input is null, handling accordingly');
            return true;  // or return false if null should be invalid
        }
        
        // Normalize the input
        const normalizedClosed = closed.toString().trim().toLowerCase();
        console.log('Normalized closed type:', normalizedClosed);
        
        // Check each type with logging - return true if the value is NOT in the database
        const result = !closedTypes.some(type => {
            if (type == null) {
                console.log('Invalid type in closed array: null or undefined');
                return false;
            }
            
            const normalizedType = type.toString().trim().toLowerCase();
            const matches = normalizedType === normalizedClosed;
            console.log(`Comparing: "${normalizedType}" with "${normalizedClosed}". Match: ${matches}`);
            return matches;
        });
        
        console.log('Final result for closed type validation:', result);
        return result;
    }

    function isValidTicket(row) {
    // Ambil nilai dari row menggunakan indeks yang sudah didefinisikan
        const reg1 = isRegionOne(row[regionColumnIndexProcessed]);  // Pastikan regionColumnIndexProcessed sudah benar
        const serviceType = isValidServiceType(row[serviceColumnIndexProcessed]);
        const customerSegmen = isValidSegmen(row[segmenColumnIndexProcessed]);
        const customerOnly = isValidCustomerType(row[customertypeColumnIndexProcessed]);
        const classification = isValidClassificationType(row[classificationtypeColumnIndexProcessed]);
        const validTicketGroup = isValidCustomerSegment(row[customersegmentsColumnIndexProcessed]);
        const pda = checkPDAInSymptom(row[symptomColumnIndexProcessed]);
        const notGuarantee = !isGuaranteeStatus(row[zColumnIndexProcessed]);
        const tiketAktif = row[statusColumnIndexProcessed] !== 'CLOSED'; // Tiket aktif jika status bukan CLOSED

        // Log data untuk memastikan nilai yang diambil
            console.log('Checking row:', {
                reg1,
                serviceType,
                customerSegmen,
                customerOnly,
                classification,
                validTicketGroup,
                pda,
                notGuarantee,
                tiketAktif
            });

            // Periksa apakah semua kondisi terpenuhi
            const isValid = reg1 &&
                            serviceType &&
                            customerSegmen &&
                            customerOnly &&
                            classification &&
                            validTicketGroup &&
                            pda &&
                            notGuarantee &&
                            tiketAktif;

            console.log('Is valid ticket:', isValid);
            return isValid ? 'TRUE' : 'FALSE'; // Return 'TRUE' jika valid, 'FALSE' jika tidak
    }

    function isValidAssurance(row) {
        const reg1 = isRegionOne(row[regionColumnIndexProcessed]);
        const serviceType = isValidServiceType(row[serviceColumnIndexProcessed]);
        const customerSegmen = isValidSegmen(row[segmenColumnIndexProcessed]);
        const customerOnly = isValidCustomerType(row[customertypeColumnIndexProcessed]);
        const classification = isValidClassificationType(row[classificationtypeColumnIndexProcessed]);
        const validTicketGroup = isValidCustomerSegment(row[customersegmentsColumnIndexProcessed]);
        const pda = checkPDAInSymptom(row[symptomColumnIndexProcessed]);
        const notGuarantee = !isGuaranteeStatus(row[zColumnIndexProcessed]);

        return reg1 && 
            serviceType && 
            customerSegmen && 
            customerOnly && 
            classification && 
            validTicketGroup && 
            pda && 
            notGuarantee;
        }

        function calculateResolutionTime(resolveDate, reportedDate) {
            if (!resolveDate || resolveDate === "" || resolveDate === undefined || resolveDate === null) {
                return 0;
            }

            try {
                const resolve = dayjs(resolveDate);
                const reported = dayjs(reportedDate);
                
                if (!resolve.isValid() || !reported.isValid()) {
                    return 0;
                }

                // Calculate difference in hours
                const diffInHours = resolve.diff(reported, 'hour', true);
                
                if (diffInHours < 0) {
                    return 0;
                }

                return diffInHours.toFixed(2);
            } catch (error) {
                console.error('Error calculating resolution time:', error);
                return 0;
            }
    }

    function processBookingDate(bookingDate) {
        if (!bookingDate || bookingDate === "" || bookingDate === undefined || bookingDate === null) {
            return ""; // Return empty string if booking date is empty
        }

        try {
            // Check if length is greater than 19
            if (bookingDate.length > 19) {
                // Take first 19 characters and try to convert to a valid date
                const truncatedDate = bookingDate.substring(0, 19);
                const parsedDate = dayjs(truncatedDate);
                
                if (parsedDate.isValid()) {
                    return truncatedDate;
                }
            }
            
            // If length is not greater than 19 or parsing failed, return original value
            return bookingDate;
            
        } catch (error) {
            console.error('Error processing booking date:', error);
            return bookingDate;
        }
    }

    function calculateResolutionTime(resolveDate, reportedDate) {
        if (!resolveDate || resolveDate === "" || resolveDate === undefined || resolveDate === null) {
            return 0;
        }

        try {
            const resolve = dayjs(resolveDate);
            const reported = dayjs(reportedDate);
            
            if (!resolve.isValid() || !reported.isValid()) {
                return 0;
            }

            const diffInHours = resolve.diff(reported, 'hour', true);
            
            if (diffInHours < 0) {
                return 0;
            }

            return diffInHours.toFixed(2);
        } catch (error) {
            console.error('Error calculating resolution time:', error);
            return 0;
        }
    }

    function processBookingDate(bookingDate) {
        if (!bookingDate || bookingDate === "" || bookingDate === undefined || bookingDate === null) {
            return ""; // Return empty string if booking date is empty
        }

        try {
            // Check if length is greater than 19
            if (bookingDate.length > 19) {
                // Take first 19 characters and try to convert to a valid date
                const truncatedDate = bookingDate.substring(0, 19);
                const parsedDate = dayjs(truncatedDate);
                
                if (parsedDate.isValid()) {
                    return truncatedDate;
                }
            }
            
            // If length is not greater than 19 or parsing failed, return original value
            return bookingDate;
            
        } catch (error) {
            console.error('Error processing booking date:', error);
            return bookingDate;
        }
    }

    function calculateTimeFromResolveToBooking(resolveDate, processedBookingDate) {
        console.log('Input for time calculation:', {
            resolveDate,
            processedBookingDate
        });

        try {
            function getExcelSerialDate(jsDate) {
                // Excel epoch starts on December 31, 1899
                const excelEpoch = new Date(1899, 11, 31);
                
                // Get the difference in milliseconds
                const diff = jsDate - excelEpoch;
                
                // Convert to days
                const days = diff / (24 * 60 * 60 * 1000);
                
                // Add 1 to match Excel's system
                // Excel considers January 1, 1900 as day 1, not 0
                return days + 1;
            }

            // Kasus ketika resolveDate kosong
            if (!resolveDate || resolveDate.length === 0) {
                if (processedBookingDate && processedBookingDate.length > 0) {
                    const bookingDateTime = new Date(processedBookingDate);
                    const excelDate = getExcelSerialDate(bookingDateTime);
                    
                    // Konversi ke jam (negatif karena resolveDate kosong)
                    const hours = -excelDate * 24;
                    
                    // Handle fractional part precisely
                    return hours.toFixed(2);
                }
                return "0.00";
            }
            
            // Kasus ketika processedBookingDate kosong
            if (!processedBookingDate || processedBookingDate.length === 0) {
                const resolveDateTime = new Date(resolveDate);
                const excelDate = getExcelSerialDate(resolveDateTime);
                return (excelDate * 24).toFixed(2);
            }
            
            // Kasus ketika kedua tanggal ada
            const resolveDateTime = new Date(resolveDate);
            const bookingDateTime = new Date(processedBookingDate);
            
            const resolveDays = getExcelSerialDate(resolveDateTime);
            const bookingDays = getExcelSerialDate(bookingDateTime);
            
            const diffInDays = resolveDays - bookingDays;
            return (diffInDays * 24).toFixed(2);
        } catch (error) {
            console.error('Error calculating time difference:', error);
            return "0.00";
        }
    }

    function compareDateWithToday(resolveDate) {
        console.log('Comparing dates:', {
            resolveDate,
            today: new Date()
        });

        try {
            // If resolveDate is empty or invalid, return 'FALSE'
            if (!resolveDate || resolveDate.length === 0) {
                console.log('Empty or invalid resolve date');
                return 'FALSE';
            }

            // Format resolveDate to YYYYMMDD
            const resolve = dayjs(resolveDate);
            if (!resolve.isValid()) {
                console.log('Invalid resolve date format');
                return 'FALSE';
            }
            const formattedResolveDate = resolve.format('YYYYMMDD');

            // Format today's date to YYYYMMDD
            const today = dayjs();
            const formattedToday = today.format('YYYYMMDD');

            console.log('Formatted dates comparison:', {
                formattedResolveDate,
                formattedToday
            });

            // Compare the formatted dates and return 'TRUE' or 'FALSE'
            return formattedResolveDate === formattedToday ? 'TRUE' : 'FALSE';
        } catch (error) {
            console.error('Error comparing dates:', error);
            return 'FALSE';
        }
    }

    function countIfUnique(data, columnIndex, valueToCheck) {
        let count = 0;

        // Loop through the data starting from row 2 (skip header)
        for (let i = 1; i < data.length; i++) {
            const currentValue = data[i][columnIndex];

            // Check if the value matches and count its occurrences
            if (currentValue === valueToCheck) {
                count++;
            }
        }

        // Return false if the value is duplicate (count > 0), else true
        return count > 0 ? false : true;
    }

    function getMarkingDiamondCategory(timeDifferenceStr) {
        // Remove "jam" from the string and convert to number
        const timeDifference = parseFloat(timeDifferenceStr.replace(' jam', ''));
        
        // Apply the formula logic
        if (timeDifference <= maxValues[0]) {
            return idValues[0];
        } else if (timeDifference <= maxValues[1]) {
            return idValues[1];
        } else if (timeDifference <= maxValues[2]) {
            return idValues[2];
        } 
        else {
            return idValues[3];
        }
    }

    function getMarkingPlatinumCategory(timeDifferenceStr) {
        // Remove "jam" from the string and convert to number
        const timeDifference = parseFloat(timeDifferenceStr.replace(' jam', ''));
        
        // Apply the formula logic
        if (timeDifference <= maxValues2[0]) {
            return idValues2[0];
        } else if (timeDifference <= maxValues2[1]) {
            return idValues2[1];
        } else if (timeDifference <= maxValues2[2]) {
            return idValues2[2];
        } else if (timeDifference <= maxValues2[3]) {
            return idValues2[3];
        }
        else {
            return idValues2[4];
        }
    }

    function getMarkingNonCategory(timeDifferenceStr) {
        // Remove "jam" from the string and convert to number
        const timeDifference = parseFloat(timeDifferenceStr.replace(' jam', ''));
        
        // Apply the formula logic
        if (timeDifference <= maxValues3[0]) {
            return idValues3[0];
        } else if (timeDifference <= maxValues3[1]) {
            return idValues3[1];
        } else if (timeDifference <= maxValues3[2]) {
            return idValues3[2];
        } else if (timeDifference <= maxValues3[3]) {
            return idValues3[3];
        } else if (timeDifference <= maxValues3[4]) {
            return idValues3[4];
        } else if (timeDifference <= maxValues3[5]) {
            return idValues3[5];
        }
        else if (timeDifference <= maxValues3[6]) {
            return idValues3[6];
        }
        else {
            return idValues3[7];
        }
    }

    function searchTextInValue(searchText, value) {
        if (!searchText || !value) {
            return false;
        }
        // Convert both to string and uppercase for case-insensitive search
        const searchStr = String(searchText).toUpperCase();
        const valueStr = String(value).toUpperCase();
        
        // Return true if searchStr is found in valueStr
        return valueStr.indexOf(searchStr) !== -1;
    }

    function isBothSegmentsFalse(row, secondCustomerSegment, thirdCustomerSegment) 
    {
        const secondSegmentMatch = searchTextInValue(secondCustomerSegment.customer_segment, row[customertypeColumnIndex]);
        const thirdSegmentMatch = searchTextInValue(thirdCustomerSegment.customer_segment, row[customertypeColumnIndex]);
        return !secondSegmentMatch && !thirdSegmentMatch;
    }

    function isAllValid(status, row, closed, isUnique) 
    {
        return (
            isValidStatusType(status) && 
            isValidAssurance(row) && 
            isValidClosedType(closed) && 
            isUnique
        );
    }

    function checkTTR(timeValue) 
    {
        // Remove "jam" and convert to float
        const hours = parseFloat(timeValue.replace(' jam', ''));
        return !isNaN(hours) && hours <= ttrThreshold;
    }

    function checkTTR1(timeValue1) 
    {
        // Remove "jam" and convert to float
        const hours = parseFloat(timeValue1.replace(' jam', ''));
        return !isNaN(hours) && hours <= ttrThreshold1;
    }

    function checkTTR2(timeValue2) 
    {
        // Remove "jam" and convert to float
        const hours = parseFloat(timeValue2.replace(' jam', ''));
        return !isNaN(hours) && hours <= ttrThreshold2;
    }

    function checkTTR3(timeValue3) 
    {
        // Remove "jam" and convert to float
        const hours = parseFloat(timeValue3.replace(' jam', ''));
        return !isNaN(hours) && hours <= ttrThreshold3;
    }

    function calculateNonHVCTimeDifference(reportedDate, firstNonHVCValue) 
    {
        try {
            console.log('Starting calculation for NonHVC Time Difference...');
            console.log('Reported Date:', reportedDate);
            console.log('First NonHVC Value:', firstNonHVCValue);

            // Get the time difference for the reported date
            const reportedTimeDiff = calculateTimeDifference(reportedDate);
            console.log('Reported Time Difference:', reportedTimeDiff);

            // Remove 'jam' from the string and convert to number
            const reportedHours = parseFloat(reportedTimeDiff.replace(' jam', ''));
            console.log('Reported Hours (numeric):', reportedHours);

            // Access the value property of firstNonHVCValue object
            const nonHVCValue = firstNonHVCValue.value;
            
            // Calculate the difference between first NonHVC value and reported time
            const difference = nonHVCValue - reportedHours;
            console.log('Calculated Difference (numeric):', difference);

            return difference;
        } catch (error) {
            console.error('Error calculating NonHVC time difference:', error);
            return '0.00 jam';
        }
    }

    function getMarking36(timeDifferenceInput) {
    // Handle both string and number inputs
    let timeDifference;
    
    if (typeof timeDifferenceInput === 'string') {
        // If it's a string, remove "jam" and convert to number
        timeDifference = parseFloat(timeDifferenceInput.replace(' jam', ''));
        console.log("Input is a string. Converted to number:", timeDifference);
    } else {
        // If it's already a number, use it directly
        timeDifference = timeDifferenceInput;
        console.log("Input is already a number:", timeDifference);
    }
    
    // Handle NaN case
    if (isNaN(timeDifference)) {
        console.log("Invalid input detected. Returning default value.");
        return idValues4[7]; // Return default value for invalid input
    }
    
    // Apply the formula logic
    console.log("Applying formula logic for timeDifference:", timeDifference);
    if (timeDifference >= maxValues4[0]) {
        console.log("Returning idValues4[0] for timeDifference:", timeDifference);
        return idValues4[0];
    } else if (timeDifference >= maxValues4[1]) {
        console.log("Returning idValues4[1] for timeDifference:", timeDifference);
        return idValues4[1];
    } else if (timeDifference >= maxValues4[2]) {
        console.log("Returning idValues4[2] for timeDifference:", timeDifference);
        return idValues4[2];
    } else if (timeDifference >= maxValues4[3]) {
        console.log("Returning idValues4[3] for timeDifference:", timeDifference);
        return idValues4[3];
    } else if (timeDifference >= maxValues4[4]) {
        console.log("Returning idValues4[4] for timeDifference:", timeDifference);
        return idValues4[4];
    } else if (timeDifference >= maxValues4[5]) {
        console.log("Returning idValues4[5] for timeDifference:", timeDifference);
        return idValues4[5];
    } else if (timeDifference >= maxValues4[6]) {
        console.log("Returning idValues4[6] for timeDifference:", timeDifference);
        return idValues4[6];
    } else {
        console.log("Returning idValues4[7] for timeDifference:", timeDifference);
        return idValues4[7];
    }
}

    mergedData.forEach((row, index) => {
        console.log(`\n=== Processing Row ${index + 1} ===`);
        
        const bookingDate = row[bookingDateColumnIndex];
        const reportedDate = row[reportedDateColumnIndex];
        const region = row[regionColumnIndex];
        const service = row[serviceColumnIndex];
        const segmen = row[segmenColumnIndex];
        const customer = row[customertypeColumnIndex];  
        const classification = row[classificationtypeColumnIndex];
        const customersegment = row[customersegmentsColumnIndex];
        const symptom = row[symptomColumnIndex];
        const z = row[zColumnIndex];
        const guaranteeStatus = row[zColumnIndex];
        const status = row[statusColumnIndex]; 
        const closed = row[closedColumnIndex];
        const resolveDate = row[resolveDateColumnIndex]; 
        const processedBookingDate = processBookingDate(bookingDate);
        const timeFromResolveToBooking = calculateTimeFromResolveToBooking(resolveDate, processedBookingDate);
        const Tiket = row[TiketColumnIndex]; 
        const incidentValue = row[incidentColumnIndex]; 

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';

        const isClosed = status === 'CLOSED'; // Memeriksa jika statusnya CLOSED
        const ticketAktif = isClosed ? false : true;

        const isBookingDateValid = bookingDate && bookingDate.length > 0;
        const isTiketValid = Tiket && Tiket.length > 0;

        const isUnique = countIfUnique(mergedData, incidentColumnIndex, incidentValue);
        console.log(isUnique ? "Unique" : "Duplicate");
        

        const cells = [
            { value: bookingDate || '' },
            { value: calculateTimeDifference(bookingDate) },
            { value: isTodayWO(bookingDate) ? 'TRUE' : 'FALSE' },
            { value: extractHourFromDate(bookingDate) },
            { value: calculateTimeDifference(reportedDate) },
            { value: isRegionOne(region) ? 'TRUE' : 'FALSE' },
            { value: isValidServiceType(service) ? 'TRUE' : 'FALSE' },
            { value: isValidSegmen(segmen) ? 'TRUE' : 'FALSE' },
            { value: isValidCustomerType(customer) ? 'TRUE' : 'FALSE' },
            { value: isValidClassificationType(classification) ? 'TRUE' : 'FALSE' },
            { value: isValidCustomerSegment(customersegment) ? 'TRUE' : 'FALSE' },
            { value: checkPDAInSymptom(symptom) ? 'TRUE' : 'FALSE' },
            { value: isValidZType(z) ? 'TRUE' : 'FALSE' },
            { value: ticketAktif ? 'TRUE' : 'FALSE' },
            { value: isGuaranteeStatus(guaranteeStatus) ? 'TRUE' : 'FALSE' },
            { value: isValidStatusType(status) ? 'TRUE' : 'FALSE' },
            { value: isValidTicket(row) ? 'TRUE' : 'FALSE' },
            { value: isValidAssurance(row) ? 'TRUE' : 'FALSE' },
            { value: searchTextInValue(secondCustomerSegment.customer_segment, row[customertypeColumnIndex]) ? 'TRUE' : 'FALSE' },
            { value: getMarkingDiamondCategory(calculateTimeDifference(reportedDate)) },
            { value: searchTextInValue(thirdCustomerSegment.customer_segment, row[customertypeColumnIndex]) ? 'TRUE' : 'FALSE' },
            { value: getMarkingPlatinumCategory(calculateTimeDifference(reportedDate)) },
            { value: isBothSegmentsFalse(row, secondCustomerSegment, thirdCustomerSegment) ? 'TRUE' : 'FALSE' },
            { value: getMarkingNonCategory(calculateTimeDifference(reportedDate)) },
            { value: isValidClosedType(closed) ? 'TRUE' : 'FALSE'},
            { value: calculateResolutionTime(resolveDate, reportedDate) + ' jam' },
            { value: processedBookingDate },
            { value: timeFromResolveToBooking + ' jam' },
            { value: isAllValid(status, row, closed, isUnique) ? 'TRUE' : 'FALSE' },
            { value: checkTTR(timeFromResolveToBooking + ' jam') ? 'TRUE' : 'FALSE' },
            { value: checkTTR1(calculateResolutionTime(resolveDate, reportedDate) + ' jam') ? 'TRUE' : 'FALSE' },
            { value: checkTTR2(calculateResolutionTime(resolveDate, reportedDate) + ' jam') ? 'TRUE' : 'FALSE' },
            { value: checkTTR3(calculateResolutionTime(resolveDate, reportedDate) + ' jam') ? 'TRUE' : 'FALSE' },
            { value: compareDateWithToday(resolveDate) },
            { value: isBookingDateValid ? 'TRUE' : 'FALSE' },
            { value: calculateNonHVCTimeDifference(reportedDate, firstNonHVCValue) },
            { value: getMarking36(calculateNonHVCTimeDifference(reportedDate, firstNonHVCValue)) }, 
            { value: isTiketValid ? 'TRUE' : 'FALSE' },
            { value: isUnique ? 'TRUE' : 'FALSE' },
            
        ];

        console.log('Generated cell values:', cells.map(cell => cell.value));

        cells.forEach((cell, cellIndex) => {
            const td = document.createElement('td');
            td.className = 'border border-gray-300 px-4 py-2 text-center text-sm text-gray-600';
            td.textContent = cell.value;
            tr.appendChild(td);
        });

        bookingDateTbody.appendChild(tr);
        console.log(`Row ${index + 1} added to table`);
    });

    bookingDateTable.classList.remove('hidden');
});

        columnSelect.addEventListener('change', populateCheckboxes);
        populateCheckboxes();

        
</script>
    
<script>
document.getElementById('downloadExcel').addEventListener('click', async function() {
    try {
        const mergedTable = document.getElementById('merged-table');
        const processedTable = document.getElementById('booking-date-table');
        
        if (!mergedTable || !processedTable) {
            throw new Error('Tabel tidak ditemukan!');
        }

        // Generate filename dengan benar
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hour = String(now.getHours()).padStart(2, '0');
        
        // Perbaikan: Gunakan backticks untuk template string
        const filename = `Report TTR WSA - ${day}${month}${year} - ${hour}.00 Wib.xlsx`;

        // Fungsi untuk mendapatkan data tabel (sama seperti sebelumnya)
        function getTableData(table) {
    const rows = table.getElementsByTagName('tr');
    const data = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const rowData = [];
        const cells = row.getElementsByTagName('td');
        const headers = row.getElementsByTagName('th');
        
        // Tangani header
        if (headers.length > 0) {
            for (let j = 0; j < headers.length; j++) {
                rowData.push(headers[j].textContent.trim());
            }
        }
        
        // Tangani sel dengan format tanggal khusus
        if (cells.length > 0) {
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.trim();
                
                // Cek apakah ini adalah tanggal dengan format yang diketahui
                const isDateFormat = /^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}:\d{2}$/.test(cellText) || 
                                   /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/.test(cellText);
                
                if (isDateFormat) {
                    // Jika ini tanggal, simpan dengan format aslinya
                    rowData.push(cellText);
                } else if (!isNaN(cellText) && cellText.includes('.') && 
                          parseFloat(cellText) > 40000 && parseFloat(cellText) < 50000) {
                    // Ini kemungkinan serial date Excel, konversi kembali ke format tanggal
                    const excelDate = parseFloat(cellText);
                    const jsDate = new Date((excelDate - 25569) * 86400 * 1000);
                    const formattedDate = dayjs(jsDate).format('DD/MM/YYYY HH:mm:ss');
                    rowData.push(formattedDate);
                } else {
                    // Bukan tanggal, simpan apa adanya
                    rowData.push(cellText);
                }
            }
        }
        
        if (rowData.length > 0) {
            data.push(rowData);
        }
    }
    return data;
}

// Fungsi untuk menggabungkan data tabel
const mergedData = getTableData(mergedTable);
const processedData = getTableData(processedTable);

const combinedData = [];
const maxRows = Math.max(mergedData.length, processedData.length);

for (let i = 0; i < maxRows; i++) {
    const row = [];
    if (mergedData[i]) row.push(...mergedData[i]);
    if (processedData[i]) row.push(...processedData[i]);
    combinedData.push(row);
}

        const response = await fetch('/api/save-excel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ data: combinedData })
        });

        // Cek jika status sukses
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } else {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Terjadi kesalahan saat menyimpan file');
        }
    } catch (error) {
        console.error('Error detail:', error);
        alert('Gagal menyimpan file. Error: ' + error.message);
    }
});
</script>
<!-- <script>
document.getElementById('shareWhatsApp').addEventListener('click', function() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const hour = String(now.getHours()).padStart(2, '0');
    
    const reportName = `Report TTR WSA - ${day}${month}${year} - ${hour}.00 Wib`;
    
    // Create WhatsApp message
    const message = `*${reportName}*\n\n` +
        `Report has been generated on ${day}/${month}/${year} at ${hour}:00 WIB.\n\n` +
        `Please check the Excel file that has been downloaded for complete details.`;
    
    // Encode the message for WhatsApp URL
    const encodedMessage = encodeURIComponent(message);
    
    // Open WhatsApp Web with the pre-filled message
    window.open(`https://wa.me/?text=${encodedMessage}`, '_blank');
});
</script> -->

</body>
</html>