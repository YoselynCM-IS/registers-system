<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Exports\RegistroExport;
use App\Exports\RegistrosExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\PreRegister;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use App\Folio;
use Excel;

class RegistroController extends Controller
{
    public function by_date(Request $request){
        $registros = Registro::select('student_id')->where('date', 'like', '%'.$request->fecha.'%')->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('book', 'asc')->get();
        return response()->json($students);
    }

    public function by_type(Request $request){
        $registros = Registro::select('student_id')->where('type', $request->type)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_folio(Request $request){
        $registros = Registro::select('student_id')->where('invoice', $request->folio)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_total(Request $request){
        $registros = Registro::select('student_id')->where('total', $request->total)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_book(Request $request){
        $students = Student::where('book', $request->book)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function download($temporal1){
        // if($temporal1 === "null") $temporal1 = "null";
        
        return Excel::download(new RegistroExport($temporal1), 'registros.xlsx');
    }

    public function download_status($status){
        return Excel::download(new RegistrosExport($status), 'registros.xlsx');
    }

    public function update_status(){
        $students = Student::where('check', 'process')->with('registros')->limit(50)->get();
        
        \DB::beginTransaction();
        try {
            $estudiantes = array();
            foreach($students as $student){
                
                foreach ($student->registros as $registro) {
                    $pago = new Carbon($registro->date);
                    $hoy = Carbon::now()->format('Y-m-d');
                    $difference = $pago->diff($hoy)->days;

                    // if($difference > 2){
                        if($registro->type === 'practicaja'){
                            $folio = Folio::where('concepto', 'like', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                ->where('fecha',$registro->date)
                                ->where('concepto','like','%FOLIO:'.$registro->invoice.'%')
                                ->where('abono','like','%'.$registro->total.'%')
                                ->where('occupied', 0)
                                ->first();
                        }
                        if($registro->type === 'ventanilla'){
                            $folio = Folio::where('fecha',$registro->date)
                                ->where('concepto','like','%'.$registro->invoice.'%')
                                ->where('abono','like','%'.$registro->total.'%')
                                ->where('occupied', 0)
                                ->where(function($query){
                                    $query->where('concepto', 'like', '%DEPOSITO EN EFECTIVO/%')
                                    ->orWhere('concepto', 'like', '%DEPOSITO POR CORRECCION/%');
                                })
                                ->first();
                        }
                        if($registro->type === 'transferencia'){
                            if($registro->bank === NULL) {
                                $folio = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                    ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
                                    ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
                                    ->where('fecha',$registro->date)
                                    ->where('concepto','like','%'.$registro->invoice.'%')
                                    ->where('abono','like','%'.$registro->total.'%')
                                    ->where('occupied', 0)
                                    ->first();
                            } else {
                                if($registro->bank === 'BANCOMER') {
                                    $folio = Folio::where('concepto', 'like', '%PAGO CUENTA DE TERCERO/%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$registro->invoice.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                                if($registro->bank === 'BANAMEX') $folio = $this->search_folio('BANAMEX', $registro);
                                if($registro->bank === 'BANCO AZTECA') $folio = $this->search_folio('AZTECA', $registro);
                                if($registro->bank === 'BANCOPPEL') {
                                    $corto = substr($registro->invoice,0,10);
                                    $folio = Folio::where('concepto', 'like', '%BANCOPPEL%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$corto.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                                if($registro->bank === 'BANBAJIO') $folio = $this->search_folio('BAJIO', $registro);
                                if($registro->bank === 'BANORTE') $folio = $this->search_folio('BANORTE', $registro);
                                if($registro->bank === 'HSBC') $folio = $this->search_folio('HSBC', $registro);
                                if($registro->bank === 'SANTANDER') $folio = $this->search_folio('SANTANDER', $registro);
                                if($registro->bank === 'SCOTIABANK') $folio = $this->search_folio('SCOTIABANK', $registro);
                                
                                if($registro->bank !== 'BANCOMER' && $registro->bank !== 'BANAMEX' && 
                                    $registro->bank !== 'BANCO AZTECA' && $registro->bank !== 'BANCOPPEL' &&
                                    $registro->bank !== 'BANBAJIO' && $registro->bank !== 'BANORTE' &&
                                    $registro->bank !== 'HSBC' && $registro->bank !== 'SCOTIABANK') {
                                    $folio = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                        ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
                                        ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
                                        ->where('concepto', 'NOT LIKE', '%PAGO CUENTA DE TERCERO/%')
                                        ->where('concepto', 'NOT LIKE', '%BANAMEX%')->where('concepto', 'NOT LIKE', '%AZTECA%')
                                        ->where('concepto', 'NOT LIKE', '%BANCOPPEL%')->where('concepto', 'NOT LIKE', '%BAJIO%')
                                        ->where('concepto', 'NOT LIKE', '%BANORTE%')->where('concepto', 'NOT LIKE', '%HSBC%')
                                        ->where('concepto', 'NOT LIKE', '%SANTANDER%')->where('concepto', 'NOT LIKE', '%SCOTIABANK%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$registro->invoice.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                            }
                        }
                        
                        if($folio !== null){
                            $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                        } else {
                            if($difference > 0){
                                $registro->update(['status' => 'rejected']);
                            }
                        }
                        
                    // }
                    
                }

                $count_registros = Registro::where('student_id', $student->id)
                            ->where('status', 'accepted')->count();
                
                if($count_registros === $student->registros->count()){
                    foreach ($student->registros as $registro) {
                        Folio::whereId($registro->folio_id)->update(['occupied' => 1]);
                    }
                    $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

                    if(Str::contains($student->book, 'DIGITAL')){
                        $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente. Aproximadamente en 48 horas hábiles recibirás por correo tu código.';
                    } else {
                        $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';
                    }
                    array_push($estudiantes, ['student' => $student, 'message' => $message]);
                    // Mail::to($student->email)->send(new PreRegister($message, $student));
                } else {
                    $count_process = Registro::where('student_id', $student->id)
                            ->where('status', 'process')->count();
                    if($count_process === 0){
                        $student->update(['check' => 'rejected', 'validate' => 'NO ENVIADO']);
                        $message = 'Los datos que ingresaste en tu pre-registro no son correctos. Te pedimos que los verifiques y vuelvas a intentarlo.';
                        array_push($estudiantes, ['student' => $student, 'message' => $message]);
                        // Mail::to($student->email)->send(new PreRegister($message, $student));
                    }
                }

            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        
        foreach ($estudiantes as $estudiante) {
            $s = Student::whereId($estudiante['student']['id'])->first();
            if($s->check !== 'process'){
                Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                $s->update(['validate' => 'ENVIADO']);
            }
        }
        
        $students = Student::with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }

    public function search_folio($banco, $registro){
        return Folio::where('concepto', 'like', '%'.$banco.'%')
                    ->where('fecha',$registro->date)
                    ->where('concepto','like','%'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)
                    ->first();
    }

    public function update_rejected(){
        $students = Student::where('check', 'rejected')->with('registros')->limit(50)->get();
        
        \DB::beginTransaction();
        try {
            $estudiantes = array();
            foreach($students as $student){
                
                foreach ($student->registros as $registro) {
                    $pago = new Carbon($registro->date);
                    $hoy = Carbon::now()->format('Y-m-d');
                    $difference = $pago->diff($hoy)->days;

                    // if($difference > 2){
                        if($registro->type === 'practicaja'){
                            $folio = Folio::where('concepto', 'like', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                ->where('fecha',$registro->date)
                                ->where('concepto','like','%FOLIO:'.$registro->invoice.'%')
                                ->where('abono','like','%'.$registro->total.'%')
                                ->where('occupied', 0)
                                ->first();
                        }
                        if($registro->type === 'ventanilla'){
                            $folio = Folio::where('fecha',$registro->date)
                                ->where('concepto','like','%'.$registro->invoice.'%')
                                ->where('abono','like','%'.$registro->total.'%')
                                ->where('occupied', 0)
                                ->where(function($query){
                                    $query->where('concepto', 'like', '%DEPOSITO EN EFECTIVO/%')
                                    ->orWhere('concepto', 'like', '%DEPOSITO POR CORRECCION/%');
                                })
                                ->first();
                        }
                        if($registro->type === 'transferencia'){
                            if($registro->bank === NULL) {
                                $folio = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                    ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
                                    ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
                                    ->where('fecha',$registro->date)
                                    ->where('concepto','like','%'.$registro->invoice.'%')
                                    ->where('abono','like','%'.$registro->total.'%')
                                    ->where('occupied', 0)
                                    ->first();
                            } else {
                                if($registro->bank === 'BANCOMER') {
                                    $folio = Folio::where('concepto', 'like', '%PAGO CUENTA DE TERCERO/%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$registro->invoice.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                                if($registro->bank === 'BANAMEX') $folio = $this->search_folio('BANAMEX', $registro);
                                if($registro->bank === 'BANCO AZTECA') $folio = $this->search_folio('AZTECA', $registro);
                                if($registro->bank === 'BANCOPPEL') {
                                    $corto = substr($registro->invoice,0,10);
                                    $folio = Folio::where('concepto', 'like', '%BANCOPPEL%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$corto.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                                if($registro->bank === 'BANBAJIO') $folio = $this->search_folio('BAJIO', $registro);
                                if($registro->bank === 'BANORTE') $folio = $this->search_folio('BANORTE', $registro);
                                if($registro->bank === 'HSBC') $folio = $this->search_folio('HSBC', $registro);
                                if($registro->bank === 'SANTANDER') $folio = $this->search_folio('SANTANDER', $registro);
                                if($registro->bank === 'SCOTIABANK') $folio = $this->search_folio('SCOTIABANK', $registro);
                                
                                if($registro->bank !== 'BANCOMER' && $registro->bank !== 'BANAMEX' && 
                                    $registro->bank !== 'BANCO AZTECA' && $registro->bank !== 'BANCOPPEL' &&
                                    $registro->bank !== 'BANBAJIO' && $registro->bank !== 'BANORTE' &&
                                    $registro->bank !== 'HSBC' && $registro->bank !== 'SCOTIABANK') {
                                    $folio = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/******7206%')
                                        ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
                                        ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
                                        ->where('concepto', 'NOT LIKE', '%PAGO CUENTA DE TERCERO/%')
                                        ->where('concepto', 'NOT LIKE', '%BANAMEX%')->where('concepto', 'NOT LIKE', '%AZTECA%')
                                        ->where('concepto', 'NOT LIKE', '%BANCOPPEL%')->where('concepto', 'NOT LIKE', '%BAJIO%')
                                        ->where('concepto', 'NOT LIKE', '%BANORTE%')->where('concepto', 'NOT LIKE', '%HSBC%')
                                        ->where('concepto', 'NOT LIKE', '%SANTANDER%')->where('concepto', 'NOT LIKE', '%SCOTIABANK%')
                                        ->where('fecha',$registro->date)
                                        ->where('concepto','like','%'.$registro->invoice.'%')
                                        ->where('abono','like','%'.$registro->total.'%')
                                        ->where('occupied', 0)
                                        ->first();
                                }
                            }
                        }
                        
                        if($folio !== null){
                            $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                        } else {
                            $registro->update(['status' => 'rejected']);
                        }
                        
                    // }
                    
                }

                $count_registros = Registro::where('student_id', $student->id)
                            ->where('status', 'accepted')->count();
                
                if($count_registros === $student->registros->count()){
                    foreach ($student->registros as $registro) {
                        Folio::whereId($registro->folio_id)->update(['occupied' => 1]);
                    }
                    $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

                    if(Str::contains($student->book, 'DIGITAL')){
                        $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente. Aproximadamente en 48 horas hábiles recibirás por correo tu código.';
                    } else {
                        $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';
                    }
                    array_push($estudiantes, ['student' => $student, 'message' => $message]);
                    // Mail::to($student->email)->send(new PreRegister($message, $student));
                } else {
                    $count_process = Registro::where('student_id', $student->id)
                            ->where('status', 'process')->count();
                    if($count_process === 0){
                        $student->update(['check' => 'rejected']);
                        // $message = 'Los datos que ingresaste en tu pre-registro no son correctos. Te pedimos que los verifiques y vuelvas a intentarlo.';
                        // array_push($estudiantes, ['student' => $student, 'message' => $message]);
                        // Mail::to($student->email)->send(new PreRegister($message, $student));
                    }
                }

            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        
        foreach ($estudiantes as $estudiante) {
            $s = Student::whereId($estudiante['student']['id'])->first();
            if($s->check == 'accepted'){
                Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                $s->update(['validate' => 'ENVIADO']);
            }
        }
        
        $students = Student::with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students); 
    }

    public function by_status(Request $request){
        $students = Student::where('check', $request->status)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_student(Request $request){
        $students = Student::where('name', $request->student)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function resend_mail(Request $request){
        $student = Student::find($request->id);

        if(Str::contains($student->book, 'DIGITAL')){
            $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente. Aproximadamente en 48 horas hábiles recibirás por correo tu código.';
        } else {
            $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';
        }

        Mail::to($student->email)->send(new PreRegister($message, $student));
        $student->update(['validate' => 'ENVIADO']);

        $students = Student::with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }
}
