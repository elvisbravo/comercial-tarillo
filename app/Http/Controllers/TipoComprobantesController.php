<?php

namespace App\Http\Controllers;

use App\Tipo_comprobantes;
use Illuminate\Http\Request;

class TipoComprobantesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');


    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tipo_comprobantes  $tipo_comprobantes
     * @return \Illuminate\Http\Response
     */
    public function show(Tipo_comprobantes $tipo_comprobantes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tipo_comprobantes  $tipo_comprobantes
     * @return \Illuminate\Http\Response
     */
    public function edit(Tipo_comprobantes $tipo_comprobantes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tipo_comprobantes  $tipo_comprobantes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tipo_comprobantes $tipo_comprobantes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tipo_comprobantes  $tipo_comprobantes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tipo_comprobantes $tipo_comprobantes)
    {
        //
    }
}
