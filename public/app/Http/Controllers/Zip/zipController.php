<?php

namespace App\Http\Controllers\Zip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Filesystem\Filesystem;
use Session;
use Illuminate\Support\Facades\Storage;
use File;
use App\Models\Zip;
use Carbon\Carbon;


class zipController extends Controller
{
    public function downloadZip(){

        
        $imagens = Session::get('download.images');
        $arquivos = Session::get('download.arquivos');
      
        $dirName = public_path() . '/images/despesas/';
        $dirName2 = public_path() . '/storage/arquivos/';

        // Choose a name for the archive.
        $zipFileName =  'Download_'.uniqid('oetker_', true).'.zip';

        // Create "MyCoolName.zip" file in public directory of project.
        $zip = new ZipArchive;

        if ( $zip->open( public_path() . '/images/zip/' . $zipFileName, ZipArchive::CREATE ) === true )
        {
            // Copy all the files from the folder and place them in the archive.
            if($imagens){
                foreach ( $imagens as $key => $p ){
                    $zip->addFile( $dirName.$p, $p );
                }
            }

            if($arquivos){   
                foreach ( $arquivos as $key => $a ){
                    $zip->addFile( $dirName2.$a, $a );
                }
            }


            // foreach ( glob( $dirName . '/*' ) as $fileName )
            // {
            //     $file = basename( $fileName );
              
            // }

            $zip->close();

            Session::forget('download');

            // $headers = array(
            //     'Content-Type' => 'application/octet-stream',
            // );

            return response()->download(public_path() . '/images/zip/'. $zipFileName);

    }
}

    public function deleteZips(){

        $zips = Zip::where( 'created_at', '<', Carbon::now()->subDays(7))
        ->get();

        if($zips){
            foreach($zips as $z){
                $path = public_path('images/zip/'.$z->arquivo);

                if (File::exists($path)) {
                    unlink($path);
                }

                $z->delete();
            }
        }

        $file = new Filesystem;
        $file->cleanDirectory(public_path('images/zip'));

        exit;

    }

    public function compartilhar(){

        $imagens = Session::get('download.images');
        $arquivos = Session::get('download.arquivos');
        $dirName = public_path() . '/images/despesas/';
        $dirName2 = public_path() . '/storage/arquivos/';

        // Choose a name for the archive.
        $zipFileName =  'Download_'.uniqid('oetker_', true).'.zip';

        $zip = new ZipArchive;

        if ( $zip->open( public_path() . '/images/zip/' . $zipFileName, ZipArchive::CREATE ) === true )
        {
            // Copy all the files from the folder and place them in the archive.
            if($imagens){
                foreach ( $imagens as $key => $p ){
                    $zip->addFile( $dirName.$p, $p );
                }
            }

            if($arquivos){   
                foreach ( $arquivos as $key => $a ){
                    $zip->addFile( $dirName2.$a, $a );
                }
            }


            $zip->close();
        }

        $arq = new Zip();
        $arq->hash = md5(date('Y-m-d H:i:s:u'));
        $arq->arquivo = $zipFileName;
        
        $arq->save();

        Session::forget('download');

        return json_encode($arq->hash);
        exit;
    }

    public function fazerDownload($hash){

        $zip = Zip::where('hash', $hash)->first();

        if(!$zip){
            abort(404);
        }else{

            $file_path = public_path('images/zip/'.$zip->arquivo);

            return response()->download($file_path);
    
        }

    }

}