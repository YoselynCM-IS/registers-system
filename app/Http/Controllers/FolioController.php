<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FoliosImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Folio;

class FolioController extends Controller
{
    // BUSCAR FOLIO
    public function get_folio(Request $request){
        // return response()->json($request);
        if($request->auto !== ''){
            $folio = Folio::where('fecha',$request->date)
                    ->where('abono','like','%'.$request->total.'%')
                    ->where('concepto','like','%'. Str::of($request->auto)->upper().'%')
                    ->where('concepto','like','%'.$request->folio.'%')->first();
        } else {
            $folio = Folio::where('fecha',$request->date)
                    ->where('abono','like','%'.$request->total.'%')
                    ->where('concepto','like','%'.$request->folio.'%')->first();
        }

        if($folio === null) return response()->json($data = 'NO EXISTE');
        else return response()->json($folio);
    }

    public function search_folios(Request $request){
        if($request->bank !== 'BANCOMER' && $request->bank !== 'OTRO'){
            $folios = Folio::where('fecha',$request->fecha)
            ->where('abono','like','%'.$request->abono.'%')
            ->where('concepto','like','%'.$request->bank.'%')
            ->where('occupied', 0)
            ->get();
        } 
        if($request->bank === 'BANCOMER'){
            $folios = Folio::where('fecha',$request->fecha)
            ->where('abono','like','%'.$request->abono.'%')
            ->where(function($query){
                $query->where('concepto', 'like', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                ->orWhere('concepto', 'like', '%DEPOSITO EN EFECTIVO/%')
                ->orWhere('concepto', 'like', '%DEPOSITO POR CORRECCION/%')
                ->orWhere('concepto', 'like', '%PAGO CUENTA DE TERCERO/%');
            })
            ->where('occupied', 0)
            ->get();
        }
        if($request->bank === 'OTRO'){
            $folios = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
            ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
            ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
            ->where('concepto', 'NOT LIKE', '%PAGO CUENTA DE TERCERO/%')
            ->where('concepto', 'NOT LIKE', '%BANAMEX%')->where('concepto', 'NOT LIKE', '%AZTECA%')
            ->where('concepto', 'NOT LIKE', '%BANCOPPEL%')->where('concepto', 'NOT LIKE', '%BAJIO%')
            ->where('concepto', 'NOT LIKE', '%BANORTE%')->where('concepto', 'NOT LIKE', '%HSBC%')
            ->where('concepto', 'NOT LIKE', '%SANTANDER%')->where('concepto', 'NOT LIKE', '%SCOTIABANK%')
            ->where('fecha',$request->fecha)
            ->where('abono','like','%'.$request->abono.'%')
            ->where('occupied', 0)
            ->get();
        }
        return response()->json($folios);
    }

    public function store(Request $request){
        Excel::import(new FoliosImport, request()->file('file'));
        $folios = Folio::orderBy('fecha', 'desc')->get();
        return response()->json($folios);
    }

    public function show(Request $request){
        $folio = Folio::whereId($request->folio_id)->first();
        return response()->json($folio);
    }

    public function by_date(Request $request){
        $folios = Folio::where('fecha', $request->fecha)->orderBy('fecha', 'desc')->get();
        return response()->json($folios);
    }

    public function by_date_abono(Request $request){
        $folios = Folio::where('fecha', $request->fecha)->where('abono', $request->abono)
                        ->orderBy('fecha', 'desc')->get();
        return response()->json($folios);
    }
}
