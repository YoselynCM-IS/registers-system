<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;

class CapturistController extends Controller
{
    public function registers(){
        // $students = Student::with('school')
        //         ->where('check', 'accepted')->orderBy('created_at', 'desc')->get();
        return view('capturist.registers');
    }
}
