<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pasta extends Authenticatable
{
    protected $fillable = [
        'nome',
        'pasta_pai',
        'subpasta'
    ];

    protected $table = 'pasta';

    protected $guarded = ['id'];

    public function arquivo()
    {
      return $this->hasMany(Arquivo::class, 'pasta', 'id');
    }

    public function pasta()
    {
      return $this->hasOne(Pasta::class,'id','pasta_pai');
    }





}
