<?php

namespace App\Http\Controllers\Cupom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cupom;
use App\Models\Itens;
use App\Models\User;
use App\Models\Servicos;
use App\Models\Cimagens;
use App\Models\Cdoc;
use Auth;
use File;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CupomController extends Controller
{
    public function index(Request $request){

       
        $users = User::where([
            ['status', 1],
            ['nivel_acesso', 2]
        ])->get();

        $nivel = Auth::user()->nivel_acesso;

        if($request->acao){
            $rs = array();
        
            $query = DB::table('cupom as c')
            ->where('c.status', 1)
            ->orderByDesc('c.id');

        if($request->inicio_filtro) {
            $ini = Carbon::createFromFormat('d/m/Y', $request->inicio_filtro);
            $query->where('c.inicio', '>', $ini->format('Y-m-d').' 00:00' );
        }

        if($request->fim_filtro) {
            $fim = Carbon::createFromFormat('d/m/Y', $request->fim_filtro);
            $query->where('c.fim', '<', $fim->format('Y-m-d').' 23:59' );
        }

        if($request->user_filtro) {
            $query->where('c.usuario_id', $request->user_filtro );
        }

        if($nivel == 2){
            $query->where('c.usuario_id', Auth::user()->id );
        }

        $result = $query->get();

        foreach($result as $r){

            $rs[] = $r->id;
         
        }

        $cupons = Cupom::whereIn('id', $rs)->paginate(20)->appends(request()->query());

        }else{

            if($nivel == 2){
                $cupons = Cupom::where([['status', 1], ['usuario_id', Auth::user()->id]])->paginate(20);
            }else{
                $cupons = Cupom::where('status', 1)->paginate(20);
            }

        }

        return view('Cupom.Index', array(
            'cupons' => $cupons,
            'users' => $users
        ));

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'cidade' => ['required', 'min:3'],
            'inicio' => ['required'],
            'fim' => ['required'],
            'km_inicial' => ['required'],
            'km_final' => ['required'],
            ]);
    }

    public function store(Request $request){

        $validator = $this->validator($request->all());

        if ($validator->fails()) {

            flash('Preencha todos os campos obrigatórios')->warning();
            return redirect()->back();

        }else{

            $data= $request->all();

            $data['data'] = Carbon::now();

            $data['valor_total'] = '0';

            $data['status'] = 1;

            $data['pago'] = 0;

            $data['inicio'] = Carbon::createFromFormat('d/m/Y', $data['inicio']);

            $data['fim'] = Carbon::createFromFormat('d/m/Y', $data['fim']);

            $data['usuario_id'] = Auth::user()->id;

            $cupom = new Cupom($data);

            if($cupom->save()){
                flash('Cadastrado com Sucesso')->success();
                return redirect()->route('cupom.visualizar', $cupom->id);
            }else{
                    flash('Erro ao Cadastrar, tente novamente')->warning();
                return redirect()->back();
            }

        }   

    }

    public function visualizar($id){
     
        $cupom = Cupom::find($id);
        $servicos = Servicos::where('status',1)->orderBy('nome', 'asc')->get();
        $itens = Itens::where('cupom', '=', $cupom->id)->orderBy('id', 'ASC')->get();
        $fotos = Cimagens::where('cupom', '=', $cupom->id)->orderBy('id', 'ASC')->get();
        $dataAtual = Carbon::now();
        $docs = Cdoc::where('cupom', '=', $cupom->id)->orderBy('nome', 'ASC')->get();

        return view('Cupom.visualizar', array(
            'cupom' => $cupom,
            'servicos'=> $servicos,
            'itens' => $itens,
            'docs' => $docs,
            'imagens' => $fotos,
            'dataAtual' => $dataAtual
        ));
    }

    public function excluir($id){

        $cupom = Cupom::find($id);

        $cupom->status = 0;
        
        if ($cupom->save()){
            flash('Excluido com Sucesso')->success();
            return redirect()->back();
        }else{
            flash('Falha, tente novamente')->danger();
            return redirect()->back();
        }
    }

    public function addItem(Request $request){

        $ret = [];

        $data = $request->all();

        $cupons = Itens::where('cupom', '=', $data['cupom'])->count();

        $cpm = Cupom::find($data['cupom']);

        $valor = $cpm->valor_total;

        $data['valor'] = str_replace(',', '.' ,str_replace('.', '', $data['valor'])) ;

        $item = new Itens($data);

        if( $item->save() ){
            $ret['cod'] = 1;
            $ret['id'] = $item->id;
            $ret['descricao'] = $item->descricao;
            $ret['valor'] =  number_format($item->valor, 2, ',', '.');
            $ret['contador'] = $cupons+1; 

            $valor = $valor+$item->valor;

            $cpm->valor_total = $valor;
            $cpm->save();

            $ret['valor_total'] = number_format($cpm->valor_total, 2, ',', '.');
        }else{
            $ret['cod'] = 0;
            $ret['msg'] = 'Algo deu Errado';
        }
        
        return json_encode($ret);
    }

    public function removeItem($id){

        $item = Itens::find($id);
        $cpm = Cupom::where('id', $item->cupom)->first(); 

        $valor = $cpm->valor_total;
        $valor = $valor - $item->valor;
        if($item->delete()){
            Cupom::where('id', $cpm->id)->update(['valor_total' => $valor]);
        }

    

        return true;
    }

    public function removeFoto($id) {

        $arquivo = Cimagens::findOrFail($id);
        $image_path = public_path('images/cupons/'.$arquivo->img);

        if (File::exists($image_path)) {
            //File::delete($image_path);
            unlink($image_path);
        }
        $arquivo->delete();

        return true;
    }

    public function update($id,Request $request){

        $validator = $this->validator($request->all());

        if ($validator->fails()) {

            flash('Preencha todos os campos obrigatórios')->warning();
            return redirect()->back();

        }else{

            $cupom = Cupom::findOrFail($id);

            $data= $request->all();

            $data['data'] = Carbon::now();

           // $data['valor_total'] = '0';

            $data['inicio'] = Carbon::createFromFormat('d/m/Y', $data['inicio']);

            $data['fim'] = Carbon::createFromFormat('d/m/Y', $data['fim']);

          //  $data['usuario_id'] = Auth::user()->id;

            if($cupom->fill($data)->save()){
                flash('Salvo com sucesso')->success();
                return redirect()->route('cupom.visualizar', $cupom->id);
            }else{
                    flash('Erro ao Cadastrar, tente novamente')->warning();
                return redirect()->back();
            }

        }   

    }

    public function uploadImage(Request $request){
        $image = $request->img;
        $cupom = $request->cupom;
        $nome = $request->nome;

        

        $data = $request->all();

        list($type, $image) = explode(';', $image);

        list(, $image)      = explode(',', $image);

        $image = base64_decode($image);

        $image_name= time().'.png';

        $path = public_path('images/cupons/'.$image_name);
        $data['img'] = $image_name;
        $ret = new Cimagens($data);
        $ret->save();

        file_put_contents($path, $image);

        return response()->json([
            'status'=>true,
            'img' => '"' . $ret->img .  '"',
            'nome' =>  $ret->nome  ,
            'id' => $ret->id
            ]);

        }

        public function alteraStatus($id, Request $request){

            $cupom = Cupom::find($id);

            $dados = $request->all();


            $cupom->pago = (int)$dados['status'];

            $cupom->save();

            
            return json_encode('success');
        }


        public function uploadDoc($id,Request $request)
        {
        
            // Define o valor default para a variável que contém o nome da imagem
                $nameFile = null;
    
                // Verifica se informou o arquivo e se é válido
                if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
    
                    // Define um aleatório para o arquivo baseado no timestamps atual
                    $name = md5(date('Y-m-d H:i:s:u'));
    
                    // if(@is_array(getimagesize($request->arquivo))){
                    //     $image = true;
                    // } else {
                    //     $image = false;
                    // }
    
                    // if($image == false){
                    //     flash('Erro, esse arquivo não é uma imagem válida')->warning();
                    //     return redirect()->back();
                    // }
                    // Recupera a extensão do arquivo
                    $extension = $request->arquivo->extension();
    
    
                    // Define finalmente o nome
                    $nameFile = "{$name}.{$extension}";
    
                    //$path = public_path('images/despesas/'.$nameFile);
        
                   // $upload = file_put_contents($path, $request->arquivo);
    
                    // Faz o upload:
                    //$upload = Storage::disk('public')->put($nameFile, $request->arquivo);
                    $upload = $request->arquivo->storeAs('', $nameFile, 'docs');
                    //$upload = $request->arquivo->store('public', ['disk' => 'public']);
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
    
                    // Verifica se NÃO deu certo o upload (Redireciona de volta)
                    if ( !$upload ){
                        flash('Erro ao fazer upload da imagem,')->warning();
                        return redirect()->back();
                    }
    
                    $file = new Cdoc();
                    $file->cupom = $id;
                    $file->nome = $request->nome;
                    $file->arquivo = $nameFile;
                    $file->save();
             
                    return redirect()->back();
 
                }
            
        }

        public function downloadDoc($name){

            $file_path = public_path('images/docs/'.$name);
    
            return response()->download($file_path);
        }

        public function deleteDoc($id) {

            $arquivo = Cdoc::findOrFail($id);
            $image_path = public_path('images/docs/'.$arquivo->arquivo);
    
            if (File::exists($image_path)) {
               
                //File::delete($image_path);
                unlink($image_path);
            }
      
            $arquivo->delete();
    
            flash('Deletado com sucesso')->success();
            return redirect()->back();
        }
}


