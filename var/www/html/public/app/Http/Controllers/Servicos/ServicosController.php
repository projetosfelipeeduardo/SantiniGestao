<?php

namespace App\Http\Controllers\Servicos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use File;
use DB;
use App\Models\Servicos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ServicosController extends Controller
{
    public function index(){

        $servicos = Servicos::where('status' , 1)->orderBy('nome', 'ASC')->paginate(20);

        return view('Servicos.Index', array(
            'servicos' => $servicos
        ));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nome' => ['required', 'min:3'],
            ]);
    }

    public function store(Request $request){

        $validator = $this->validator($request->all());

        if ($validator->fails()) {

            flash('Preencha um nome válido')->warning();
            return redirect()->back();

        }else{

            $data= $request->all();
            $data['status'] = 1;
            $servicos = new Servicos($data);

            if($servicos->save()){
                flash('Cadastrado com Sucesso')->success();
                return redirect()->back();
            }else{
                    flash('Erro ao Cadastrar, tente novamente')->warning();
                return redirect()->back();
            }

        }   

    }

    public function excluir($id){

        $servico = Servicos::find($id);

        $servico->status = 0;
        
        if ($servico->save()){
            flash('Excluido com Sucesso')->success();
            return redirect()->back();
        }else{
            flash('Falha, tente novamente')->danger();
            return redirect()->back();
        }
    }

}
