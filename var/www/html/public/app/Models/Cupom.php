<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    protected $fillable = [
        'cidade',
        'km_inicial',
        'km_final',
        'local',
        'observacao',
        'valor_total',
        'inicio',
        'fim',
        'data',
        'usuario_id',
        'status',
        'pago'
    ];

    protected $table = 'cupom';

    protected $guarded = ['id'];

    protected $dates = ['inicio', 'fim'];

    public function usuario()
    {
      return $this->hasOne(User::class, 'id', 'usuario_id');
    }
}
