<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Remcliente;
use App\Cctotale;

class RemclienteController extends Controller
{
    // MOSTRAR TODAS LAS REMCLIENTE
    public function index(){
        $remclientes = \DB::table('remclientes')
                        ->join('clientes', 'remclientes.cliente_id', '=', 'clientes.id')
                        ->select('clientes.id as cliente_id', 'clientes.name as name', 'total', 'total_devolucion', 'total_pagos', 'total_pagar')
                        ->where('total', '>', 0)
                        ->orderBy('clientes.name', 'asc')
                        ->paginate(20);
        return response()->json($remclientes);
    }

    // MOSTRAR PAGOS POR CLIENTE
    public function by_cliente(){
        $cliente_id = Input::get('cliente_id');
        $remcliente = \DB::table('remclientes')
                        ->join('clientes', 'remclientes.cliente_id', '=', 'clientes.id')
                        ->select('clientes.id as cliente_id', 'clientes.name as name', 'total', 'total_devolucion', 'total_pagos', 'total_pagar')
                        ->where('cliente_id', $cliente_id)
                        ->where('total', '>', 0)
                        ->paginate(1);
        return response()->json($remcliente);
    }

    // OBTENER TOTALES
    public function get_totales(){
        $totales = Remcliente::select(
            \DB::raw('SUM(total) as total'),
            \DB::raw('SUM(total_devolucion) as total_devolucion'),
            \DB::raw('SUM(total_pagos) as total_pagos'),
            \DB::raw('SUM(total_pagar) as total_pagar')
        )->get();
        return response()->json($totales);
    }

    // OBTENER COTEJO DE LA CUENTA GENERAL CON LA DEL CLIENTE
    public function get_gralcortes(){
        $remclientes = \DB::table('remclientes')
                        ->join('clientes', 'remclientes.cliente_id', '=', 'clientes.id')
                        ->select('clientes.id as cliente_id', 'clientes.name as name', 'total', 'total_devolucion', 'total_pagos', 'total_pagar')
                        ->where('total', '>', 0)
                        ->orderBy('clientes.name', 'asc')
                        ->get();
        
        $datos = [];
        $remclientes->map(function($remcliente) use(&$datos){
            $datos[] = $this->set_cctotales($remcliente, $remcliente->name);
        });
        return response()->json($datos);
    }

    // OBTENER CUENTA POR CLIENTE
    public function gc_bycliente(Request $request){
        $remcliente = Remcliente::where('cliente_id', $request->cliente_id)
                                    ->where('total', '>', 0)->first();
        $datos = $this->set_cctotales($remcliente, $remcliente->cliente->name);
        return response()->json($datos);
    }

    // ACOMODAR DATOS
    public function set_cctotales($remcliente, $cliente){
        $cctotales = Cctotale::where('cliente_id', $remcliente->cliente_id)
                            ->select(
                                \DB::raw('SUM(total) as total'),
                                \DB::raw('SUM(total_devolucion) as total_devolucion'),
                                \DB::raw('SUM(total_pagos) as total_pagos'),
                                \DB::raw('SUM(total_pagar) as total_pagar')
                            )->get();
        $check_total = (double)$remcliente->total - ((double)$remcliente->total_devolucion + (double)$remcliente->total_pagos);
        $ta = $remcliente->total;
        $td = $remcliente->total_devolucion;
        $tp = $remcliente->total_pagos;
        $tr = $remcliente->total_pagar;
        $ct = $cctotales[0];
        return [
            'cliente_id' => $remcliente->cliente_id, 
            'name' => $cliente, 
            'total' => $ta, 
            'total_devolucion' => $td, 
            'total_pagos' => $tp, 
            'total_pagar' => $tr,
            '_cellVariants'=> [
                'name' => $check_total !== $tr ? 'danger':'',
                'total' => $ta !== $ct['total'] ? 'danger':'', 
                'total_devolucion' => $td !== $ct['total_devolucion'] ? 'danger':'', 
                'total_pagos' => $tp !== $ct['total_pagos'] ? 'danger':'', 
                'total_pagar' => $tr !== $ct['total_pagar'] ? 'danger':''
            ]
        ];
    }
}
