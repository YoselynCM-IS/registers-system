<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Folio;
use App\File;

class AdministratorController extends Controller
{
    // VISTA DE ARCHIVOS
    public function files(){
        $files = File::orderBy('created_at', 'desc')->get();
        return view('administrator.files', compact('files'));
    }

    public function folios(){
        $folios = Folio::orderBy('created_at', 'desc')->get();
        return view('administrator.folios', compact('folios'));
    }

    public function registers(){
        $students = Student::with('school')->orderBy('created_at', 'desc')->get();
        return view('administrator.registers', compact('students'));
    }
}
