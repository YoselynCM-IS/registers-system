<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Registro;
use App\Student;
use App\School;
use App\Book;

class SchoolController extends Controller
{
    public function show_schools(Request $request){
        $schools = School::where('name','like', '%'.$request->escuela.'%')->get();
        return response()->json($schools);
    }

    public function show(Request $request){
        if(auth()->user()->role == 'capturist'){
            $students = Student::where('school_id', $request->school_id)
                        ->where('check', 'accepted')
                        ->where('delivery', false)
                        ->where('book','NOT LIKE','%DIGITAL%')
                        ->with('school')->orderBy('name', 'asc')->get();
        } else {
            $students = Student::where('school_id', $request->school_id)
                        // ->where('check', 'accepted')
                        ->with('school')->orderBy('created_at', 'desc')->get();
        }
        return response()->json($students);
    }

    public function schools_to_email(Request $request){
        $digitales = Student::where('school_id', $request->school_id)
                    ->where('check', 'accepted')->with('school')
                    ->where('book', 'like', '%DIGITAL%')
                    ->orderBy('book', 'asc')->get();
        $fisicos = Student::where('school_id', $request->school_id)
                    ->where('check', 'accepted')->with('school')
                    ->where('book', 'NOT LIKE', '%DIGITAL%')
                    ->orderBy('book', 'asc')->get();
        $data = [
            'digitales' => $digitales,
            'fisicos'   => $fisicos
        ];
        return response()->json($data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', 'unique:schools']
        ]);
        \DB::beginTransaction();
        try {
            $name = Str::of($request->name)->upper();
            $s = School::create(['name' => Str::of($name)->upper()]); 
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        $school = School::whereId($s->id)->first();
        return response()->json($school);
    }

    public function update(Request $request){
        \DB::beginTransaction();
        try {
            School::whereId($request->id)->update(['name' => Str::of($request->name)->upper()]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        $school = School::whereId($request->id)->first();
        return response()->json($school);
    }

    public function get_schools(Request $request){
        $book = Book::whereId($request->book_id)->with('schools')->first();
        
        if($book->schools->count() === 0){
            $schools = School::orderBy('name', 'asc')->get();
        } else {
            $ids = array();
            foreach($book->schools as $school){
                array_push($ids, $school->id);
            }
            $schools = School::whereNotIn('id', $ids)
                        ->orderBy('name', 'asc')->get();
        }
        
        return response()->json($schools);
    }

    public function get_books(Request $request){
        $school = School::whereId($request->school_id)->with('books')->first();
        return response()->json($school->books);
    }
}
