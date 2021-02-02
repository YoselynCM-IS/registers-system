<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Registro;

class Folio extends Model
{
    protected $fillable = [ 'fecha', 'concepto', 'abono', 'saldo', 'occupied' ];

    public function registro(){
        return $this->hasOne(Registro::class);
    }
}
