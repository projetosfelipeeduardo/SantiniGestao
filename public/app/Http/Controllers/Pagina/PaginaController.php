<?php

namespace App\Http\Controllers\Pagina;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roleta;

class PaginaController extends Controller
{
    public function index($id){

        $roleta = Roleta::where('user_id', $id)->get();

        return view('Pagina.Index', [
            'roleta' => $roleta
        ]);

    }

    public function customizarPagina(){


        return view('Pagina.Customizar',[

        ]);
    }
}
