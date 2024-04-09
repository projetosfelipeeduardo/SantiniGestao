<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itens extends Model
{

    protected $fillable = [
        'cupom',
        'descricao',
        'valor'
    ];

    protected $table = 'itens';

    protected $guarded = ['id'];


    public function cupom()
    {
      return $this->hasOne(Cupom::class, 'id', 'cupom');
    }
}


