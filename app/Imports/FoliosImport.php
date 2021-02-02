<?php

namespace App\Imports;

use App\Folio;
use Maatwebsite\Excel\Concerns\ToModel;

class FoliosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $search_folio = Folio::where([
            'fecha' => $row[0], 'concepto' => $row[1], 
            'abono' => $row[2], 'saldo' => $row[3]
        ])->first();

        $folio = $search_folio;
        if($search_folio === null){
            $folio = Folio::create([
                'fecha' => $row[0], 'concepto' => $row[1], 
                'abono' => $row[2], 'saldo' => $row[3]
            ]);
        }

        return $folio;
    }
}
