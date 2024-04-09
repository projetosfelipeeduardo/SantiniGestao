<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdoc extends Model
{
    protected $fillable = [
      'cupom',
      'arquivo',
      'nome'
    ];

    protected $table = 'cupom_doc';

    protected $guarded = ['id'];

    public function cupom()
    {
      return $this->hasOne(Cupom::class, 'id', 'cupom');
    }
}
