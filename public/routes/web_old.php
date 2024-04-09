<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::any('/foo', function () {
    Artisan::call('storage:link');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Usuarios
Route::middleware('auth')->group(function () {
    Route::get('/usuarios', 'Usuarios\UsuariosController@index')->name('user.index');
    Route::get('/usuarios/edit/{id}', 'Usuarios\UsuariosController@edit')->name('user.edit');
    Route::post('/usuarios/update/{id}', 'Usuarios\UsuariosController@update')->name('user.update');
    Route::post('/usuarios/store',  'Usuarios\UsuariosController@store')->name('user.store');
    Route::post('/usuarios/validar',  'Usuarios\UsuariosController@validar')->name('user.validar');
    Route::post('/usuarios/validarUpdate',  'Usuarios\UsuariosController@validarUpdate')->name('user.validar-update');
    Route::post('crop-image', ['as' => 'upload.image', 'uses' => 'Usuarios\UsuariosController@uploadImage']);
});

//Cupom
//Route::middleware('auth')->group(function () {
Route::group(['prefix' => 'cupom'], function () {
    Route::get('index', 'Cupom\CupomController@index')->name('cupom.index');
    Route::get('visualizar/{id}', 'Cupom\CupomController@visualizar')->name('cupom.visualizar');
    Route::get('excluir/{id}', 'Cupom\CupomController@excluir')->name('cupom.excluir');
    Route::post('store', 'Cupom\CupomController@store')->name('cupom.store');
    Route::post('update/{id}', 'Cupom\CupomController@update')->name('cupom.update');
    Route::post('add-item', 'Cupom\CupomController@addItem');
    Route::get('remove-item/{id}', 'Cupom\CupomController@removeItem');
    Route::get('remove-foto/{id}', 'Cupom\CupomController@removeFoto');
    Route::post('/crop', ['as' => 'upload.cupom', 'uses' => 'Cupom\CupomController@uploadImage']);
    Route::post('altera-status/{id}', 'Cupom\CupomController@alteraStatus');
    Route::post('upload-doc/{id}', 'Cupom\CupomController@UploadDoc')->name('cupom.doc');
    Route::get('download-doc/{name}', 'Cupom\CupomController@downloadDoc')->name('cupom.download');
    Route::get('delete-doc/{id}', 'Cupom\CupomController@deleteDoc')->name('cupom.deletedoc');
    Route::post('/cupom/finalizar/{id}', 'Cupom\CupomController@finalizar')->name('cupom.finalizar');
});

//Servicos
Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'servicos'], function () {
        Route::get('index', 'Servicos\ServicosController@index')->name('servicos.index');
        Route::get('excluir/{id}', 'Servicos\ServicosController@excluir')->name('servicos.excluir');
        Route::post('store', 'Servicos\ServicosController@store')->name('servicos.store');
        Route::post('update/{id}', 'Servicos\ServicosController@update')->name('servicos.update');
    });
});

//Images
Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'images'], function () {
        Route::get('index', 'Images\ImagesController@index')->name('images.index');
        Route::get('delete-pasta/{id}', 'Images\ImagesController@deletePasta')->name('delete-pasta-image');
        Route::post('edit', 'Images\ImagesController@edit')->name('edit-image');
        Route::post('edit-pasta-image', 'Images\ImagesController@editPasta')->name('edit-pasta-image');
        Route::post('retorna', 'Images\ImagesController@retorna')->name('retorna-image');
        Route::get('delete/{id}', 'Images\ImagesController@delete')->name('delete-image');
        Route::get('visualizar/{id}', 'Images\ImagesController@visualizar')->name('visualizar-imagem');
        Route::post('nova-pasta', 'Images\ImagesController@novaPasta')->name('nova-pasta-imagem');
        Route::post('nova-subpasta', 'Images\ImagesController@novaSubpasta')->name('nova-subpasta-image');
        Route::post('upload', 'Images\ImagesController@upload')->name('upload-image');
        Route::post('upload-multiplas', 'Images\ImagesController@uploadMultiplas')->name('upload-multiplas');
        Route::post('retorna-pasta-image', 'Images\ImagesController@retornaPasta')->name('retorna-pasta-image');
        Route::post('adicionar-na-sessao', 'Images\ImagesController@adicionarNaSessao')->name('adiciona-sessao');
        Route::post('remover-da-sessao', 'Images\ImagesController@removerDaSessao')->name('remover-sessao');
        Route::get('destruir', 'Images\ImagesController@destruir');
        Route::get('atualizar-sessao', 'Images\ImagesController@atualizarSessao');
        Route::post('move-pasta-image', 'Images\ImagesController@movePasta')->name('move-pasta-image');
    });
});

//zip
Route::get('download-zip', 'Zip\zipController@downloadZip')->name('download-zip');
Route::get('delete-zips', 'Zip\zipController@deleteZips')->name('delete-zips');
Route::get('compartilhar-zip', 'Zip\zipController@compartilhar')->name('compartilhar-zips');
Route::get('download-zips/{hash}', 'Zip\zipController@fazerDownload')->name('download-zips');


Route::get('images/download/{name}', 'Images\ImagesController@download')->name('download-image');
Route::get('broadside/download/{name}', 'Arquivo\ArquivoController@download')->name('download-arquivo');

// #FP Adicionado rota pra exclusão do usuario
Route::post('/usuarios/remove', 'Usuarios\UsuariosController@removeUser')->name('usuarios.remove');
// Adicione estas linhas no seu arquivo routes/web.php após a definição das rotas existentes

Route::get('/relatorio', 'Report\ReportController@index')->name('reports.index');
Route::post('/relatorio/gerar', 'Report\ReportController@generate')->name('reports.generate');
