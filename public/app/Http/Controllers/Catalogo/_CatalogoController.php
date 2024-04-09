<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Catalogo;
use Carbon\Carbon;
use Auth;
use File;

class CatalogoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginas = Catalogo::orderBy('pagina', 'ASC')->get();

        return view('Catalogo.index',[
            'paginas' => $paginas
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'arquivo' => ['required']
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imagem = Catalogo::find($id);
        $pag = $imagem->pagina;
        $proximas = Catalogo::where('pagina', '>', $pag)->orderBy('pagina')->get();

        if($imagem){
        $image_path = public_path('storage/catalogo/'.$imagem->arquivo);

        if (File::exists($image_path)) {
            unlink($image_path);
        }
        $imagem->delete();
        }

        if($proximas){
            foreach($proximas as $p){
                $nova = $p->pagina - 1;

                $nome = $p->arquivo;
                $ret = Catalogo::where('id', $p->id)
                ->update(['pagina' => $nova, 'arquivo' => $nova.'.jpg']);

                rename(public_path('/storage/catalogo/'.$nome), public_path('/storage/catalogo/'.$nova.'.jpg'));
            }

        }

        return redirect()->back()->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function updatePosicao(Request $request){
        if(isset($request['update'])){
            foreach($request['positions'] as $position){
                $index = $position[0];
                $newPosition = $position[1];

                $arquivo = Catalogo::find($index);
                $nome = $arquivo->arquivo;
                $unique = uniqid();
             $ret =  Catalogo::where('id', $index)
               ->update(['pagina' => $newPosition, 'arquivo' => $unique.'.jpg']);

               rename(public_path('/storage/catalogo/'.$nome), public_path('/storage/catalogo/'.$unique.'.jpg'));

            }

            $files = Catalogo::orderBy('pagina', 'ASC')->get();

            foreach($files as $f){
                $n = $f->arquivo;
                $f->update(['arquivo' => $f->pagina.'.jpg']);

                rename(public_path('/storage/catalogo/'.$n), public_path('/storage/catalogo/'.$f->pagina.'.jpg'));
            }

            exit('success');
        }
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
            $ultimo = Catalogo::max('pagina');

            if($ultimo){
                $pagina = $ultimo + 1;
            }else{
                $pagina = 1;
            }


            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {

                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = md5(date('Y-m-d H:i:s:u'));

                if(@is_array(getimagesize($request->arquivo))){
                    $image = true;
                } else {
                    $image = false;
                }

                if($image == false){
                    flash('Erro, esse arquivo não é uma imagem válida')->warning();
                    return redirect()->back();
                }
                // Recupera a extensão do arquivo
                $extension = $request->arquivo->extension();


                // Define finalmente o nome
                $nameFile = "{$pagina}.jpg";

                // Faz o upload:
                $upload = $request->arquivo->storeAs('public/catalogo', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                // Verifica se NÃO deu certo o upload (Redireciona de volta)
                if ( !$upload ){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }

                $paginas = Catalogo::all();

                $file = new Catalogo();
                $file->pagina = $pagina;
                $file->arquivo = $nameFile;
                $file->save();

                if(!$file){
                    flash('Erro ao fazer upload da imagem,')->warning();
                    return redirect()->back();
                }


                flash('Upload feito com sucesso')->success();
                return redirect()->back()->header('Cache-Control', 'no-store, no-cache, must-revalidate');


            }
        }
    }

    public function catalogo(){

        $paginas = Catalogo::orderBy('pagina', 'ASC')->get();
        return view('Catalogo.catalogo', [
            'paginas' => $paginas
        ]);

    }
}
