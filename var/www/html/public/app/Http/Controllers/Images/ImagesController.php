<?php

namespace App\Http\Controllers\Images;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Pasta_imagens;
use File;
use Storage;
use Auth;
use Illuminate\Http\UploadedFile;
use App\Models\Images;
use Session;
class ImagesController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nome' => ['required', 'min:3'],
            'arquivo' => ['required']
            ]);
    }

    protected function validator2(array $data)
    {
        return Validator::make($data, [
            'nome' => ['required', 'min:3'],
            ]);
    }

    protected function validator3(array $data)
    {
        return Validator::make($data, [
            'pasta_pai' => ['required'],
            ]);
    }

    public function index(Request $request){

        $arquivosBusca = null;
        $pastasBusca = null;
        $filtro = null;
        $nome = null;

        if($request['acao']){
            $filtro = $request['acao'];

            if(Auth::user()->nivel_acesso == 2){
                $arquivosBusca = Images::where([['nome', 'LIKE', '%'. $request['nome'] .'%'], ['user_id', Auth::user()->id]])->paginate(10);
            $pastasBusca = Pasta_imagens::where([['nome', 'LIKE', '%'. $request['nome'] .'%'],  ['user_id', Auth::user()->id] ])->paginate(10);
            }else{
                $arquivosBusca = Images::where('nome', 'LIKE', '%'. $request['nome'] .'%')->paginate(10);
                $pastasBusca = Pasta_imagens::where('nome', 'LIKE', '%'. $request['nome'] .'%')->paginate(10);
            }

            if(!$request['nome']){
                $filtro = null;
            }else{
                $nome = $request['nome'];
            }
        }

        if(Auth::user()->nivel_acesso == 2){
            $pastas = Pasta_imagens::where([['subpasta', 0], ['user_id', Auth::user()->id] ])->paginate(15);
        }else{
            $pastas = Pasta_imagens::where('subpasta', 0)->paginate(15);
        }
    
   

        return view('Images.index', [
            'pastas' => $pastas,
            'arquivosBusca'  => $arquivosBusca,
            'pastasBusca' => $pastasBusca,
            'filtro' => $filtro,
            'nome' => $nome
        ]);

    }

    public function novaSubpasta(Request $request){

        $data = $request->all();
        $data['subpasta'] = 1;
        $data['user_id'] = Auth::user()->id;

        $pasta = new Pasta_imagens($data);


        if($pasta->save()){
            flash('Pasta Criada com sucesso')->success();
            return redirect()->back();
        }else{
            flash('Erro ao criar pasta')->warning();
            return redirect()->back();
        }
    }

    public function delete($id) {

        $arquivo = Images::findOrFail($id);
        $image_path = public_path('images/despesas/'.$arquivo->arquivo);

        print_r($image_path);
   

        if (File::exists($image_path)) {
           
            //File::delete($image_path);
            unlink($image_path);
        }
  
        $arquivo->delete();

        flash('Deletado com sucesso')->success();
        return redirect()->back();
    }

    public function deletePasta($id){

        $pasta = Pasta_imagens::where('id' , $id)->orWhere('pasta_pai', $id)->get();

        
        $subpastas = Pasta_imagens::where('pasta_pai' , $id)->get();

        if($subpastas){
            foreach($subpastas as $s){
                $subsub = Pasta_imagens::where('pasta_pai', $s->id)->get();

                foreach($subsub as $sub){
                    $arq = Images::where('pasta', $sub->id)->get();

                    if($arq){
                        foreach($arq as $a){
        
                            $image_path = public_path('images/despesas/'.$a->arquivo);
        
                            if (File::exists($image_path)) {
                                unlink($image_path);
                            }
        
                            $a->delete();
                        }
                    }
                    $sub->delete();
                } 
            }
        }

        foreach($pasta as $p){
            $arquivos = Images::where('pasta', $p->id)->get();

            if($arquivos){
                foreach($arquivos as $a){

                    $image_path = public_path('storage/images/'.$a->arquivo);

                    if (File::exists($image_path)) {
                        unlink($image_path);
                    }

                    $a->delete();
                }
            }

            $p->delete();
        }
        flash('Arquivos Deletados')->success();
        return redirect()->back();
        exit;

    }

    public function novaPasta(Request $request){
        $data = $request->all();
        $data['status'] = 1;
        $data['subpasta'] = 0;
        $data['user_id'] = Auth::user()->id;

        $validator = $this->validator2($request->all());

        if ($validator->fails()) {
            flash('O nome deve conter pelo menos 3 caracteres')->warning();
            return redirect()->back();

        }else{

        $pasta = new Pasta_imagens($data);

        $pasta->save();

        if($pasta){
            return redirect()->back();
        }

        return redirect()->back();
        }
    }

    public function editPasta(Request $request){

        $validator = $this->validator2($request->all());

        if ($validator->fails()) {
            flash('Erro, verifique os dados inseridos')->warning();
            return redirect()->back();

        }else{
            $data = $request->all();

            $img = Pasta_imagens::find($request->id);
            $img->nome = $request->nome;

            if($img->save()){
                flash('Editado com sucesso')->success();
                return redirect()->back();
            }else{
                flash('Falha ao Editar')->danger();
                return redirect()->back();
            }
        }
    }

    public function visualizar($id){

        $pasta = Pasta_imagens::find($id);

        if(Auth::user()->id != $pasta->user_id && Auth::user()->nivel_acesso == 2){
            die('Permissão negada');
        }
        $arquivos = Images::where('pasta', $pasta->id)->paginate(9);
        $subpastas = Pasta_imagens::where('pasta_pai', $pasta->id)->get();


        return view('Images.visualizar',[
            'pasta' => $pasta,
            'arquivos' => $arquivos,
            'subpastas' => $subpastas
        ]);
    }



    public function upload(Request $request)
    {


        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            flash('Erro, verifique os dados inseridos')->warning();
            return redirect()
                        ->back();


        }else{

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
                $upload = $request->arquivo->storeAs('', $nameFile, 'public');
                //$upload = $request->arquivo->store('public', ['disk' => 'public']);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                // Verifica se NÃO deu certo o upload (Redireciona de volta)
                if ( !$upload ){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }

                $file = new Images();
                $file->user_id = Auth::user()->id;
                $file->nome = $request->nome;
                $file->arquivo = $nameFile;
                $file->descricao = $request->descricao;
                $file->status = 1;
                $file->pasta = $request->pasta;
                $file->save();

                if(!$file){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }


                flash('Upload feito com sucesso')->success();
                return redirect()->back();


            }
        }
    }

    public function download($name){

        $file_path = public_path('images/despesas/'.$name);

        return response()->download($file_path);
    }


    public function retorna(Request $request){

    $array = [];
    $img = Images::find($request->id);

    $array['nome'] = $img->nome;
    $array['arquivo'] = $img->arquivo;
    $array['id'] = $img->id;
    $array['descricao'] = $img->descricao;

    return json_encode($array);
    exit;

    }

    public function retornaPasta(Request $request){

        $array = [];
        $img = Pasta_imagens::find($request->id);

        $array['nome'] = $img->nome;
        $array['id'] = $img->id;

        return json_encode($array);
        exit;
        }

    public function edit(Request $request){
        $data = $request->all();

        $img = Images::find($request->id);
        $img->nome = $request->nome;
        $img->descricao = $request->descricao;

        if($img->save()){
            flash('Editado com sucesso')->success();
            return redirect()->back();
        }else{
            flash('Falha ao Editar')->danger();
            return redirect()->back();
        }

    }

    public function uploadMultiplas(Request $request){

        if (($request->has('arquivos'))) {
            $files = $request->file('arquivos');
           
            // echo '<pre>'; 
            //     var_dump($files);
            // echo'</pre>';

            // die;

            $destinationPath = 'public/images';
            foreach ($files as $file) {

                $fileName = '';
                $storeName = '';
                $extension = '';

                // if(@is_array(getimagesize($file))){
                //     $image = true;
                // } else {
                //     $image = false;
                // }

                // if($image == false){
                //     flash('Erro, Contém arquivos inválidos')->warning();
                //     return redirect()->back();
                // }

                $fileName = md5(date('Y-m-d H:i:s:u')).rand (1,9999999 ) ;
                $extension = $file->getClientOriginalExtension();
                $storeName = $fileName . '.' . $extension;
                // Store the file in the disk
                //$upload = $file->storeAs('public/images', $storeName);
               // $upload = $file->store('despesas', ['disk' => 'public']);
                $upload = $file->storeAs('', $storeName, 'public');

                if ( !$upload ){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }

     

                $img = new Images();
                $img->nome = $file->getClientOriginalName();
                $img->arquivo = $storeName;
                $img->descricao = '';
                $img->user_id = Auth::user()->id;
                $img->status = 1;
                $img->pasta = $request->pasta;
                $img->save();

                if(!$img){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }
                
            }



            flash('Upload feito com sucesso')->success();
            return redirect()->back();
        }

    }

    public function adicionarNaSessao(Request $request){
      
           Session::push('download.images', $request->valor);
           Session::save();
           print_r(Session::all());
           echo count(Session::get('download'))
           ;

           return json_encode('ok');
  
       exit;
    }

    public function removerDaSessao(Request $request){
        
         $downloads = Session::get('download.images');

         foreach($downloads as $key => $d){
             if($d == $request->valor){
                 unset($downloads[$key]);
             }
         }

        Session::put('download.images', $downloads);
        Session::save();

        return json_encode('ok');
  

   exit;
    }

    public function destruir(){
        Session::forget('download');

        print_r(Session::all());
    }

    public function atualizarSessao(){
     
        $contador = 0;
        $arquivos = 0;
        $imagens = 0;
    
        if(Session::has("download.arquivos")){
            $arquivos = Session::get('download.arquivos');
            $arquivos = count($arquivos);
        }
    
        if(Session::has("download.images")){
            $imagens = Session::get('download.images');
            $imagens = count($imagens);
        }
       
        $contador = ($arquivos + $imagens);
    
        return json_encode($contador);
        exit;
    }

     public function movePasta(Request $request){

        
        $validator = $this->validator3($request->all());

        if ($validator->fails()) {
            flash('Selecione Uma pasta')->warning();
            return redirect()->back();

        }else{

        $pasta = Pasta_imagens::findOrFail($request->pasta);


        $pasta->pasta_pai = $request->pasta_pai;
        $pasta->subpasta = 1;

        $pasta->save();

        flash('Pasta Movida com sucesso')->success();
        return redirect()->back();
        
    }

    }

    
}
