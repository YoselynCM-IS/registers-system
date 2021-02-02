<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class EmailsAllExport implements FromCollection
{
    protected $school;

    public function __construct($school)
    {
        $this->school = $school;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $s = $this->school;
        $students = Student::select('schools.name as school', 'students.name', 'email', 'book')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->where(function($query) use($s) {
                        $query->where('schools.name', $s)
                                ->where('check', 'accepted')
                                ->where('book', 'like', '%DIGITAL%')
                                ->where('codes', false);
                    })
                    ->orWhere(function($query) use($s) {
                        $query->where('schools.name', $s)
                                ->where('check', 'accepted')
                                ->where('book', 'NOT LIKE', '%DIGITAL%')
                                ->where('delivery', false);
                    })
                    ->orderBy('book', 'asc')->get();
        return $students;
    }
}
