<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index'); // Sesuaikan dengan nama view yang ada
    }

    public function history()
    {
        $histories = History::orderBy('download_time', 'desc')->get();
        return view('history', compact('histories'));
    }
}
