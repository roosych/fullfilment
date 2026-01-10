<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        //
    }

    public function receipt(StockBatch $stockBatch)
    {
        $stockBatch->load('entries.product');
        return view('admin.stock.batches.receipt', compact('stockBatch'));
    }
}
