<?php
namespace App\Http\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsuariosService{

    public function store($dados){


        $user = new User();
        $user->name = $dados['name'];
        $user->password = Hash::make($dados['password']);
        $user->email = $dados['email'];
        $user->profile_pic = 'oetker1.png';
        $user->status = 1;

        if(isset($dados['banco'])){
            $user->banco = $dados['banco'];
        }

        if(isset($dados['agencia'])){
            $user->agencia = $dados['agencia'];
        }

        if(isset($dados['conta'])){
            $user->conta = $dados['conta'];
        }


        // $user->cpf = $dados['cpf'];
        $user->nivel_acesso = $dados['nivel_acesso'];

        $user->save();

        return $user;

    }

    public function update($dados){


        $user = User::findOrFail($dados['id']);

        if($dados['password']){
            $user->password = Hash::make($dados['password']);
        }

        if($dados['name']){
            $user->name = $dados['name'];
        }

        if($dados['email']){
            $user->email = $dados['email'];
        }

        if($dados['banco']){
            $user->banco = $dados['banco'];
        }

        if($dados['agencia']){
            $user->agencia = $dados['agencia'];
        }

        if($dados['conta']){
            $user->conta = $dados['conta'];
        }

    

       



        $update = $user->save();

        return $update;

    }


}//
