<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [
        'pagina',
        'arquivo'
    ];

    protected $table = 'catalogo';

}
