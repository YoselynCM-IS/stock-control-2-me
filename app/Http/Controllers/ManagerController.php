<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enteditoriale;
use App\Promotion;
use App\Entrada;
use App\Regalo;
use App\Order;
use App\Note;

class ManagerController extends Controller
{
    // INICIO CORTES
    public function lista_cortes(){
        return view('manager.cortes.lista');
    }

    // PAGOS DE CLIENTES
    public function cortes_pagos(){
        return view('manager.cortes.pagos');
    }

    // MOVIMEINTOS DE LAS CUENTAS DE LOS CLIENTES
    public function movimientos_clientes(){
        return view('manager.movimientos.movimientos-clientes');
    }

    // MOVIMIENTOS DE LOS LIBROS
    public function movimientos_libros(){
        $editoriales = $this->get_editoriales();
        return view('manager.movimientos.movimientos-libros', compact('editoriales'));
    }

    // LISTA DE REMISIONES
    public function lista_remisiones(){
        $responsables = $this->get_responsables();
        return view('manager.remisiones.lista', compact('responsables'));
    }

    // PAGO POR REMISION
    public function pago_devolucion(){
        $responsables = $this->get_responsables();
        return view('manager.remisiones.pago-devolucion', compact('responsables'));
    }

    // ADEUDOS
    public function fecha_adeudo(){
        return view('manager.remisiones.fecha-adeudo');
    }

    // NOTAS
    public function notas(){
        $notes = Note::orderBy('folio','desc')->get();
        $responsables = $this->get_responsables();
        return view('manager.otros.notas', compact('notes', 'responsables'));
    }

    public function pedidos(){
        $pedidos = Order::orderBy('created_at', 'desc')->get();
        return view('manager.otros.pedidos', compact('pedidos'));
    }

    public function promociones(){
        $promotions = Promotion::with('departures')->orderBy('folio','desc')->get();
        $responsables = $this->get_responsables();
        return view('manager.otros.promociones', compact('promotions', 'responsables'));
    }

    public function donaciones(){
        $regalos = Regalo::orderBy('id','desc')->get();
        $responsables = $this->get_responsables();
        return view('manager.otros.donaciones', compact('regalos', 'responsables'));
    }

    public function lista_entradas(){
        $entradas = $this->get_entradas();
        $editoriales = $this->get_editoriales();
        return view('manager.entradas.lista', compact('entradas', 'editoriales'));
    }

    public function lista_crear_entradas(){
        $entradas = $this->get_entradas();
        $editoriales = $this->get_editoriales();
        return view('manager.entradas.lista-crear', compact('entradas', 'editoriales'));
    }

    public function entradas_pagos(){
        $editoriales = Enteditoriale::orderBy('editorial', 'asc')
                        ->withCount('entdepositos')->get();
        return view('manager.entradas.pagos', compact('editoriales'));
    }

    public function libros(){
        $editoriales = $this->get_editoriales();
        return view('manager.libros', compact('editoriales'));
    }

    public function clientes(){
        return view('manager.clientes');
    }

    public function get_responsables(){
        return \DB::table('responsables')->orderBy('responsable', 'asc')->get();
    }

    public function get_entradas(){
        return Entrada::with('registros')
            ->whereNotIn('editorial', ['MAJESTIC EDUCATION'])
            ->orderBy('id','desc')->get();
    }

    public function get_editoriales(){
        return \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
    }

}
