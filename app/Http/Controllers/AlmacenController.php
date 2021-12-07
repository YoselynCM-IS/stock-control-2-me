<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Promotion;
use App\Remisione;
use App\Entrada;
use App\Compra;
use App\Regalo;
use App\Libro;
use App\Note;
use App\Remcliente;
use App\Order;

class AlmacenController extends Controller
{
    public function remisiones(){;
        return view('almacen.remisiones');
    }

    public function pagos(){ 
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.pagos', compact('responsables'));
    }

    public function notas(){
        $notes = Note::orderBy('folio','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.notas', compact('notes', 'responsables'));
    }

    public function pedidos(){
        // $compras = Compra::orderBy('id','desc')->get();
        // $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        // return view('almacen.pedidos', compact('compras', 'responsables'));
        $pedidos = Order::orderBy('created_at', 'desc')->get();
        return view('almacen.pedidos', compact('pedidos'));
    }

    public function promociones(){
        $promotions = Promotion::with('departures')->orderBy('folio','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.promociones', compact('promotions', 'responsables'));
    }

    public function donaciones(){
        $regalos = Regalo::orderBy('id','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.donaciones', compact('regalos', 'responsables'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')
            ->whereNotIn('editorial', ['MAJESTIC EDUCATION'])
            ->orderBy('id','desc')->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('almacen.entradas', compact('entradas', 'editoriales'));
    }
    
    public function libros(){
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('almacen.libros', compact('editoriales'));
    }

    public function movimientos(){
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('almacen.movimientos', compact('editoriales'));
    }
}
