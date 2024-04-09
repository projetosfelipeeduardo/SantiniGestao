<?php

namespace App\Http\Controllers\Solicitacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class SolicitacaoController extends Controller
{
    public function index(){

        if(Auth::user()->solicitacao != 1 && Auth::user()->nivel_acesso > 1){
            abort(404);
        }

        $login = Auth::user()->login_solicitacao;
        $senha = Auth::user()->senha_solicitacao;

        return view('Solicitacao.index', [
            'login' => $login,
            'senha' => $senha
        ]);

    }
}
