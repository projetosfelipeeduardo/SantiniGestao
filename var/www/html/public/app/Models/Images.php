<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Images extends Authenticatable
{
    protected $fillable = [
        'nome',
        'pasta',
        'arquivo',
        'descricao',
        'status',
        'user_id'
    ];

    protected $table = 'imagens';

    protected $guarded = ['id'];

    public function pasta()
    {
      return $this->hasOne(Images::class, 'id', 'pasta');
    }

    public function usuario()
    {
      return $this->hasOne(User::class, 'id', 'usuario_id');
    }

}
