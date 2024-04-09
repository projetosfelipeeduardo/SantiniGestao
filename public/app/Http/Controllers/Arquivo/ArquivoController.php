<?php

namespace App\Http\Controllers\Arquivo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use File;
use Illuminate\Support\Facades\Validator;
use App\Models\Pasta;
use App\Models\Arquivo;


class ArquivoController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nome' => ['required', 'min:3'],
            'arquivo' => ['required']
            ]);
    }


    
    protected function validator4(array $data)
    {
        return Validator::make($data, [
            'nome' => ['required', 'min:3'],
            'descricao' => ['required']
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
            $arquivosBusca = Arquivo::where('nome', 'LIKE', '%'. $request['nome'] .'%')->paginate(15);
            $pastasBusca = Pasta::where('nome', 'LIKE', '%'. $request['nome'] .'%')->paginate(15);

            if(!$request['nome']){
                $filtro = null;
            }else{
                $nome = $request['nome'];
            }
        }

        $pastas = Pasta::where('subpasta', 0)->paginate(15);

        $listagem = Pasta::orderBy('nome', 'asc')->get();


        return view('Arquivo.index', [
            'pastas' => $pastas,
            'listagem' => $listagem,
            'arquivosBusca'  => $arquivosBusca,
            'pastasBusca' => $pastasBusca,
            'filtro' => $filtro,
            'nome' => $nome
        ]);
    }

    public function novaPasta(Request $request){

        $validator = $this->validator2($request->all());

        if ($validator->fails()) {
            flash('O nome deve conter pelo menos 3 caracteres')->warning();
            return redirect()->back();

        }else{
            $data = $request->all();
            $data['subpasta'] = 0;

            $pasta = new Pasta($data);


            if($pasta->save()){
                flash('Pasta Criada com sucesso')->success();
                return redirect()->back();
            }else{
                flash('Erro ao criar pasta')->warning();
                return redirect()->back();
            }
        }
    }

    public function movePasta(Request $request){

        
        $validator = $this->validator3($request->all());

        if ($validator->fails()) {
            flash('Selecione Uma pasta')->warning();
            return redirect()->back();

        }else{

        $pasta = Pasta::findOrFail($request->pasta);
        
        if($request->pasta_pai == 'raiz'){
            $pasta->pasta_pai = null;
            $pasta->subpasta = 0;
        }else{

        $pasta->pasta_pai = $request->pasta_pai;
        $pasta->subpasta = 1;
        }

        $pasta->save();

        flash('Pasta Movida com sucesso')->success();
        return redirect()->back();
        
    }

    }



    public function novaSubpasta(Request $request){

        $data = $request->all();
        $data['subpasta'] = 1;

        $pasta = new Pasta($data);


        if($pasta->save()){
            flash('Pasta Criada com sucesso')->success();
            return redirect()->back();
        }else{
            flash('Erro ao criar pasta')->warning();
            return redirect()->back();
        }
    }

    public function edit(Request $request){

        $validator = $this->validator2($request->all());

        if ($validator->fails()) {
            flash('Erro, verifique os dados inseridos')->warning();
            return redirect()->back();

        }else{
            $data = $request->all();

            $img = Arquivo::find($request->id);
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
    }

    public function editPasta(Request $request){

        $validator = $this->validator2($request->all());

        if ($validator->fails()) {
            flash('Erro, verifique os dados inseridos')->warning();
            return redirect()->back();

        }else{
            $data = $request->all();

            $img = Pasta::find($request->id);
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

    public function deletePasta($id){

        $pasta = Pasta::where('id' , $id)->orWhere('pasta_pai', $id)->get();

        $subpastas = Pasta::where('pasta_pai' , $id)->get();

        if($subpastas){
            foreach($subpastas as $s){
                $subsub = Pasta::where('pasta_pai', $s->id)->get();

                foreach($subsub as $sub){
                    $arq = Arquivo::where('pasta', $sub->id)->get();

                    if($arq){
                        foreach($arq as $a){
        
                            $image_path = public_path('storage/arquivos/'.$a->arquivo);
        
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
            $arquivos = Arquivo::where('pasta', $p->id)->get();

            if($arquivos){
                foreach($arquivos as $a){

                    $image_path = public_path('storage/arquivos/'.$a->arquivo);

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

    public function visualizar($id){

        $icones = array(
            "zip" => '<i style="color: purple; font-size: 18px;" class="fa fa-file-archive-o" aria-hidden="true"></i>',
            'pdf' => '<i style="color: red; font-size: 18px;" class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            'docx' => '<i style="color: #1059b3; font-size: 18px;" class="fa fa-file-text" aria-hidden="true"></i>',
            'doc' => '<i style="color: #1059b3; font-size: 18px;" class="fa fa-file-text" aria-hidden="true"></i>',
            'jpg' => '<i style="color: gold; font-size: 18px;" class="fa fa-file-image-o" aria-hidden="true"></i>',
            'jpeg' => '<i style="color: gold; font-size: 18px;" class="fa fa-file-image-o" aria-hidden="true"></i>',
            'png' => '<i style="color: gold; font-size: 18px;" class="fa fa-file-image-o" aria-hidden="true"></i>',
            'gif' => '<i <i style="color:gold; font-size: 18px;" class="fa fa-file-image-o" aria-hidden="true"></i>',
            'xlsx' => '<i style="color: #1b926c; font-size: 18px;" class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'xlsm' => '<i style="color: #1b926c; font-size: 18px;" class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'xlsb' => '<i style="color: #1b926c; font-size: 18px;" class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'xltx' => '<i style="color: #1b926c; font-size: 18px;" class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'xls' => '<i style="color: #1b926c; font-size: 18px;" class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'ppt' => '<i style="color: #fd962c; font-size: 18px;" class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'pptx' => '<i style="color: #fd962c; font-size: 18px;" class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',

        );

        $pasta = Pasta::find($id);
        $arquivos = Arquivo::where('pasta', $pasta->id)->paginate(8);
        $subpastas = Pasta::where('pasta_pai', $pasta->id)->get();
        $listagem = Pasta::orderBy('nome', 'asc')->get();

        return view('Arquivo.visualizar',[
            'pasta' => $pasta,
            'listagem' => $listagem,
            'arquivos' => $arquivos,
            'icones' => $icones,
            'subpastas' => $subpastas
        ]);
    }



    public function download($name){

        $file_path = public_path('storage/arquivos/'.$name);
        return response()->download($file_path);
        exit;
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
                $name =  md5(date('Y-m-d H:i:s:u'));

                // Recupera a extensão do arquivo
                $extension = $request->arquivo->getClientOriginalExtension();


                // Define finalmente o nome
                $nameFile = "{$name}.{$extension}";

                  // Faz o upload:
                  $upload = $request->arquivo->storeAs('public/arquivos', $nameFile);
                  // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                  // Verifica se NÃO deu certo o upload (Redireciona de volta)
                  if ( !$upload ){
                      flash('Erro ao fazer upload da imagem,')->warning();
                      return redirect()->back();
                  }

                $file = new Arquivo();
                $file->extensao = $extension;
                $file->nome = $request->nome;
                $file->arquivo = $nameFile;
                $file->descricao = $request->descricao;
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

    public function cadastraLink(Request $request){

        $validator = $this->validator4($request->all());

        if ($validator->fails()) {
            flash('Erro ao cadastrar')->warning();
            return redirect()->back();

        }else{

            $file = new Arquivo();
            $file->extensao = 'link';
            $file->nome = $request->nome;
            $file->descricao = $request->descricao;
            $file->pasta = $request->pasta;
            $file->arquivo = 'link';
       
        
            if($file->save()){
                flash('Link cadastrado com sucesso')->success();
                return redirect()->back();
            }else{
                flash('Erro ao cadastrar link')->warning();
                return redirect()->back();
            }

         }
    }

    public function retorna(Request $request){

    $array = [];
    $img = Arquivo::find($request->id);

    $array['nome'] = $img->nome;
    $array['arquivo'] = $img->arquivo;
    $array['id'] = $img->id;
    $array['descricao'] = $img->descricao;

    return json_encode($array);
    exit;
    }

    public function retornaPasta(Request $request){

        $array = [];
        $img = Pasta::find($request->id);

        $array['nome'] = $img->nome;
        $array['id'] = $img->id;

        return json_encode($array);
        exit;
        }

    public function delete($id) {

        $arquivo = Arquivo::findOrFail($id);
        $image_path = public_path('storage/arquivos/'.$arquivo->arquivo);

        if (File::exists($image_path)) {
            //File::delete($image_path);
            unlink($image_path);
        }
        $arquivo->delete();

        flash('Deletado com sucesso')->success();
        return redirect()->back();
    }

    public function uploadMultiplos(Request $request){

        if (($request->has('arquivos'))) {
            $files = $request->file('arquivos');

            $destinationPath = 'public/arquivos';
            foreach ($files as $file) {

                $fileName = $myuid = uniqid('gfg', true);
                $extension = $file->getClientOriginalExtension();
                $storeName = $fileName . '.' . $extension;
                // Store the file in the disk
                $upload = $file->storeAs('public/arquivos', $storeName);

                if ( !$upload ){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }

                $img = new Arquivo();
                $img->nome = $file->getClientOriginalName();
                $img->arquivo = $storeName;
                $img->descricao = '';
                $img->extensao = $extension;
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
      
        Session::push('download.arquivos', $request->valor);
        Session::save();
        print_r(Session::all());
        echo count(Session::get('download'));

        return json_encode('ok');
  

    exit;
 }

 public function removerDaSessao(Request $request){
     
    $downloads = Session::get('download.arquivos');

    foreach($downloads as $key => $d){
        if($d == $request->valor){
            unset($downloads[$key]);
        }
    }

    Session::put('download.arquivos', $downloads);
    Session::save();
    print_r(Session::all());

    return json_encode('ok');
  
    exit;
 }





}
