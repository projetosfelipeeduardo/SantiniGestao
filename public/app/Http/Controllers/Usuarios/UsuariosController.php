<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Services\UsuariosService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bancos;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{

    private $service;

    public function __construct(UsuariosService $Service)
    {
        $this->service = $Service;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'cpf' => [
                'max:18',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail('O campo CNPJ é obrigatório.');
                    }
                },
            ],
            'empresa' => ['required'],
        ]);
    }

    public function validar(Request $request)
    {


        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()->all()
            ], 400);
        } else {
            return response()->json(['success' => true], 200);
        }
    }

    public function validarUpdate(Request $request)
    {

        $validator =  Validator::make($request->all(), [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()->all()
            ], 400);
        } else {
            return response()->json(['success' => true], 200);
        }
    }

    public function index(Request $request)
    {


        if ($request['nome'] and !$request['cpf']) {
            $usuarios = User::orderBy('name')->where('name', 'like', '%' . $request['nome'] . '%')->paginate(15);
        } else if (!$request['nome'] and $request['cpf']) {
            $usuarios = User::orderBy('name')->where('cpf', 'like', '%' . $request['cpf'] . '%')->paginate(15);
        } else if ($request['nome'] and $request['cpf']) {
            $usuarios = User::orderBy('name')->where('cpf', 'like', '%' . $request['cpf'] . '%')->orWhere('name', 'like', '%' . $request['nome'] . '%')->paginate(15);
        } else {
            $usuarios = User::orderBy('name')->paginate(15);
        }


        $bancos = Bancos::orderBy('banco')->get();


        return view('Usuarios.index', [
            'usuarios' => $usuarios,
            'bancos' => $bancos
        ]);
    }

    // public function store(Request $request)
    // {

    //     $user = new User(); // Criar uma nova instância do modelo User

    //     // Preencher o modelo com os dados do formulário
    //     $user->name = $request->input('name');
    //     $user->email = $request->input('email');
    //     $user->password = Hash::make($request->input('password'), ['rounds' => 10]);
    //     $user->cpf = $request->input('cpf');
    //     $user->empresa = $request->input('empresa');
    //     $user->nivel_acesso = $request->input('nivel_acesso');
    //     $user->status = 1;

    //     $user->save(); // Salvar o modelo no banco de dados

    //     if ($user) {
    //         flash('Usuário Cadastrado com Sucesso')->success();

    //         return redirect()->back();
    //     } else {
    //         flash('Erro ao Cadastrar usuário')->warning();
    //         return redirect()->back();
    //     }
    // }

    public function store(Request $request)
    {
        $email = $request->input('email');
        $cpf = $request->input('cpf');
        $name = $request->input('name');
        // Verificar se o e-mail já está cadastrado
        $existingUser = User::where('email', $email)->first();
        $existingCNPJ = User::where('cpf', $cpf)->first();
        $existingName = User::where('name', $name)->first();

        if ($existingName) {
            flash('O Nome já está cadastrado.')->warning();
            return redirect()->route('user.edit', ['id' => $existingName->id]);
        }
        if ($existingUser) {
            flash('O e-mail já está cadastrado.')->warning();
            return redirect()->route('user.edit', ['id' => $existingUser->id]);
        }
        if ($existingCNPJ) {
            flash('O CNPJ já está cadastrado.')->warning();
            return redirect()->route('user.edit', ['id' => $existingCNPJ->id]);
        }


        // O e-mail não está duplicado, então podemos continuar com o cadastro
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $email;
        $user->password = Hash::make($request->input('password'), ['rounds' => 10]);
        $user->cpf = $request->input('cpf');
        $user->empresa = $request->input('empresa');
        $user->nivel_acesso = $request->input('nivel_acesso');
        $user->status = 1;

        $user->save();

        if ($user) {
            flash('Usuário Cadastrado com Sucesso')->success();
            return redirect()->back();
        } else {
            flash('Erro ao Cadastrar usuário')->warning();
            return redirect()->back();
        }
    }


    public function edit($id)
    {

        if (Auth::user()->nivel_acesso > 1 and Auth::user()->id != $id) {
            abort(404);
        }

        $user = User::findOrFail($id);
        $bancos = Bancos::orderBy('banco')->get();

        return view('Usuarios.edit', [
            'user' => $user,
            'bancos' => $bancos
        ]);
    }

    public function uploadImage(Request $request)
    {

        $image = $request->image;

        $id = Auth::user()->id;

        list($type, $image) = explode(';', $image);

        list(, $image)      = explode(',', $image);

        $image = base64_decode($image);

        $image_name = time() . '.png';

        $path = public_path('images/profiles/' . $image_name);

        User::where('id', $id)->update(['profile_pic' => $image_name]);

        file_put_contents($path, $image);

        return response()->json(['status' => true]);
    }

    public function update($id, Request $request)
    {
        $dados = $request->all();
        $dados['id'] = $id;

        // Verifica se o CPF foi fornecido no request
        if (isset($dados['cpf'])) {
            $cpf = $dados['cpf'];

            // Consulta no banco de dados para verificar se o CPF já está cadastrado para outro usuário
            $cpfConsulta = User::where('cpf', $cpf)->where('id', '!=', $id)->exists();

            if ($cpfConsulta) {
                // CPF já cadastrado para outro usuário, retorna com mensagem de alerta
                flash('CNPJ já está cadastrado para outro usuário.')->warning();
                return redirect()->back();
            }
            if (strlen($cpf) < 11) {
                flash('Favor digitar o CNPJ correto!')->error();
                return redirect()->back();
            }
        }

        // Continua com a atualização do usuário
        $user = $this->service->update($dados);

        if ($user) {
            flash('Usuário Editado com Sucesso')->success();
        } else {
            flash('Erro ao Editar usuário')->warning();
        }

        return redirect()->back();
    }



    public function updateStatus(Request $request)
    { {
            $userId = $request->input('id');

            // Encontrar o usuário pelo ID
            $user = User::find($userId);

            if ($user) {
                // Atualizar o status para 0 (ou qualquer outro valor que represente o status de inativo)
                $user->status = 1;
                $user->save();

                return response()->json(['message' => 'Status do usuário atualizado com sucesso']);
            } else {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }
            //  #FP - Criado funcao para exclusão do Usuario
        }
    }

    //  #FP - Criado funcao para exclusão do Usuario
    public function removeUser(Request $request)
    {
        $userId = $request->input('id');

        // Encontrar o usuário pelo ID
        $user = User::find($userId);

        if ($user) {
            // Atualizar o status para 0 (ou qualquer outro valor que represente o status de inativo)
            $user->status = 0;
            $user->save();

            return response()->json(['message' => 'Status do usuário atualizado com sucesso']);
        } else {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        //  #FP - Criado funcao para exclusão do Usuario
    }
}
