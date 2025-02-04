<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;

class HistoryController extends Controller
{
    public function showUploadForm() {
        $histories = History::all();
        return view('upload-form', compact('histories'));
    }
    
    public function showHistory() {
        $histories = History::all();
        return view('history', compact('histories'));
    }
    
}
