<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('ventas_moviles.index');
    }

    public function asignar()
    {
        return view('ventas_moviles.asignar');
    }
}
