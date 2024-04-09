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
        $user->cpf = $dados['cpf'];
        $user->nivel_acesso = $dados['nivel_acesso'];

        $user->save();

        return $user;

    }

    public function update($dados){


        $user = User::findOrFail($dados['id']);;

        if($dados['password']){
            $user->password = Hash::make($dados['password']);
        }

        if($dados['name']){
            $user->name = $dados['name'];
        }

        if($dados['email']){
            $user->email = $dados['email'];
        }

        if($dados['cpf']){
            $user->cpf = $dados['cpf'];
        }

        $update = $user->save();

        return $update;

    }


}//
