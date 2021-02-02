<?php

namespace App\Imports;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodes;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Student;

class CodesImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // $name, $code, $book, $editotial
            // Mail::to($row[4])->send(new SendCodes($row[3], $row[5], $row[6], $row[1], $row[2]));
            // Student::where(['name' => $row[3], 'email' => $row[4], 'check' => true])->update(['codes' => true]);
        }
    }
}
