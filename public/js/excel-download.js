document.getElementById('downloadExcel').addEventListener('click', function() {
    // Ambil data dari kedua tabel
    const mergedTableData = getTableData('merged-table');
    const processedTableData = getTableData('booking-date-table');
    
    // Gabungkan data secara horizontal
    const combinedData = combineDataHorizontally(mergedTableData, processedTableData);
    
    // Kirim data ke controller
    fetch('/download-excel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ data: combinedData })
    })
    .then(response => response.blob())
    .then(blob => {
        // Download file
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'processed_data.xlsx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    });
});

function getTableData(tableId) {
    const table = document.getElementById(tableId);
    const data = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('th, td');
        cells.forEach(cell => {
            rowData.push(cell.textContent);
        });
        data.push(rowData);
    });
    
    return data;
}

function combineDataHorizontally(table1Data, table2Data) {
    const maxRows = Math.max(table1Data.length, table2Data.length);
    const combinedData = [];
    
    for (let i = 0; i < maxRows; i++) {
        const row1 = table1Data[i] || [];
        const row2 = table2Data[i] || [];
        combinedData.push([...row1, ...row2]);
    }
    
    return combinedData;
}