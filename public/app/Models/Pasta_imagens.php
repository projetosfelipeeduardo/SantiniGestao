<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pasta_imagens extends Authenticatable
{
    protected $fillable = [
        'nome',
        'status',
        'subpasta',
        'pasta_pai',
        'user_id'
    ];

    protected $table = 'pasta_imagens';

    protected $guarded = ['id'];

    public function imagem()
    {
      return $this->hasMany(Images::class, 'id', 'pasta');
    }

    public function pasta()
    {
      return $this->hasOne(Pasta_imagens::class,'id','pasta_pai');
    }

    public function usuario()
    {
      return $this->hasOne(User::class, 'id', 'user_id');
    }

}
