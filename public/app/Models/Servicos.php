<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicos extends Model
{
    protected $fillable = [
        'nome',
        'status',

    ];

    protected $table = 'servicos';

    protected $guarded = ['id'];
}
