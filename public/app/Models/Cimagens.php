<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cimagens extends Model
{
    protected $fillable = [
      'cupom',
      'img',
      'nome'
    ];

    protected $table = 'cupom_imagens';

    protected $guarded = ['id'];

    public function cupom()
    {
      return $this->hasOne(Cupom::class, 'id', 'cupom');
    }
}
