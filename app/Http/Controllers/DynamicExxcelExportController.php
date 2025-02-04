<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DynamicExxcelExportController extends Controller
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
}
