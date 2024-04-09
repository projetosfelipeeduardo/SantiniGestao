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

class UsuariosController extends Controller
{

    private $Service;
    /**
     * @param App\Http\Services\UsuariosService $UsuariosService;
     */
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
            ]);
    }

    public function validar(Request $request){


        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()->all()
            ], 400);
        }else{
            return response()->json(['success' => true], 200);
        }
    }

    public function validarUpdate(Request $request){

      $validator =  Validator::make($request->all(), [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()->all()
                ], 400);
            }else{
                return response()->json(['success' => true], 200);
            }
    }

    public function index(Request $request){


        if($request['nome'] and !$request['cpf']){
            $usuarios = User::orderBy('name')->where('name', 'like', '%'.$request['nome'].'%')->paginate(15) ;
        }else if(!$request['nome'] and $request['cpf']){
            $usuarios = User::orderBy('name')->where('cpf', 'like', '%'.$request['cpf'].'%')->paginate(15) ;
        }else if ($request['nome'] and $request['cpf']) {
            $usuarios = User::orderBy('name')->where('cpf', 'like', '%'.$request['cpf'].'%')->orWhere('name', 'like', '%'.$request['nome'].'%')->paginate(15) ;
        }else{
            $usuarios = User::orderBy('name')->paginate(15);
        }


        $bancos = Bancos::orderBy('banco')->get();


        return view('Usuarios.index', [
            'usuarios' => $usuarios,
            'bancos' => $bancos
        ]);
    }

    public function store(Request $request){

        $dados= $request->all();


        if(isset($dados['catalogo'])){
            $dados['catalogo'] = 1;
        }else{
            $dados['catalogo'] = 0;
        }

        if(isset($dados['broadside'])){
            $dados['broadside'] = 1;
        }else{
            $dados['broadside'] = 0;
        }

        if(isset($dados['imagens'])){
            $dados['imagens'] = 1;
        }else{
            $dados['imagens'] = 0;
        }

        if(isset($dados['solicitacao'])){
            $dados['solicitacao'] = 1;
        }else{
            $dados['solicitacao'] = 0;
        }

        $user = $this->service->store($dados);

        if($user){
            flash('Usuário Cadastrado com Sucesso')->success();
            return redirect()->back();
        }else{
                flash('Erro ao Cadastrar usuário')->warning();
            return redirect()->back();
        }

    }

    public function edit($id){

        if(Auth::user()->nivel_acesso > 1 and Auth::user()->id != $id){
            abort(404);
        }

        $user = User::findOrFail($id);
        $bancos = Bancos::orderBy('banco')->get();

        return view('Usuarios.edit',[
            'user' => $user,
            'bancos' => $bancos
        ]);
    }

      public function uploadImage(Request $request){

        $image = $request->image;

        $id = Auth::user()->id;

        list($type, $image) = explode(';', $image);

        list(, $image)      = explode(',', $image);

        $image = base64_decode($image);

        $image_name= time().'.png';

        $path = public_path('images/profiles/'.$image_name);

        User::where('id', $id)->update(['profile_pic' => $image_name]);

        file_put_contents($path, $image);

        return response()->json(['status'=>true]);

        }

        public function update($id, Request $request){

            $dados = $request->all();
            $dados['id'] = $id;

            $user = $this->service->update($dados);

            if($user){
                flash('Usuário Editado com Sucesso')->success();
                return redirect()->back();
            }else{
                    flash('Erro ao Editar usuário')->warning();
                return redirect()->back();
            }

        }

        public function updateStatus(Request $request){
            $data = $request->all();

            $ret = User::where('id', $data['id'])->update(['status' => $data['status']]);
            return response()->json(['success' => true], 200);
        }


}
