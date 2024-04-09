<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $fillable = [
        'titulo',
        'conteudo',
        'data_inicio',
        'data_fim',
        // Adicione outros campos conforme necessário para o relatório
    ];

    // Outros métodos do modelo, se necessário
}
