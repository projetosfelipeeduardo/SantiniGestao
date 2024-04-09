<?php

namespace App\Http\Controllers\SegundaVia;
use App\Http\Services\BoletoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SegundaviaController extends Controller
{
    private $Service;
    /**
     * @param App\Http\Services\BoletoService $BoletoService;
     */
    public function __construct(BoletoService $Service)
    {
        $this->service = $Service;
    }

    public function index(){

        return view('SegundaVia.index');

    }

    public function requisicao(Request $request){

    //configuracoes
    $codEmp ="134383013";
    $chave="Chave da empresa";


    $data = $request->all();

    $fiscal=preg_replace('/\D/', '', $data['fiscal']);

    //gera o dc
    $dc = $this->service->geraCripto($codEmp,$fiscal,$chave);

    return view('SegundaVia.requisicao', [
        'dc' => $dc
    ]);



    }
}
