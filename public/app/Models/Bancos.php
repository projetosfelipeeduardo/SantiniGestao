<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [
        'banco'
    ];

    protected $table = 'bancos';

}
