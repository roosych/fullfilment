<?php

namespace App\Http\Controllers\Receiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        return view('receiver.index');
    }
}
