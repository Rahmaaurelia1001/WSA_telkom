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
        .max-h-72 {
            max-height: 18rem;
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
                <h2 class="text-xl font-bold mb-4 text-center">Data yang Dihapus</h2>
                <form action="{{ route('upload.delete') }}" method="POST">
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

            <div id="booking-date-table" class="bg-white shadow-md rounded-lg p-6 mt-10" style="width: 100vw; margin-left: calc(-50vw + 50%); padding-left: 20px; padding-right: 20px;">
                <h3 class="text-lg font-semibold text-center mb-4">Hasil Proses Data</h3>
                <div class="overflow-x-auto overflow-y-auto max-h-96">
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead class="sticky top-0 bg-gray-200">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Booking Date</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Durasi Manja</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Today WO</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Jam Manja</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Durasi Tiket</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Reg-1</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Service Type</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Customer Segmen</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Customer Only</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Classification</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">Valid Ticket Group</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">PDA</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">NOT GUARANTEE</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">TIKET AKTIF</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">GUARANTEE</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">CLOSED</th>
                                <!-- <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">TIKET AKTIF</th> -->
                            </tr>
                        </thead>
                        <tbody id="booking-date-tbody">
                            <!-- Table body will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        const mergedData = @json(session('merged_data', []));
        const header = @json(session('header', []));
        const serviceTypes = @json(session('service_types', []));
        const segmens = @json(session('segmens', []));
        const customerTypes = @json(session('customer_types', []));
        const classificationTypes = @json(session('classification_types', []));
        const customerSegments = @json(session('customer_segments', []));
        const zTypes = @json(session('zs', []));
        const columnSelect = document.getElementById('column-select');
        const checkboxContainer = document.getElementById('checkbox-container');
        const bookingDateColumnIndex = header.indexOf('BOOKING DATE');
        const reportedDateColumnIndex = header.indexOf('REPORTED DATE');
        const regionColumnIndex = header.indexOf('REGION');
        const serviceColumnIndex = header.indexOf('SERVICE TYPE');
        const segmenColumnIndex = header.indexOf('CUSTOMER SEGMENT');
        const customertypeColumnIndex = header.indexOf('SOURCE TICKET');
        const classificationtypeColumnIndex = header.indexOf('CLASSIFICATION FLAG');
        const customersegmentsColumnIndex = header.indexOf('CUSTOMER TYPE');
        const symptomColumnIndex = header.indexOf('SYMPTOM');
        const zColumnIndex = header.indexOf('GUARANTE STATUS');
        const statusColumnIndex = header.indexOf('STATUS');
        const processButton = document.getElementById('process-booking-date');
        const bookingDateTable = document.getElementById('booking-date-table');
        const bookingDateTbody = document.getElementById('booking-date-tbody');

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
            console.log('Available service type from database:', serviceTypes);
            if (!service || service === "" || service === undefined || service === null) {
                return false;
            }
            
            // Normalize service type for comparison
            const normalizedService = service.trim().toLowerCase();
            
            // Compare with service types from database
            return serviceTypes.some(type => {
                const normalizedType = type.toLowerCase().trim();
                return normalizedType === normalizedService;
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
            
            if (!symptom) {
                console.log('SYMPTOM is empty/null/undefined');
                console.log('Returning: false');
                console.log('=== PDA Check End ===');
                return false;
            }
            
            const upperSymptom = symptom.toUpperCase();
            console.log('Uppercase SYMPTOM:', upperSymptom);
            
            const hasPDA = upperSymptom.includes('PDA');
            console.log('Contains PDA:', hasPDA);
            console.log('Returning:', !hasPDA);
            console.log('=== PDA Check End ===');
            
            return !hasPDA;
        }

        function isGuaranteeStatus(status) {
            if (!status || status === "" || status === undefined || status === null) {
                return false;
            }

            // Cek apakah status adalah "GUARANTEE"
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

            // Update the table header to include PDA column
            // const thead = document.querySelector('#booking-date-table thead tr');
            // if (!thead.querySelector('th:nth-last-child(1)').textContent.includes('PDA')) {
            //     console.log('Adding PDA column header');
            //     const pdaTh = document.createElement('th');
            //     pdaTh.className = 'border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700';
            //     pdaTh.textContent = 'PDA';
            //     thead.appendChild(pdaTh);
            // }

    console.log('Processing', mergedData.length, 'rows of data');

    function isValidStatusType(status) {
        const isClosed = status === 'CLOSED';
        console.log('Checking status:', status, 'Is it CLOSED?', isClosed); // Log untuk memeriksa status
        return isClosed;
    }

    function determineTicketStatus(status) {
    const isClosed = isValidStatusType(status);
    
    // Jika statusnya CLOSED, maka TIKET AKTIF adalah false
    // const ticketAktif = isClosed ? false : true;
    

    // return {
    //     ticketAktif: ticketAktif,
    //     closedStatus: closedStatus
    // };
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

        console.log('Row Data:', {
            bookingDate,
            reportedDate,
            region,
            service,
            segmen,
            customer,
            classification,
            customersegment,
            symptom
        });

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';

        const isClosed = status === 'CLOSED'; // Memeriksa jika statusnya CLOSED
        const ticketAktif = isClosed ? false : true;
        
        const cells = [
            { value: bookingDate || '' },
            { value: calculateTimeDifference(bookingDate) },
            { value: isTodayWO(bookingDate) ? 'True' : 'False' },
            { value: extractHourFromDate(bookingDate) },
            { value: calculateTimeDifference(reportedDate) },
            { value: isRegionOne(region) ? 'True' : 'False' },
            { value: isValidServiceType(service) ? 'True' : 'False' },
            { value: isValidSegmen(segmen) ? 'True' : 'False' },
            { value: isValidCustomerType(customer) ? 'True' : 'False' },
            { value: isValidClassificationType(classification) ? 'True' : 'False' },
            { value: isValidCustomerSegment(customersegment) ? 'True' : 'False' },
            { value: checkPDAInSymptom(symptom) ? 'True' : 'False' },
            { value: isValidZType(z) ? 'True' : 'False' },
            { value: ticketAktif ? 'True' : 'False' },
            { value: isGuaranteeStatus(guaranteeStatus) ? 'True' : 'False' },
            { value: isValidStatusType(status) ? 'True' : 'False' }
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
    console.log('Table visibility updated');
    console.log('=== Process Complete ===');
});

        columnSelect.addEventListener('change', populateCheckboxes);
        populateCheckboxes();
    </script>
</body>
</html>