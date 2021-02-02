<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class EmailsExport implements FromCollection
{
    protected $school;
    protected $book;
    protected $type;

    public function __construct($school, $book, $type)
    {
        $this->school = $school;
        $this->book = $book;
        $this->type = $type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->type == '1') {
            if($this->book === 'null'){
                $students = Student::select('schools.name as school', 'students.name', 'email', 'book')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('schools.name', $this->school)
                    ->where('check', 'accepted')
                    ->where('codes', false) 
                    ->orderBy('students.name', 'asc')->get();
            }
            if($this->school === 'null'){
                $students = Student::select('schools.name as school', 'students.name', 'email', 'book')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('book', $this->book)
                    ->where('check', 'accepted')
                    ->where('codes', false) 
                    ->orderBy('students.name', 'asc')->get(); 
            }
            if($this->book === 'null' && $this->school === 'null'){
                $students = Student::select('schools.name as school', 'students.name', 'email', 'book')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('check', 'accepted')
                    ->where('codes', false) 
                    ->orderBy('students.name', 'asc')->get();
            }
        }
        if($this->type == '2'){
            if($this->book === 'null'){
                $students = Student::select('schools.name as school', 'book', 'books.editorial', 'students.name', 'email')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('students.book', 'like', '%DIGITAL%')
                    ->where('check', 'accepted')
                    ->where('codes', false) 
                    ->where('schools.name', $this->school)
                    ->orderBy('book', 'asc')->limit(25)->get();
            }
            if($this->school === 'null'){
                $students = Student::select('schools.name as school', 'book', 'books.editorial', 'students.name', 'email')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('students.book', 'like', '%DIGITAL%')
                    ->where('check', 'accepted')
                    ->where('codes', false)
                    ->where('book', $this->book)
                    ->orderBy('book', 'asc')->limit(25)->get(); 
            }
            if($this->book === 'null' && $this->school === 'null'){
                $students = Student::select('schools.name as school', 'book', 'books.editorial', 'students.name', 'email')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->join('books', 'students.book', '=', 'books.name')
                    ->where('students.book', 'like', '%DIGITAL%')
                    ->where('check', 'accepted')
                    ->where('codes', false)
                    ->orderBy('book', 'asc')->limit(25)->get();
            }
        } 
        
        return $students;
    }
}
