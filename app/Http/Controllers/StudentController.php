<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\PreRegister;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmailsExport;
use App\Exports\EmailsAllExport;
use App\Imports\CodesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client;
use App\Mail\SendCodes;
use App\Comprobante;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use App\School;
use App\Folio;

class StudentController extends Controller
{

    public function __construct()
    {
        // Necesitamos obtener una instancia de la clase Client la cual tiene algunos métodos
        // que serán necesarios.
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();   
    }

    // VISTA AL REGISTRO DEL ALUMNO
    public function register(){
        $schools = \DB::table('schools')->orderBy('name', 'asc')->get();
        return view('student.register', compact('schools'));
    }

    // GUARDAR ESTUDIANTE
    public function store(Request $request){
        \DB::beginTransaction();
        try {
            $this->validate($request, [
                'name' => ['required', 'string', 'min:3'],
                'lastname' => ['required', 'string', 'min:5'],
                'email' => ['required', 'email', 'max:60'],
                'quantity' => ['required', 'numeric', 'min:1'],
                'telephone' => ['required', 'numeric', 'min:1000000000']
            ]);

            $name = Str::of($request->name.' '.$request->lastname)->upper();

            $verificar = Student::where('name', 'like', '%'.$name.'%')->first();
            if($verificar !== null && $verificar->check === 'process')  return response()->json(1);
            if($verificar !== null && $verificar->check === 'accepted') return response()->json(2);
            if($verificar === null || $verificar->check === 'rejected'){
                $student = Student::create([
                    'school_id' => $request->school,
                    'name' => $name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'book' => Str::of($request->book)->upper(), 
                    'quantity' => (int)$request->quantity, 
                    'price' => (float)$request->price,
                    'total' => (float)$request->a_depositar,
                    'validate' => 'CREADO',
                ]);
                
                $comprobantes = json_decode($request->comprobantes);
                
                foreach($comprobantes as $comprobante) {
                    if($comprobante->type === 'oxxo') $bank = 'BANAMEX EXTERNA';
                    if($comprobante->type === 'practicaja' || $comprobante->type === 'ventanilla') $bank = 'BANCOMER';
                    if($comprobante->type === 'transferencia') $bank = $comprobante->bank;
                    

                    if($comprobante->type === 'ventanilla') $invoice = (int)ltrim($comprobante->folio,0);
                    else $invoice = $comprobante->folio;

                    $registro = Registro::create([
                        'student_id' => $student->id, 
                        'type' => $comprobante->type, 
                        'invoice' => $invoice, 
                        'auto' => Str::of($comprobante->auto)->upper(), 
                        'total' => (float)$comprobante->total, 
                        'date' => $comprobante->date, 
                        'plaza' => Str::of($comprobante->plaza)->upper(),
                        'bank' => $bank
                    ]);
                }

                $files = $request->file('files');

                foreach($files as $file) {
                    $name_student = Str::slug($name, '-');
                    $extension = $file->getClientOriginalExtension();
                    $name_file = Carbon::now()->format('Y-m-d h:m:s')."_id".$student->id."_".$name_student.".".$extension;

                    Storage::disk('dropbox')->putFileAs(
                        '/comprobantes', $file, $name_file
                    );
        
                    $response = $this->dropbox->createSharedLinkWithSettings(
                        '/comprobantes/'.$name_file, 
                        ["requested_visibility" => "public"]
                    );

                    Comprobante::create([
                        'student_id' => $student->id,
                        'name' => $response['name'],
                        'extension' => $extension,
                        'size' => $response['size'],
                        'public_url' => $response['url']
                    ]);
                }
            }
            \DB::commit();

        }  catch (Exception $e) {
            \DB::rollBack();
            $success = $exception->getMessage();
        }

        return response()->json(3);
    }

    // CONSULTAR REGISTROS DE STUDENT
    public function show_registers(Request $request){
        $student = Student::whereId($request->student_id)->with('registros.folio', 'comprobantes', 'school')->first();
        return response()->json($student);
    }

    public function consult_data($date, $id){
        $student = Student::where(['id' => $id, 'created_at' => $date])
                    ->with('registro.comprobante', 'school')->first();
        return view('student.consult', compact('student'));
    }

    public function download_emails($school, $book, $type){
        return Excel::download(new EmailsExport($school, $book, $type), 'correos.xlsx');
    }

    public function download_all($school){
        return Excel::download(new EmailsAllExport($school), 'pendientes-todos.xlsx');
    }

    public function send_codes(Request $request){
        $array = Excel::toArray(new CodesImport, request()->file('file'));
        // $stds = array();
        foreach ($array[0] as $row) {
            try {
                $student = Student::where([
                    'name' => $row[3], 'email' => $row[4], 
                    'check' => true, 'codes' => false,
                    'book' => $row[1]])
                    ->first();
                                                            // $name, $code, $code2, $book, $editorial
                Mail::to($student->email)->send(new SendCodes($student->name, $row[5], $row[6], $student->book, $row[2]));
                $student->update(['codes' => true]);
                // array_push($stds, $student->id);
            }  catch (Exception $e) {
                $success = $exception->getMessage();
            }
        }

        $students = Student::where('check', 'accepted')->with('school')->get();
        return response()->json($students);
    }

    public function books_to_email(Request $request){
        $digitales = Student::where('book', $request->book)
                    ->where('book', 'like', '%DIGITAL%')
                    ->where('check', 'accepted')->with('school')
                    ->orderBy('name', 'asc')->get();
        $fisicos = Student::where('book', $request->book)
                    ->where('check', 'accepted')->with('school')
                    ->where('book', 'NOT LIKE', '%DIGITAL%')
                    ->orderBy('book', 'asc')->get();
        $data = [
            'digitales' => $digitales,
            'fisicos'   => $fisicos
        ];
        return response()->json($data);
    }

    public function delete(Request $request){
        $student = Student::whereId($request->student_id)->delete();
        $students = Student::with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }
    
    public function download_tutorial(){
        return Storage::disk('dropbox')->download('/PRE-REGISTRO-DE-BAUCHERS.pdf');
    }

    public function show_students(Request $request){
        $students = Student::select('name')
                ->where('name','like', '%'.$request->student.'%')
                ->groupBy('name')->get();
        return response()->json($students);
    }

    public function update_status(Request $request){
        $student = Student::whereId($request->student_id)->first();
        
        \DB::beginTransaction();
        try {
            foreach ($request->registros as $r){
                $registro = Registro::whereId($r['id'])->first();
                $folio = Folio::whereId($r['folio_id'])->first();
                
                $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                $folio->update(['occupied' => 1]);
            }

            $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

            if(Str::contains($student->book, 'DIGITAL')){
                $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente. Aproximadamente en 48 horas hábiles recibirás por correo tu código.';
            } else {
                $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';
            }

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        if($student->check !== 'rejected'){
            Mail::to($student->email)->send(new PreRegister($message, $student));
            $student->update(['validate' => 'ENVIADO']);
        }

        $students = Student::with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }

    public function update_delivery(Request $request){
        \DB::beginTransaction();
        try {
            $student = Student::whereId($request->id)->update([
                    'delivery' => true,
                    'date_delivery' => Carbon::now()->format('Y-m-d h:m:s'),
                    'user_delivery' => auth()->user()->name
                ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        if(auth()->user()->role == 'capturist'){
            $students = Student::where('school_id', $request->school_id)
                        ->where('check', 'accepted')
                        ->where('delivery', false)
                        ->where('book','NOT LIKE','%DIGITAL%')
                        ->with('school')->orderBy('name', 'asc')->get();
        } else {
            $students = Student::with('school')
                ->orderBy('created_at', 'desc')->where('check', 'accepted')->get();
        }
        return response()->json($students);
    }

    public function by_school(Request $request){
        $students = Student::where('school_id', $request->school_id)
                        ->where('name','like', '%'.$request->student.'%')
                        ->where('check', 'accepted')
                        ->where('delivery', false)
                        ->where('book','NOT LIKE','%DIGITAL%')
                        ->with('school')->orderBy('name', 'asc')->get();

        return response()->json($students);
    }

    public function mark_delivery(Request $request){
        $selected = $request->selected;
        $school = $request->school;

        foreach ($selected as $sel) {
            if(!$sel['delivery']) {
                $student = Student::whereId($sel['id'])->update([
                    'delivery' => true,
                    'date_delivery' => Carbon::now()->format('Y-m-d h:m:s'),
                    'user_delivery' => auth()->user()->name
                ]);
            }
        }
        $school = School::whereName($school)->first();
        $fisicos = Student::where('school_id', $school->id)
                    ->where('check', 'accepted')->with('school')
                    ->where('book', 'NOT LIKE', '%DIGITAL%')
                    ->orderBy('book', 'asc')->get();

        return response()->json($fisicos);
    }
}
