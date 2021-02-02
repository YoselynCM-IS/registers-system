<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class RegistrosExport implements FromCollection
{
    protected $status;
    
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::where('check', $this->status)->orderBy('created_at', 'desc')->get();
    }
}
