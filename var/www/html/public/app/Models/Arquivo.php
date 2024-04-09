<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Arquivo extends Authenticatable
{
    protected $fillable = [
        'nome',
        'pasta',
        'arquivo',
        'extensao',
        'descricao',
    ];

    protected $table = 'arquivo';

    protected $guarded = ['id'];

    public function pasta()
    {
      return $this->hasOne(Pasta::class, 'id', 'pasta');
    }

}
