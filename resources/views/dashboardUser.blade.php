<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring KPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <!-- Card Pertama -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-10">
            <!-- Judul -->
            <h1 class="text-center text-2xl font-bold text-gray-800 mb-6">
                MONITORING KPI WSA 3 JAM MANJA (TARGET: 94.79%)
            </h1>
            
            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border border-gray-300 bg-yellow-200 text-left px-4 py-2">WITEL</th>
                            <th colspan="10" class="border border-gray-300 bg-yellow-200 text-center px-4 py-2">JAM MANJA</th>
                            <th rowspan="2" class="border border-gray-300 bg-yellow-200 text-center px-4 py-2">TOTAL</th>
                            <th colspan="5" class="border border-gray-300 bg-orange-400 text-center px-4 py-2">CLOSED (HI)</th>
                        </tr>
                        <tr>
                            <?php
                            $times = ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00"];
                            foreach ($times as $time) {
                                echo "<th class='border border-gray-300 bg-yellow-200 text-center px-4 py-2'>$time</th>";
                            }
                            ?>
                            <th class="border border-gray-300 bg-orange-400 text-center px-4 py-2">COMP</th>
                            <th class="border border-gray-300 bg-orange-400 text-center px-4 py-2">NCOMP</th>
                            <th class="border border-gray-300 bg-orange-400 text-center px-4 py-2">TOTAL</th>
                            <th class="border border-gray-300 bg-orange-400 text-center px-4 py-2">REAL</th>
                            <th class="border border-gray-300 bg-orange-400 text-center px-4 py-2">ACH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $regions = ["ACEH", "BABEL", "BENGKULU", "JAMBI", "LAMPUNG", "MEDAN", "RIDAR", "RIKEP", "SUMBAR", "SUMSEL", "SUMUT"];
                        $totalJamManja = array_fill(0, 10, 0); // Placeholder untuk kolom JAM MANJA total
                        $totalComp = 0;
                        $totalNComp = 0;

                        foreach ($regions as $region) {
                            echo "<tr>";
                            echo "<td class='border border-gray-300 bg-gray-100 px-4 py-2'>$region</td>";

                            // Placeholder nilai acak untuk Jam Manja
                            for ($i = 0; $i < 10; $i++) {
                                $value = rand(0, 5); // Nilai acak antara 0 hingga 5
                                echo "<td class='border border-gray-300 text-center px-4 py-2'>$value</td>";
                                $totalJamManja[$i] += $value;
                            }

                            // Total baris
                            $rowTotal = array_sum($totalJamManja);
                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$rowTotal</td>";

                            // COMP dan NCOMP
                            $comp = rand(0, 10); // Nilai acak untuk COMP
                            $ncomp = rand(0, 10); // Nilai acak untuk NCOMP
                            $totalComp += $comp;
                            $totalNComp += $ncomp;

                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$comp</td>";
                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$ncomp</td>";

                            // TOTAL
                            $totalHi = $comp + $ncomp;
                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$totalHi</td>";

                            // REAL dan ACH
                            $realValue = "100,00%";
                            $achValue = "105,50%";
                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$realValue</td>";
                            echo "<td class='border border-gray-300 text-center px-4 py-2 font-bold'>$achValue</td>";
                            echo "</tr>";
                        }

                        // Baris Total
                        echo "<tr>";
                        echo "<td class='border border-gray-300 bg-gray-200 px-4 py-2 font-bold text-center'>TOTAL</td>";
                        foreach ($totalJamManja as $total) {
                            echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$total</td>";
                        }
                        $grandTotal = $totalComp + $totalNComp;
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$grandTotal</td>";
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$totalComp</td>";
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$totalNComp</td>";
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$grandTotal</td>";
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$realValue</td>";
                        echo "<td class='border border-gray-300 px-4 py-2 text-center font-bold'>$achValue</td>";
                        echo "</tr>";
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Keterangan -->
            <p class="text-sm text-gray-500 text-right mt-4">
                Update at <?= date('d/m/Y') ?> pukul <?= date('H:i') ?> WIB <br>
                Source: <a href="https://insera.telkom.co.id" class="text-blue-500 underline">insera.telkom.co.id</a>
            </p>
        </div>
    </div>
</body>
</html>
