<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardUserController extends Controller
{
    public function dashboardUser()
    {
        // Data yang akan ditampilkan di tabel
        $data = [
            ['Kolom 1' => 'Data 1', 'Kolom 2' => 'Data 2', 'Kolom 3' => 'Data 3'],
            ['Kolom 1' => 'Data 4', 'Kolom 2' => 'Data 5', 'Kolom 3' => 'Data 6'],
            ['Kolom 1' => 'Data 7', 'Kolom 2' => 'Data 8', 'Kolom 3' => 'Data 9'],
        ];

        // Return ke view dashboard dengan data
        return view('dashboardUser', ['data' => $data]);
    }

    
}
