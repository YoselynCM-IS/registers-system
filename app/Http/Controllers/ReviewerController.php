<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Registro;
use App\Student;
use App\School;
use App\Folio;
use App\Book;

class ReviewerController extends Controller
{
    public function registers(){
        $students = Student::with('school')->orderBy('created_at', 'desc')->get();
        return view('reviewer.registers', compact('students'));
    }

    public function codes(){
        $digitales = Student::where('check', 'accepted')
                ->where('book', 'like', '%DIGITAL%')
                ->with('school')->orderBy('book', 'asc')->get();
        $fisicos = Student::where('check', 'accepted')
                ->where('book', 'NOT LIKE', '%DIGITAL%')
                ->with('school')->orderBy('book', 'asc')->get();
        
        return view('reviewer.codes', compact('digitales', 'fisicos'));
    }

    public function schools(){
        $schools = School::orderBy('name', 'asc')->get();
        return view('reviewer.schools', compact('schools'));
    }

    public function books(){
        $books = Book::orderBy('name', 'asc')->get();
        return view('reviewer.books', compact('books'));
    }

    public function folios(){
        $folios = Folio::orderBy('created_at', 'desc')->get();
        return view('reviewer.folios', compact('folios'));
    }

}
