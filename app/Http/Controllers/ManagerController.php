<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Folio;
use App\File;

class ManagerController extends Controller
{
    // MOSTRAR Y SUBIR FOLIOS
    public function folios(){
        $folios = Folio::orderBy('created_at', 'desc')->get();
        return view('manager.folios', compact('folios'));
    }

    public function files(){
        $files = File::orderBy('created_at', 'desc')->get();
        return view('manager.files', compact('files'));
    }

    public function registers(){
        $students = Student::with('school')->withCount('registros')->orderBy('created_at', 'desc')->get();
        return view('manager.registers', compact('students'));
    }
}
